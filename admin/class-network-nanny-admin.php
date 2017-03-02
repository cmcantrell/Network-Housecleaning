<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link		https://github.com/cmcantrell/Network-Nanny
 * @since		1.0.0
 *
 * @package		Network_Nanny
 * @subpackage	Network_Nanny/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since		1.0.0
 * @package		Network_Nanny
 * @subpackage	Network_Nanny/admin
 * @author		Clinton Cantrell <https://github.com/cmcantrell>
 */
class Network_Nanny_Admin {

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

	private $notices;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		$this->notices 		= array();
		$this->options 		= get_option('network-nanny-options');
		$this->profiles		= $this->get_profiles();

		// $script_compiler			= new Network_Nanny_Script();
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/network-nanny-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/network-nanny-admin.js', array( 'jquery' ), false, false );
		wp_localize_script( $this->plugin_name, '_networknanny', array('ajax_url'=>admin_url('admin-ajax.php')));
	}

	public function jscompile(){
		$request 		= new Network_Nanny_Script_Compiler();
		echo json_encode($request->wp_scripts);
		die();
	}

	private function get_profiles(){
		$profiles 		= false;
		global $wpdb;
		$table_name = $wpdb->prefix . "networknanny_networkprofiles";
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) :
			$profileData		= $wpdb->get_row( "SELECT * FROM ".$table_name, ARRAY_N );
			if($profileData):
				$profiles[] 			= $profileData;
			endif;
		endif;
		return $profiles;
	}

	public function saveProfile(){
		$response 				= array();
		if(!isset($_REQUEST['profile'])){
			$response[] 		= array("error"=>"error","message"=>"no profile to update.");
			echo json_encode($response);
			die();
		}
		global $wpdb;
		$table_name = $wpdb->prefix . "networknanny_networkprofiles";
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name ):
			foreach($_REQUEST['profile'] as $name=>$profile):
				$existingData = $wpdb->get_row( "SELECT id FROM ".$table_name." WHERE name='".$name."'", ARRAY_N );
				if($existingData):
					$profileId		= $existingData[0];
					$update = $wpdb->update( 
					 	$table_name, 
					 	array( 
					 		'time' => current_time( 'mysql' ), 
					 		'name' => $name, 
					 		'text' => serialize($profile), 
					 	),
					 	array( 'id' => $profileId )
					);
					if($update):
						$response[]	 		= array("error"=>"success","message"=>"profile for ".$name." succesfully updated.");
					endif;
				else:
					$dbInsert	= $wpdb->insert( 
				 		$table_name, 
						array( 
							'time' => current_time( 'mysql' ), 
							'name' => $name, 
							'text' => serialize($profile), 
						)
					);
					if($dbInsert):
						$response[] 		= array("error"=>"success","message"=>"profile for ".$name." successfully created.");
					endif;

				endif;
				Network_Nanny_Script_Compiler::write($profile);
			endforeach;
			echo json_encode($response);
		else:
			$response[] 		= array("error"=>"error","message"=>"could not resolve required database resources.");
			echo json_encode($response);
		endif;
		die();
	}
	
	/*
		*
		*
		*
		*
	**/
	public function register_menus(){
		add_submenu_page( 
			'options-general.php',
			'Network Nanny',
			'Network Nanny',
			'administrator',
			'network-nanny',
			array($this,'network_nanny_index')
		);
	}
	
	/*
		*
		*
		*
		*
	**/
	public function register_settings(){
		register_setting('network-nanny', 'network-nanny-options');
		
		add_settings_section(
			'network-nanny-js-section',							// section identifier
			__('JavaScript Network Settings', 'network-nanny-js'),			// title
			array($this,'network_nanny_js_settings_section_callback'),			// callback
			'network-nanny'										// display page
		);
		
		add_settings_field(
			'network_nanny_js_toggle', 							// field identifier
			__('Enable JavaScript cleanup?', 'network-nanny-js'),						// Title
			array($this,'network_nanny_js_toggle_callback'),	// callback function
			'network-nanny',									// page
			'network-nanny-js-section',							// section identifier
			[
				'label_for'					=> 'network_nanny_js_toggle',
				'class'             		=> 'network-nanny-row',
				'network-nanny-custom-data' => 'custom',
			]													// args
		);
		
		add_settings_field(
			'network_nanny_js_compile', 							// field identifier
			__('Begin Compiling...', 'network-nanny-js'),						// Title
			array($this,'network_nanny_js_compile_callback'),	// callback function
			'network-nanny',									// page
			'network-nanny-js-section',							// section identifier
			[
				'label_for'					=> 'network_nanny_js_compile',
				'class'             		=> 'network-nanny-row'
			]													// args
		);
		
		add_settings_field(
			'network_nanny_js_compile_ui', 							// field identifier
			__(null, 'network-nanny-js'),						// Title
			array($this,'network_nanny_js_compile_ui_callback'),	// callback function
			'network-nanny',									// page
			'network-nanny-js-section',							// section identifier
			[
				'label_for'					=> 'network_nanny_js_compile_ui',
				'class'             		=> 'network-nanny-row'
			]													// args
		);
	}

	/*
		*
		*
		*
		*
	**/	
	public function network_nanny_index(){
		if (!current_user_can('manage_options')) {
			return;
		} 
		// check database connection
		global $wpdb;
		$table_name = $wpdb->prefix . "networknanny_networkprofiles";
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) :
			array_push($this->notices, array('error'=>'error', 'message'=>'Database table \'networknanny_networkprofiles\' could not be found.  No network profiles can be saved. Deactivate and reactive the plugin, ensure you have proper database permissions or check your error logs for more information.'));
		else:
			array_push($this->notices, array('error'=>'success', 'message'=>'Database connected. You can start saving & managing profiles.'));
			$this->status 			= true;
		endif;

		// check memory settings
		$iniMemory		= (int)ini_get('memory_limit');
		$iniExecTime	= ini_get('max_execution_time');
		array_push($this->notices, array('error'=>$iniMemory >= 256 ? 'success' : 'error','message'=>"Memory Limit: " . $iniMemory . "Mb"));

		// check for profiles
		$profiles 		= $this->get_profiles();
		$jsprofile		= false;
		if($profiles):
			foreach($profiles as $profile):
				if($profile[2] === 'jscompile'){
					$jsprofile 		= true;
					?>
					<script>
						jQuery(document).ready(function(){
							if(typeof NetworkNanny !== undefined){
								
								let jscompile  		= <?php echo json_encode(unserialize($profile[3])); ?>, 
									uiEle 			= document.getElementById('network-nanny-js-compile-ui');
								NetworkNanny.prototype.updateNetworkNannyUI(NetworkNanny.prototype.buildCompileResponseHTML(jscompile),uiEle);
							}
						});
					</script>
					<?php
				}
				array_push($this->notices, array('error'=>'warning', 'message'=>'You have a saved ' . $profile[2] . ' profile'));
			endforeach;
		endif;
		?>

		<?php
		if(count($this->notices)>0) :
			foreach($this->notices as $notice) :
		?>
		<div class="notice notice-<?php _e($notice['error']); ?> is-dismissible">
			<p><?php _e( $notice['message']); ?></p>
		</div>
		<?php
			endforeach;
		endif;
		?>

		<div class="wrap network-nanny-ui">
			<h1><?= esc_html(get_admin_page_title()); ?></h1>
			<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "network-nanny"
			settings_fields('network-nanny');
			// output setting sections and their fields
			// (sections are registered for "network-nanny", each field is registered to a specific section)
			do_settings_sections('network-nanny');
			// output save settings button
			submit_button('Save Settings');
			?>
			</form>
    	</div>
    	
		<?php
	}

	/*
		*
		*
		*
		*
	**/
	public function network_nanny_js_settings_section_callback($args){
		return;
	}
	
	/*
		*
		*
		*
		*
	**/
	public function network_nanny_js_toggle_callback($args){
		$options = $this->options;
		
		// output the field
		?>
			<input 
				type="checkbox" 
				name="network-nanny-options[<?= esc_attr($args['label_for']); ?>]" 
				id="network-nanny-options[<?= esc_attr($args['label_for']); ?>]" 
				class="network-nanny-options[<?= esc_attr($args['label_for']); ?>]" 
				data-custom="<?= esc_attr($args['network-nanny-custom-data']); ?>" 
				value="1"
				<?php echo isset($options[$args['label_for']]) && (int)$options[$args['label_for']] === 1 ? 'checked="true"' : ''; ?>  
			/>
		<?php	
	}
	
	/*
		*
		*
		*
		*
	**/
	public function network_nanny_js_compile_callback($args){
		?>
			<button data-action="networkNannyCompileGetAutoProfile" data-wpajax_action="jscompile" class="button button-secondary">Compile</button>
		<?php	
	}
	
	/*
		*
		*
		*
		*
	**/
	public function network_nanny_js_compile_ui_callback($args){
		?>
			<div id="network-nanny-js-compile-ui" class="network-nanny-ui">
				
			</div>
		<?php	
	}
	
}
