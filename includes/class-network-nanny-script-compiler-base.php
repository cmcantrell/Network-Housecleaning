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
class Network_Nanny_Script_Compiler_Base{
	
	public $helper;

	public $wp_scripts;

	protected function init(){
		$this->helper			= new Network_Nanny_Script_Compiler_Helper();
		$this->scripts 			= $this->get_scripts();
	}

	private function get_scripts($extension = 'js'){
		$wp_scripts;
		switch($extension){
			case 'js' : 
				
				echo "<pre>";
				echo "jump, jump, jump";
				print_r(wp_scripts()->queue);
				echo "</pre>";

				// foreach(wp_scripts()->registered as $script){
				// 	if(in_array($script->handle,wp_scripts()->queue)):
				// 		mail()
				// 	endif;
				// }

				$wp_scripts		= wp_scripts();
				break;
			default :
				return false;
		}
		$this->wp_scripts		= $wp_scripts->registered;
		unset($wp_scripts);
	}

	/*
		*
		*	@description 		sorts scripts property by full depth dependencies
		*
		*
		*
	**/
	protected function negotiate_dependencies(){

		set_time_limit(0);
		$dependencies	= array();
		foreach( $this->wp_scripts as $index=>$script ) :
			$handle									= $script->handle;
			$dependencies[$index] 					= isset($dependencies[$index]) ? $dependencies[$index] : array(
				'handle' => $handle, 
				'dependencies' => array()
			);
			// add handle and dependencies to locally scoped dependencies array
			foreach($script->deps as $i=>$dependency){
				$dependencies[$index]['dependencies'][] = $dependency;
			}
		endforeach;

		foreach( $dependencies as $index=>$data ){
			$fullDependencies		= array();
			foreach($dependencies as $i=>$dependency){
			 	$fullDependencies[]		= $this->helper->getWithDependencies($dependency['handle'], $dependencies);
			}
		}
		unset($dependencies);
		$_fullDependencies = $fullDependencies;
		foreach( $fullDependencies as $index=>$data ){
			$this->helper->sortDependency($data,$_fullDependencies);
		}
		$fullDependencies = $_fullDependencies;
		$_fullDependencies			= array();

		// make sure jquery is first and foremost
		$jquery 		= false;
		$jqueryCore		= false;
		$keys			= array('jqueryCore','jqueryMigrate','jquery');
		foreach( $fullDependencies as $i=>$dependency ){
			$handle = $this->helper->normalizeHandle($dependency['handle']);
			if(in_array($handle, $keys)){
				$$handle = $fullDependencies[$i];
			}
		}
		foreach($keys as $k=>$key){
			foreach( $fullDependencies as $i=>$dependency ){
				if( $this->helper->normalizeHandle($dependency['handle']) === $key ){
					$this->helper->moveArrayElement($fullDependencies, $i, $k);
				}
			}
		}
		foreach( $fullDependencies as $k=>$dependency ){
			foreach($this->wp_scripts as $i=>$script){
				if($dependency['handle'] === $script->handle){
					$_fullDependencies[] = $script;
					break;
				}
			}
			
		}
		unset($fullDependencies);
		$this->wp_scripts		= $_fullDependencies;
		unset($_fullDependencies);
		return true;
	}

	protected function getFileHandle(){
		$handle 				= $this->helper->getFileHandle();
	}
	
}

?>