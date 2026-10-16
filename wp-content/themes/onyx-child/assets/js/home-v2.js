/* Home V2 behaviour (deploy28i). This code used to live in Elementor HTML widgets as inline <script>. On save or import, kses
   (a user without unfiltered_html) stripped the <script> tags and entity-escaped the code, so it printed as visible text.
   It now ships as a theme file. Any leftover code text in the page data is removed below, so live is clean even before the SQL runs. */
(function () {
	function clean() {
		document.querySelectorAll('.hv2-sec .elementor-widget-html').forEach(function (w) {
			var it = document.createTreeWalker(w, NodeFilter.SHOW_TEXT), n, dead = [];
			while ((n = it.nextNode())) { if (/^\s*\(function\(\)\{/.test(n.data)) { dead.push(n); } }
			dead.forEach(function (t) { t.parentNode.removeChild(t); });
		});
	}
	function pager() {
		document.querySelectorAll('.hv2-best2').forEach(function (sec) {
			var ul = sec.querySelector('.hv2-products ul.products'), nav = sec.querySelector('.hv2-b2-pager');
			if (!ul || !nav || nav.dataset.ready) { return; }
			nav.dataset.ready = '1';
			var c = nav.querySelector('.hv2-b2-count'), pv = nav.querySelector('.hv2-b2-prev'), nx = nav.querySelector('.hv2-b2-next');
			function step() { var li = ul.querySelector('li.product'); if (!li) { return ul.clientWidth; } var g = parseFloat(getComputedStyle(ul).columnGap) || 0; var per = Math.max(1, Math.floor((ul.clientWidth + g) / (li.offsetWidth + g))); return per * (li.offsetWidth + g); }
			function upd() { var st = step(), max = ul.scrollWidth - ul.clientWidth, n = max > 2 ? Math.ceil(max / st) + 1 : 1, p = max > 2 ? Math.min(n, Math.round(ul.scrollLeft / st) + 1) : 1; if (ul.scrollLeft >= max - 2) { p = n; } if (c) { c.textContent = p + '/' + n; } if (pv) { pv.disabled = ul.scrollLeft <= 2; } if (nx) { nx.disabled = ul.scrollLeft >= max - 2; } nav.hidden = n < 2; }
			if (pv) { pv.addEventListener('click', function () { ul.scrollBy({ left: -step(), behavior: 'smooth' }); }); }
			if (nx) { nx.addEventListener('click', function () { ul.scrollBy({ left: step(), behavior: 'smooth' }); }); }
			ul.addEventListener('scroll', function () { window.requestAnimationFrame(upd); }, { passive: true });
			window.addEventListener('resize', upd); upd();
		});
	}
	function reveal() {
		var d = document.documentElement;
		if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches || d.classList.contains('hv2-anim')) { return; }
		d.classList.add('hv2-anim');
		var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('hv2-in'); io.unobserve(e.target); } }); }, { rootMargin: '0px 0px -8% 0px' });
		document.querySelectorAll('.hv2-sec').forEach(function (s, i) { if (i > 0) { io.observe(s); } else { s.classList.add('hv2-in'); } });
	}
	function go() { clean(); pager(); reveal(); }
	if (document.readyState !== 'loading') { go(); } else { document.addEventListener('DOMContentLoaded', go); }
})();
