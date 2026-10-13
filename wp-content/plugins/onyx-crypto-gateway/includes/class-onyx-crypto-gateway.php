<?php
/**
 * Offline crypto gateway (based on WooCommerce's BACS gateway flow).
 *
 * Wallets are configured in WooCommerce → Settings → Payments → Crypto, one per line:
 *   USDT (TRC20) | Txxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 *   USDT (ERC20) | 0xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 * No wallet addresses are stored in code.
 *
 * @package onyx-crypto-gateway
 */

defined( 'ABSPATH' ) || exit;

class Onyx_Crypto_Gateway extends WC_Payment_Gateway {

	public $instructions;
	public $wallets_raw;

	public function __construct() {
		$this->id                 = 'onyx_crypto';
		$this->icon               = '';
		$this->has_fields         = true;
		$this->method_title       = __( 'Crypto (USDT / USDC)', 'onyx-crypto' );
		$this->method_description = __( 'Customer pays by stablecoin transfer to your wallet. Order is placed on-hold until you verify the transfer and the uploaded proof.', 'onyx-crypto' );

		$this->init_form_fields();
		$this->init_settings();

		$this->title        = $this->get_option( 'title' );
		$this->description  = $this->get_option( 'description' );
		$this->instructions = $this->get_option( 'instructions' );
		$this->wallets_raw  = $this->get_option( 'wallets' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'      => array(
				'title'   => __( 'Enable/Disable', 'onyx-crypto' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable crypto payments', 'onyx-crypto' ),
				'default' => 'no',
			),
			'title'        => array(
				'title'   => __( 'Title', 'onyx-crypto' ),
				'type'    => 'safe_text',
				'default' => __( 'Crypto (USDT / USDC)', 'onyx-crypto' ),
			),
			'description'  => array(
				'title'   => __( 'Description', 'onyx-crypto' ),
				'type'    => 'textarea',
				'default' => __( 'Pay with a stablecoin transfer. Choose your network below; the wallet address and QR code are shown after you place the order.', 'onyx-crypto' ),
			),
			'wallets'      => array(
				'title'       => __( 'Wallets', 'onyx-crypto' ),
				'type'        => 'textarea',
				'description' => __( 'One per line: <code>Network label | wallet address</code>. Double-check every address – payments sent to a wrong address cannot be recovered.', 'onyx-crypto' ),
				'default'     => '',
				'css'         => 'min-height:90px;font-family:monospace',
			),
			'instructions' => array(
				'title'   => __( 'Instructions', 'onyx-crypto' ),
				'type'    => 'textarea',
				'default' => __( 'Send the exact order amount on the selected network to the wallet address shown. Your order ships once payment is verified by our team, usually within 24 hours.', 'onyx-crypto' ),
			),
		);
	}

	/** @return array<int, array{label:string,address:string}> */
	public function get_wallets() {
		$out = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $this->wallets_raw ) as $line ) {
			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			if ( 2 === count( $parts ) && $parts[0] && $parts[1] ) {
				$out[] = array( 'label' => $parts[0], 'address' => $parts[1] );
			}
		}
		return $out;
	}

	public function is_available() {
		return parent::is_available() && (bool) $this->get_wallets();
	}

	public function payment_fields() {
		if ( $this->description ) {
			echo wpautop( wp_kses_post( $this->description ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '<fieldset class="onyx-crypto-networks">';
		foreach ( $this->get_wallets() as $i => $w ) {
			printf(
				'<label style="display:block;margin:.35rem 0"><input type="radio" name="onyx_crypto_network" value="%1$d" %2$s> %3$s</label>',
				(int) $i,
				checked( 0, $i, false ),
				esc_html( $w['label'] )
			);
		}
		echo '</fieldset>';
	}

	public function validate_fields() {
		$wallets = $this->get_wallets();
		$idx     = isset( $_POST['onyx_crypto_network'] ) ? absint( $_POST['onyx_crypto_network'] ) : -1; // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! isset( $wallets[ $idx ] ) ) {
			wc_add_notice( __( 'Please choose a crypto network.', 'onyx-crypto' ), 'error' );
			return false;
		}
		return true;
	}

	public function process_payment( $order_id ) {
		$order   = wc_get_order( $order_id );
		$wallets = $this->get_wallets();
		$idx     = isset( $_POST['onyx_crypto_network'] ) ? absint( $_POST['onyx_crypto_network'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		$wallet  = $wallets[ $idx ] ?? $wallets[0];

		$order->update_meta_data( '_onyx_crypto_network', $wallet['label'] );
		$order->update_meta_data( '_onyx_crypto_address', $wallet['address'] );
		$order->update_status( 'on-hold', sprintf( __( 'Awaiting %s transfer.', 'onyx-crypto' ), $wallet['label'] ) );
		wc_reduce_stock_levels( $order_id );
		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	public static function payment_box( WC_Order $order, $with_qr = true ) {
		$address = $order->get_meta( '_onyx_crypto_address' );
		if ( ! $address ) {
			return;
		}
		?>
		<section class="onyx-crypto-box" style="border:1px solid #dce2e9;border-radius:14px;padding:1.25rem;background:#fff;margin:1.5rem 0">
			<h2 style="font-size:1.25rem;font-weight:800;margin:0 0 .75rem"><?php esc_html_e( 'Complete your payment', 'onyx-crypto' ); ?></h2>
			<p style="margin:0 0 .25rem"><strong><?php esc_html_e( 'Amount:', 'onyx-crypto' ); ?></strong> <?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></p>
			<p style="margin:0 0 .75rem"><strong><?php esc_html_e( 'Network:', 'onyx-crypto' ); ?></strong> <?php echo esc_html( $order->get_meta( '_onyx_crypto_network' ) ); ?></p>
			<div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap">
				<?php if ( $with_qr ) : ?>
					<div class="onyx-crypto-qr" data-address="<?php echo esc_attr( $address ); ?>" style="width:120px;height:120px;background:#fff;padding:4px;border:1px solid #dce2e9;border-radius:8px"></div>
				<?php endif; ?>
				<div style="flex:1;min-width:220px">
					<code class="onyx-crypto-address" style="display:block;word-break:break-all;background:#f0f4f9;padding:.6rem .8rem;border-radius:8px;font-size:.85rem"><?php echo esc_html( $address ); ?></code>
					<button type="button" class="button onyx-crypto-copy" data-address="<?php echo esc_attr( $address ); ?>" style="margin-top:.5rem"><?php esc_html_e( 'Copy address', 'onyx-crypto' ); ?></button>
				</div>
			</div>
		</section>
		<?php
		if ( $with_qr ) {
			wp_enqueue_script( 'onyx-qrcode', ONYX_CRYPTO_URL . 'assets/qrcode.min.js', array(), ONYX_CRYPTO_VERSION, true );
			wp_add_inline_script(
				'onyx-qrcode',
				"document.querySelectorAll('.onyx-crypto-qr').forEach(function(el){new QRCode(el,{text:el.dataset.address,width:112,height:112});});document.querySelectorAll('.onyx-crypto-copy').forEach(function(b){b.addEventListener('click',function(){navigator.clipboard&&navigator.clipboard.writeText(b.dataset.address);b.textContent='Copied';});});"
			);
		}
	}

	public function thankyou_page( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $this->instructions ) {
			echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) );
		}
		self::payment_box( $order );
		Onyx_Payment_Proof::form( $order );
	}

	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $sent_to_admin || $this->id !== $order->get_payment_method() || ! $order->has_status( 'on-hold' ) ) {
			return;
		}
		$addr = $order->get_meta( '_onyx_crypto_address' );
		$net  = $order->get_meta( '_onyx_crypto_network' );
		if ( $plain_text ) {
			echo esc_html( wp_strip_all_tags( $this->instructions ) . "\n\n{$net}: {$addr}\n\n" );
			return;
		}
		echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) );
		printf( '<p><strong>%1$s</strong><br><code>%2$s</code></p>', esc_html( $net ), esc_html( $addr ) );
		printf( '<p><a href="%s">%s</a></p>', esc_url( $order->get_view_order_url() ), esc_html__( 'Upload your payment proof', 'onyx-crypto' ) );
	}
}
