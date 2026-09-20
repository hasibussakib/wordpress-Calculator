<?php
/**
 * Batterysizing theme bootstrap.
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

define( 'BATTERYSIZING_VERSION', '1.1.0' );
define( 'BATTERYSIZING_DIR', get_template_directory() );
define( 'BATTERYSIZING_URI', get_template_directory_uri() );

require_once BATTERYSIZING_DIR . '/inc/setup.php';
require_once BATTERYSIZING_DIR . '/inc/enqueue.php';
require_once BATTERYSIZING_DIR . '/inc/template-tags.php';
require_once BATTERYSIZING_DIR . '/inc/calculators.php';
require_once BATTERYSIZING_DIR . '/inc/guides.php';
require_once BATTERYSIZING_DIR . '/inc/faq.php';
require_once BATTERYSIZING_DIR . '/inc/shortcodes.php';
require_once BATTERYSIZING_DIR . '/inc/seo.php';
require_once BATTERYSIZING_DIR . '/inc/contact-form.php';
require_once BATTERYSIZING_DIR . '/inc/customizer.php';
require_once BATTERYSIZING_DIR . '/inc/demo-content.php';

if ( is_admin() ) {
	require_once BATTERYSIZING_DIR . '/inc/admin-page.php';
}

/**
 * One-time site setup right after the theme is activated:
 * import demo pages, menus, set the front page and permalinks.
 */
function batterysizing_after_switch_theme() {
	if ( get_option( 'batterysizing_demo_imported' ) ) {
		return;
	}
	if ( function_exists( 'batterysizing_import_demo_content' ) ) {
		batterysizing_import_demo_content();
	}
}
add_action( 'after_switch_theme', 'batterysizing_after_switch_theme' );
