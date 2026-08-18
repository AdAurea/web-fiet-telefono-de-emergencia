<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$uri   = get_template_directory_uri();
$telv  = fiet_option( 'telefono_display', '900 759 759' );
$tel   = fiet_option( 'telefono_tel', '900759759' );
$email = fiet_option( 'email_contacto', 'informacion@fiet.ong' );

// Enlaces legales: (etiqueta, opción con la URL). Si la URL está vacía se muestra el texto sin enlace.
$legales = array(
	'Política de privacidad'             => fiet_option( 'privacidad_url', '' ),
	'Política de cookies'                => fiet_option( 'cookies_url', '' ),
	'Política y condiciones de donación' => fiet_option( 'donaciones_url', '' ),
);
?>
<footer class="site-footer" role="contentinfo">
	<div class="footer-inner">
		<div class="footer-brand">
			<a class="footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="FIET · Inicio">
				<img src="<?php echo esc_url( $uri . '/logo_footer.png' ); ?>" alt="FIET">
			</a>
			<p class="footer-tag">Teléfono de Ayuda Contra la Trata</p>
		</div>

		<div class="footer-col">
			<h3>Contacto</h3>
			<ul class="footer-contact">
				<li>
					<span class="footer-ico" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.53 15.53 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24 11.36 11.36 0 0 0 3.57.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.36 11.36 0 0 0 .57 3.57 1 1 0 0 1-.24 1.02l-2.21 2.2z"/></svg></span>
					<a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $telv ); ?></a>
				</li>
				<li>
					<span class="footer-ico" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg></span>
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				</li>
			</ul>
		</div>

		<div class="footer-col">
			<h3>Información legal</h3>
			<ul class="footer-legal">
				<?php foreach ( $legales as $label => $url ) : ?>
					<li>
						<?php if ( $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $label ); ?></a>
						<?php else : ?>
							<span><?php echo esc_html( $label ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="footer-bottom">
		<p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> FIET · Teléfono de Ayuda Contra la Trata. Todos los derechos reservados.</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
