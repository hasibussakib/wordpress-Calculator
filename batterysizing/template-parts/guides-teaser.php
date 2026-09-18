<?php
/**
 * Homepage guides teaser.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;
?>
<section class="bs-section bs-section-muted" id="guides">
	<div class="bs-container">
		<header class="bs-section-head">
			<p class="bs-kicker"><?php batterysizing_icon( 'book' ); ?> <?php esc_html_e( 'Guides', 'batterysizing' ); ?></p>
			<h2><?php esc_html_e( 'The bits the label does not tell you', 'batterysizing' ); ?></h2>
			<p class="bs-lede"><?php esc_html_e( 'Ah vs Wh, runtime, how many Ah you need, depth of discharge, chemistry and the formulas — in plain language.', 'batterysizing' ); ?></p>
		</header>
		<?php get_template_part( 'template-parts/guides-grid' ); ?>
		<p class="bs-center">
			<a class="bs-btn bs-btn-ghost" href="<?php echo esc_url( home_url( '/guides/' ) ); ?>">
				<?php esc_html_e( 'All guides', 'batterysizing' ); ?>
				<?php batterysizing_icon( 'arrow-right' ); ?>
			</a>
		</p>
	</div>
</section>
