<?php
/**
 * Styles and scripts.
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

function batterysizing_enqueue_assets() {
	// Compiled Tailwind utilities.
	wp_enqueue_style(
		'batterysizing-tailwind',
		BATTERYSIZING_URI . '/assets/css/tailwind.css',
		array(),
		BATTERYSIZING_VERSION
	);

	// Custom component styles.
	wp_enqueue_style(
		'batterysizing-theme',
		BATTERYSIZING_URI . '/assets/css/theme.css',
		array( 'batterysizing-tailwind' ),
		BATTERYSIZING_VERSION
	);

	// Theme header (empty, but keeps WP conventions happy).
	wp_enqueue_style(
		'batterysizing-style',
		get_stylesheet_uri(),
		array( 'batterysizing-theme' ),
		BATTERYSIZING_VERSION
	);

	// Core battery math (shared by every interactive widget).
	wp_enqueue_script(
		'batterysizing-math',
		BATTERYSIZING_URI . '/assets/js/battery-math.js',
		array(),
		BATTERYSIZING_VERSION,
		true
	);

	// Chrome: mobile nav, accordions, reveal-on-scroll.
	wp_enqueue_script(
		'batterysizing-theme',
		BATTERYSIZING_URI . '/assets/js/theme.js',
		array(),
		BATTERYSIZING_VERSION,
		true
	);

	// Universal calculator engine — only where a calculator is rendered.
	if ( batterysizing_page_has_calculator() ) {
		wp_enqueue_script(
			'batterysizing-calculator',
			BATTERYSIZING_URI . '/assets/js/calculator.js',
			array( 'batterysizing-math' ),
			BATTERYSIZING_VERSION,
			true
		);
	}

	// Homepage widgets.
	if ( is_front_page() ) {
		wp_enqueue_script(
			'batterysizing-estimator',
			BATTERYSIZING_URI . '/assets/js/estimator.js',
			array( 'batterysizing-math' ),
			BATTERYSIZING_VERSION,
			true
		);
		wp_enqueue_script(
			'batterysizing-whatif',
			BATTERYSIZING_URI . '/assets/js/whatif.js',
			array( 'batterysizing-math' ),
			BATTERYSIZING_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'batterysizing_enqueue_assets' );

/**
 * Preconnect + Inter font (same family the original site used).
 */
function batterysizing_fonts() {
	?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<?php
}
add_action( 'wp_head', 'batterysizing_fonts', 1 );

/**
 * Inline SVG favicon (battery bolt) unless the site has a site icon.
 */
function batterysizing_favicon() {
	if ( has_site_icon() ) {
		return;
	}
	echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( BATTERYSIZING_URI . '/assets/img/favicon.svg' ) . '">' . "\n";
}
add_action( 'wp_head', 'batterysizing_favicon', 2 );
