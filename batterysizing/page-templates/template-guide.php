<?php
/**
 * Template Name: Guide
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

get_header();

$slug  = get_post_field( 'post_name', get_the_ID() );
$guide = batterysizing_get_guide( $slug );
?>
<section class="bs-page-hero">
	<div class="bs-container">
		<nav class="bs-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'batterysizing' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'batterysizing' ); ?></a>
			<span>/</span>
			<a href="<?php echo esc_url( home_url( '/guides/' ) ); ?>"><?php esc_html_e( 'Guides', 'batterysizing' ); ?></a>
			<span>/</span>
			<span><?php the_title(); ?></span>
		</nav>
		<p class="bs-kicker"><?php batterysizing_icon( 'book' ); ?> <?php esc_html_e( 'Guide', 'batterysizing' ); ?><?php echo $guide ? ' · ' . esc_html( $guide['read_time'] ) : ''; ?></p>
		<h1><?php the_title(); ?></h1>
		<?php if ( $guide ) : ?>
			<p class="bs-lede"><?php echo esc_html( $guide['tagline'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
<section class="bs-section">
	<div class="bs-container bs-split">
		<article class="bs-prose">
			<?php
			if ( $guide && ! empty( $guide['content'] ) ) {
				echo wp_kses_post( $guide['content'] );
			} else {
				while ( have_posts() ) {
					the_post();
					the_content();
				}
			}
			?>
		</article>
		<aside class="bs-aside">
			<div class="bs-card bs-card-pad">
				<h3><?php esc_html_e( 'More guides', 'batterysizing' ); ?></h3>
				<ul class="bs-related">
					<?php foreach ( batterysizing_get_guides() as $g ) : ?>
						<?php if ( $guide && $g['slug'] === $guide['slug'] ) { continue; } ?>
						<li>
							<a href="<?php echo esc_url( batterysizing_guide_url( $g['slug'] ) ); ?>">
								<span class="bs-drop-icon"><?php batterysizing_icon( 'book' ); ?></span>
								<span>
									<strong><?php echo esc_html( $g['name'] ); ?></strong>
									<small><?php echo esc_html( $g['read_time'] ); ?></small>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="bs-card bs-card-pad bs-card-brand">
				<h3><?php esc_html_e( 'Open a calculator', 'batterysizing' ); ?></h3>
				<p><?php esc_html_e( 'Run the same formulas with your numbers.', 'batterysizing' ); ?></p>
				<a class="bs-btn bs-btn-primary" href="<?php echo esc_url( home_url( '/battery-calculator/' ) ); ?>"><?php esc_html_e( 'Battery Calculator', 'batterysizing' ); ?></a>
			</div>
		</aside>
	</div>
</section>
<?php
get_footer();
