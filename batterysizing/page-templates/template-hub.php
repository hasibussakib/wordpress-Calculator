<?php
/**
 * Template Name: Hub (Calculators or Guides)
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

get_header();

$slug = get_post_field( 'post_name', get_the_ID() );
$is_guides = ( 'guides' === $slug );
?>
<section class="bs-page-hero">
	<div class="bs-container">
		<p class="bs-kicker"><?php echo $is_guides ? esc_html__( 'Learn', 'batterysizing' ) : esc_html__( 'Tools', 'batterysizing' ); ?></p>
		<h1><?php the_title(); ?></h1>
		<p class="bs-lede">
			<?php
			echo $is_guides
				? esc_html__( 'Plain-language battery guides: Ah vs Wh, runtime, how many Ah you need, depth of discharge, chemistry and the formulas themselves.', 'batterysizing' )
				: esc_html__( 'Twelve free battery calculators. Same math, different question — runtime, capacity, backup, banks, solar, UPS, 12V / 24V / 48V, LiFePO4 and Ah↔Wh.', 'batterysizing' );
			?>
		</p>
	</div>
</section>

<?php if ( $is_guides ) : ?>
	<section class="bs-section">
		<div class="bs-container">
			<?php get_template_part( 'template-parts/guides-grid' ); ?>
		</div>
	</section>
<?php else : ?>
	<?php get_template_part( 'template-parts/calculator-grid' ); ?>
<?php endif; ?>

<?php get_template_part( 'template-parts/cta' ); ?>
<?php
get_footer();
