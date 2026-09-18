<?php
/**
 * Template Name: FAQ
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="bs-page-hero">
	<div class="bs-container">
		<p class="bs-kicker"><?php esc_html_e( 'Questions', 'batterysizing' ); ?></p>
		<h1><?php the_title(); ?></h1>
		<p class="bs-lede"><?php esc_html_e( 'Runtime, Ah vs Wh, depth of discharge, system voltage and lithium vs lead-acid — the answers the calculators assume you already know.', 'batterysizing' ); ?></p>
	</div>
</section>
<section class="bs-section">
	<div class="bs-container">
		<?php
		set_query_var( 'batterysizing_faq_items', batterysizing_get_faq() );
		get_template_part( 'template-parts/faq-list' );
		?>
	</div>
</section>
<?php get_template_part( 'template-parts/cta' ); ?>
<?php
get_footer();
