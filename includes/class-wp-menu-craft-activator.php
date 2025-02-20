<?php

/**
 * Fired during plugin activation
 *
 * @link       https://http://localhost/restaurant
 * @since      1.0.0
 *
 * @package    Wp_Menu_Craft
 * @subpackage Wp_Menu_Craft/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Menu_Craft
 * @subpackage Wp_Menu_Craft/includes
 * @author     Techy Trion <testingemailer1212@gmail.com>
 */
class Wp_Menu_Craft_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() 
	{
		global $wpdb;
		$menu_table_name = $wpdb->prefix . 'trion_menu_tbl_meta';
		$service_table_name = $wpdb->prefix . 'trion_service_tbl_meta';
		$dish_table_name = $wpdb->prefix . 'trion_dish_tbl_meta';
		$category_table_name = $wpdb->prefix . 'trion_category_tbl_meta';
		$other_menu_dish_meta = $wpdb->prefix . 'trion_other_menu_dish_meta';
		$main_menu_service_tbl_meta = $wpdb->prefix . 'trion_main_menu_service_tbl_meta';
		$trion_main_service_tbl_dish_meta = $wpdb->prefix . 'trion_main_service_tbl_dish_meta';
		$daily_menu_service_tbl_meta = $wpdb->prefix . 'trion_daily_menu_service_tbl_meta';
		$daily_service_tbl_dish_meta = $wpdb->prefix . 'trion_daily_service_tbl_dish_meta';
		$pdf_table_name = $wpdb->prefix . 'trion_pdf_tbl_data';
		$special_menu_service_tbl_meta = $wpdb->prefix . 'trion_special_menu_service_tbl_meta';
		$special_service_tbl_dish_meta = $wpdb->prefix . 'trion_special_service_tbl_dish_meta';


		// Create the table if it doesn't exist

		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS $menu_table_name (
			id INT(11) NOT NULL AUTO_INCREMENT,
			menu_name varchar(255) NOT NULL,
			menu_name_eng varchar(255) NOT NULL,
			menu_name_eus varchar(255) NOT NULL,
			menu_name_fr varchar(255) NOT NULL,
			menu_description TEXT NOT NULL,
			menu_ordering TEXT NULL,
			menu_pricing varchar(255) NULL,
			menu_pricing_eng varchar(255) NULL,
			menu_pricing_eus varchar(255) NULL,
			menu_pricing_fr varchar(255) NULL,
			category_id INT(50) NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql);

		$sql1 = "CREATE TABLE IF NOT EXISTS $service_table_name (
			id INT(11) NOT NULL AUTO_INCREMENT,
			service_name TEXT NOT NULL,
			service_description TEXT NOT NULL,
			service_ordering TEXT NULL,
			parent_menu TEXT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql1);

		$sql2 = "CREATE TABLE IF NOT EXISTS $dish_table_name (
			id INT(11) NOT NULL AUTO_INCREMENT,
			dish_name_eng varchar(255) NOT NULL,
			dish_name_es varchar(255) NOT NULL,
			dish_name_eus varchar(255) NOT NULL,
			dish_name_fr varchar(255) NOT NULL,
			dish_description TEXT NOT NULL,
			dish_ordering TEXT NULL,
			dish_pricing TEXT NULL,
			parent_service TEXT NULL,
			dish_status varchar(50) NULL,
			daily_dish_status varchar(50) NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql2);

		$sql3 = "CREATE TABLE IF NOT EXISTS $category_table_name (
			id INT(11) NOT NULL AUTO_INCREMENT,
			category_name TEXT NOT NULL,
			category_description TEXT NOT NULL,
			slug VARCHAR(255) NOT NULL,
			status VARCHAR(20) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql3);

		// Create the new dish meta table if it doesn't exist
		$sql4 = "CREATE TABLE IF NOT EXISTS $special_service_tbl_dish_meta (
			id INT(11) NOT NULL AUTO_INCREMENT,
			special_menu_id	INT(11) NOT NULL,
			service_id INT(11) NOT NULL,
			dish_meta_key VARCHAR(255) NOT NULL,
			dish_meta_value VARCHAR(255) NOT NULL,
			dish_status varchar(50) NULL,
			special_dish_ordering VARCHAR(255) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql4);

		// Create the new dish meta table if it doesn't exist
		$sql5 = "CREATE TABLE IF NOT EXISTS $other_menu_dish_meta (
			id INT(11) NOT NULL AUTO_INCREMENT,
			other_menu_id INT(11) NOT NULL,
			other_dish_meta_key VARCHAR(255) NOT NULL,
			other_dish_meta_value VARCHAR(255) NOT NULL,
			other_dish_status varchar(50) NULL,
			other_dish_ordering varchar(50) NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql5);

		// main menu service meta 
		// Create the new dish meta table if it doesn't exist
		$sql6 = "CREATE TABLE IF NOT EXISTS $main_menu_service_tbl_meta (
			id INT(11) NOT NULL,
			service_name VARCHAR(255) NOT NULL,
			service_name_eng varchar(255) NOT NULL,
			service_name_eus varchar(255) NOT NULL,
			service_name_fr varchar(255) NOT NULL,
			service_description VARCHAR(255) NOT NULL,
			parent_menu INT(11) NOT NULL,
			main_service_ordering VARCHAR(255) NOT NULL,
			-- PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql6);

		// main menu service dish meta
		$sql7 = "CREATE TABLE IF NOT EXISTS $trion_main_service_tbl_dish_meta (
			id INT(11) NOT NULL AUTO_INCREMENT,
			main_service_id INT(11) NOT NULL,
			main_dish_meta_key VARCHAR(255) NOT NULL,
			main_dish_meta_value VARCHAR(255) NOT NULL,
			main_dish_status VARCHAR(50) NOT NULL,
			main_dish_ordering VARCHAR(50) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql7);

		// daily menu services meta
		$sql8 = "CREATE TABLE IF NOT EXISTS $daily_menu_service_tbl_meta (
			id INT(11) NOT NULL,
			service_name VARCHAR(255) NOT NULL,
			service_description VARCHAR(255) NOT NULL,
			parent_menu INT(11) NOT NULL,
			daily_service_ordering VARCHAR(255) NOT NULL
			-- PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql8);

		// daily menu service dish meta
		$sql9 = "CREATE TABLE IF NOT EXISTS $daily_service_tbl_dish_meta (
			id INT(11) NOT NULL AUTO_INCREMENT,
			daily_service_id INT(11) NOT NULL,
			daily_dish_meta_key VARCHAR(255) NOT NULL,
			daily_dish_meta_value VARCHAR(255) NOT NULL,
			daily_dish_status VARCHAR(50) NOT NULL,
			daily_dish_ordering VARCHAR(50) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql9);

		// daily menu service dish meta
		$sql10 = "CREATE TABLE IF NOT EXISTS $pdf_table_name (
			id INT(11) NOT NULL AUTO_INCREMENT,
			pdf_menu_id INT(11) NOT NULL,
			pdf_link VARCHAR(255) NOT NULL,
			pdf_path VARCHAR(255) NOT NULL,
			pdf_lang VARCHAR(255) NOT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		$wpdb->query($sql10);

		// special menu service data
		$sql11 = "CREATE TABLE IF NOT EXISTS $special_menu_service_tbl_meta (
			id INT(11) NOT NULL,
			service_name VARCHAR(255) NOT NULL,
			service_description VARCHAR(255) NOT NULL,
			parent_menu INT(11) NOT NULL,
			specail_service_ordering VARCHAR(255) NOT NULL,
		) $charset_collate;";
		$wpdb->query($sql11);

	
		/********* insert data *************/ 
		global $wpdb;
		$category_table_name = $wpdb->prefix . 'trion_category_tbl_meta';

		// Define an array of data for the three entries
		$category_data = array(
			array(
				'category_name' => 'Principal',
				'category_description' => 'Descripción para la Categoría 1',
				'slug' => 'main',
				'status' => 1,
			),
			array(
				'category_name' => 'A diario',
				'category_description' => 'Descripción para la Categoría 2',
				'slug' => 'daily',
				'status' => 1,
			),
			array(
				'category_name' => 'Especial',
				'category_description' => 'Descripción para la Categoría 3',
				'slug' => 'special',
				'status' => 1,
			),
		);

		foreach ($category_data as $data) 
		{
			$slug = $data['slug'];
			
			// Check if a row with the same slug exists
			$existing_row = $wpdb->get_row("SELECT * FROM $category_table_name WHERE slug = '$slug'");
			
			if (!$existing_row) 
			{
				// Insert the data into the category table if the slug doesn't exist
				$wpdb->insert($category_table_name, $data);
			}
		}

		/************* insert data in menu table ****************/ 
		$menu_table_name = $wpdb->prefix . 'trion_menu_tbl_meta';

		// Define an array of menu data
		$menu_data = array(
			array(
				'menu_name' => 'Menú principal',
				'menu_description' => 'Description for Menú principal',
				'menu_ordering' => '',
				'menu_pricing' => '',
				'category_id' => 1,
			),
			array(
				'menu_name' => 'Menú del Día',
				'menu_description' => 'Description for Menú del Día',
				'menu_ordering' => '',
				'menu_pricing' => '',
				'category_id' => 2,
			),
		);

		foreach ($menu_data as $data) {
			$menu_name = $data['menu_name'];

			// Check if a row with the same menu name exists
			$existing_row = $wpdb->get_row("SELECT * FROM $menu_table_name WHERE menu_name = '$menu_name'");

			if (!$existing_row) {
				// Insert the data into the menu table if the menu name doesn't exist
				$wpdb->insert($menu_table_name, $data);
			}
		}
	}

}
