<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://http://localhost/restaurant
 * @since      1.0.0
 *
 * @package    Wp_Menu_Craft
 * @subpackage Wp_Menu_Craft/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Menu_Craft
 * @subpackage Wp_Menu_Craft/includes
 * @author     Techy Trion <testingemailer1212@gmail.com>
 */
class Wp_Menu_Craft_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-menu-craft',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
