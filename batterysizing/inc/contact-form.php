<?php
/**
 * Simple contact form (no third-party plugin).
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

function batterysizing_contact_email() {
	$email = get_theme_mod( 'batterysizing_contact_email' );
	if ( $email ) {
		return $email;
	}
	$admin = get_option( 'admin_email' );
	return $admin ? $admin : 'hello@batterysizing.xyz';
}

function batterysizing_handle_contact_form() {
	if ( empty( $_POST['batterysizing_contact_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['batterysizing_contact_nonce'] ) ), 'batterysizing_contact' ) ) {
		return;
	}

	// Honeypot.
	if ( ! empty( $_POST['batterysizing_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'contact', 'sent', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
		exit;
	}

	$name    = isset( $_POST['bs_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bs_name'] ) ) : '';
	$email   = isset( $_POST['bs_email'] ) ? sanitize_email( wp_unslash( $_POST['bs_email'] ) ) : '';
	$message = isset( $_POST['bs_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bs_message'] ) ) : '';

	if ( ! $name || ! is_email( $email ) || ! $message ) {
		wp_safe_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
		exit;
	}

	$to      = batterysizing_contact_email();
	$subject = sprintf( '[BatterySizing] Message from %s', $name );
	$body    = sprintf( "From: %s <%s>\n\n%s", $name, $email, $message );
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );
	wp_safe_redirect( add_query_arg( 'contact', $sent ? 'sent' : 'error', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
	exit;
}
add_action( 'template_redirect', 'batterysizing_handle_contact_form' );
