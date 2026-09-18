<?php
/**
 * 12-calculator card grid (homepage + /calculators/ hub).
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

$calculators = batterysizing_get_calculators();
?>
<section class="bs-section" id="calculators">
	<div class="bs-container">
		<header class="bs-section-head">
			<p class="bs-kicker"><?php batterysizing_icon( 'calculator' ); ?> <?php esc_html_e( 'Calculators', 'batterysizing' ); ?></p>
			<h2><?php esc_html_e( 'Every battery question, one formula away', 'batterysizing' ); ?></h2>
			<p class="bs-lede"><?php esc_html_e( 'Runtime, capacity, backup, banks, solar, UPS, 12V / 24V / 48V, LiFePO4 and Ah↔Wh. Same math the original BatterySizing.xyz site shipped.', 'batterysizing' ); ?></p>
		</header>
		<div class="bs-calc-grid">
			<?php foreach ( $calculators as $c ) : ?>
				<a class="bs-card bs-calc-card" href="<?php echo esc_url( batterysizing_calculator_url( $c['slug'] ) ); ?>">
					<span class="bs-calc-icon"><?php batterysizing_icon( $c['icon'] ); ?></span>
					<h3><?php echo esc_html( $c['name'] ); ?></h3>
					<p><?php echo esc_html( $c['tagline'] ); ?></p>
					<span class="bs-card-go"><?php esc_html_e( 'Open', 'batterysizing' ); ?> <?php batterysizing_icon( 'arrow-right' ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
