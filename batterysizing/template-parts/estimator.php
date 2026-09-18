<?php
/**
 * Appliance Load Estimator — editable rows, live totals, live SVG chart.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

$presets = batterysizing_get_appliance_presets();
?>
<section class="bs-section bs-section-muted" id="estimator">
	<div class="bs-container">
		<header class="bs-section-head">
			<p class="bs-kicker"><?php batterysizing_icon( 'plug' ); ?> <?php esc_html_e( 'Step 1 — total the load', 'batterysizing' ); ?></p>
			<h2><?php esc_html_e( 'Appliance Load Estimator', 'batterysizing' ); ?></h2>
			<p class="bs-lede"><?php esc_html_e( 'Add what you actually run. Totals update live — watts, daily watt-hours, and a chart of where the energy goes. Paste the daily Wh into any capacity or solar calculator.', 'batterysizing' ); ?></p>
		</header>

		<div class="bs-estimator" id="bs-estimator" data-presets="<?php echo esc_attr( wp_json_encode( $presets ) ); ?>">
			<div class="bs-estimator-table-wrap">
				<table class="bs-estimator-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Appliance', 'batterysizing' ); ?></th>
							<th><?php esc_html_e( 'Watts', 'batterysizing' ); ?></th>
							<th><?php esc_html_e( 'Hours / day', 'batterysizing' ); ?></th>
							<th><?php esc_html_e( 'Qty', 'batterysizing' ); ?></th>
							<th><?php esc_html_e( 'Wh / day', 'batterysizing' ); ?></th>
							<th><span class="screen-reader-text"><?php esc_html_e( 'Remove', 'batterysizing' ); ?></span></th>
						</tr>
					</thead>
					<tbody data-rows></tbody>
				</table>
				<div class="bs-estimator-actions">
					<button type="button" class="bs-btn bs-btn-ghost" data-add-row>
						<?php batterysizing_icon( 'plus' ); ?>
						<?php esc_html_e( 'Add appliance', 'batterysizing' ); ?>
					</button>
					<label class="bs-preset-label">
						<span><?php esc_html_e( 'Quick add', 'batterysizing' ); ?></span>
						<select data-preset>
							<option value=""><?php esc_html_e( 'Choose a preset…', 'batterysizing' ); ?></option>
							<?php foreach ( $presets as $p ) : ?>
								<option value="<?php echo esc_attr( $p['name'] . '|' . $p['watts'] ); ?>">
									<?php echo esc_html( $p['name'] . ' · ' . $p['watts'] . 'W' ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
			</div>

			<div class="bs-estimator-side">
				<div class="bs-stat-grid">
					<div class="bs-stat">
						<span><?php esc_html_e( 'Running load', 'batterysizing' ); ?></span>
						<strong data-total-w>0 W</strong>
					</div>
					<div class="bs-stat">
						<span><?php esc_html_e( 'Daily energy', 'batterysizing' ); ?></span>
						<strong data-total-wh>0 Wh</strong>
					</div>
					<div class="bs-stat">
						<span><?php esc_html_e( 'Peak simultaneous', 'batterysizing' ); ?></span>
						<strong data-peak-w>0 W</strong>
					</div>
				</div>
				<div class="bs-chart-wrap">
					<svg class="bs-chart" viewBox="0 0 240 240" data-chart role="img" aria-label="<?php esc_attr_e( 'Share of daily watt-hours by appliance', 'batterysizing' ); ?>"></svg>
					<ul class="bs-chart-legend" data-legend></ul>
				</div>
				<a class="bs-btn bs-btn-primary" href="<?php echo esc_url( home_url( '/battery-capacity-calculator/' ) ); ?>">
					<?php esc_html_e( 'Size a battery for this load', 'batterysizing' ); ?>
					<?php batterysizing_icon( 'arrow-right' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
