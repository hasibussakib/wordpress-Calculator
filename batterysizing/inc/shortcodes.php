<?php
/**
 * Shortcodes: [battery_calculator], [battery_faq], [battery_grid], [battery_guides].
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

function batterysizing_shortcode_calculator( $atts ) {
	$atts = shortcode_atts(
		array(
			'slug' => 'battery-calculator',
		),
		$atts,
		'battery_calculator'
	);

	$calc = batterysizing_get_calculator( sanitize_title( $atts['slug'] ) );
	if ( ! $calc ) {
		$calc = batterysizing_get_calculator( 'battery-calculator' );
	}
	if ( ! $calc ) {
		return '';
	}

	ob_start();
	$GLOBALS['batterysizing_current_calc'] = $calc;
	get_template_part( 'template-parts/calculator-widget' );
	unset( $GLOBALS['batterysizing_current_calc'] );
	return ob_get_clean();
}
add_shortcode( 'battery_calculator', 'batterysizing_shortcode_calculator' );

function batterysizing_shortcode_grid( $atts ) {
	ob_start();
	get_template_part( 'template-parts/calculator-grid' );
	return ob_get_clean();
}
add_shortcode( 'battery_grid', 'batterysizing_shortcode_grid' );

function batterysizing_shortcode_faq( $atts ) {
	$atts = shortcode_atts( array( 'home' => 'false' ), $atts, 'battery_faq' );
	$items = filter_var( $atts['home'], FILTER_VALIDATE_BOOLEAN )
		? batterysizing_get_home_faq()
		: batterysizing_get_faq();
	ob_start();
	set_query_var( 'batterysizing_faq_items', $items );
	get_template_part( 'template-parts/faq-list' );
	return ob_get_clean();
}
add_shortcode( 'battery_faq', 'batterysizing_shortcode_faq' );

function batterysizing_shortcode_guides( $atts ) {
	ob_start();
	get_template_part( 'template-parts/guides-grid' );
	return ob_get_clean();
}
add_shortcode( 'battery_guides', 'batterysizing_shortcode_guides' );
