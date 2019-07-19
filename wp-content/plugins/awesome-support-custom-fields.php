<?php
/**
 * @package   Awesome Support/Custom Fields
 * @author    ThemeAvenue <miguel@heartwp.pe>
 * @license   GPL-2.0+
 * @link      http://themeavenue.net
 * @copyright 2014 ThemeAvenue
 *
 * @wordpress-plugin
 * Plugin Name:       Awesome Support: My Custom Fields
 * Plugin URI:        http://www.heartwp.pe
 * Description:       Adds custom fields to the Awesome Support ticket submission form.
 * Version:           0.1.0
 * Author:            Miguel Fuentes
 * Author URI:        http://www.heartwp.pe
 * Text Domain:       wpas
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// CUSTOM FIELD - USUARIO

add_action( 'plugins_loaded', 'wpas_user_custom_fields_1' );

function wpas_user_custom_fields_1() {
	wpas_add_custom_field( 'field_user_ca', 
		array(
			'title' => 'Usuario',
			'field_type' => 'text',
			'label' => 'Usuario Casa Apuesta (donde esta registrado)',
		)
	);
}

// CUSTOM FIELD - CASA-DE-APUESTA

add_action( 'plugins_loaded', 'wpas_user_custom_fields' );

function wpas_user_custom_fields() {
	if ( function_exists( 'wpas_add_custom_field' ) ) {
		wpas_add_custom_taxonomy( 'field_c_a', 
			array(
				'title' => 'Casa de apuesta', 
				'label' => 'Casa de apuesta', 
			));
	}
}

/**/

//add_action( 'plugins_loaded', 'wpas_user_custom_fields_select' );

function wpas_user_custom_fields_select() {
	if ( function_exists( 'wpas_add_custom_field' ) ) {
		wpas_add_custom_field( 'field_c_select', 
			array(
				'title' => 'Seleccionar casa de apuesta',
				//'field_type' => 'text',
				'label' => 'Casa de apuesta', 
			)
		);
	}

	/*wpas_add_custom_field( 'field_select_custom', 
		array(
			'title' => 'My Custom Services',
			'field_type' => 'select',
			'label' => 'Seleccionar casa de apuesta',
			'options' => array( 
				'option1' => 'Betson Perú', 
				'option2' => 'Second Option'
			)
		)
	);*/
}

 