<?php
/**
 * Batch code authenticity check.
 *
 * Admins add batch codes under Products → Batch Codes (code = post title, plus purity,
 * test date, linked product and certificate URL). Visitors verify via [onyx_check_code].
 *
 * @package onyx-core
 */

defined( 'ABSPATH' ) || exit;

class Onyx_Batch_Codes {

	const CPT = 'onyx_batch';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
		add_action( 'save_post_' . self::CPT, array( __CLASS__, 'save' ) );
		add_shortcode( 'onyx_check_code', array( __CLASS__, 'shortcode' ) );
		add_filter( 'manage_' . self::CPT . '_posts_columns', fn( $c ) => array_merge( $c, array( 'purity' => 'Purity', 'tested' => 'Tested' ) ) );
		add_action( 'manage_' . self::CPT . '_posts_custom_column', function ( $col, $id ) {
			if ( 'purity' === $col ) {
				echo esc_html( get_post_meta( $id, '_onyx_purity', true ) );
			} elseif ( 'tested' === $col ) {
				echo esc_html( get_post_meta( $id, '_onyx_tested', true ) );
			}
		}, 10, 2 );
	}

	public static function register_post_type() {
		register_post_type(
			self::CPT,
			array(
				'labels'       => array(
					'name'          => __( 'Batch Codes', 'onyx-core' ),
					'singular_name' => __( 'Batch Code', 'onyx-core' ),
					'add_new_item'  => __( 'Add batch code', 'onyx-core' ),
					'edit_item'     => __( 'Edit batch code', 'onyx-core' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => class_exists( 'WooCommerce' ) ? 'edit.php?post_type=product' : true,
				'supports'     => array( 'title' ),
				'menu_icon'    => 'dashicons-yes-alt',
			)
		);
	}

	public static function meta_box() {
		add_meta_box( 'onyx_batch_meta', __( 'Batch details', 'onyx-core' ), array( __CLASS__, 'render_meta' ), self::CPT, 'normal' );
	}

	public static function render_meta( $post ) {
		wp_nonce_field( 'onyx_batch_save', 'onyx_batch_nonce' );
		$fields = array(
			'_onyx_product' => __( 'Product name', 'onyx-core' ),
			'_onyx_purity'  => __( 'Purity (e.g. 99.4%)', 'onyx-core' ),
			'_onyx_tested'  => __( 'Test date (YYYY-MM-DD)', 'onyx-core' ),
			'_onyx_coa'     => __( 'Certificate of analysis URL', 'onyx-core' ),
		);
		echo '<p>' . esc_html__( 'The post title is the batch code customers type in (case-insensitive).', 'onyx-core' ) . '</p>';
		foreach ( $fields as $key => $label ) {
			printf(
				'<p><label><strong>%1$s</strong><br><input type="text" class="widefat" name="%2$s" value="%3$s"></label></p>',
				esc_html( $label ),
				esc_attr( $key ),
				esc_attr( get_post_meta( $post->ID, $key, true ) )
			);
		}
	}

	public static function save( $post_id ) {
		if ( ! isset( $_POST['onyx_batch_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['onyx_batch_nonce'] ) ), 'onyx_batch_save' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( array( '_onyx_product', '_onyx_purity', '_onyx_tested' ) as $k ) {
			if ( isset( $_POST[ $k ] ) ) {
				update_post_meta( $post_id, $k, sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) );
			}
		}
		if ( isset( $_POST['_onyx_coa'] ) ) {
			update_post_meta( $post_id, '_onyx_coa', esc_url_raw( wp_unslash( $_POST['_onyx_coa'] ) ) );
		}
	}

	public static function lookup( $code ) {
		$code = strtoupper( trim( $code ) );
		if ( strlen( $code ) < 4 ) {
			return null;
		}
		$q = get_posts(
			array(
				'post_type'   => self::CPT,
				'post_status' => 'publish',
				'title'       => $code,
				'numberposts' => 1,
			)
		);
		return $q ? $q[0] : null;
	}

	public static function shortcode() {
		$code   = isset( $_GET['batch'] ) ? sanitize_text_field( wp_unslash( $_GET['batch'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$result = '';
		if ( '' !== $code ) {
			$batch = self::lookup( $code );
			if ( $batch ) {
				$coa    = get_post_meta( $batch->ID, '_onyx_coa', true );
				$result = sprintf(
					'<div class="onyx-cc__result is-ok"><strong>%1$s</strong> %2$s%3$s%4$s%5$s</div>',
					esc_html( sprintf( __( 'Batch %s verified.', 'onyx-core' ), strtoupper( $code ) ) ),
					esc_html( get_post_meta( $batch->ID, '_onyx_product', true ) ),
					( $p = get_post_meta( $batch->ID, '_onyx_purity', true ) ) ? esc_html( ' · Purity ' . $p ) : '',
					( $t = get_post_meta( $batch->ID, '_onyx_tested', true ) ) ? esc_html( ' · Tested ' . $t ) : '',
					$coa ? ' · <a href="' . esc_url( $coa ) . '" target="_blank" rel="noopener">' . esc_html__( 'View certificate', 'onyx-core' ) . '</a>' : ''
				);
			} else {
				$result = '<div class="onyx-cc__result is-bad">' . esc_html__( 'We could not find that batch code. Check the code printed on your label and try again, or contact us.', 'onyx-core' ) . '</div>';
			}
		}
		ob_start();
		?>
		<form class="onyx-cc" method="get" action="">
			<label class="screen-reader-text" for="onyx-cc-code"><?php esc_html_e( 'Batch code', 'onyx-core' ); ?></label>
			<input id="onyx-cc-code" name="batch" type="text" required minlength="4" value="<?php echo esc_attr( $code ); ?>" placeholder="<?php esc_attr_e( 'Enter batch code (e.g. NP12345)', 'onyx-core' ); ?>">
			<button type="submit"><?php esc_html_e( 'Verify', 'onyx-core' ); ?></button>
		</form>
		<?php echo $result; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<style>
			.onyx-cc{display:flex;gap:.75rem;flex-wrap:wrap}.onyx-cc input{flex:1;min-width:220px;padding:.75rem 1rem!important}
			.onyx-cc button{background:var(--onyx-primary,#001a61);color:#fff;border:0;border-radius:.5rem;padding:.75rem 1.5rem;font-weight:700;cursor:pointer}
			.onyx-cc__result{margin-top:1rem;border-radius:.5rem;padding:1rem;font-size:.875rem;border:1px solid}
			.onyx-cc__result.is-ok{border-color:rgba(16,185,129,.5);background:#ecfdf5;color:#064e3b}
			.onyx-cc__result.is-bad{border-color:rgba(231,0,11,.4);background:rgba(231,0,11,.06);color:#b91c1c}
		</style>
		<?php
		return ob_get_clean();
	}
}
