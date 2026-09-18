<?php
/**
 * Homepage hero — matches index.html title/description + original landing.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;
?>
<section class="bs-hero">
	<div class="bs-hero-glow" aria-hidden="true"></div>
	<div class="bs-container bs-hero-inner">
		<div class="bs-hero-copy">
			<p class="bs-pill">
				<?php batterysizing_icon( 'sparkles' ); ?>
				<?php esc_html_e( '12 free calculators · no account', 'batterysizing' ); ?>
			</p>
			<h1><?php echo wp_kses_post( batterysizing_hero_heading() ); ?></h1>
			<p class="bs-lede"><?php echo esc_html( batterysizing_hero_sub() ); ?></p>
			<div class="bs-hero-actions">
				<a class="bs-btn bs-btn-primary bs-btn-lg" href="<?php echo esc_url( home_url( '/battery-calculator/' ) ); ?>">
					<?php batterysizing_icon( 'zap' ); ?>
					<?php esc_html_e( 'Open battery calculator', 'batterysizing' ); ?>
				</a>
				<a class="bs-btn bs-btn-ghost bs-btn-lg" href="#calculators">
					<?php esc_html_e( 'Browse all 12 tools', 'batterysizing' ); ?>
				</a>
			</div>
			<ul class="bs-hero-points">
				<li><?php batterysizing_icon( 'check' ); ?> <?php esc_html_e( 'Runtime, capacity, backup, banks, solar, UPS', 'batterysizing' ); ?></li>
				<li><?php batterysizing_icon( 'check' ); ?> <?php esc_html_e( 'DoD + inverter efficiency built in', 'batterysizing' ); ?></li>
				<li><?php batterysizing_icon( 'check' ); ?> <?php esc_html_e( '12V · 24V · 48V · LiFePO4', 'batterysizing' ); ?></li>
			</ul>
		</div>

		<div class="bs-hero-visual" aria-hidden="true">
			<div class="bs-battery-art">
				<div class="bs-battery-shell">
					<div class="bs-battery-nub"></div>
					<div class="bs-battery-body">
						<div class="bs-battery-fill"></div>
						<div class="bs-battery-bolt"><?php batterysizing_icon( 'zap' ); ?></div>
					</div>
				</div>
				<div class="bs-float-card bs-float-card-a">
					<span><?php esc_html_e( 'Usable energy', 'batterysizing' ); ?></span>
					<strong>1,026 Wh</strong>
					<small>12V 100Ah LiFePO4 · 90% DoD</small>
				</div>
				<div class="bs-float-card bs-float-card-b">
					<span><?php esc_html_e( 'Runtime @ 100W', 'batterysizing' ); ?></span>
					<strong>10h 16m</strong>
					<small><?php esc_html_e( 'vs 5h 06m lead-acid', 'batterysizing' ); ?></small>
				</div>
			</div>
		</div>
	</div>
</section>
