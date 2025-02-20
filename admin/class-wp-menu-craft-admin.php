<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://http://localhost/restaurant
 * @since      1.0.0
 *
 * @package    Wp_Menu_Craft
 * @subpackage Wp_Menu_Craft/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Menu_Craft
 * @subpackage Wp_Menu_Craft/admin
 * @author     Techy Trion <testingemailer1212@gmail.com>
 */
class Wp_Menu_Craft_Admin {

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
		 * defined in Wp_Menu_Craft_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Menu_Craft_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-menu-craft-admin.css', array(), $this->version, 'all' );

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
		 * defined in Wp_Menu_Craft_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Menu_Craft_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Enqueue jQuery UI script
    	wp_enqueue_script('jquery-ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'), '1.12.1', true);

		// DataTables
        wp_enqueue_script('datatables-script', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', array('jquery'), null, false);
		
        wp_enqueue_style('datatables-style', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css'); 
		
		// bootstarp
		wp_enqueue_style('bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css');
		
		wp_enqueue_script( 'bootstrap-script', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js' );
		
		//sweetalert
		wp_enqueue_script( 'sweetalert2-toaster', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js', array(), $this->version, false );
		
		
		// Enqueue SweetAlert JS
		wp_enqueue_script('sweetalert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js', array('jquery'), '11.0.19', true);
		
		// Enqueue SweetAlert CSS
		wp_enqueue_style('sweetalert-css', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css', array(), '11.0.19');
		
		// Enqueue Font awesome
		wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '11.0.19');
		
		// Enqueue  Font awesome
		wp_enqueue_script('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js', array('jquery'), '11.0.19', true);
		
		// custom js 
		wp_enqueue_script( 'custom-service-script', plugin_dir_url( __FILE__ ) . 'js/wp-menu-craft-admin.js', array( 'jquery' ), '2.3.5', false );
		
		// ajax 
		wp_localize_script('custom-service-script', 'ajax_object', array( 'ajaxurl' => admin_url('admin-ajax.php')));
	}
	
}
