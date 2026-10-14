<?php
/**
 * Login / register – port of React Auth.tsx (live onyxhealth.net/login and /register).
 * ?action=register shows the "Create account" card. WooCommerce handlers and nonces are used.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

$onyx_reg = ( isset( $_GET['action'] ) && 'register' === $_GET['action'] ) || isset( $_POST['register'] ); // phpcs:ignore WordPress.Security.NonceVerification
$acc      = wc_get_page_permalink( 'myaccount' );
?>
<div class="onyx-tw onyx-auth"><div class="mx-auto max-w-md px-4 py-8 2xl:py-24"><div class="rounded-3xl border bg-card p-8 shadow-xl md:p-10">
<?php if ( ! $onyx_reg ) : ?>
	<?php echo onyx_auth_head( 'log-in', 'Sign in', 'Access your wishlist and order history.' ) . onyx_login_form( false ); // phpcs:ignore ?>
	<p class="mt-6 text-center text-sm text-muted-foreground">No account? <a href="<?php echo esc_url( add_query_arg( 'action', 'register', $acc ) ); ?>" class="font-semibold text-accent hover:underline">Register</a></p>
<?php else : ?>
	<?php echo onyx_auth_head( 'user-plus', 'Create account', 'Join ONYX Research.' ); // phpcs:ignore ?>
	<form method="post" class="woocommerce-form woocommerce-form-register register space-y-5" action="<?php echo esc_url( add_query_arg( 'action', 'register', $acc ) ); ?>" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
		<?php do_action( 'woocommerce_register_form_start' ); ?>
		<?php $em = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore ?>
		<div><?php echo onyx_auth_label( 'register-email', 'Email' ) . onyx_auth_input( 'register-email', 'email', 'email', 'you@example.com', 'mail', $em, 'autocomplete="email"' ); // phpcs:ignore ?></div>
		<div><?php echo onyx_auth_label( 'register-password', 'Password' ) . onyx_auth_input( 'register-password', 'password', 'password', 'Min. 8 characters', 'lock', '', 'minlength="8" autocomplete="new-password"' ); // phpcs:ignore ?></div>
		<div><?php echo onyx_auth_label( 'register-confirm-password', 'Confirm password' ) . onyx_auth_input( 'register-confirm-password', 'password2', 'password', 'Re-enter your password', 'lock', '', 'minlength="8" autocomplete="new-password"' ); // phpcs:ignore ?></div>
		<?php
		// Keep third-party register fields, but not WooCommerce's privacy text (live has none).
		remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );
		do_action( 'woocommerce_register_form' );
		echo onyx_auth_notices(); // phpcs:ignore
		wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' );
		?>
		<button type="submit" name="register" value="Create account" class="w-full rounded-lg bg-primary py-3 text-sm font-bold text-primary-foreground transition hover:bg-primary/90 disabled:opacity-60">Create account</button>
		<?php do_action( 'woocommerce_register_form_end' ); ?>
	</form>
	<p class="mt-6 text-center text-sm text-muted-foreground">Already have one? <a href="<?php echo esc_url( $acc ); ?>" class="font-semibold text-accent hover:underline">Sign in</a></p>
<?php endif; ?>
</div></div></div>
