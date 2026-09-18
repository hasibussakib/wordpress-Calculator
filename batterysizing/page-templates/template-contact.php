<?php
/**
 * Template Name: Contact
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

get_header();

$status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
$email  = batterysizing_contact_email();
?>
<section class="bs-page-hero">
	<div class="bs-container">
		<p class="bs-kicker"><?php esc_html_e( 'Hello', 'batterysizing' ); ?></p>
		<h1><?php the_title(); ?></h1>
		<p class="bs-lede"><?php esc_html_e( 'Questions, corrections, a chemistry we do not cover — send a note. We read everything.', 'batterysizing' ); ?></p>
	</div>
</section>
<section class="bs-section">
	<div class="bs-container bs-split">
		<div>
			<?php if ( 'sent' === $status ) : ?>
				<div class="bs-alert bs-alert-ok"><?php esc_html_e( 'Message sent. We will get back to you.', 'batterysizing' ); ?></div>
			<?php elseif ( 'error' === $status ) : ?>
				<div class="bs-alert bs-alert-err"><?php esc_html_e( 'Something went missing — check the name, a valid email, and a message.', 'batterysizing' ); ?></div>
			<?php endif; ?>

			<form class="bs-form" method="post" action="">
				<?php wp_nonce_field( 'batterysizing_contact', 'batterysizing_contact_nonce' ); ?>
				<p class="bs-hp" aria-hidden="true">
					<label><?php esc_html_e( 'Website', 'batterysizing' ); ?>
						<input type="text" name="batterysizing_website" tabindex="-1" autocomplete="off">
					</label>
				</p>
				<label>
					<span><?php esc_html_e( 'Name', 'batterysizing' ); ?></span>
					<input type="text" name="bs_name" required>
				</label>
				<label>
					<span><?php esc_html_e( 'Email', 'batterysizing' ); ?></span>
					<input type="email" name="bs_email" required>
				</label>
				<label>
					<span><?php esc_html_e( 'Message', 'batterysizing' ); ?></span>
					<textarea name="bs_message" rows="7" required></textarea>
				</label>
				<button type="submit" class="bs-btn bs-btn-primary"><?php esc_html_e( 'Send message', 'batterysizing' ); ?></button>
			</form>
		</div>
		<aside class="bs-aside">
			<div class="bs-card bs-card-pad">
				<h3><?php esc_html_e( 'Email us directly', 'batterysizing' ); ?></h3>
				<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
				<p class="bs-muted"><?php esc_html_e( 'We are not an installer and cannot spec a full electrical design, but we will fix calculator bugs and missing formulas fast.', 'batterysizing' ); ?></p>
			</div>
		</aside>
	</div>
</section>
<?php
get_footer();
