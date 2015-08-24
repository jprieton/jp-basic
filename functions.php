<?php

// Disable File Editing
if ( !defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

include_once __DIR__ . '/includes/register-script.php';
//include_once __DIR__ . '/includes/register-script-local.php';
include_once __DIR__ . '/includes/register-style.php';
//include_once __DIR__ . '/includes/register-style-local.php';
include_once __DIR__ . '/includes/register-sidebar.php';

if ( !defined( 'THEME_URI' ) ) {
	define( 'THEME_URI', get_template_directory_uri() );
}

add_action( 'wp_enqueue_scripts', function () {

	/* Styles */
	wp_enqueue_style( 'animate' );
	wp_enqueue_style( 'hover' );

	/* Scripts */
	wp_enqueue_script( 'modernizr' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'bootstrap' );
	wp_enqueue_script( 'jquery-form' );

	// Bootstrap Alerts
	wp_register_script( 'bootstrap-alerts', apply_filters( 'js_cdn_uri', THEME_URI . '/js/bootstrap-alerts.min.js', 'bootstrap-alerts' ), array( 'jquery', 'bootstrap' ), NULL, TRUE );
	wp_enqueue_script( 'bootstrap-alerts' );

	/**
	 * TODO: Importar y ajustar las actualizaciones
	 */
	// Bootstrap
	wp_register_style( 'bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css', array(), '3.3.4' );
	wp_enqueue_style( 'bootstrap' );

	// Bootstrap Theme
	wp_register_style( 'bootstrap-theme', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css', array( 'bootstrap' ), '3.3.4' );
	wp_enqueue_style( 'bootstrap-theme' );

	// Font Awesome
	wp_register_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array( 'bootstrap' ), '4.3.0' );
	wp_enqueue_style( 'font-awesome' );

	// Add defer atribute
	do_action( 'defer_script', array( 'jquery-form', 'bootstrap-alerts' ) );

	// Bootstrap complemetary text align
	wp_register_style( 'bs-text-align', THEME_URI . '/css/bootstrap-text-align.min.css', array( 'bootstrap' ), '1.0' );
	wp_enqueue_style( 'bs-text-align' );

	// Wordpress Core
	wp_register_style( 'wordpress-core', THEME_URI . '/css/wordpress-core.min.css', array( 'bootstrap', 'bs-text-align' ), '1.0' );
	wp_enqueue_style( 'wordpress-core' );

	// Theme
	wp_register_style( 'main-theme', THEME_URI . '/style.css', array(), '1.0' );
	wp_enqueue_style( 'main-theme' );

	if ( is_child_theme() ) {
		// Theme
		wp_register_style( 'theme', get_stylesheet_uri(), array( 'animate' ), '1.0' );
		wp_enqueue_style( 'theme' );
	}
} );

include_once __DIR__ . '/includes/theme-features.php';

/**
 * Encoded Mailto Link
 *
 * Create a spam-protected mailto link written in Javascript
 *
 * @param	string	the email address
 * @param	string	the link title
 * @param	mixed	any attributes
 * @return	string
 */
function safe_mailto( $email, $title = '', $attributes = '' ) {
	$title = (string) $title;

	if ( $title === '' ) {
		$title = $email;
	}

	$x = str_split( '<a href="mailto:', 1 );

	for ( $i = 0, $l = strlen( $email ); $i < $l; $i++ ) {
		$x[] = '|' . ord( $email[$i] );
	}

	$x[] = '"';

	if ( $attributes !== '' ) {
		if ( is_array( $attributes ) ) {
			foreach ( $attributes as $key => $val ) {
				$x[] = ' ' . $key . '="';
				for ( $i = 0, $l = strlen( $val ); $i < $l; $i++ ) {
					$x[] = '|' . ord( $val[$i] );
				}
				$x[] = '"';
			}
		} else {
			for ( $i = 0, $l = strlen( $attributes ); $i < $l; $i++ ) {
				$x[] = $attributes[$i];
			}
		}
	}

	$x[] = '>';

	$temp = array();
	for ( $i = 0, $l = strlen( $title ); $i < $l; $i++ ) {
		$ordinal = ord( $title[$i] );

		if ( $ordinal < 128 ) {
			$x[] = '|' . $ordinal;
		} else {
			if ( count( $temp ) === 0 ) {
				$count = ($ordinal < 224) ? 2 : 3;
			}

			$temp[] = $ordinal;
			if ( count( $temp ) === $count ) {
				$number = ($count === 3) ? (($temp[0] % 16) * 4096) + (($temp[1] % 64) * 64) + ($temp[2] % 64) : (($temp[0] % 32) * 64) + ($temp[1] % 64);
				$x[] = '|' . $number;
				$count = 1;
				$temp = array();
			}
		}
	}

	$x[] = '<';
	$x[] = '/';
	$x[] = 'a';
	$x[] = '>';

	$x = array_reverse( $x );

	$output = "<script type=\"text/javascript\">\n"
					. "\t//<![CDATA[\n"
					. "\tvar l=new Array();\n";

	for ( $i = 0, $c = count( $x ); $i < $c; $i++ ) {
		$output .= "\tl[" . $i . "] = '" . $x[$i] . "';\n";
	}

	$output .= "\n\tfor (var i = l.length-1; i >= 0; i=i-1) {\n"
					. "\t\tif (l[i].substring(0, 1) === '|') document.write(\"&#\"+unescape(l[i].substring(1))+\";\");\n"
					. "\t\telse document.write(unescape(l[i]));\n"
					. "\t}\n"
					. "\t//]]>\n"
					. '</script>';

	return $output;
}
