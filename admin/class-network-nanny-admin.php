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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = get_option('network-nanny-options');
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
		echo "lemme smash";
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
		
		add_settings_section(
			'network-nanny-css-section',							// section identifier
			__('CSS Network Settings', 'network-nanny-css'),			// title
			array($this,'network_nanny_css_settings_section_callback'),			// callback
			'network-nanny'										// display page
		);
		
		add_settings_field(
			'network_nanny_css_toggle', 							// field identifier
			__('Enable CSS cleanup?', 'network-nanny-css'),						// Title
			array($this,'network_nanny_css_toggle_callback'),	// callback function
			'network-nanny',									// page
			'network-nanny-css-section',							// section identifier
			[
				'label_for'					=> 'network_nanny_css_toggle',
				'class'             		=> 'network-nanny-row',
				'network-nanny-custom-data' => 'custom',
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
	
	public function network_nanny_css_settings_section_callback($args){
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
	
	public function network_nanny_css_toggle_callback($args){
		$options = $this->options;
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
			<button data-action="networkNannyCompile" class="button button-secondary">Compile</button>
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
			<div id="network-nanny-js-compile-ui" class="network-nanny-ui" data-action="updateNetworkNannyUI">
				
			</div>
		<?php	
	}
	
}
