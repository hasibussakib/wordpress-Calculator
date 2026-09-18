<?php
/**
 * Site header — logo, Calculators + Guides dropdowns, CTA.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="bs-skip" href="#main"><?php esc_html_e( 'Skip to content', 'batterysizing' ); ?></a>

<header class="bs-header" id="top">
	<div class="bs-container bs-header-inner">
		<a class="bs-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="bs-logo-mark" aria-hidden="true">
				<?php batterysizing_icon( 'bolt-circle' ); ?>
			</span>
			<span class="bs-logo-word">BatterySizing</span><span class="bs-logo-tld">.xyz</span>
		</a>

		<nav class="bs-nav" aria-label="<?php esc_attr_e( 'Primary', 'batterysizing' ); ?>">
			<button class="bs-nav-toggle" type="button" aria-expanded="false" aria-controls="bs-nav-panel" data-bs-nav-toggle>
				<span class="bs-nav-toggle-open"><?php batterysizing_icon( 'menu' ); ?></span>
				<span class="bs-nav-toggle-close"><?php batterysizing_icon( 'x' ); ?></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'batterysizing' ); ?></span>
			</button>

			<div class="bs-nav-panel" id="bs-nav-panel">
				<ul class="bs-menu">
					<li class="bs-menu-item has-children">
						<button class="bs-menu-trigger" type="button" aria-expanded="false">
							<?php esc_html_e( 'Calculators', 'batterysizing' ); ?>
							<?php batterysizing_icon( 'chevron-down', 'bs-chevron' ); ?>
						</button>
						<div class="bs-dropdown bs-dropdown-wide">
							<a class="bs-dropdown-head" href="<?php echo esc_url( home_url( '/calculators/' ) ); ?>">
								<?php batterysizing_icon( 'calculator' ); ?>
								<span>
									<strong><?php esc_html_e( 'All calculators', 'batterysizing' ); ?></strong>
									<small><?php esc_html_e( '12 free tools in one hub', 'batterysizing' ); ?></small>
								</span>
							</a>
							<ul class="bs-dropdown-grid">
								<?php foreach ( batterysizing_get_calculators() as $c ) : ?>
									<li>
										<a href="<?php echo esc_url( batterysizing_calculator_url( $c['slug'] ) ); ?>">
											<span class="bs-drop-icon"><?php batterysizing_icon( $c['icon'] ); ?></span>
											<span>
												<strong><?php echo esc_html( $c['name'] ); ?></strong>
												<small><?php echo esc_html( $c['tagline'] ); ?></small>
											</span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</li>
					<li class="bs-menu-item has-children">
						<button class="bs-menu-trigger" type="button" aria-expanded="false">
							<?php esc_html_e( 'Guides', 'batterysizing' ); ?>
							<?php batterysizing_icon( 'chevron-down', 'bs-chevron' ); ?>
						</button>
						<div class="bs-dropdown">
							<a class="bs-dropdown-head" href="<?php echo esc_url( home_url( '/guides/' ) ); ?>">
								<?php batterysizing_icon( 'book' ); ?>
								<span>
									<strong><?php esc_html_e( 'All guides', 'batterysizing' ); ?></strong>
									<small><?php esc_html_e( 'Ah vs Wh, runtime, DoD, chemistry', 'batterysizing' ); ?></small>
								</span>
							</a>
							<ul class="bs-dropdown-list">
								<?php foreach ( batterysizing_get_guides() as $g ) : ?>
									<li>
										<a href="<?php echo esc_url( batterysizing_guide_url( $g['slug'] ) ); ?>">
											<?php echo esc_html( $g['name'] ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</li>
					<li class="bs-menu-item"><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'batterysizing' ); ?></a></li>
					<li class="bs-menu-item"><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'batterysizing' ); ?></a></li>
				</ul>
				<a class="bs-btn bs-btn-primary bs-nav-cta" href="<?php echo esc_url( home_url( '/battery-calculator/' ) ); ?>">
					<?php batterysizing_icon( 'zap' ); ?>
					<?php esc_html_e( 'Open calculator', 'batterysizing' ); ?>
				</a>
			</div>
		</nav>
	</div>
</header>

<main id="main" class="bs-main">
