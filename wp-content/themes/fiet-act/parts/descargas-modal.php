<?php
/**
 * Modal de descarga de material (captación nombre + correo).
 * Reutilizable en cualquier página con enlaces/botones .js-descarga.
 * Requiere que descargas.js y FIET_DL estén encolados (ver functions.php).
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
  <!-- Modal de descarga de material (captación de nombre + correo) -->
  <div class="dl-backdrop" id="dlBackdrop" hidden></div>
  <aside class="dl-modal" id="dlModal" aria-hidden="true" aria-label="Descargar material">
    <button class="dl-close" id="dlClose" type="button" aria-label="Cerrar">&times;</button>

    <form class="dl-form" id="dlForm" novalidate>
      <h3>Descarga el material</h3>
      <p class="dl-sub">Déjanos tu nombre y correo para acceder a la descarga. Solo lo usaremos para darte acceso.</p>
      <input type="hidden" name="sector" id="dlSector" value="">
      <label>Nombre y apellidos
        <input type="text" name="nombre" id="dlNombre" autocomplete="name" required>
      </label>
      <label>Correo electrónico
        <input type="email" name="correo" id="dlCorreo" autocomplete="email" required>
      </label>
      <label class="dl-consent">
        <input type="checkbox" name="consent" id="dlConsent" value="1" required>
        <span><?php
          $priv = fiet_option( 'privacidad_url', '' );
          $enlace = $priv
            ? '<a href="' . esc_url( $priv ) . '" target="_blank" rel="noopener">política de privacidad</a>'
            : 'política de privacidad';
          echo 'He leído y acepto la ' . $enlace . ' y el tratamiento de mis datos para gestionar el acceso al material.';
        ?></span>
      </label>
      <button class="btn-hero" type="submit" id="dlSubmit">Descargar</button>
      <p class="dl-msg" id="dlMsg" hidden></p>
    </form>

    <div class="dl-done" id="dlDone" hidden>
      <span class="dl-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.29 5.71 9 17l-5.29-5.29 1.42-1.42L9 14.17l9.88-9.88 1.41 1.42z"/></svg></span>
      <h3>¡Todo listo!</h3>
      <p class="dl-sub">Ya puedes descargar el material.</p>
      <a class="btn-hero" id="dlLink" href="#" target="_blank" rel="noopener">Descargar material</a>
    </div>
  </aside>
