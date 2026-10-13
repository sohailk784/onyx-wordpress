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

/* Header (port of React Header.tsx): ticker, logo shrink, dropdowns, search, mobile menu, language, delay notice. */
( function () {
	var h = document.getElementById( 'onyx-header' );
	if ( ! h ) return;
	var items = h.querySelectorAll( '.onyx-ticker-item' ), ti = 0;
	if ( items.length > 1 ) setInterval( function () {
		items[ ti ].classList.remove( 'is-active' ); items[ ti ].classList.add( 'is-leaving' );
		var prev = items[ ti ]; setTimeout( function () { prev.classList.remove( 'is-leaving' ); }, 900 );
		ti = ( ti + 1 ) % items.length; items[ ti ].classList.add( 'is-active' );
	}, 4500 );
	var logo = h.querySelector( '[data-onyx-logo]' ), sc = false;
	window.addEventListener( 'scroll', function () {
		var y = window.scrollY;
		if ( ! sc && y > 64 ) sc = true; else if ( sc && y < 10 ) sc = false; else return;
		[ 'h-12', 'md:h-14' ].forEach( function ( c ) { logo.classList.toggle( c, ! sc ); } );
		[ 'h-10', 'md:h-11' ].forEach( function ( c ) { logo.classList.toggle( c, sc ); } );
	}, { passive: true } );
	var st = h.querySelector( '[data-onyx-search-toggle]' ), sp = h.querySelector( '.onyx-search-panel' );
	if ( st ) st.addEventListener( 'click', function () { sp.hidden = ! sp.hidden; st.classList.toggle( 'text-accent', ! sp.hidden ); if ( ! sp.hidden ) sp.querySelector( 'input' ).focus(); } );
	var mt = h.querySelector( '[data-onyx-menu-toggle]' ), mm = h.querySelector( '.onyx-mobile-menu' );
	if ( mt ) mt.addEventListener( 'click', function () {
		mm.hidden = ! mm.hidden; var r = h.offsetHeight; mm.style.top = r + 'px'; mm.style.height = 'calc(100dvh - ' + r + 'px)';
		document.body.style.overflow = mm.hidden ? '' : 'hidden';
	} );
	h.querySelectorAll( '[data-onyx-sub-toggle]' ).forEach( function ( b ) {
		b.addEventListener( 'click', function () { var s = b.parentNode.nextElementSibling; s.hidden = ! s.hidden; b.querySelector( 'svg' ).classList.toggle( 'rotate-180', ! s.hidden ); } );
	} );
	// Language switcher – Google Translate cookie, like the React LanguageSwitcher.
	var lt = h.querySelector( '[data-onyx-lang-toggle]' ), lm = h.querySelector( '[data-onyx-lang-menu]' );
	var m = document.cookie.match( /googtrans=\/en\/([^;]+)/ ), cur = m ? m[ 1 ] : 'en';
	if ( lm ) {
		lm.querySelectorAll( '[data-lang]' ).forEach( function ( b ) {
			var on = b.dataset.lang === cur;
			if ( on ) { h.querySelector( '[data-onyx-lang-flag]' ).className = 'fi fi-' + b.dataset.flag + ' rounded-sm'; h.querySelector( '[data-onyx-lang-code]' ).textContent = b.dataset.code; }
			[ 'border-primary', 'bg-primary/5', 'font-semibold', 'text-primary' ].forEach( function ( c ) { b.classList.toggle( c, on ); } );
			[ 'border-transparent', 'text-foreground' ].forEach( function ( c ) { b.classList.toggle( c, ! on ); } );
			b.addEventListener( 'click', function () {
				var v = b.dataset.lang === 'en' ? '' : '/en/' + b.dataset.lang, exp = v ? '' : ';expires=Thu, 01 Jan 1970 00:00:00 GMT';
				document.cookie = 'googtrans=' + v + ';path=/' + exp;
				document.cookie = 'googtrans=' + v + ';path=/;domain=' + location.hostname.replace( /^www\./, '.' ) + exp;
				location.reload();
			} );
		} );
		var tog = function ( open ) { [ 'pointer-events-none', '-translate-y-1', 'opacity-0' ].forEach( function ( c ) { lm.classList.toggle( c, ! open ); } ); [ 'pointer-events-auto', 'translate-y-0', 'opacity-100' ].forEach( function ( c ) { lm.classList.toggle( c, open ); } ); };
		lt.addEventListener( 'click', function () { tog( lm.classList.contains( 'opacity-0' ) ); } );
		document.addEventListener( 'mousedown', function ( e ) { if ( ! lt.parentNode.contains( e.target ) ) tog( false ); } );
		if ( cur !== 'en' ) {
			window.googleTranslateElementInit = function () { new google.translate.TranslateElement( { pageLanguage: 'en', autoDisplay: false }, 'onyx-gt' ); };
			var d = document.createElement( 'div' ); d.id = 'onyx-gt'; d.style.display = 'none'; document.body.appendChild( d );
			var s = document.createElement( 'script' ); s.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit'; document.body.appendChild( s );
		}
	}
	var dn = document.querySelector( '.onyx-delay' );
	try { if ( dn && sessionStorage.getItem( 'onyx_order_delay_notice_dismissed_v1' ) !== '1' ) dn.hidden = false; } catch ( e ) { if ( dn ) dn.hidden = false; }
	var dc = document.querySelector( '[data-onyx-delay-close]' );
	if ( dc ) dc.addEventListener( 'click', function () { dn.hidden = true; try { sessionStorage.setItem( 'onyx_order_delay_notice_dismissed_v1', '1' ); } catch ( e ) {} } );
} )();

/* Home hero "Scroll to explore" hint (live parity) */
document.addEventListener('DOMContentLoaded',function(){var h=document.querySelector('.elementor-element-502616f');if(h&&!h.querySelector('.onyx-scroll-hint')){var d=document.createElement('div');d.className='onyx-scroll-hint';d.setAttribute('aria-hidden','true');d.textContent='Scroll to explore';h.appendChild(d);['b1','b2'].forEach(function(c){var b=document.createElement('div');b.className='onyx-hero-blob '+c;b.setAttribute('aria-hidden','true');h.appendChild(b);});}});

/* .onyx-acc collapsible containers (doctor FAQ, live parity) */
document.addEventListener('click',function(e){var q=e.target.closest('.onyx-acc>.elementor-element:first-child');if(!q)return;q.parentElement.classList.toggle('is-open');});
