<?php
/**
 * Template tags & helpers — inline SVG icon set (lucide-style, hand-inlined
 * so the theme needs no icon package at runtime).
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return an inline SVG icon by name. 24×24 viewBox, stroke-based, currentColor.
 *
 * @param string $name  Icon key.
 * @param string $class Extra classes.
 * @return string SVG markup.
 */
function batterysizing_get_icon( $name, $class = '' ) {
	$paths = array(
		'battery'     => '<rect x="2" y="7" width="16" height="10" rx="2"/><line x1="22" y1="11" x2="22" y2="13"/><rect x="5" y="10" width="4" height="4" rx="1" fill="currentColor" stroke="none"/><rect x="10.5" y="10" width="4" height="4" rx="1" fill="currentColor" stroke="none"/>',
		'battery12'   => '<rect x="2" y="7" width="16" height="10" rx="2"/><line x1="22" y1="11" x2="22" y2="13"/><text x="10" y="14.4" font-size="6.5" font-weight="700" text-anchor="middle" fill="currentColor" stroke="none">12</text>',
		'battery24'   => '<rect x="2" y="7" width="16" height="10" rx="2"/><line x1="22" y1="11" x2="22" y2="13"/><text x="10" y="14.4" font-size="6.5" font-weight="700" text-anchor="middle" fill="currentColor" stroke="none">48</text>',
		'backup'      => '<rect x="2" y="7" width="16" height="10" rx="2"/><line x1="22" y1="11" x2="22" y2="13"/><path d="M11 8.5 8.5 12h2l-.5 2.5L13 11h-2l1-2.5z" fill="currentColor" stroke="none"/>',
		'lithium'     => '<circle cx="12" cy="12" r="9"/><path d="M12.5 7 9.5 12.5h2.5L11 17l3.5-5.5H12z" fill="currentColor" stroke="none"/>',
		'timer'       => '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 2.5"/><path d="M9 2h6"/><path d="M18.5 5.5 20 4"/>',
		'gauge'       => '<path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M14.1 9.9 18 6"/><path d="M3.6 15.4A9 9 0 1 1 20.4 15.4"/>',
		'bank'        => '<rect x="3" y="14" width="7" height="6" rx="1.5"/><rect x="14" y="14" width="7" height="6" rx="1.5"/><rect x="8.5" y="4" width="7" height="6" rx="1.5"/><path d="M12 10v4M6.5 14v-2h11v2"/>',
		'sun'         => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
		'ups'         => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M12.5 6.5 9 12h3l-.5 4L15 10h-3z" fill="currentColor" stroke="none"/><circle cx="12" cy="18.5" r="0.5" fill="currentColor"/>',
		'convert'     => '<path d="M4 8h13l-3-3M20 16H7l3 3"/><text x="12" y="23" font-size="0" fill="none">.</text>',
		'convert2'    => '<path d="M20 8H7l3-3M4 16h13l-3 3"/>',
		'calculator'  => '<rect x="4" y="2" width="16" height="20" rx="2"/><rect x="8" y="6" width="8" height="3" rx="0.5"/><line x1="8" y1="13" x2="8.01" y2="13"/><line x1="12" y1="13" x2="12.01" y2="13"/><line x1="16" y1="13" x2="16.01" y2="13"/><line x1="8" y1="17" x2="8.01" y2="17"/><line x1="12" y1="17" x2="12.01" y2="17"/><line x1="16" y1="17" x2="16.01" y2="17"/>',
		'zap'         => '<path d="M13 2 4.5 13.5H11L10 22l8.5-11.5H12z"/>',
		'book'        => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
		'chart'       => '<path d="M3 3v18h18"/><rect x="7" y="12" width="3" height="6" rx="0.5"/><rect x="12" y="8" width="3" height="10" rx="0.5"/><rect x="17" y="5" width="3" height="13" rx="0.5"/>',
		'plug'        => '<path d="M9 2v6M15 2v6"/><path d="M6 8h12v3a6 6 0 0 1-6 6 6 6 0 0 1-6-6z"/><path d="M12 17v5"/>',
		'check'       => '<path d="M20 6 9 17l-5-5"/>',
		'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'chevron-down'=> '<path d="m6 9 6 6 6-6"/>',
		'menu'        => '<line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/>',
		'x'           => '<path d="M18 6 6 18M6 6l12 12"/>',
		'plus'        => '<path d="M12 5v14M5 12h14"/>',
		'trash'       => '<path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>',
		'mail'        => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
		'shield'      => '<path d="M12 22s8-3.5 8-10V5l-8-3-8 3v7c0 6.5 8 10 8 10z"/>',
		'info'        => '<circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v4h1"/>',
		'home'        => '<path d="m3 10 9-7 9 7v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 21v-8h6v8"/>',
		'search'      => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
		'sparkles'    => '<path d="m12 3 1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/><path d="M19 15.5 19.9 18l2.5.9-2.5.9L19 22.5l-.9-2.5-2.5-.9 2.5-.9z"/>',
		'bolt-circle' => '<circle cx="12" cy="12" r="9"/><path d="M12.8 7 9.6 12.6h2.6L11.4 17l3.4-5.6h-2.6z" fill="currentColor" stroke="none"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		$name = 'battery';
	}

	return sprintf(
		'<svg class="bs-icon %s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
		esc_attr( $class ),
		$paths[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG paths defined above.
	);
}

/**
 * Echo an icon.
 */
function batterysizing_icon( $name, $class = '' ) {
	echo batterysizing_get_icon( $name, $class ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Format hours as "3h 25m".
 */
function batterysizing_format_hours( $hours ) {
	$hours = max( 0, (float) $hours );
	$h     = floor( $hours );
	$m     = round( ( $hours - $h ) * 60 );
	if ( $m >= 60 ) {
		$h++;
		$m -= 60;
	}
	if ( $h <= 0 ) {
		return sprintf( '%dm', $m );
	}
	return sprintf( '%dh %02dm', $h, $m );
}

/**
 * Small helper: site tagline used in hero (customizable).
 */
function batterysizing_hero_heading() {
	$custom = get_theme_mod( 'batterysizing_hero_heading' );
	if ( $custom ) {
		return $custom;
	}
	return __( 'Battery Calculator &amp; Battery Life Calculator', 'batterysizing' );
}

function batterysizing_hero_sub() {
	$custom = get_theme_mod( 'batterysizing_hero_sub' );
	if ( $custom ) {
		return $custom;
	}
	return __( 'Free battery calculator and battery life calculator tools for runtime, capacity, backup time, energy, battery banks, solar systems, and more.', 'batterysizing' );
}

/**
 * Whether the current page uses one of our templates.
 */
function batterysizing_is_calc_page() {
	return is_page_template( 'page-templates/template-calculator.php' );
}
