<?php
/**
 * Appearance → Batterysizing Setup.
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

function batterysizing_admin_menu() {
	add_theme_page(
		__( 'Batterysizing Setup', 'batterysizing' ),
		__( 'Batterysizing Setup', 'batterysizing' ),
		'edit_theme_options',
		'batterysizing-setup',
		'batterysizing_admin_page'
	);
}
add_action( 'admin_menu', 'batterysizing_admin_menu' );

function batterysizing_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$notice = '';
	if ( isset( $_POST['batterysizing_import'] ) && check_admin_referer( 'batterysizing_import' ) ) {
		$created = batterysizing_import_demo_content();
		$notice  = sprintf(
			/* translators: %d: number of pages processed */
			__( 'Imported / refreshed %d pages and assigned menus. Your front page is now the Batterysizing homepage.', 'batterysizing' ),
			count( $created )
		);
	}

	$imported = get_option( 'batterysizing_demo_imported' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Batterysizing Setup', 'batterysizing' ); ?></h1>
		<?php if ( $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>

		<div class="card" style="max-width:720px;padding:20px 24px;">
			<h2><?php esc_html_e( '1. Import pages &amp; menus', 'batterysizing' ); ?></h2>
			<p><?php esc_html_e( 'Creates every calculator, guide, FAQ, About, Contact, Privacy and Terms page, builds the primary + footer menus, and sets a static front page. Safe to run more than once — existing pages are updated, not duplicated.', 'batterysizing' ); ?></p>
			<?php if ( $imported ) : ?>
				<p><em><?php echo esc_html( sprintf( __( 'Last imported: %s', 'batterysizing' ), date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $imported ) ) ); ?></em></p>
			<?php endif; ?>
			<form method="post">
				<?php wp_nonce_field( 'batterysizing_import' ); ?>
				<button type="submit" name="batterysizing_import" class="button button-primary button-hero">
					<?php echo $imported ? esc_html__( 'Re-import / refresh pages', 'batterysizing' ) : esc_html__( 'Import demo content', 'batterysizing' ); ?>
				</button>
			</form>
		</div>

		<div class="card" style="max-width:720px;padding:20px 24px;margin-top:16px;">
			<h2><?php esc_html_e( '2. What you get', 'batterysizing' ); ?></h2>
			<ul style="list-style:disc;padding-left:1.25rem;">
				<li><?php esc_html_e( '12 calculator pages + /calculators/ hub', 'batterysizing' ); ?></li>
				<li><?php esc_html_e( '7 guides under /guides/ + the hub', 'batterysizing' ); ?></li>
				<li><?php esc_html_e( 'FAQ, About, Contact, Privacy, Terms', 'batterysizing' ); ?></li>
				<li><?php esc_html_e( 'Homepage with appliance load estimator and what-if runtime comparison', 'batterysizing' ); ?></li>
				<li><?php esc_html_e( 'Primary and footer menus already assigned', 'batterysizing' ); ?></li>
			</ul>
		</div>
	</div>
	<?php
}
