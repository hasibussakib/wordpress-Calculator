<?php
/**
 * Template Name: Legal
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="bs-page-hero">
	<div class="bs-container">
		<p class="bs-kicker"><?php esc_html_e( 'Legal', 'batterysizing' ); ?></p>
		<h1><?php the_title(); ?></h1>
	</div>
</section>
<section class="bs-section">
	<div class="bs-container bs-prose bs-prose-wide">
		<?php
		while ( have_posts() ) {
			the_post();
			the_content();
		}
		?>
	</div>
</section>
<?php
get_footer();
