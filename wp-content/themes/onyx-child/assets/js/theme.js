/* ONYX child theme: small progressive enhancements. */
( function () {
	// Sticky header shadow once the page scrolls.
	var header = document.querySelector( '.ehf-header #masthead, header.onyx-header, .ehf-header' );
	if ( header ) {
		window.addEventListener( 'scroll', function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 8 );
		}, { passive: true } );
	}
} )();

/* Variation pills: mirror WooCommerce variation <select>s as pill buttons (like the React "Select specification"). */
( function () {
	function build( select ) {
		if ( select.dataset.onyxPills ) return;
		select.dataset.onyxPills = '1';
		var wrap = document.createElement( 'div' );
		wrap.className = 'onyx-pills';
		Array.prototype.forEach.call( select.options, function ( opt ) {
			if ( ! opt.value ) return;
			var b = document.createElement( 'button' );
			b.type = 'button';
			b.className = 'onyx-pill';
			b.textContent = opt.text;
			b.dataset.value = opt.value;
			b.addEventListener( 'click', function () {
				select.value = opt.value;
				select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
				if ( window.jQuery ) window.jQuery( select ).trigger( 'change' );
				sync();
			} );
			wrap.appendChild( b );
		} );
		function sync() {
			wrap.querySelectorAll( '.onyx-pill' ).forEach( function ( b ) {
				b.classList.toggle( 'is-active', b.dataset.value === select.value );
			} );
		}
		select.addEventListener( 'change', sync );
		if ( window.jQuery ) window.jQuery( select ).on( 'change', sync );
		select.classList.add( 'onyx-visually-hidden' );
		select.parentNode.insertBefore( wrap, select );
		sync();
	}
	document.querySelectorAll( 'table.variations select' ).forEach( build );
} )();
