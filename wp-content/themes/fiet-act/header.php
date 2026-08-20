<?php if ( ! defined( 'ABSPATH' ) ) exit; ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
	<meta name="format-detection" content="telephone=no"><!-- evita que Safari iOS auto-enlace los números (azul/subrayado); los enlaces tel: propios siguen funcionando -->
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
