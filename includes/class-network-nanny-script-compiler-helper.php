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
class Network_Nanny_Script_Compiler_Helper{

	public function getDependencies($dependency, $collection){
		foreach( $collection as $index=>$data ){
			if($dependency === $data['handle']){
				return $data['dependencies'];
			}
		}
		return false;
	}

	public function moveArrayElement(&$arr, $a, $b){
		$out			= array_splice($arr, $a, 1);
		array_splice($arr, $b, 0, $out);
	}

	public function normalizeHandle($handle){
		$parts;
		$_handle 		= trim(strtolower($handle));
		preg_match('/^([a-z0-9]+)+((?:\-+)|(?:\s+))*([a-z0-9]+)*$/', $_handle, $parts);
		if(count($parts) > 1):
			$handle = $parts[1];
			$parts = array_slice($parts, 2 );
			foreach($parts as $part){
				if($part !== '-' && $part !== ' ' ){
					$handle = $handle . ucfirst($part);
				}
			}
		endif;
		return $handle;
	}
	
	public function getWithDependencies($dependency, $collection){
		$set = array(
			'handle'		=> $dependency,
			'dependencies'	=> array()
		);
		// isolate current instance and break loop
		$inst;
		foreach($collection as $index=>$data) :
			if( $data['handle'] === $set['handle'] ) :
				$inst 				= $data;
				break;
			endif;
		endforeach;
		$deps 				= $this->getDependencies($inst['handle'], $collection);
		foreach($deps as $i=>$dep):
		 	$set['dependencies'][$i]		= array(
		 		'handle'		=> $dep,
		 		'dependencies' 	=> array()
		 	);
		 	$subDeps		= $this->getDependencies($dep, $collection);
		 	if(gettype($subDeps) === 'array' && count($subDeps) > 0 ) :
		 		foreach($subDeps as $_si=>$_sh) :
		 			foreach($this->getWithDependencies($_sh,$collection) as $k=>$l) :
		 				// array index($k)
		 				// file handle($l)
		 				if(gettype($l) === 'string') :
		 					$set['dependencies'][$i]['dependencies'][]	= array(
		 						'handle'		=> $l,
		 						'dependencies'	=> array()
		 					);
		 				endif;
		 		 	endforeach;
		 		endforeach;
		 	endif;
		endforeach;
		unset($deps);
		return $set;
	}

	function sortDependency($data,&$collection){
		foreach($collection as $index=>$inst){
			if( $inst['handle'] === $data['handle'] && count($data['dependencies'])>0){
				// iterate over each dependency
				foreach( $inst['dependencies'] as $i=>$dependency ){
					// iterate over the collection to find dependencies position for comparison against dependent file.
					foreach( $collection as $k=>$kdep ){
						if($kdep['handle'] === $dependency['handle'] && $index < $k){
							$this->moveArrayElement($collection, $k, $index);
							// call recursively to search full depth
							if( count($dependency['dependencies']) > 0 ){
								$this->sortDependency($dependency,$collection);
							}
						}
					}
				}
			}
		}
	}
}

?>