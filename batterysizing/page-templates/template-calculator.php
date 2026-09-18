<?php
/**
 * Template Name: Calculator
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

get_header();

$slug = get_post_field( 'post_name', get_the_ID() );
$calc = batterysizing_get_calculator( $slug );
if ( ! $calc ) {
	$calc = batterysizing_get_calculator( 'battery-calculator' );
}
$GLOBALS['batterysizing_current_calc'] = $calc;
?>
<section class="bs-page-hero bs-page-hero-calc">
	<div class="bs-container">
		<nav class="bs-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'batterysizing' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'batterysizing' ); ?></a>
			<span>/</span>
			<a href="<?php echo esc_url( home_url( '/calculators/' ) ); ?>"><?php esc_html_e( 'Calculators', 'batterysizing' ); ?></a>
			<span>/</span>
			<span><?php echo esc_html( $calc['name'] ); ?></span>
		</nav>
		<div class="bs-page-hero-row">
			<div>
				<p class="bs-kicker"><?php batterysizing_icon( $calc['icon'] ); ?> <?php esc_html_e( 'Free calculator', 'batterysizing' ); ?></p>
				<h1><?php echo esc_html( $calc['name'] ); ?></h1>
				<p class="bs-lede"><?php echo esc_html( $calc['tagline'] ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="bs-section bs-section-tight">
	<div class="bs-container">
		<?php get_template_part( 'template-parts/calculator-widget' ); ?>
	</div>
</section>

<section class="bs-section">
	<div class="bs-container bs-split">
		<article class="bs-prose">
			<?php echo wp_kses_post( $calc['intro'] ); ?>
			<?php if ( ! empty( $calc['formula'] ) ) : ?>
				<h2><?php esc_html_e( 'Formula', 'batterysizing' ); ?></h2>
				<pre><code><?php echo esc_html( $calc['formula'] ); ?></code></pre>
			<?php endif; ?>
			<?php if ( ! empty( $calc['steps'] ) ) : ?>
				<h2><?php esc_html_e( 'How to use it', 'batterysizing' ); ?></h2>
				<ol>
					<?php foreach ( $calc['steps'] as $step ) : ?>
						<li><?php echo esc_html( $step ); ?></li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</article>
		<aside class="bs-aside">
			<div class="bs-card bs-card-pad">
				<h3><?php esc_html_e( 'Related calculators', 'batterysizing' ); ?></h3>
				<ul class="bs-related">
					<?php
					$related = ! empty( $calc['related'] ) ? $calc['related'] : array();
					foreach ( $related as $rel_slug ) :
						$rel = batterysizing_get_calculator( $rel_slug );
						if ( ! $rel ) {
							continue;
						}
						?>
						<li>
							<a href="<?php echo esc_url( batterysizing_calculator_url( $rel['slug'] ) ); ?>">
								<span class="bs-drop-icon"><?php batterysizing_icon( $rel['icon'] ); ?></span>
								<span>
									<strong><?php echo esc_html( $rel['name'] ); ?></strong>
									<small><?php echo esc_html( $rel['tagline'] ); ?></small>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</aside>
	</div>
</section>

<?php if ( ! empty( $calc['faq'] ) ) : ?>
	<section class="bs-section bs-section-muted">
		<div class="bs-container">
			<header class="bs-section-head">
				<p class="bs-kicker"><?php esc_html_e( 'FAQ', 'batterysizing' ); ?></p>
				<h2><?php echo esc_html( sprintf( __( '%s questions', 'batterysizing' ), $calc['name'] ) ); ?></h2>
			</header>
			<?php
			set_query_var( 'batterysizing_faq_items', $calc['faq'] );
			get_template_part( 'template-parts/faq-list' );
			?>
		</div>
	</section>
<?php endif; ?>

<?php
unset( $GLOBALS['batterysizing_current_calc'] );
get_footer();
