<?php
/**
 * One-click import of every calculator, guide and legal page, plus menus
 * and a static front page. Safe to re-run: existing slugs are skipped.
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

/**
 * Insert (or fetch) a page by slug.
 *
 * @return int Page ID.
 */
function batterysizing_ensure_page( $slug, $title, $args = array() ) {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		$update = array( 'ID' => $existing->ID );
		if ( ! empty( $args['post_parent'] ) && (int) $existing->post_parent !== (int) $args['post_parent'] ) {
			$update['post_parent'] = (int) $args['post_parent'];
		}
		if ( ! empty( $args['post_content'] ) && $existing->post_content !== $args['post_content'] ) {
			$update['post_content'] = $args['post_content'];
		}
		if ( count( $update ) > 1 ) {
			wp_update_post( $update );
		}
		if ( ! empty( $args['page_template'] ) ) {
			update_post_meta( $existing->ID, '_wp_page_template', $args['page_template'] );
		}
		return (int) $existing->ID;
	}

	$parent = 0;
	if ( ! empty( $args['post_parent'] ) ) {
		$parent = (int) $args['post_parent'];
	} elseif ( false !== strpos( $slug, '/' ) ) {
		$parts     = explode( '/', $slug );
		$slug      = array_pop( $parts );
		$parent_path = implode( '/', $parts );
		$parent_page = get_page_by_path( $parent_path );
		if ( $parent_page ) {
			$parent = (int) $parent_page->ID;
		}
	}

	$id = wp_insert_post( array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_content' => isset( $args['post_content'] ) ? $args['post_content'] : '',
		'post_parent'  => $parent,
		'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
	) );

	if ( $id && ! is_wp_error( $id ) && ! empty( $args['page_template'] ) ) {
		update_post_meta( $id, '_wp_page_template', $args['page_template'] );
	}

	return is_wp_error( $id ) ? 0 : (int) $id;
}

function batterysizing_about_content() {
	return <<<HTML
<p>BatterySizing.xyz is a free battery calculator and sizing platform. Every tool on this site exists to answer one of two questions:</p>
<ul>
<li><strong>How long will this battery last?</strong></li>
<li><strong>What battery do I actually need?</strong></li>
</ul>
<p>The numbers on a battery case — 12V, 100Ah — are not the numbers that run your fridge. Usable energy depends on chemistry (depth of discharge), the inverter in between, and how hard you pull. The calculators bake those in, so a 5-hour answer is a 5-hour answer, not a brochure.</p>
<h2>What's on the site</h2>
<ul>
<li>12 interactive calculators covering runtime, capacity, backup, banks, solar, UPS, 12V / 24V / 48V, LiFePO4 and Ah↔Wh conversions.</li>
<li>An appliance load estimator with a live chart (homepage) so you can total watts before you size anything.</li>
<li>Guides on Ah vs Wh, runtime, DoD, chemistry and the formulas themselves.</li>
</ul>
<p>Nothing here is a substitute for an electrician or a manufacturer's datasheet. It is a fast, honest first pass — the kind of math you would do on paper if you still remembered Peukert.</p>
<h2>Get in touch</h2>
<p>Corrections, missing chemistries, or a setup the calculators don't cover: <a href="/contact/">contact us</a> or email <a href="mailto:hello@batterysizing.xyz">hello@batterysizing.xyz</a>.</p>
HTML;
}

function batterysizing_privacy_content() {
	return <<<HTML
<p><em>Last updated: update this date before publishing.</em></p>
<p>BatterySizing.xyz is a calculator site. We do not require an account and we do not sell personal data.</p>
<h2>What we collect</h2>
<ul>
<li><strong>Calculator inputs</strong> stay in your browser. They are not sent to our servers.</li>
<li><strong>Contact form</strong> submissions (name, email, message) are emailed to us so we can reply, and are not used for marketing.</li>
<li><strong>Server logs</strong> may include your IP address, browser and the pages you hit, kept only as long as needed for security and debugging.</li>
</ul>
<h2>Cookies</h2>
<p>WordPress may set a cookie if you log in to the admin. The public calculators do not set tracking cookies. If a future analytics tool is added, this page will be updated first.</p>
<h2>Contact</h2>
<p>Privacy questions: <a href="mailto:hello@batterysizing.xyz">hello@batterysizing.xyz</a>.</p>
HTML;
}

function batterysizing_terms_content() {
	return <<<HTML
<p><em>Last updated: update this date before publishing.</em></p>
<p>The calculators and guides on BatterySizing.xyz are provided free, as-is, for education and planning. They are not a professional electrical design, a code-compliant specification, or a warranty.</p>
<h2>Accuracy</h2>
<p>Formulas use well-known relationships (Wh = V × Ah, runtime = usable energy ÷ load) plus conservative defaults for depth of discharge and inverter efficiency. Real batteries vary with age, temperature, Peukert effect and manufacturing tolerance. Always confirm with the manufacturer's datasheet and a qualified installer before you buy or wire anything.</p>
<h2>Liability</h2>
<p>BatterySizing.xyz and its authors are not liable for damage, injury, data loss or costs arising from use of these tools. You are responsible for your system design, fusing, ventilation and local electrical code.</p>
<h2>Contact</h2>
<p><a href="mailto:hello@batterysizing.xyz">hello@batterysizing.xyz</a></p>
HTML;
}

/**
 * Import pages, menus, reading settings.
 *
 * @return array Summary of what was created.
 */
function batterysizing_import_demo_content() {
	$created = array();

	$home_id = batterysizing_ensure_page( 'home', 'Home', array(
		'page_template' => 'front-page.php',
	) );
	$created[] = 'home';

	$calc_hub = batterysizing_ensure_page( 'calculators', 'All Calculators', array(
		'page_template' => 'page-templates/template-hub.php',
		'post_content'  => '<!-- calculators hub -->',
	) );
	$created[] = 'calculators';

	foreach ( batterysizing_get_calculators() as $calc ) {
		batterysizing_ensure_page( $calc['slug'], $calc['name'], array(
			'page_template' => 'page-templates/template-calculator.php',
			'post_parent'   => 0,
			'post_content'  => sprintf( '<!-- calculator:%s -->', $calc['slug'] ),
		) );
		$created[] = $calc['slug'];
	}

	$guides_hub = batterysizing_ensure_page( 'guides', 'Guides', array(
		'page_template' => 'page-templates/template-hub.php',
		'post_content'  => '<!-- guides hub -->',
	) );
	$created[] = 'guides';

	foreach ( batterysizing_get_guides() as $guide ) {
		batterysizing_ensure_page( $guide['slug'], $guide['name'], array(
			'page_template' => 'page-templates/template-guide.php',
			'post_parent'   => $guides_hub,
			'post_content'  => $guide['content'],
		) );
		$created[] = 'guides/' . $guide['slug'];
	}

	batterysizing_ensure_page( 'faq', 'FAQ', array(
		'page_template' => 'page-templates/template-faq.php',
	) );
	batterysizing_ensure_page( 'about', 'About', array(
		'post_content' => batterysizing_about_content(),
	) );
	batterysizing_ensure_page( 'contact', 'Contact', array(
		'page_template' => 'page-templates/template-contact.php',
	) );
	batterysizing_ensure_page( 'privacy-policy', 'Privacy Policy', array(
		'page_template' => 'page-templates/template-legal.php',
		'post_content'  => batterysizing_privacy_content(),
	) );
	batterysizing_ensure_page( 'terms-of-use', 'Terms of Use', array(
		'page_template' => 'page-templates/template-legal.php',
		'post_content'  => batterysizing_terms_content(),
	) );
	$created[] = 'faq';
	$created[] = 'about';
	$created[] = 'contact';
	$created[] = 'privacy-policy';
	$created[] = 'terms-of-use';

	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		if ( function_exists( 'flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
		}
	}

	batterysizing_ensure_menus();

	update_option( 'batterysizing_demo_imported', time() );
	update_option( 'blogdescription', 'Free battery calculator and battery life calculator tools.' );

	return $created;
}

function batterysizing_ensure_menus() {
	$primary_id = batterysizing_create_menu( 'Batterysizing Primary', 'primary', array(
		array( 'title' => 'Calculators', 'slug' => 'calculators' ),
		array( 'title' => 'Guides', 'slug' => 'guides' ),
		array( 'title' => 'FAQ', 'slug' => 'faq' ),
		array( 'title' => 'About', 'slug' => 'about' ),
	) );

	$footer_id = batterysizing_create_menu( 'Batterysizing Footer', 'footer', array(
		array( 'title' => 'Calculators', 'slug' => 'calculators' ),
		array( 'title' => 'Guides', 'slug' => 'guides' ),
		array( 'title' => 'FAQ', 'slug' => 'faq' ),
		array( 'title' => 'About', 'slug' => 'about' ),
		array( 'title' => 'Contact', 'slug' => 'contact' ),
		array( 'title' => 'Privacy', 'slug' => 'privacy-policy' ),
		array( 'title' => 'Terms', 'slug' => 'terms-of-use' ),
	) );

	return array( $primary_id, $footer_id );
}

function batterysizing_create_menu( $name, $location, $items ) {
	$existing = wp_get_nav_menu_object( $name );
	if ( $existing ) {
		$menu_id = (int) $existing->term_id;
	} else {
		$menu_id = (int) wp_create_nav_menu( $name );
	}
	if ( ! $menu_id ) {
		return 0;
	}

	$existing_items = wp_get_nav_menu_items( $menu_id );
	$have           = array();
	if ( $existing_items ) {
		foreach ( $existing_items as $item ) {
			$have[] = $item->title;
		}
	}

	$order = 1;
	foreach ( $items as $item ) {
		if ( in_array( $item['title'], $have, true ) ) {
			$order++;
			continue;
		}
		$page = get_page_by_path( $item['slug'] );
		if ( ! $page ) {
			continue;
		}
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'     => $item['title'],
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $page->ID,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-position'  => $order,
		) );
		$order++;
	}

	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	return $menu_id;
}
