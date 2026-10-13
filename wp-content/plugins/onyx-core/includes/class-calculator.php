<?php
/**
 * [onyx_reference_calculator] – click-dial pen analytical reference calculator
 * (port of the React ReferenceCalculator input panel). Pure client-side maths.
 *
 * @package onyx-core
 */

defined( 'ABSPATH' ) || exit;

class Onyx_Calculator {

	public static function init() {
		add_shortcode( 'onyx_reference_calculator', array( __CLASS__, 'render' ) );
	}

	public static function render() {
		$fields = array(
			array( 'mass', __( 'Total mass within sealed unit', 'onyx-core' ), 'mg', 'e.g. 100' ),
			array( 'volume', __( 'Total solution volume', 'onyx-core' ), 'mL', 'e.g. 3' ),
			array( 'target', __( 'Target research quantity', 'onyx-core' ), 'mg', 'e.g. 2' ),
		);
		$outputs = array(
			'mass'   => __( 'Total mass present', 'onyx-core' ),
			'conc'   => __( 'Calculated concentration', 'onyx-core' ),
			'mcg'    => __( 'Calculated microgram value (lab reference)', 'onyx-core' ),
			'vol'    => __( 'Dispensing volume required', 'onyx-core' ),
			'clicks' => __( 'Approx. device increments (laboratory dispensing)', 'onyx-core' ),
		);
		ob_start();
		?>
		<div class="onyx-calc" data-click-ml="0.01">
			<div class="onyx-calc__head">
				<span class="onyx-calc__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 2 4 4"/><path d="m17 7 3-3"/><path d="M19 9 8.7 19.3c-1 1-2.5 1-3.4 0l-.6-.6c-1-1-1-2.5 0-3.4L15 5"/><path d="m9 11 4 4"/><path d="m5 19-3 3"/><path d="m14 4 6 6"/></svg></span>
				<h3><?php esc_html_e( 'Click-Dial Pen', 'onyx-core' ); ?></h3>
			</div>
			<hr>
			<?php foreach ( $fields as $f ) : ?>
				<label class="onyx-calc__field"><span><?php echo esc_html( $f[1] ); ?></span>
					<span class="onyx-calc__input"><input type="number" min="0" step="any" inputmode="decimal" data-k="<?php echo esc_attr( $f[0] ); ?>" placeholder="<?php echo esc_attr( $f[3] ); ?>"><em><?php echo esc_html( $f[2] ); ?></em></span>
				</label>
			<?php endforeach; ?>
			<p class="onyx-calc__warn" hidden><?php esc_html_e( 'The target quantity exceeds the total mass held in the cartridge. Check the values entered.', 'onyx-core' ); ?></p>
			<hr>
			<p class="onyx-calc__label"><?php esc_html_e( 'Calculated output', 'onyx-core' ); ?></p>
			<dl>
				<?php foreach ( $outputs as $k => $label ) : ?>
					<div><dt><?php echo esc_html( $label ); ?></dt><dd data-o="<?php echo esc_attr( $k ); ?>">—</dd></div>
				<?php endforeach; ?>
			</dl>
			<p class="onyx-calc__fine"><?php esc_html_e( 'For research use only. Not for human or veterinary use. Figures support research planning and analytical preparation, and must not be used to derive clinical or therapeutic quantities.', 'onyx-core' ); ?></p>
		</div>
		<style>
			.onyx-calc{background:var(--onyx-primary,#001a61);color:#f6f9fc;border-radius:1rem;padding:1.5rem;font-size:.875rem}
			.onyx-calc__head{display:flex;align-items:center;gap:.75rem}.onyx-calc__head h3{font-size:1rem;line-height:1.5rem;font-weight:700;margin:0;color:#fff}
			.onyx-calc__icon{display:flex;width:2.25rem;height:2.25rem;border-radius:999px;background:rgba(255,255,255,.1);align-items:center;justify-content:center}
			.onyx-calc hr{border:0;height:1px;background:rgba(255,255,255,.1);margin:1.25rem 0}
			.onyx-calc__field{display:block;margin-bottom:1rem}.onyx-calc__field>span:first-child,.onyx-calc__label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;line-height:16.5px;color:rgba(246,249,252,.6);margin:0}
			.onyx-calc__input{display:flex;align-items:center;margin-top:.5rem;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);border-radius:.5rem}
			.onyx-calc__input:focus-within{border-color:var(--onyx-accent,#2885ef)}
			.onyx-calc__input input{flex:1;background:transparent!important;border:0!important;color:#fff!important;box-shadow:none!important;-moz-appearance:textfield}
			.onyx-calc__input input::-webkit-inner-spin-button{-webkit-appearance:none}
			.onyx-calc__input input::placeholder{color:rgba(246,249,252,.35)}
			.onyx-calc__input em{font-style:normal;font-size:.75rem;font-weight:600;color:rgba(246,249,252,.5);padding-right:.75rem}
			.onyx-calc__warn{border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);border-radius:.5rem;padding:.5rem .75rem;font-size:.75rem}
			.onyx-calc dl{margin:.75rem 0 0}.onyx-calc dl div{line-height:20px;display:flex;justify-content:space-between;gap:1rem;align-items:baseline;padding:.75rem 0;border-bottom:1px solid rgba(255,255,255,.1)}
			.onyx-calc dl div:last-child{border-bottom:0}.onyx-calc dt,.onyx-calc dd{line-height:20px}.onyx-calc dt{color:rgba(246,249,252,.85);font-weight:400}.onyx-calc dd{margin:0;font-weight:700;font-variant-numeric:tabular-nums;white-space:nowrap}
			.onyx-calc__fine{margin:1.25rem 0 0;font-size:11px;line-height:1.625;color:rgba(246,249,252,.45)}
		</style>
		<script>
		(function(){document.querySelectorAll('.onyx-calc').forEach(function(c){
			var click=parseFloat(c.dataset.clickMl)||0.01;
			function v(k){var n=parseFloat(c.querySelector('[data-k="'+k+'"]').value);return isNaN(n)||n<=0?null:n;}
			function o(k,t){c.querySelector('[data-o="'+k+'"]').textContent=t;}
			function run(){var m=v('mass'),vol=v('volume'),t=v('target');
				o('mass',m!==null?m.toFixed(2)+' mg':'—');
				o('conc',m!==null&&vol!==null?(m/vol).toFixed(2)+' mg/mL':'—');
				o('mcg',t!==null?Math.round(t*1000)+' mcg':'—');
				var d=(m!==null&&vol!==null&&t!==null)?(t*vol)/m:null;
				o('vol',d!==null?d.toFixed(3)+' mL':'—');
				o('clicks',d!==null?Math.round(d/click)+' clicks':'—');
				c.querySelector('.onyx-calc__warn').hidden=!(m!==null&&t!==null&&t>m);}
			c.querySelectorAll('input').forEach(function(i){i.addEventListener('input',run);});
		});})();
		</script>
		<?php
		return ob_get_clean();
	}
}
