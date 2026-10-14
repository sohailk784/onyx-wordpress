<?php
/**
 * Account pages – port of React Auth.tsx (Login/Register), BusinessAuth.tsx (B2B sign in),
 * Profile.tsx (dashboard) and Wishlist.tsx. WooCommerce login/registration handlers are reused.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

/** True on My account, the B2B login page and the wishlist page. */
function onyx_is_account_like() {
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return true;
	}
	if ( is_page( 'b2b-login' ) ) {
		return true;
	}
	$wl = get_option( 'tinvwl-page' );
	return is_array( $wl ) && ! empty( $wl['wishlist'] ) && is_page( (int) $wl['wishlist'] );
}

add_filter( 'hello_elementor_page_title', function ( $show ) {
	return onyx_is_account_like() ? false : $show;
}, 20 );
add_filter( 'body_class', function ( $c ) {
	if ( onyx_is_account_like() ) {
		$c[] = 'onyx-cart-page';
		$c[] = 'onyx-account-page';
	}
	return $c;
} );

/** Live has its own email+password (+confirm) form; WooCommerce must not mail a generated password. */
add_filter( 'pre_option_woocommerce_registration_generate_password', function () {
	return 'no';
} );
add_filter( 'pre_option_woocommerce_registration_generate_username', function () {
	return 'yes';
} );
add_filter( 'pre_option_woocommerce_enable_myaccount_registration', function () {
	return 'yes';
} );
add_filter( 'woocommerce_process_registration_errors', function ( $err ) {
	// phpcs:disable WordPress.Security.NonceVerification -- WooCommerce verified the register nonce.
	if ( isset( $_POST['password2'] ) && ( $_POST['password'] ?? '' ) !== $_POST['password2'] ) {
		$err->add( 'onyx_pw', 'Passwords do not match.' );
	}
	if ( isset( $_POST['password'] ) && strlen( (string) wp_unslash( $_POST['password'] ) ) < 8 ) {
		$err->add( 'onyx_pw_len', 'Password must be at least 8 characters.' );
	}
	// phpcs:enable
	return $err;
} );

/** /login, /register and /profile (live URLs) land on the WooCommerce account page. */
add_action( 'template_redirect', function () {
	if ( ! is_404() || ! function_exists( 'wc_get_page_permalink' ) ) {
		return;
	}
	$path = trim( (string) wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH ), '/' );
	$home = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
	$slug = trim( $home ? preg_replace( '#^' . preg_quote( $home, '#' ) . '#', '', $path ) : $path, '/' );
	$acc  = wc_get_page_permalink( 'myaccount' );
	$map  = array(
		'login'        => $acc,
		'profile'      => $acc,
		'register'     => add_query_arg( 'action', 'register', $acc ),
		'b2b-register' => home_url( '/wholesale-application/' ),
	);
	if ( isset( $map[ $slug ] ) ) {
		wp_safe_redirect( $map[ $slug ], 301 );
		exit;
	}
} );

/** Text input with a leading lucide icon, as in Auth.tsx. */
function onyx_auth_input( $id, $name, $type, $placeholder, $icon, $value = '', $extra = '' ) {
	return '<div class="relative">' . onyx_icon( $icon, 'pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground' )
		. sprintf(
			'<input id="%1$s" name="%2$s" type="%3$s" required placeholder="%4$s" value="%5$s" class="w-full rounded-lg border bg-background py-3 pl-10 pr-4 text-sm outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/20" %6$s>',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $type ),
			esc_attr( $placeholder ),
			esc_attr( $value ),
			$extra
		) . '</div>';
}

function onyx_auth_label( $for, $text ) {
	return '<label for="' . esc_attr( $for ) . '" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted-foreground">' . esc_html( $text ) . '</label>';
}

/** Card header: icon tile, title, sub-line. */
function onyx_auth_head( $icon, $title, $sub ) {
	return '<div class="mb-8 text-center"><span class="mx-auto mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-accent/10 text-accent ring-1 ring-inset ring-accent/20">'
		. onyx_icon( $icon, 'h-6 w-6' ) . '</span><h1 class="text-2xl font-black">' . esc_html( $title ) . '</h1><p class="mt-2 text-sm text-muted-foreground">' . esc_html( $sub ) . '</p></div>';
}

/** WooCommerce notices rendered like the React inline error line. */
function onyx_auth_notices() {
	if ( ! function_exists( 'wc_get_notices' ) ) {
		return '';
	}
	$out = '';
	foreach ( array( 'error', 'success', 'notice' ) as $type ) {
		foreach ( wc_get_notices( $type ) as $n ) {
			$msg  = is_array( $n ) ? $n['notice'] : $n;
			$cls  = 'error' === $type ? 'text-red-600' : 'text-accent';
			$out .= '<p class="text-sm font-medium ' . $cls . '">' . wp_kses_post( $msg ) . '</p>';
		}
	}
	wc_clear_notices();
	return $out;
}

/** Sign-in form (WooCommerce login handler). $b2b switches to the BusinessAuth copy. */
function onyx_login_form( $b2b = false, $redirect = '' ) {
	$p    = $b2b ? 'b2b-login' : 'login';
	$user = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	ob_start();
	?>
	<form class="woocommerce-form woocommerce-form-login login space-y-5" method="post" action="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
		<?php do_action( 'woocommerce_login_form_start' ); ?>
		<div><?php echo onyx_auth_label( $p . '-email', $b2b ? 'Business email' : 'Email' ) . onyx_auth_input( $p . '-email', 'username', 'email', $b2b ? 'you@business.com' : 'you@example.com', 'mail', $user, 'autocomplete="username"' ); // phpcs:ignore ?></div>
		<div>
			<div class="mb-1.5 flex items-center justify-between"><label for="<?php echo esc_attr( $p ); ?>-password" class="block text-xs font-bold uppercase tracking-wider text-muted-foreground">Password</label><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-xs font-semibold text-accent hover:underline">Forgot password?</a></div>
			<?php echo onyx_auth_input( $p . '-password', 'password', 'password', 'Enter your password', 'lock', '', 'autocomplete="current-password"' ); // phpcs:ignore ?>
		</div>
		<?php do_action( 'woocommerce_login_form' ); ?>
		<?php if ( $b2b ) : ?>
			<label class="flex items-center gap-2 text-sm text-muted-foreground"><input name="rememberme" value="forever" class="h-4 w-4 rounded border accent-accent" type="checkbox">Remember me</label>
		<?php else : ?>
			<input type="hidden" name="rememberme" value="forever">
		<?php endif; ?>
		<?php echo onyx_auth_notices(); // phpcs:ignore ?>
		<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
		<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ? $redirect : wc_get_page_permalink( 'myaccount' ) ); ?>">
		<button type="submit" name="login" value="Sign in" class="w-full rounded-lg bg-primary py-3 text-sm font-bold text-primary-foreground transition hover:bg-primary/90 disabled:opacity-60">Sign in</button>
		<?php do_action( 'woocommerce_login_form_end' ); ?>
	</form>
	<?php
	return ob_get_clean();
}

/** [onyx_b2b_login] – the live /b2b-login page (sign-in card + trade-account perks). */
add_shortcode( 'onyx_b2b_login', function () {
	if ( is_user_logged_in() && ! ( defined( 'ELEMENTOR_VERSION' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) ) {
		$acc = wc_get_page_permalink( 'myaccount' );
		return '<div class="onyx-tw"><div class="mx-auto max-w-md px-4 py-16 text-center text-sm text-muted-foreground">You are signed in. <a class="font-semibold text-accent hover:underline" href="' . esc_url( $acc ) . '">Go to your account</a></div></div>';
	}
	$apply = home_url( '/wholesale-application/' );
	$perks = array(
		'Explore a range of trade account options built around how your business buys.',
		'Unlock preferential pricing as your order volumes grow.',
		'Get direct support from our dedicated trade specialists.',
		'Wholesale and private-label orders are arranged under a separate agreement.',
		'Every trade account requires acknowledgement of our research-use terms at signup.',
	);
	$li = '';
	foreach ( $perks as $t ) {
		$li .= '<li class="flex items-start gap-3 text-sm"><span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-accent/15 text-accent">' . onyx_icon( 'check', 'h-3.5 w-3.5' ) . '</span><span class="text-foreground/80">' . esc_html( $t ) . '</span></li>';
	}
	return '<div class="onyx-tw onyx-auth"><div class="mx-auto max-w-5xl px-4 py-8 2xl:py-24"><div class="grid gap-10 md:grid-cols-2 md:gap-14">'
		. '<div class="rounded-3xl border bg-card p-8 shadow-xl md:p-10">' . onyx_auth_head( 'log-in', 'B2B sign in', 'Access your trade account, wholesale pricing, and order history.' )
		. onyx_login_form( true )
		. '<p class="mt-6 text-center text-sm text-muted-foreground">Not a trade customer yet? <a class="font-semibold text-accent hover:underline" href="' . esc_url( $apply ) . '">Open a trade account</a></p></div>'
		. '<div class="rounded-3xl border bg-card p-6 shadow-xl md:p-8"><h2 class="text-lg font-black md:text-xl">New to ONYX Research? Open a trade account and unlock extra perks.</h2><ul class="mt-5 space-y-3">' . $li . '</ul>'
		. '<a class="mt-6 inline-flex items-center justify-center rounded-md bg-accent px-5 py-3 text-sm font-bold text-accent-foreground hover:bg-accent/90" href="' . esc_url( $apply ) . '">Open Trade Account</a></div>'
		. '</div></div></div>';
} );

/** Dashboard tabs: live Profile.tsx has "My Orders" and "My Addresses"; Account details kept for password changes. */
add_filter( 'woocommerce_account_menu_items', function () {
	return array(
		'orders'          => 'My Orders',
		'edit-address'    => 'My Addresses',
		'edit-account'    => 'Account details',
		'customer-logout' => 'Log out',
	);
}, 20 );
/** Dashboard root shows the orders tab, like /profile. */
add_action( 'init', function () {
	remove_action( 'woocommerce_account_content', 'woocommerce_account_content' );
	add_action( 'woocommerce_account_content', function () {
		global $wp;
		$has_ep = false;
		foreach ( WC()->query->get_query_vars() as $key => $v ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				$has_ep = true;
				break;
			}
		}
		if ( $has_ep ) {
			woocommerce_account_content();
		} else {
			woocommerce_account_orders( 1 );
		}
	} );
} );

/** Wishlist heart behaves like the React toggle: second click removes, no pop-up dialog. */
add_filter( 'option_tinvwl-general', function ( $o ) {
	$o                  = is_array( $o ) ? $o : array();
	$o['simple_flow']   = true;
	$o['show_notice']   = false;
	$o['redirect']      = false;
	return $o;
} );
/** After registering, land on the account page without ?action=register. */
add_filter( 'woocommerce_registration_redirect', function () {
	return wc_get_page_permalink( 'myaccount' );
} );
/** Live shows plain messages, without WooCommerce's bold "Error:" prefix. */
add_filter( 'woocommerce_add_error', function ( $m ) {
	return preg_replace( '#^\s*<strong>\s*Error\s*:?\s*</strong>\s*:?\s*#i', '', $m );
} );
