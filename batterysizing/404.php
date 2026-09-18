<?php
/**
 * 404 template.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="bs-page-hero">
	<div class="bs-container bs-center">
		<p class="bs-kicker"><?php esc_html_e( '404', 'batterysizing' ); ?></p>
		<h1><?php esc_html_e( 'This page is not on the map', 'batterysizing' ); ?></h1>
		<p class="bs-lede"><?php esc_html_e( 'The URL may have changed, or the calculator you want lives under a slightly different name.', 'batterysizing' ); ?></p>
		<p>
			<a class="bs-btn bs-btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to homepage', 'batterysizing' ); ?></a>
			<a class="bs-btn bs-btn-ghost" href="<?php echo esc_url( home_url( '/calculators/' ) ); ?>"><?php esc_html_e( 'Browse calculators', 'batterysizing' ); ?></a>
		</p>
	</div>
</section>
<?php
get_template_part( 'template-parts/calculator-grid' );
get_footer();
