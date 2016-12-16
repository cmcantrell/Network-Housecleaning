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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/network-nanny-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	public function register_menus(){
	
		add_submenu_page( 
			'options-general.php',
			'Network Nanny',
			'Network Nanny',
			'administrator',
			'network-nanny',
			array($this,'wporg_options_page_html')
		);
	}
	
	public function wporg_options_page_html(){
		if (!current_user_can('manage_options')) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?= esc_html(get_admin_page_title()); ?></h1>
			<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "wporg"
			settings_fields('wporg');
			// output setting sections and their fields
			// (sections are registered for "wporg", each field is registered to a specific section)
			do_settings_sections('wporg');
			// output save settings button
			submit_button('Save Settings');
			?>
			</form>
    	</div>
		<?php
	}
	
	public function register_settings(){
		register_setting('wporg', 'wporg_options');
		
		add_settings_section(
			'wporg_section_developers',
			__('The Matrix has you.', 'wporg'),
			array($this,'wporg_section_developers_cb'),
			'wporg'
		);
		
		add_settings_field(
			'wporg_field_pill', // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			__('Pill', 'wporg'),
			array($this,'wporg_field_pill_cb'),
			'wporg',
			'wporg_section_developers',
			[
				'label_for'         => 'wporg_field_pill',
				'class'             => 'wporg_row',
				'wporg_custom_data' => 'custom',
			]
		);
	}
	
	function wporg_section_developers_cb($args){
		?>
		<p id="<?= esc_attr($args['id']); ?>"><?= esc_html__('Follow the white rabbit.', 'wporg'); ?></p>
		<?php
	}
	
	function wporg_field_pill_cb($args){
		$options = get_option('wporg_options');
		// output the field
		?>
		<select id="<?= esc_attr($args['label_for']); ?>" data-custom="<?= esc_attr($args['wporg_custom_data']); ?>" name="wporg_options[<?= esc_attr($args['label_for']); ?>]">
			<option value="red" <?= isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'red', false)) : (''); ?>>
				<?= esc_html('red pill', 'wporg'); ?>
			</option>
			<option value="blue" <?= isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'blue', false)) : (''); ?>>
				<?= esc_html('blue pill', 'wporg'); ?>
			</option>
		</select>
		<p class="description">
			<?= esc_html('You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'wporg'); ?>
		</p>
		<p class="description">
			<?= esc_html('You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'wporg'); ?>
		</p>
		<?php
	}
}
