<?php
/**
 * Interactive calculator widget. Config comes from the current calculator
 * definition (set by the page template or the [battery_calculator] shortcode).
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

$calc = isset( $GLOBALS['batterysizing_current_calc'] ) ? $GLOBALS['batterysizing_current_calc'] : batterysizing_get_calculator( 'battery-calculator' );
if ( ! $calc ) {
	return;
}
$config = batterysizing_calculator_config( $calc );
$tabs   = in_array( $calc['engine'], array( 'runtime_capacity' ), true );
?>
<div class="bs-widget"
	id="bs-calc-<?php echo esc_attr( $calc['slug'] ); ?>"
	data-calculator
	data-engine="<?php echo esc_attr( $calc['engine'] ); ?>"
	data-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">

	<?php if ( $tabs ) : ?>
		<div class="bs-tabs" role="tablist">
			<button type="button" class="bs-tab is-active" role="tab" aria-selected="true" data-mode="runtime">
				<?php esc_html_e( 'Runtime', 'batterysizing' ); ?>
				<small><?php esc_html_e( 'How long will it last?', 'batterysizing' ); ?></small>
			</button>
			<button type="button" class="bs-tab" role="tab" aria-selected="false" data-mode="capacity">
				<?php esc_html_e( 'Capacity needed', 'batterysizing' ); ?>
				<small><?php esc_html_e( 'What battery do I need?', 'batterysizing' ); ?></small>
			</button>
		</div>
	<?php endif; ?>

	<div class="bs-widget-grid">
		<form class="bs-widget-form" data-form>
			<?php foreach ( $config['fields'] as $field ) : ?>
				<?php
				$modes_attr = '';
				if ( ! empty( $field['modes'] ) ) {
					$modes_attr = ' data-modes="' . esc_attr( implode( ',', $field['modes'] ) ) . '"';
				}
				$hidden = ! empty( $field['modes'] ) && ! in_array( 'runtime', $field['modes'], true );
				?>
				<label class="bs-field<?php echo $hidden ? ' is-hidden' : ''; ?>" data-field="<?php echo esc_attr( $field['key'] ); ?>"<?php echo $modes_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="bs-field-label">
						<?php echo esc_html( $field['label'] ); ?>
						<?php if ( ! empty( $field['help'] ) ) : ?>
							<button type="button" class="bs-help" data-tip="<?php echo esc_attr( $field['help'] ); ?>" aria-label="<?php esc_attr_e( 'Help', 'batterysizing' ); ?>">?</button>
						<?php endif; ?>
					</span>

					<?php if ( 'select' === $field['type'] ) : ?>
						<select name="<?php echo esc_attr( $field['key'] ); ?>" <?php disabled( ! empty( $field['locked'] ) ); ?>>
							<?php foreach ( $field['options'] as $opt ) : ?>
								<option value="<?php echo esc_attr( $opt['value'] ); ?>" <?php selected( (string) $opt['value'], (string) $field['default'] ); ?>>
									<?php echo esc_html( $opt['label'] ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php elseif ( 'slider' === $field['type'] ) : ?>
						<div class="bs-slider">
							<input type="range"
								name="<?php echo esc_attr( $field['key'] ); ?>"
								min="<?php echo esc_attr( $field['min'] ); ?>"
								max="<?php echo esc_attr( isset( $field['max'] ) ? $field['max'] : 100 ); ?>"
								step="<?php echo esc_attr( $field['step'] ); ?>"
								value="<?php echo esc_attr( $field['default'] ); ?>">
							<output><?php echo esc_html( $field['default'] . ( $field['unit'] ? ' ' . $field['unit'] : '' ) ); ?></output>
						</div>
					<?php else : ?>
						<div class="bs-input-unit">
							<input type="number"
								name="<?php echo esc_attr( $field['key'] ); ?>"
								value="<?php echo esc_attr( $field['default'] ); ?>"
								min="<?php echo esc_attr( $field['min'] ); ?>"
								step="<?php echo esc_attr( $field['step'] ); ?>"
								<?php echo isset( $field['max'] ) ? 'max="' . esc_attr( $field['max'] ) . '"' : ''; ?>
								inputmode="decimal">
							<?php if ( $field['unit'] ) : ?>
								<span><?php echo esc_html( $field['unit'] ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</label>
			<?php endforeach; ?>
		</form>

		<div class="bs-results" data-results>
			<p class="bs-results-kicker"><?php esc_html_e( 'Result', 'batterysizing' ); ?></p>
			<div data-primary class="bs-result-primary">—</div>
			<ul class="bs-result-list" data-secondary></ul>
		</div>
	</div>
</div>
