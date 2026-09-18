<?php
/**
 * Homepage — mirrors the original BatterySizing.xyz landing page:
 * Hero → 12 calculator cards → Appliance Load Estimator → What-If
 * Runtime Comparison → Guides → FAQ → CTA.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/hero' );
get_template_part( 'template-parts/calculator-grid' );
get_template_part( 'template-parts/estimator' );
get_template_part( 'template-parts/whatif' );
get_template_part( 'template-parts/guides-teaser' );
get_template_part( 'template-parts/faq-teaser' );
get_template_part( 'template-parts/cta' );

get_footer();
