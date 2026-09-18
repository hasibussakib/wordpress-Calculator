<?php
/**
 * Accordion FAQ list.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

$items = get_query_var( 'batterysizing_faq_items' );
if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}
?>
<div class="bs-faq" data-faq>
	<?php foreach ( $items as $i => $item ) : ?>
		<details class="bs-faq-item" <?php echo 0 === $i ? 'open' : ''; ?>>
			<summary>
				<?php echo esc_html( isset( $item['q'] ) ? $item['q'] : '' ); ?>
				<?php batterysizing_icon( 'chevron-down', 'bs-faq-chevron' ); ?>
			</summary>
			<div class="bs-faq-body">
				<p><?php echo esc_html( isset( $item['a'] ) ? $item['a'] : '' ); ?></p>
			</div>
		</details>
	<?php endforeach; ?>
</div>
