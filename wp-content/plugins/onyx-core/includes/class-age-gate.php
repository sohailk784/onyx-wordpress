<?php
/**
 * Age & eligibility confirmation modal (mirrors the React AgeGate component).
 * Remembers confirmation for 10 days in the `onyx_age_verified` cookie.
 *
 * @package onyx-core
 */

defined( 'ABSPATH' ) || exit;

class Onyx_Age_Gate {

	const COOKIE = 'onyx_age_verified';

	public static function init() {
		add_action( 'wp_footer', array( __CLASS__, 'render' ), 5 );
	}

	public static function render() {
		if ( is_admin() || isset( $_COOKIE[ self::COOKIE ] ) || ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) ) {
			return;
		}
		if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$terms = home_url( '/terms-conditions/' );
		$logo  = ONYX_CORE_URL . 'assets/logo-onyx.webp';
		?>
		<div id="onyx-age-gate" role="dialog" aria-modal="true" aria-labelledby="onyx-age-gate-title">
			<div class="onyx-ag__box">
				<img src="<?php echo esc_url( $logo ); ?>" alt="ONYX Research" class="onyx-ag__logo" width="120" height="48">
				<h2 id="onyx-age-gate-title" class="onyx-ag__title"><?php esc_html_e( 'Age & Eligibility Confirmation', 'onyx-core' ); ?></h2>
				<p class="onyx-ag__text">
					<?php
					printf(
						/* translators: %s: terms link */
						wp_kses_post( __( 'By choosing <strong>“I Am Over 21 &amp; I Agree,”</strong> you confirm that you are 21 or older and that these products are being acquired strictly for legitimate scientific research, never for personal use, resale, or human consumption. You also agree to our %s. Any misuse of research compounds is strictly prohibited.', 'onyx-core' ) ),
						'<a href="' . esc_url( $terms ) . '">' . esc_html__( 'Terms & Conditions', 'onyx-core' ) . '</a>'
					);
					?>
				</p>
				<div class="onyx-ag__actions">
					<button type="button" class="onyx-ag__accept"><?php esc_html_e( 'I Am Over 21 & I Agree', 'onyx-core' ); ?></button>
					<a class="onyx-ag__leave" href="https://www.google.com"><?php esc_html_e( 'Under 21, Leave Site', 'onyx-core' ); ?></a>
				</div>
				<p class="onyx-ag__fine"><?php esc_html_e( 'ONYX Research products are research-grade compounds supplied for research use only. They are not approved for human consumption, veterinary use, or any clinical or therapeutic application. ONYX Research is not a pharmacy or medical provider and offers no dosing or medical guidance.', 'onyx-core' ); ?></p>
			</div>
		</div>
		<style>
			#onyx-age-gate{position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.6);backdrop-filter:blur(4px)}
			.onyx-ag__box{width:100%;max-width:32rem;background:#fff;border-radius:1rem;padding:1.5rem;text-align:center;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);font-family:var(--onyx-font-sans,system-ui,sans-serif)}
			.onyx-ag__logo{height:3rem;width:auto;margin:0 auto}
			.onyx-ag__title{margin:1rem 0 .75rem;font-size:.8rem;font-weight:800;letter-spacing:.15em;text-transform:uppercase;color:#001a61}
			.onyx-ag__text{font-size:.875rem;line-height:1.6;color:#5d646f;margin:0}
			.onyx-ag__text strong{color:#040d1e}.onyx-ag__text a{color:#2885ef;font-weight:600}
			.onyx-ag__actions{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:1.25rem}
			.onyx-ag__accept,.onyx-ag__leave{border-radius:.5rem;padding:.75rem 1rem;font-size:.85rem;font-weight:700;cursor:pointer;text-decoration:none;display:block}
			.onyx-ag__accept{background:#001a61;color:#fff;border:0}.onyx-ag__accept:hover{background:#2885ef}
			.onyx-ag__leave{border:1px solid #dce2e9;color:#040d1e;background:#fff}
			.onyx-ag__fine{margin:1.25rem 0 0;font-size:.7rem;line-height:1.5;color:#5d646f}
			@media(max-width:480px){.onyx-ag__actions{grid-template-columns:1fr}}
		</style>
		<script>
			(function(){var g=document.getElementById('onyx-age-gate');if(!g)return;document.documentElement.style.overflow='hidden';
			g.querySelector('.onyx-ag__accept').addEventListener('click',function(){var d=new Date(Date.now()+864e6);document.cookie='<?php echo esc_js( self::COOKIE ); ?>=1; expires='+d.toUTCString()+'; path=/; SameSite=Lax';g.remove();document.documentElement.style.overflow='';});})();
		</script>
		<?php
	}
}
