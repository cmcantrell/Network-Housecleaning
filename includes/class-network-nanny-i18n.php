<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link		https://github.com/cmcantrell/Network-Nanny
 * @since		1.0.0
 *
 * @package		Network_Nanny
 * @subpackage	Network_Nanny/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since		1.0.0
 * @package		Network_Nanny
 * @subpackage	Network_Nanny/includes
 * @author		Clinton Cantrell <https://github.com/cmcantrell>
 */
class Network_Nanny_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'network-nanny',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
