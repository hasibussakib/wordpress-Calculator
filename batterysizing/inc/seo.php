<?php
/**
 * Titles, meta descriptions, canonical, Open Graph, JSON-LD.
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

/**
 * Per-route SEO overlay. Falls back to the page/post title.
 */
function batterysizing_current_seo() {
	$defaults = array(
		'title'       => 'Battery Calculator & Battery Life Calculator',
		'description' => 'Free battery calculator and battery life calculator tools for runtime, capacity, backup time, energy, battery banks, solar systems, and more.',
		'type'        => 'website',
	);

	if ( is_front_page() ) {
		return $defaults;
	}

	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		$calc = batterysizing_get_calculator( $slug );
		if ( $calc ) {
			return array(
				'title'       => $calc['meta_title'],
				'description' => $calc['meta_description'],
				'type'        => 'website',
			);
		}
		$guide = batterysizing_get_guide( $slug );
		if ( $guide ) {
			return array(
				'title'       => $guide['meta_title'],
				'description' => $guide['meta_description'],
				'type'        => 'article',
			);
		}
		$map = array(
			'calculators'    => array( 'Battery Calculators — All 12 Free Tools', 'All 12 BatterySizing calculators in one place: runtime, capacity, backup, bank, solar, UPS, 12V, 24V/48V, LiFePO4 and Ah↔Wh converters.' ),
			'guides'         => array( 'Battery Guides — Ah vs Wh, Runtime, DoD, Chemistry', 'Plain-language battery guides: Ah vs Wh, how to calculate runtime, how many Ah you need, depth of discharge, chemistry and formulas.' ),
			'faq'            => array( 'Battery Calculator FAQ', 'Answers to the most common battery sizing questions: runtime, Ah vs Wh, depth of discharge, 12/24/48V and lithium vs lead-acid.' ),
			'about'          => array( 'About BatterySizing.xyz', 'Why BatterySizing exists, how the calculators work, and how to get in touch.' ),
			'contact'        => array( 'Contact BatterySizing.xyz', 'Questions, corrections or partnership ideas — send a note to the BatterySizing team.' ),
			'privacy-policy' => array( 'Privacy Policy — BatterySizing.xyz', 'How BatterySizing.xyz handles the (very little) data this site touches.' ),
			'terms-of-use'   => array( 'Terms of Use — BatterySizing.xyz', 'Terms for using the BatterySizing.xyz calculators and guides.' ),
		);
		if ( isset( $map[ $slug ] ) ) {
			return array(
				'title'       => $map[ $slug ][0],
				'description' => $map[ $slug ][1],
				'type'        => 'website',
			);
		}
	}

	return array(
		'title'       => wp_get_document_title(),
		'description' => $defaults['description'],
		'type'        => 'website',
	);
}

function batterysizing_document_title( $title ) {
	if ( is_admin() ) {
		return $title;
	}
	$seo = batterysizing_current_seo();
	if ( ! empty( $seo['title'] ) ) {
		$site = wp_strip_all_tags( get_bloginfo( 'name' ) );
		if ( ! $site ) {
			$site = 'BatterySizing.xyz';
		}
		return $seo['title'] . ' | ' . $site;
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'batterysizing_document_title', 20 );

function batterysizing_head_meta() {
	$seo  = batterysizing_current_seo();
	$url  = is_singular() ? get_permalink() : home_url( '/' );
	$desc = $seo['description'];
	$ttl  = $seo['title'] . ' | BatterySizing.xyz';
	$img  = home_url( '/og-image.png' );
	?>
	<meta name="description" content="<?php echo esc_attr( $desc ); ?>">
	<link rel="canonical" href="<?php echo esc_url( $url ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $ttl ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $seo['type'] ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $img ); ?>">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $ttl ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $desc ); ?>">
	<?php
}
add_action( 'wp_head', 'batterysizing_head_meta', 3 );

function batterysizing_json_ld() {
	$graph = array();

	$graph[] = array(
		'@type' => 'WebSite',
		'@id'   => home_url( '/#website' ),
		'name'  => 'BatterySizing.xyz',
		'url'   => home_url( '/' ),
	);

	if ( is_front_page() ) {
		$graph[] = array(
			'@type'       => 'WebPage',
			'name'        => 'Battery Sizing Calculator & Battery Tools',
			'url'         => home_url( '/' ),
			'description' => 'Free battery sizing and calculation tools for runtime, capacity, backup time, energy, battery banks, solar systems, and more.',
			'isPartOf'    => array( '@id' => home_url( '/#website' ) ),
		);
	}

	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		$calc = batterysizing_get_calculator( $slug );
		if ( $calc && ! empty( $calc['faq'] ) ) {
			$entities = array();
			foreach ( $calc['faq'] as $item ) {
				$entities[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $item['q'] ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $item['a'] ),
					),
				);
			}
			$graph[] = array(
				'@type'      => 'FAQPage',
				'mainEntity' => $entities,
			);
		}
		if ( 'faq' === $slug ) {
			$entities = array();
			foreach ( batterysizing_get_faq() as $item ) {
				$entities[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $item['q'] ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $item['a'] ),
					),
				);
			}
			$graph[] = array(
				'@type'      => 'FAQPage',
				'mainEntity' => $entities,
			);
		}
	}

	$payload = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'batterysizing_json_ld', 4 );
