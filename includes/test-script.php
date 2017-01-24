<?php
/*
	*
	*
	*
	*
	*
	*
**/
class Network_Nanny_Script extends Network_Nanny_Script_Base{
	
	public function __construct(){
		ini_set('max_execution_time',0);
		$this->init();
		$this->negotiate_dependencies();

		return $this;
	}
}

class Network_Nanny_Script_Base{
	
	public $wp_scripts;

	protected function init(){
		$this->scripts 				= $this->get_scripts();
	}

	private function get_scripts($extension = 'js'){
		$wp_scripts;
		switch($extension){
			case 'js' : 
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
		/*
			*
			*	@description 		return nested array with all dependencies & sub dependencies
			*
		**/
		function getDependencies($dependency, $collection){
			foreach( $collection as $index=>$data ){
				if($dependency === $data['handle']){
					return $data['dependencies'];
				}
			}
			return false;
		} // end local function getDependencies()

		function moveArrayElement(&$arr, $a, $b){
			$out			= array_splice($arr, $a, 1);
			array_splice($arr, $b, 0, $out);
		} // end local function moveArrayElement()

		function normalizeHandle($handle){
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

		function getWithDependencies($dependency, $collection){
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

			$deps 				= getDependencies($inst['handle'], $collection);
			foreach($deps as $i=>$dep):
			 	$set['dependencies'][$i]		= array(
			 		'handle'		=> $dep,
			 		'dependencies' 	=> array()
			 	);
			 	$subDeps		= getDependencies($dep, $collection);
			 	if(gettype($subDeps) === 'array' && count($subDeps) > 0 ) :
			 		foreach($subDeps as $_si=>$_sh) :
			 			foreach(getWithDependencies($_sh,$collection) as $k=>$l) :
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
		} // end local function getWithDependencies()

		/*
			*
			*	@description 		recursively sorts dependencies to depth returned by getWithDependencies()
			*
		**/
		function sortDependency($data,&$collection){
			foreach($collection as $index=>$inst){
				if( $inst['handle'] === $data['handle'] && count($data['dependencies'])>0){
					// iterate over each dependency
					foreach( $inst['dependencies'] as $i=>$dependency ){
						// iterate over the collection to find dependencies position for comparison against dependent file.
						foreach( $collection as $k=>$kdep ){
							if($kdep['handle'] === $dependency['handle'] && $index < $k){
								moveArrayElement($collection, $k, $index);
								// call recursively to search full depth
								if( count($dependency['dependencies']) > 0 ){
									sortDependency($dependency,$collection);
								}
							}
						}
					}
				}
			}
		} // end local function sortDependency();

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
			 	$fullDependencies[]		= getWithDependencies($dependency['handle'], $dependencies);
			}
		}
		unset($dependencies);
		$_fullDependencies = $fullDependencies;
		foreach( $fullDependencies as $index=>$data ){
			sortDependency($data,$_fullDependencies);
		}
		$fullDependencies = $_fullDependencies;
		$_fullDependencies			= array();

		// make sure jquery is first and foremost
		$jquery 		= false;
		$jqueryCore		= false;
		$keys			= array('jqueryCore','jqueryMigrate','jquery');
		foreach( $fullDependencies as $i=>$dependency ){
			$handle = normalizeHandle($dependency['handle']);
			if(in_array($handle, $keys)){
				$$handle = $fullDependencies[$i];
			}
		}
		foreach($keys as $k=>$key){
			foreach( $fullDependencies as $i=>$dependency ){
				if( normalizeHandle($dependency['handle']) === $key ){
					moveArrayElement($fullDependencies, $i, $k);
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
	} // end negotiate_dependencies()
}

$compile			= new Network_Nanny_Script();

?>