<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="report-inner">
	<div class="report-left">
		<span class="tag">Informar una sospecha</span>
		<h2>Podemos ayudarte.</h2>
		<p>Eres víctima o sospechas de una potencial situación de trata. Describe la situación con el mayor detalle posible, incluyendo fechas, horas, ubicación exacta (país, ciudad, dirección, código postal y referencias), descripción de las personas implicadas y, si procede, matrículas u otros datos identificativos.</p>
		<p>Si la situación ocurre en el ámbito digital, facilita el enlace de la publicación, los nombres de las cuentas implicadas y una breve descripción de lo sucedido. Todas las comunicaciones son confidenciales y puedes permanecer en el anonimato.</p>
	</div>
	<div class="report-right">
		<form id="reportForm" novalidate>
			<span class="tag">Detalles de la descripción</span>
			<textarea name="descripcion" rows="5" placeholder="Describe la situación con el mayor detalle posible..."></textarea>
			<p class="report-note">Si consientes que un miembro de nuestro equipo pueda ponerse en contacto contigo, facilita alguno de los siguientes datos. Todos son opcionales.</p>
			<div class="report-grid">
				<label>Nombre<input type="text" name="nombre" autocomplete="name"></label>
				<label>Número de teléfono<input type="tel" name="telefono" autocomplete="tel"></label>
				<label>Correo<input type="email" name="correo" autocomplete="email"></label>
				<label>Redes sociales<input type="text" name="redes"></label>
			</div>
			<label class="check"><input type="checkbox" name="privacidad"> Acepto la Política de Privacidad.</label>
			<label class="check"><input type="checkbox" name="comunicaciones"> Acepto recibir comunicaciones informativas de FIET.</label>
			<div class="report-submit">
				<button type="submit" class="btn-hero btn-dark">Enviar</button>
			</div>
		</form>
	</div>
</div>
