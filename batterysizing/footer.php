<?php
/**
 * Site footer.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="bs-footer">
	<div class="bs-container">
		<div class="bs-footer-grid">
			<div class="bs-footer-brand">
				<a class="bs-logo bs-logo-light" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span class="bs-logo-mark" aria-hidden="true"><?php batterysizing_icon( 'bolt-circle' ); ?></span>
					<span class="bs-logo-word">BatterySizing</span><span class="bs-logo-tld">.xyz</span>
				</a>
				<p><?php esc_html_e( 'Free battery calculator and battery life calculator tools for runtime, capacity, backup time, energy, battery banks, solar systems, and more.', 'batterysizing' ); ?></p>
			</div>

			<div>
				<h3><?php esc_html_e( 'Calculators', 'batterysizing' ); ?></h3>
				<ul>
					<?php foreach ( array_slice( batterysizing_get_calculators(), 0, 6 ) as $c ) : ?>
						<li><a href="<?php echo esc_url( batterysizing_calculator_url( $c['slug'] ) ); ?>"><?php echo esc_html( $c['name'] ); ?></a></li>
					<?php endforeach; ?>
					<li><a href="<?php echo esc_url( home_url( '/calculators/' ) ); ?>"><?php esc_html_e( 'All calculators →', 'batterysizing' ); ?></a></li>
				</ul>
			</div>

			<div>
				<h3><?php esc_html_e( 'Guides', 'batterysizing' ); ?></h3>
				<ul>
					<?php foreach ( batterysizing_get_guides() as $g ) : ?>
						<li><a href="<?php echo esc_url( batterysizing_guide_url( $g['slug'] ) ); ?>"><?php echo esc_html( $g['name'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h3><?php esc_html_e( 'Site', 'batterysizing' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'batterysizing' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'batterysizing' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'batterysizing' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'batterysizing' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms-of-use/' ) ); ?>"><?php esc_html_e( 'Terms of Use', 'batterysizing' ); ?></a></li>
				</ul>
			</div>
		</div>

		<div class="bs-footer-bottom">
			<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> BatterySizing.xyz. <?php esc_html_e( 'Calculators are for planning — confirm with a datasheet and a qualified installer.', 'batterysizing' ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
