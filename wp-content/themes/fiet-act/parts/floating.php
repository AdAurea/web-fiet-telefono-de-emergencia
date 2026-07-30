<?php if ( ! defined( 'ABSPATH' ) ) exit;
$tel   = fiet_option( 'telefono_tel', '900759759' );
$email = fiet_option( 'email_contacto', 'informacion@fiet.ong' );
?>
<!-- Botones flotantes persistentes -->
<a class="fab-quiz js-open-quiz" href="#cuestionario">Ir al cuestionario</a>
<div class="fab-stack">
	<a class="fab-sec fab-mail" href="mailto:<?php echo esc_attr( $email ); ?>?subject=Contacto%20-%20Tel%C3%A9fono%20contra%20la%20Trata" aria-label="Enviar un correo">
		<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
	</a>
	<button class="fab-sec" id="fabForm" type="button" aria-label="Abrir formulario de contacto">
		<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
	</button>
	<a class="fab-call" href="tel:<?php echo esc_attr( $tel ); ?>" aria-label="Llamar al teléfono de ayuda">
		<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.62 10.79a15.53 15.53 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24 11.36 11.36 0 0 0 3.57.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.36 11.36 0 0 0 .57 3.57 1 1 0 0 1-.24 1.02l-2.21 2.2z"/></svg>
	</a>
	<button class="fab-toggle" id="fabToggle" type="button" aria-label="Mostrar acciones de contacto" aria-expanded="false">
		<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 12.22C21 6.73 16.74 3 12 3c-4.69 0-9 3.65-9 9.28-.6.34-1 .98-1 1.72v2c0 1.1.9 2 2 2h1v-6.1c0-3.87 3.13-7 7-7s7 3.13 7 7V19h-8v2h8c1.1 0 2-.9 2-2v-1.22c.59-.31 1-.92 1-1.64v-2.3c0-.7-.41-1.31-1-1.62z"/><circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/><path d="M18 11.03A6.04 6.04 0 0 0 12.05 6c-3.03 0-6.29 2.51-6.03 6.45a8.075 8.075 0 0 0 4.86-5.89A8.115 8.115 0 0 0 18 11.68z"/></svg>
	</button>
</div>

<!-- Cuestionario de autoevaluación (panel lateral) -->
<div class="drawer-backdrop" id="quizBackdrop" hidden></div>
<aside class="drawer" id="quizDrawer" aria-hidden="true" aria-label="Cuestionario de autoevaluación">
	<header class="drawer-top">
		<span class="tag"><span class="dot"></span>Autoevaluación confidencial</span>
		<button class="drawer-x" id="quizClose" type="button" aria-label="Cerrar cuestionario">&times;</button>
	</header>
	<h2 class="drawer-title">Evaluación del riesgo</h2>
	<p class="drawer-sub">Marca lo que corresponda a tu situación. El resultado es orientativo y confidencial; no sustituye el asesoramiento profesional.</p>
	<div class="quiz-list" id="quizList"></div>
	<button class="btn-result" id="quizResultBtn" type="button">Ver resultado →</button>
	<div class="quiz-result" id="quizResult" hidden></div>
</aside>
