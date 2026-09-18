<?php
/**
 * Homepage FAQ teaser.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;
?>
<section class="bs-section" id="faq">
	<div class="bs-container">
		<header class="bs-section-head">
			<p class="bs-kicker"><?php esc_html_e( 'FAQ', 'batterysizing' ); ?></p>
			<h2><?php esc_html_e( 'Quick answers before you size anything', 'batterysizing' ); ?></h2>
		</header>
		<?php
		set_query_var( 'batterysizing_faq_items', batterysizing_get_home_faq() );
		get_template_part( 'template-parts/faq-list' );
		?>
		<p class="bs-center">
			<a class="bs-btn bs-btn-ghost" href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">
				<?php esc_html_e( 'Full FAQ', 'batterysizing' ); ?>
				<?php batterysizing_icon( 'arrow-right' ); ?>
			</a>
		</p>
	</div>
</section>
