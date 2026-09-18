<?php
/**
 * What-If Runtime Comparison — pick a load, compare preset batteries.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

$presets = batterysizing_get_battery_presets();
?>
<section class="bs-section" id="whatif">
	<div class="bs-container">
		<header class="bs-section-head">
			<p class="bs-kicker"><?php batterysizing_icon( 'chart' ); ?> <?php esc_html_e( 'Step 2 — compare batteries', 'batterysizing' ); ?></p>
			<h2><?php esc_html_e( 'What-If Runtime Comparison', 'batterysizing' ); ?></h2>
			<p class="bs-lede"><?php esc_html_e( 'Same load, six common batteries. Lithium vs lead-acid stops being a slogan once you see the hours side by side.', 'batterysizing' ); ?></p>
		</header>

		<div class="bs-whatif" id="bs-whatif" data-presets="<?php echo esc_attr( wp_json_encode( $presets ) ); ?>">
			<div class="bs-whatif-controls">
				<label>
					<span><?php esc_html_e( 'Load', 'batterysizing' ); ?></span>
					<div class="bs-input-unit">
						<input type="number" min="1" step="1" value="100" data-load>
						<span>W</span>
					</div>
				</label>
				<label>
					<span><?php esc_html_e( 'Inverter efficiency', 'batterysizing' ); ?></span>
					<div class="bs-input-unit">
						<input type="number" min="50" max="100" step="1" value="85" data-eff>
						<span>%</span>
					</div>
				</label>
			</div>
			<div class="bs-whatif-bars" data-bars></div>
		</div>
	</div>
</section>
