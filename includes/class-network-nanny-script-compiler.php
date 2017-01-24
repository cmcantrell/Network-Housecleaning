<?php

/**
 * Fired during plugin activation
 *
 * @link              https://github.com/cmcantrell/Network-Nanny
 * @since      1.0.0
 *
 * @package    Network_Nanny
 * @subpackage Network_Nanny/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Network_Nanny
 * @subpackage Network_Nanny/includes
 * @author     Clinton Cantrell <https://github.com/cmcantrell>
 */
class Network_Nanny_Script_Compiler extends Network_Nanny_Script_Compiler_Base{
	
	public function __construct(){
		ini_set('max_execution_time',0);
		$this->init();
		$this->negotiate_dependencies();
		return $this;
	}
	
}

?>