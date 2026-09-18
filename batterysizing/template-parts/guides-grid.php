<?php
/**
 * Guide card grid.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="bs-guide-grid">
	<?php foreach ( batterysizing_get_guides() as $g ) : ?>
		<a class="bs-card bs-guide-card" href="<?php echo esc_url( batterysizing_guide_url( $g['slug'] ) ); ?>">
			<span class="bs-guide-meta"><?php echo esc_html( $g['read_time'] ); ?></span>
			<h3><?php echo esc_html( $g['name'] ); ?></h3>
			<p><?php echo esc_html( $g['tagline'] ); ?></p>
			<span class="bs-card-go"><?php esc_html_e( 'Read', 'batterysizing' ); ?> <?php batterysizing_icon( 'arrow-right' ); ?></span>
		</a>
	<?php endforeach; ?>
</div>
