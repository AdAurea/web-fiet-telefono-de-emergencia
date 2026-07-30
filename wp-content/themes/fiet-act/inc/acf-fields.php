<?php
/**
 * Campos editables por página (ACF, grupos locales del tema).
 * La ubicación se resuelve por slug de página en tiempo de ejecución.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/** Atajo para definir un campo de texto/área */
function fiet_f( $key, $label, $name, $default = '', $type = 'text' ) {
	$f = array( 'key' => $key, 'label' => $label, 'name' => $name, 'type' => $type, 'default_value' => $default );
	if ( $type === 'textarea' ) { $f['rows'] = 3; $f['new_lines'] = ''; }
	return $f;
}

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

	/* ===================== PREVENCIÓN ===================== */
	$prev = get_page_by_path( 'prevencion' );
	if ( $prev ) {
		acf_add_local_field_group( array(
			'key'    => 'group_fiet_prevencion',
			'title'  => 'Prevención · Textos',
			'fields' => array(
				fiet_f( 'f_prev_eye', 'Portada · Sobretítulo', 'prev_eyebrow', 'Prevención' ),
				fiet_f( 'f_prev_tit', 'Portada · Título', 'prev_titulo', '¿Cómo mantenerse a salvo?' ),
				fiet_f( 'f_prev_par', 'Portada · Párrafo', 'prev_parrafo', 'La trata puede comenzar en situaciones cotidianas como la búsqueda de empleo, un viaje o el uso de internet. Conocer los riesgos y saber identificarlos es clave para protegerte.', 'textarea' ),
				fiet_f( 'f_prev_btn', 'Portada · Botón', 'prev_boton', 'Ver recomendaciones' ),
				fiet_f( 'f_prev_peye', 'Popup · Sobretítulo', 'prev_pop_eyebrow', 'Prevención' ),
				fiet_f( 'f_prev_ptit', 'Popup · Título', 'prev_pop_titulo', 'Recomendaciones para mantenerte seguro' ),
				fiet_f( 'f_prev_ppar', 'Popup · Párrafo', 'prev_pop_parrafo', 'La trata puede empezar en un empleo, un viaje o en internet. Estas son las claves para reducir riesgos e identificar señales de alerta en cada situación.', 'textarea' ),
				fiet_f( 'f_prev_c1t', 'Card 1 · Título', 'prev_card1_titulo', 'Empleo Seguro' ),
				fiet_f( 'f_prev_c1d', 'Card 1 · Descripción', 'prev_card1_desc', 'Verifica la oferta y a quien contrata, nunca entregues tus documentos y comparte con alguien de confianza dónde y con quién vas a trabajar.', 'textarea' ),
				fiet_f( 'f_prev_c2t', 'Card 2 · Título', 'prev_card2_titulo', 'Viaje Seguro' ),
				fiet_f( 'f_prev_c2d', 'Card 2 · Descripción', 'prev_card2_desc', 'Lleva copias de tus documentos, comparte tu itinerario y ten a mano los contactos de tu embajada y de organizaciones de ayuda.', 'textarea' ),
				fiet_f( 'f_prev_c3t', 'Card 3 · Título', 'prev_card3_titulo', 'Internet Seguro' ),
				fiet_f( 'f_prev_c3d', 'Card 3 · Descripción', 'prev_card3_desc', 'Protege tus datos personales, desconfía de perfiles desconocidos y extrema la precaución si conciertas una cita con alguien conocido por internet.', 'textarea' ),
				fiet_f( 'f_prev_cbtn', 'Cards · Texto del botón', 'prev_card_boton', 'Saber más' ),
			),
			'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => $prev->ID ) ) ),
		) );
	}
} );
