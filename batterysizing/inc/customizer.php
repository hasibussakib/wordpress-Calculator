<?php
/**
 * Customizer: hero copy, contact email, footer blurb.
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

function batterysizing_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'batterysizing', array(
		'title'    => __( 'Batterysizing', 'batterysizing' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'batterysizing_hero_heading', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'batterysizing_hero_heading', array(
		'label'   => __( 'Hero heading', 'batterysizing' ),
		'section' => 'batterysizing',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'batterysizing_hero_sub', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
	$wp_customize->add_control( 'batterysizing_hero_sub', array(
		'label'   => __( 'Hero subtitle', 'batterysizing' ),
		'section' => 'batterysizing',
		'type'    => 'textarea',
	) );

	$wp_customize->add_setting( 'batterysizing_contact_email', array(
		'default'           => 'hello@batterysizing.xyz',
		'sanitize_callback' => 'sanitize_email',
	) );
	$wp_customize->add_control( 'batterysizing_contact_email', array(
		'label'   => __( 'Public contact email', 'batterysizing' ),
		'section' => 'batterysizing',
		'type'    => 'email',
	) );
}
add_action( 'customize_register', 'batterysizing_customize_register' );
