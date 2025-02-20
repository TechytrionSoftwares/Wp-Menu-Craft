<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://http://localhost/restaurant
 * @since             1.0.0
 * @package           Wp_Menu_Craft
 *
 * @wordpress-plugin
 * Plugin Name:       WP Menu Craft 
 * Plugin URI:        https://http://localhost/restaurant/wp-admin/plugins.php
 * Description:       Restaurant Menu
 * Version:           1.0.0
 * Author:            Techy Trion
 * Author URI:        https://http://localhost/restaurant
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-menu-craft
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_MENU_CRAFT_VERSION', '1.0.0' );
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-menu-craft-activator.php
 */
function activate_wp_menu_craft() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-menu-craft-activator.php';
	Wp_Menu_Craft_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-menu-craft-deactivator.php
 */
function deactivate_wp_menu_craft() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-menu-craft-deactivator.php';
	Wp_Menu_Craft_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_menu_craft' );
register_deactivation_hook( __FILE__, 'deactivate_wp_menu_craft' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-menu-craft.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

/******************** Add Custom files *************************/ 
require plugin_dir_path( __FILE__ ) . 'admin/custom-addon/custom-addon-restaurant-ajax.php'; 

 /************* Register Admin Menus *************/
add_action('admin_menu', 'trion_menu_craft_plugin_menu');

function trion_menu_craft_plugin_menu() 
{
    add_menu_page(
        'Elaboración del menú WPM',     
        'Elaboración del menú WPM',      
        'manage_options',    
        'trion-menu-craft-plugin',  
        'trion_menu_craft_plugin_page',  
        'dashicons-food',
		25
    );
}

// Render the main menu page
function trion_menu_craft_plugin_page() 
{
    require plugin_dir_path( __FILE__ ) . 'admin/partials/trion-menu-craft-admin-menus-list.php';
}
// Sub menus 
// Register the Export page
add_action('admin_menu', 'trion_menu_craft_plugin_submenu');

function trion_menu_craft_plugin_submenu() 
{
    add_submenu_page(
        'trion-menu-craft-plugin',  
        'Agregar platos',     
        'Agregar platos',      
        'manage_options',   
        'trion-menu-craft-plugin-dish', 
        'trion_menu_craft_plugin_dish_page'
    );

    add_submenu_page(
        'trion-menu-craft-plugin',  
        'Añadir categoría',     
        'Añadir categoría',      
        'manage_options',   
        'trion-menu-craft-plugin-category', 
        'trion_menu_craft_plugin_category_page'
    );


    add_submenu_page(
        'trion-menu-craft-plugin',  
        'Agregar menús',     
        'Agregar menús',      
        'manage_options',   
        'trion-menu-craft-plugin-menus', 
        'trion_menu_craft_plugin_menus_page'
    );

    add_submenu_page(
        'trion-menu-craft-plugin',  
        'Agregar servicios',     
        'Agregar servicios',      
        'manage_options',   
        'trion-menu-craft-plugin-services', 
        'trion_menu_craft_plugin_services_page'
    );

    // add_submenu_page(
    //     'trion-menu-craft-plugin',  
    //     'Menu List',     
    //     'Menu List',      
    //     'manage_options',   
    //     'trion-menu-craft-plugin-menus-list', 
    //     'trion_menu_craft_plugin_menus_list_page'
    // );
	add_submenu_page(
        'trion-menu-craft-plugin',  
        'Generar PDF',     
        'Generar PDF',      
        'manage_options',   
        'trion-menu-craft-plugin-pdf', 
        'trion_menu_craft_plugin_pdf_page'
    );
    add_submenu_page(
        'trion-menu-craft-plugin',  
        'Importar Excel',     
        'Importar Excel',      
        'manage_options',   
        'trion-menu-craft-plugin-import-file', 
        'trion_menu_craft_plugin_import_file_page'
    );


}
// Render dish page 
function trion_menu_craft_plugin_dish_page() 
{
    require plugin_dir_path( __FILE__ ) . 'admin/partials/trion-menu-craft-admin-dish.php';
}

// Render category page 
function trion_menu_craft_plugin_category_page() 
{
    require plugin_dir_path( __FILE__ ) . 'admin/partials/trion-menu-craft-admin-category.php';
}

// Render menu page 
function trion_menu_craft_plugin_menus_page() 
{
    require plugin_dir_path( __FILE__ ) . 'admin/partials/trion-menu-craft-admin-menus.php';
}


// Render service page 
function trion_menu_craft_plugin_services_page() 
{
    require plugin_dir_path( __FILE__ ) . 'admin/partials/trion-menu-craft-admin-services.php';
}

//Render menu list
// function trion_menu_craft_plugin_menus_list_page() 
// {
//     require plugin_dir_path( __FILE__ ) . 'admin/partials/trion-menu-craft-admin-menus-list.php';
// }

// Render the pdf page
function trion_menu_craft_plugin_pdf_page() 
{
    require plugin_dir_path( __FILE__ ) . 'includes/templates/trion-menu-craft-pdf-page.php';
}

// Render the excel file page
function trion_menu_craft_plugin_import_file_page() 
{
    require plugin_dir_path( __FILE__ ) . 'admin/partials/trion-menu-craft-admin-import-file.php';
}

//Render generate pdf page 
function trion_menu_craft_plugin_generate_pdf($menu_id, $menu_lang_id) 
{
    require plugin_dir_path( __FILE__ ) . 'includes/templates/trion-menu-craft-generator-pdf.php';
}

/*************** Shportcode pdf**********************/ 

add_shortcode('show_all_pdf', 'show_all_pdf_fun');
function show_all_pdf_fun()
{
    global $wpdb;
    $pdf_table_name =  $wpdb->prefix . 'trion_pdf_tbl_data';
    $manu_table_name = $wpdb->prefix . 'trion_menu_tbl_meta';
    $html = '';

    $pdf_data = $wpdb->get_results("SELECT * FROM $pdf_table_name", ARRAY_A);

    ob_start();

    if($pdf_data)
    { 
        foreach($pdf_data as $data)
        {
            $pdf_url = $data['pdf_link']; 
            
            $menus_info = $wpdb->get_row("SELECT menu_name FROM $manu_table_name where id = ".$data['pdf_menu_id'], ARRAY_A);
            $html .= '<div class="main_div">';
            $html .= '<a href="' . esc_url($pdf_url) . '" target="_blank"><button class="menu_name_btn">' . $menus_info["menu_name"] . '</button></a>';
            $html .= '</div>';
        }
    }
    echo $html;
    return ob_get_clean();
}

/************* run wp-menu0-craft *****************/  
function run_wp_menu_craft() {

	$plugin = new Wp_Menu_Craft();
	$plugin->run();

}
run_wp_menu_craft();
