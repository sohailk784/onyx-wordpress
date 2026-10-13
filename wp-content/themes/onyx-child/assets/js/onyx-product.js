/* Product page: variant pills, quantity stepper, gallery thumbs, zoom origin. Mirrors React Product.tsx behaviour. */
( function () {
	var root = document.querySelector( '.onyx-product' );
	if ( ! root ) return;
	var variants = JSON.parse( root.dataset.variants || '[]' );
	var priceEl = root.querySelector( '[data-onyx-price]' );
	var coa = root.querySelector( '[data-onyx-coa]' );
	var fmt = function ( n ) { return '£' + Number( n ).toLocaleString( 'en-GB', { minimumFractionDigits: 2, maximumFractionDigits: 2 } ); };
	var ON = [ 'border-accent', 'bg-accent/10', 'text-accent' ], OFF = [ 'border-border', 'hover:border-accent/60' ];
	root.querySelectorAll( '[data-onyx-variant]' ).forEach( function ( b ) {
		b.addEventListener( 'click', function () {
			var v = variants[ +b.dataset.onyxVariant ];
			root.querySelectorAll( '[data-onyx-variant]:not([disabled])' ).forEach( function ( o ) {
				var on = o === b;
				ON.forEach( function ( c ) { o.classList.toggle( c, on ); } );
				OFF.forEach( function ( c ) { o.classList.toggle( c, ! on ); } );
			} );
			if ( priceEl ) priceEl.innerHTML = '<span translate="no" class="notranslate">' + fmt( v.price ) + '</span>';
			var vid = root.querySelector( '[data-onyx-vid]' );
			if ( vid ) vid.value = v.id;
			Object.keys( v.attrs ).forEach( function ( k ) {
				var i = root.querySelector( '[data-onyx-attr="' + k + '"]' );
				if ( i ) i.value = v.attrs[ k ];
			} );
			if ( coa ) { coa.hidden = ! v.coa; if ( v.coa ) coa.href = v.coa; }
		} );
	} );
	var qi = root.querySelector( '[data-onyx-qty-input]' ), ql = root.querySelector( '[data-onyx-qty-label]' );
	root.querySelectorAll( '[data-onyx-qty]' ).forEach( function ( b ) {
		b.addEventListener( 'click', function () {
			var q = Math.max( 1, ( +qi.value || 1 ) + ( +b.dataset.onyxQty ) );
			qi.value = q; ql.textContent = q;
		} );
	} );
	var main = root.querySelector( '[data-onyx-main]' );
	root.querySelectorAll( '[data-onyx-thumb]' ).forEach( function ( t ) {
		t.addEventListener( 'click', function () {
			main.src = t.dataset.onyxThumb;
			root.querySelectorAll( '[data-onyx-thumb]' ).forEach( function ( o ) { [ 'border-accent', 'ring-2', 'ring-accent/30' ].forEach( function ( c ) { o.classList.toggle( c, o === t ); } ); } );
		} );
	} );
	var z = root.querySelector( '.onyx-zoom' );
	if ( z ) z.addEventListener( 'mousemove', function ( e ) {
		var r = z.getBoundingClientRect();
		main.style.transformOrigin = ( ( e.clientX - r.left ) / r.width * 100 ) + '% ' + ( ( e.clientY - r.top ) / r.height * 100 ) + '%';
	} );
} )();
