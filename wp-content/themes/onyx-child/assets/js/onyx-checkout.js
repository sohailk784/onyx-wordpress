/* Onyx checkout – behaviour of the React Checkout.tsx on top of WooCommerce's checkout.js. */
(function ($) {
	'use strict';
	var $form = $('form.checkout');
	if (!$form.length) return;
	var ON = ['border-accent', 'bg-accent/5'], OFF = ['hover:bg-secondary'];
	var ERR = ['border-red-500', 'focus:border-red-500', 'focus:ring-red-500/20'];
	var submitted = false;

	function paint(name) {
		$form.find('input[name^="' + name + '"]').each(function () {
			var $l = $(this).closest('label.onyx-opt');
			$l.removeClass((this.checked ? OFF : ON).join(' ')).addClass((this.checked ? ON : OFF).join(' '));
		});
	}
	function syncNet() {
		var $c = $form.find('input[name="payment_method"]:checked');
		$form.find('input[name="onyx_crypto_network"]').val($c.data('onyx-net') !== undefined ? String($c.data('onyx-net')) : '');
	}
	$form.on('change', 'input[name^="shipping_method"]', function () { paint('shipping_method'); });
	$form.on('change', 'input[name="payment_method"]', function () { paint('payment_method'); syncNet(); });
	$(document.body).on('updated_checkout', function () { paint('shipping_method'); paint('payment_method'); syncNet(); });

	// Country picker (flag + name over a native select).
	$form.on('change', '#billing_country', function () {
		var $s = $(this), $w = $s.closest('.onyx-select');
		$w.find('[data-onyx-flag]').attr('class', 'fi fi-' + String($s.val()).toLowerCase() + ' h-4 w-5 shrink-0 rounded-[2px]');
		$w.find('[data-onyx-name]').text($s.find('option:selected').text());
		if (submitted) validate();
	});

	// Phone: dial code + national number -> billing_phone ("+44 7911 123456").
	function syncPhone() {
		var $d = $form.find('[data-onyx-dial]'), n = $.trim($('#onyx_phone_national').val());
		var dial = $d.find('option:selected').data('dial') || '';
		$('#billing_phone').val(n ? dial + ' ' + n : '');
	}
	$form.on('change', '[data-onyx-dial]', function () {
		var $o = $(this).find('option:selected');
		$form.find('[data-onyx-dial-flag]').attr('class', 'fi fi-' + String($(this).val()).toLowerCase() + ' h-4 w-5 shrink-0 rounded-[2px]');
		$form.find('[data-onyx-dial-code]').text($o.data('dial'));
		syncPhone();
	});
	$form.on('input change', '#onyx_phone_national', syncPhone);

	// Client-side validation with the live copy, shown inline under each field.
	var RULES = {
		billing_email: function (v) { return !v ? 'Email is required.' : (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? '' : 'Enter a valid email address.'); },
		billing_first_name: function (v) { return v ? '' : 'First name is required.'; },
		billing_last_name: function (v) { return v ? '' : 'Last name is required.'; },
		billing_address_1: function (v) { return v ? '' : 'Address is required.'; },
		billing_city: function (v) { return v ? '' : 'City is required.'; },
		billing_postcode: function (v) { return v ? '' : 'Postal code is required.'; },
		billing_country: function (v) { return v ? '' : 'Please select a country.'; },
		billing_phone: function (v) { return !v ? 'Phone number is required.' : (/^\+?[\d\s\-()]{6,20}$/.test(v) ? '' : 'Enter a valid phone number.'); }
	};
	function setErr(key, msg) {
		var $wrap = $form.find('[data-onyx-field="' + key + '"]');
		var $ctl = key === 'billing_phone' ? $wrap.find('.onyx-phone') : key === 'billing_country' ? $wrap.find('.onyx-select') : $wrap.find('input').first();
		var $inp = key === 'billing_phone' ? $('#onyx_phone_national') : key === 'billing_country' ? $('#billing_country') : $ctl;
		$wrap.find('.onyx-err').remove();
		$inp.attr('aria-invalid', msg ? 'true' : 'false');
		$ctl[msg ? 'addClass' : 'removeClass'](ERR.join(' '));
		if (msg) $wrap.append('<p class="onyx-err mt-1.5 text-xs font-medium text-red-600"></p>').find('.onyx-err').text(msg);
	}
	function validate() {
		syncPhone();
		var bad = 0;
		$.each(RULES, function (k, fn) { var m = fn($.trim($('#' + k).val() || '')); setErr(k, m); if (m) bad++; });
		var $t = $('#onyx_terms'), $hint = $form.find('[data-onyx-terms-hint]');
		if (!$t.is(':checked')) {
			bad++; $t.attr('aria-invalid', 'true');
			$hint.removeClass('text-muted-foreground').addClass('font-medium text-red-600').text('You must read and agree to the terms and conditions to place your order.');
		} else {
			$t.attr('aria-invalid', 'false');
			$hint.removeClass('font-medium text-red-600').addClass('text-muted-foreground').text('Please confirm this before placing your order.');
		}
		return bad === 0;
	}
	$form.on('input change', 'input', function () { if (submitted) validate(); });
	$form.on('checkout_place_order', function () {
		submitted = true; syncNet();
		if (!validate()) {
			var el = $form.find('[aria-invalid="true"]').get(0);
			if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
			return false;
		}
		$('#place_order').contents().last()[0].textContent = ' Placing order…';
		return true;
	});
	// Server-side errors: show them as the live red box above the confirmation, not as a grid cell.
	$(document.body).on('checkout_error', function () {
		var $g = $form.find('.woocommerce-NoticeGroup-checkout');
		$g.addClass('rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700 onyx-co-errors');
		$form.find('[data-onyx-field="onyx_terms"]').before($g);
		$form.find('#place_order').prop('disabled', false);
		$(document.body).trigger('update_checkout');
	});

	// Discount code inside the summary (WooCommerce apply_coupon AJAX).
	$form.on('click', '[data-onyx-apply-coupon]', function () {
		var $b = $(this), code = $.trim($('#onyx_coupon_code').val()), $e = $form.find('[data-onyx-coupon-error]');
		if (!code || typeof wc_checkout_params === 'undefined') return;
		$b.prop('disabled', true).text('…'); $e.attr('hidden', true);
		$.post(wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'), { security: wc_checkout_params.apply_coupon_nonce, coupon_code: code }).done(function (html) {
			var $h = $('<div>').html(html), $err = $h.find('.woocommerce-error, .is-error');
			if ($err.length) { $e.text($.trim($err.text())).removeAttr('hidden'); }
			else { $('#onyx_coupon_code').val(''); }
			$(document.body).trigger('update_checkout', { update_shipping_method: false });
		}).always(function () { $b.prop('disabled', false).text('Apply'); });
	});
	$form.on('keydown', '#onyx_coupon_code', function (e) { if (e.key === 'Enter') { e.preventDefault(); $form.find('[data-onyx-apply-coupon]').trigger('click'); } });
	$form.on('click', '.woocommerce-remove-coupon', function (e) {
		e.preventDefault();
		$.post(wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_coupon'), { security: wc_checkout_params.remove_coupon_nonce, coupon: $(this).data('coupon') }).always(function () { $(document.body).trigger('update_checkout', { update_shipping_method: false }); });
	});
	syncNet(); syncPhone();
})(jQuery);
