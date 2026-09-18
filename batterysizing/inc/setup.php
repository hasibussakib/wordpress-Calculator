<?php
/**
 * Theme setup: supports, menus.
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

function batterysizing_setup() {
	load_theme_textdomain( 'batterysizing', BATTERYSIZING_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 40,
		'width'       => 160,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'batterysizing' ),
		'footer'  => __( 'Footer Menu', 'batterysizing' ),
	) );
}
add_action( 'after_setup_theme', 'batterysizing_setup' );

/**
 * Add a body class when the admin bar is visible so the sticky header
 * sits below it.
 */
function batterysizing_body_classes( $classes ) {
	if ( is_admin_bar_showing() ) {
		$classes[] = 'has-admin-bar';
	}
	return $classes;
}
add_filter( 'body_class', 'batterysizing_body_classes' );
