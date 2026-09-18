<?php
/**
 * Closing CTA band.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;
?>
<section class="bs-cta">
	<div class="bs-container bs-cta-inner">
		<div>
			<p class="bs-kicker bs-kicker-light"><?php esc_html_e( 'Free, no account', 'batterysizing' ); ?></p>
			<h2><?php esc_html_e( 'Size the battery. Then buy it.', 'batterysizing' ); ?></h2>
			<p><?php esc_html_e( 'Open the all-in-one calculator, or start from the load estimator on this page. Either way you leave with a number, not a guess.', 'batterysizing' ); ?></p>
		</div>
		<div class="bs-cta-actions">
			<a class="bs-btn bs-btn-white bs-btn-lg" href="<?php echo esc_url( home_url( '/battery-calculator/' ) ); ?>">
				<?php batterysizing_icon( 'zap' ); ?>
				<?php esc_html_e( 'Open battery calculator', 'batterysizing' ); ?>
			</a>
			<a class="bs-btn bs-btn-ghost-light bs-btn-lg" href="<?php echo esc_url( home_url( '/calculators/' ) ); ?>">
				<?php esc_html_e( 'See all 12 tools', 'batterysizing' ); ?>
			</a>
		</div>
	</div>
</section>
