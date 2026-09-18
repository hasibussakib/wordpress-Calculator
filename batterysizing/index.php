<?php
/**
 * Fallback index — used if no more specific template matches.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="bs-page-hero">
	<div class="bs-container">
		<h1><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
	</div>
</section>
<section class="bs-section">
	<div class="bs-container bs-prose">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'bs-card bs-card-pad' ); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<div class="bs-pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found.', 'batterysizing' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
