<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link		https://github.com/cmcantrell/Network-Nanny
 * @since		1.0.0
 *
 * @package		Network_Nanny
 * @subpackage	Network_Nanny/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package		Network_Nanny
 * @subpackage	Network_Nanny/public
 * @author		Clinton Cantrell <https://github.com/cmcantrell>
 */
class Network_Nanny_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/network-nanny-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/network-nanny-public.js', array( 'jquery' ), $this->version, false );

	}

	public function web_pack_init(){

		global $wpdb;
		global $wp_scripts;
		$scripts_stash_handle 	= array();
		$scripts_stash 			= array();
		$scripts_stash_final 	= array();
		$table_name 			= $wpdb->prefix . "networknanny_networkprofiles";
		$profile_name 			= 'jscompile';

		// pull saved profile
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") === $table_name ):
			$existing_data = $wpdb->get_row( "SELECT text FROM ".$table_name." WHERE name='".$profile_name."'", ARRAY_N );
			if($existing_data[0]):
				$profile 			= unserialize($existing_data[0]);
			else :
				echo "<script>console.log('Network Nanny could not find a profile. Log in and save one to enable compiling.');</script>";
				return;
			endif;
		else :
			echo "<script>console.log('Network Nanny could not find a profile. Log in and save one to enable compiling.');</script>";
			return;
		endif;
		
		// get current dependencies
		foreach($wp_scripts->registered as $script){
			if(in_array($script->handle, $wp_scripts->queue)){
				$in_footer 			= false;
				if(in_array($script->handle, $wp_scripts->in_footer)){
					$in_footer 			= true;
				}
				if(count($script->deps) > 0){
					foreach ($script->deps as $dep) {
						if(!in_array($dep, $scripts_stash_handle)){
							$scripts_stash_handle[]			= $dep;
						}
					}
				}
				$script->in_footer 		= $in_footer;
				// save handle in array to make searching easier
				$scripts_stash_handle[] = $script->handle;
				$scripts_stash[] 		= $script;
			}
		}

		foreach($profile as $sorted_script){
			$handle 			= $sorted_script['handle'];
			if(in_array($handle, $scripts_stash_handle) && $sorted_script['src'] != 'false'){
				
				foreach($scripts_stash as $s){
					if($s->handle === $handle){
						array_push($scripts_stash_final, $s);
					}
				}

				if (($key = array_search($handle, $scripts_stash_handle)) !== false) {
					unset($scripts_stash_handle[$key]);
				}
			}
		}

		foreach($scripts_stash as $script){
			if(in_array($script->handle, $scripts_stash_handle) && count($script->deps)===0 && $script->src !==''){
				array_push($scripts_stash_final, $script);
				if (($key = array_search($script->handle, $scripts_stash_handle)) !== false) {
					unset($scripts_stash_handle[$key]);
				}
			}
		}

		$baseUrl 		= isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http' . ':\/\/' . $_SERVER['SERVER_NAME'];
		$reqs 			= array();
		$plugin_dir 	= ABSPATH . 'wp-content/plugins/Network-Nanny/';
		$appJs 			= $plugin_dir.'public/js/app.js';
		$script_data 	= array();
		
		foreach($scripts_stash_final as $script){
			$handle 			= $script->handle;
			$src 				= $script->src;

			if(isset($script->extra)){
				if(isset($script->extra['data']) && count($script->extra['data']) > 0){
					$script_data[] 		= $script->extra['data'];
				}
			}

			wp_dequeue_script($handle);
			if(preg_match("/^(".$baseUrl.").*/i", $src)){
				$src = preg_replace("/^(".$baseUrl.")/i", '', $src);
			};
			array_push($reqs, $src);
		}

		if(count($script_data) > 0){
			if($resource0 = fopen($plugin_dir.'public/js/data.js', 'w')){
				$string 			= "";
				foreach($script_data as $d){
					$string 		.= $d;
				}
				fwrite($resource0, $string);
				if(fclose($resource0)){
					array_unshift($reqs, '/wp-content/plugins/Network-Nanny/public/js/data.js');
				}
			}				
		}
		
		if($resource = fopen($appJs, 'w')){
			$string 			= "/*
*
* do not modify this file.  content is auto generated.
* 
*
* 
* end comment */";
			foreach($reqs as $req){
				$string.="require('../../../../..".$req."');";
			}

			if(fwrite($resource,$string)){
				if(fclose($resource)){
					exec('echo $PATH', $path);
					$path 				= $path[0];
					$cmd 				= 'export PATH=/usr/local/bin:'.$path.'; '.$plugin_dir.'node_modules/.bin/webpack --config '.$plugin_dir.'webpack.config.js';
					exec($cmd, $ret);
					$log_data 		= date('Y-m-d H:i:s ') . PHP_EOL;
					$log_data 		.= $cmd . PHP_EOL;
					$log_data 		.= print_r($ret, true);
					$this->writeLog($log_data);

					wp_enqueue_script( 'webpack-bundle', plugin_dir_url( __FILE__ ).'/js/dist/bundle.js', array(), false, false );
				}else{
					return false;
				}
			}else{
				$this->writeLog('could not write Network-Nanny/public/js/app.js');
				return false;
			}
		}else{
			$this->writeLog('could not open Network-Nanny/public/js/app.js');
			return false;
		}
	}
	
	private function writeLog($string=false){
		if(!$string){
			return false;
		}
		$plugin_dir 	= ABSPATH . 'wp-content/plugins/Network-Nanny/';
		if($errorLog = fopen($plugin_dir . 'logs/runtime.log', 'a')){
			if(filesize($plugin_dir . 'logs/runtime.log') > 1000000000){
				ftruncate($errorLog, 0);
				fwrite($errorLog, date('Y-m-d H:i:s ') . PHP_EOL . 'File truncated' . PHP_EOL);
			}
			$log_data 		= date('Y-m-d H:i:s ') . PHP_EOL;
			$log_data		.= $string . PHP_EOL;
			fwrite($errorLog, $log_data);
			fclose($errorLog);
		}
		return true;
	}

}
