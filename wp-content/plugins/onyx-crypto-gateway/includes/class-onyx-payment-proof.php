<?php
/**
 * Payment proof upload (screenshot/PDF + transaction hash) for on-hold crypto orders.
 * Files are stored outside public listing in uploads/onyx-payment-proofs/ (deny-all .htaccess,
 * random names) and served to shop managers only through an authenticated admin endpoint.
 *
 * @package onyx-crypto-gateway
 */

defined( 'ABSPATH' ) || exit;

class Onyx_Payment_Proof {

	const MAX_BYTES = 5242880; // 5 MB.

	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'handle_upload' ) );
		add_action( 'woocommerce_view_order', array( __CLASS__, 'view_order' ), 5 );
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( __CLASS__, 'admin_panel' ) );
		add_action( 'admin_post_onyx_view_proof', array( __CLASS__, 'serve' ) );
	}

	private static function dir() {
		$u   = wp_upload_dir();
		$dir = trailingslashit( $u['basedir'] ) . 'onyx-payment-proofs';
		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
			file_put_contents( $dir . '/.htaccess', "Require all denied\nDeny from all\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			file_put_contents( $dir . '/index.php', '<?php // Silence.' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}
		return $dir;
	}

	public static function view_order( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order && 'onyx_crypto' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
			Onyx_Crypto_Gateway::payment_box( $order );
			self::form( $order );
		}
	}

	public static function form( WC_Order $order ) {
		if ( 'onyx_crypto' !== $order->get_payment_method() ) {
			return;
		}
		$done = $order->get_meta( '_onyx_proof_file' ) || $order->get_meta( '_onyx_proof_txid' );
		if ( isset( $_GET['proof'] ) && 'ok' === $_GET['proof'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			echo '<div class="woocommerce-message">' . esc_html__( 'Thank you – your payment proof was received. We will verify it shortly.', 'onyx-crypto' ) . '</div>';
		}
		?>
		<section class="onyx-proof" style="border:1px solid #dce2e9;border-radius:14px;padding:1.25rem;background:#fff;margin:1.5rem 0">
			<h3 style="font-size:1.05rem;font-weight:800;margin:0 0 .5rem"><?php esc_html_e( 'Upload payment proof', 'onyx-crypto' ); ?></h3>
			<?php if ( $done ) : ?>
				<p style="margin:0 0 .75rem;color:#059669"><?php esc_html_e( 'Proof received. You can upload a replacement below if needed.', 'onyx-crypto' ); ?></p>
			<?php endif; ?>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'onyx_proof_' . $order->get_id(), 'onyx_proof_nonce' ); ?>
				<input type="hidden" name="onyx_proof_order" value="<?php echo esc_attr( $order->get_id() ); ?>">
				<input type="hidden" name="onyx_proof_key" value="<?php echo esc_attr( $order->get_order_key() ); ?>">
				<p><label><?php esc_html_e( 'Transaction hash (TXID)', 'onyx-crypto' ); ?><br><input type="text" name="onyx_proof_txid" style="width:100%" maxlength="200"></label></p>
				<p><label><?php esc_html_e( 'Screenshot or PDF (max 5 MB)', 'onyx-crypto' ); ?><br><input type="file" name="onyx_proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf"></label></p>
				<button type="submit" class="button alt"><?php esc_html_e( 'Submit proof', 'onyx-crypto' ); ?></button>
			</form>
		</section>
		<?php
	}

	public static function handle_upload() {
		if ( empty( $_POST['onyx_proof_order'] ) ) {
			return;
		}
		$order_id = absint( $_POST['onyx_proof_order'] );
		$nonce    = isset( $_POST['onyx_proof_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['onyx_proof_nonce'] ) ) : '';
		$key      = isset( $_POST['onyx_proof_key'] ) ? sanitize_text_field( wp_unslash( $_POST['onyx_proof_key'] ) ) : '';
		$order    = wc_get_order( $order_id );
		if ( ! $order || ! wp_verify_nonce( $nonce, 'onyx_proof_' . $order_id ) || ! hash_equals( $order->get_order_key(), $key ) ) {
			wc_add_notice( __( 'Could not verify the upload request.', 'onyx-crypto' ), 'error' );
			return;
		}
		$txid     = isset( $_POST['onyx_proof_txid'] ) ? sanitize_text_field( wp_unslash( $_POST['onyx_proof_txid'] ) ) : '';
		$has_file = ! empty( $_FILES['onyx_proof_file']['tmp_name'] ) && is_uploaded_file( $_FILES['onyx_proof_file']['tmp_name'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( ! $has_file && '' === $txid ) {
			wc_add_notice( __( 'Enter a transaction hash or upload a receipt.', 'onyx-crypto' ), 'error' );
			return;
		}
		$name = (string) $order->get_meta( '_onyx_proof_file' );
		if ( $has_file ) {
			$file = $_FILES['onyx_proof_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			if ( $file['size'] > self::MAX_BYTES ) {
				wc_add_notice( __( 'File is larger than 5 MB.', 'onyx-crypto' ), 'error' );
				return;
			}
			$allowed = array(
				'jpg|jpeg' => 'image/jpeg',
				'png'      => 'image/png',
				'webp'     => 'image/webp',
				'pdf'      => 'application/pdf',
			);
			$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed );
			if ( empty( $check['ext'] ) ) {
				wc_add_notice( __( 'Only JPG, PNG, WEBP or PDF files are accepted.', 'onyx-crypto' ), 'error' );
				return;
			}
			$name = 'order-' . $order_id . '-' . wp_generate_password( 20, false ) . '.' . $check['ext'];
			if ( ! move_uploaded_file( $file['tmp_name'], self::dir() . '/' . $name ) ) {
				wc_add_notice( __( 'Upload failed, please try again.', 'onyx-crypto' ), 'error' );
				return;
			}
		}
		$order->update_meta_data( '_onyx_proof_submitted', time() );
		$order->update_meta_data( '_onyx_proof_file', $name );
		$order->update_meta_data( '_onyx_proof_txid', $txid );
		$order->add_order_note( sprintf( __( 'Customer uploaded payment proof. TXID: %s', 'onyx-crypto' ), $txid ? $txid : '—' ) );
		$order->save();
		wp_safe_redirect( add_query_arg( 'proof', 'ok', wp_get_referer() ? wp_get_referer() : $order->get_checkout_order_received_url() ) );
		exit;
	}

	public static function admin_panel( $order ) {
		if ( 'onyx_crypto' !== $order->get_payment_method() ) {
			return;
		}
		echo '<p><strong>' . esc_html__( 'Crypto network:', 'onyx-crypto' ) . '</strong> ' . esc_html( $order->get_meta( '_onyx_crypto_network' ) ) . '<br><code style="word-break:break-all">' . esc_html( $order->get_meta( '_onyx_crypto_address' ) ) . '</code></p>';
		$txid = $order->get_meta( '_onyx_proof_txid' );
		if ( $order->get_meta( '_onyx_proof_file' ) ) {
			$url = wp_nonce_url( admin_url( 'admin-post.php?action=onyx_view_proof&order=' . $order->get_id() ), 'onyx_view_proof_' . $order->get_id() );
			echo '<p><strong>' . esc_html__( 'Payment proof:', 'onyx-crypto' ) . '</strong> <a href="' . esc_url( $url ) . '" target="_blank">' . esc_html__( 'View file', 'onyx-crypto' ) . '</a>' . ( $txid ? '<br>TXID: <code>' . esc_html( $txid ) . '</code>' : '' ) . '</p>';
		} else {
			echo '<p><em>' . esc_html__( 'No payment proof uploaded yet.', 'onyx-crypto' ) . '</em></p>';
		}
	}

	public static function serve() {
		$order_id = isset( $_GET['order'] ) ? absint( $_GET['order'] ) : 0;
		check_admin_referer( 'onyx_view_proof_' . $order_id );
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'onyx-crypto' ), 403 );
		}
		$order = wc_get_order( $order_id );
		$name  = $order ? basename( (string) $order->get_meta( '_onyx_proof_file' ) ) : '';
		$path  = self::dir() . '/' . $name;
		if ( ! $name || ! is_file( $path ) ) {
			wp_die( esc_html__( 'File not found.', 'onyx-crypto' ), 404 );
		}
		$type = wp_check_filetype( $path );
		nocache_headers();
		header( 'Content-Type: ' . ( $type['type'] ? $type['type'] : 'application/octet-stream' ) );
		header( 'Content-Disposition: inline; filename="' . $name . '"' );
		readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}
}
