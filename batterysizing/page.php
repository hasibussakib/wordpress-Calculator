<?php
/**
 * Default page template (About and any page without a custom template).
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<?php while ( have_posts() ) : the_post(); ?>
	<section class="bs-page-hero">
		<div class="bs-container">
			<p class="bs-kicker"><?php esc_html_e( 'BatterySizing.xyz', 'batterysizing' ); ?></p>
			<h1><?php the_title(); ?></h1>
		</div>
	</section>
	<section class="bs-section">
		<div class="bs-container bs-prose bs-prose-wide">
			<?php the_content(); ?>
		</div>
	</section>
<?php endwhile; ?>
<?php
get_footer();
