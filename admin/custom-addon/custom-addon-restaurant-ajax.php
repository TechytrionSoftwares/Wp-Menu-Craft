<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

/******************** insert main menu services ************************/ 
add_action('wp_ajax_insert_main_menu_services', 'insert_main_menu_services_callback');
add_action('wp_ajax_nopriv_insert_main_menu_services', 'insert_main_menu_services_callback');
function insert_main_menu_services_callback()
{
    global $wpdb; 
    $menu_table = $wpdb->prefix . 'trion_main_menu_service_tbl_meta';
    $service_table = $wpdb->prefix . 'trion_service_tbl_meta';

    if(isset($_POST['main_menu_id']) && isset($_POST['main_menu_service_id']))
    {
        $main_menu_id = $_POST['main_menu_id'];
        $main_menu_service_id = $_POST['main_menu_service_id'];

        $sql = $wpdb->prepare("SELECT * FROM $service_table WHERE id = %d", $main_menu_service_id);
        $result = $wpdb->get_row($sql);
       
        $main_menu_service_name = $result->service_name;
        $main_menu_service_des = $result->service_description;
    

        $menu_table_count = $wpdb->get_var("SELECT COUNT(*) FROM $menu_table");

        if ($menu_table_count >= 4) {
            $result = 'limiterror'; // Set $result to false if the count is 4 or more
        } else {
            $result = $wpdb->insert($menu_table, array(
                'id' => $main_menu_service_id, 
                'service_name' => $main_menu_service_name, 
                'service_description' => $main_menu_service_des, 
                'parent_menu' => $main_menu_id
            ));
        }
        
        if ($result === false) {
            $response = array('status' => false, 'error' => $wpdb->last_error);
        }elseif($result === 'limiterror'){
            $response = array('status' => 'limiterror', 'error' => $wpdb->last_error);
        } else {
            $response = array('status' => true);
        }
    }
    else
    {
        $response = array('status' => false,  'error' => $wpdb->last_error);
    }

    echo json_encode($response);
    wp_die();
}


/********************** Delete main menu Service *********************/ 
add_action('wp_ajax_delete_main_menu_service', 'delete_main_menu_service_callback');
add_action('wp_ajax_nopriv_delete_main_menu_service', 'delete_main_menu_service_callback');
function delete_main_menu_service_callback()
{
    global $wpdb; 
    $service_table = $wpdb->prefix . 'trion_main_menu_service_tbl_meta';

    if(isset($_POST['main_menu_service_id']))
    {
        $main_menu_service_id = $_POST['main_menu_service_id'];
        $wpdb->delete(
            $service_table,
            array('id' => $main_menu_service_id),
        );
        if ($wpdb->rows_affected > 0) 
        {
            $response = array('status' => 'success', 'message' => 'Service deleted successfully');
        } 
        else 
        {
            $response = array('status' => 'error', 'message' => 'Failed to delete service');
        }

        wp_send_json($response);
    }
    else 
    {
        $response = array('status' => 'error', 'message' => 'Invalid data received');
        wp_send_json($response);
    }
       
    wp_die();
}

/******************** insert/update main services dishes ***********************/ 
add_action('wp_ajax_insert_main_service_dishes', 'insert_main_service_dishes_callback');
add_action('wp_ajax_nopriv_insert_main_service_dishes', 'insert_main_service_dishes_callback');

function insert_main_service_dishes_callback() 
{
    global $wpdb;

    $message = '';

    if (isset($_POST['main_dish_id'], $_POST['main_service_id'], $_POST['isChecked'])) {
        $main_dish_id = $_POST['main_dish_id'];
        $main_service_id = $_POST['main_service_id'];
        $isChecked = $_POST['isChecked'];
        $main_dish_meta_key = '_main_dish';
        
        $table_name = $wpdb->prefix . 'trion_main_service_tbl_dish_meta'; 
        $service_table = $wpdb->prefix . 'trion_service_tbl_meta'; 

        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE main_dish_meta_value = %s AND main_service_id = %s",
                $main_dish_id, $main_service_id
            ),
            ARRAY_A
        );

        if ($result) {
            $main_menu_dish_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE main_service_id = $main_service_id AND main_dish_status = 'true'");
            
            $service_name = $wpdb->get_var("SELECT service_name FROM $service_table WHERE id = $main_service_id");

            $error_response = limit_dishes_main($main_menu_dish_count, $isChecked, $main_service_id);

            if($error_response === 'limiterror_extra')
            {
                $isChecked = 'false';
                $message  = 'Please remove the dish if you want to add another dish because this service dishes limit is exceed.';
            }
            else if($error_response === 'limiterror_add')
            {
                $message  = 'Please add the dish because this service dishes limit is not fulfill.';
            }
            else
            {
                $error_response = 'limitsuccess';
                $message  = 'Dishes limit is fullfilled for this service.';
            }

            $wpdb->update(
                $table_name,
                array('main_dish_status' => $isChecked),
                array('main_dish_meta_value' => $main_dish_id, 'main_service_id' => $main_service_id)
            );
        } else {
            $data = array(
                'main_service_id' => $main_service_id,
                'main_dish_meta_key' => $main_dish_meta_key,
                'main_dish_meta_value' => $main_dish_id,
                'main_dish_status' => $isChecked,
            );
            $wpdb->insert($table_name, $data);
        }

        $response = array('success' => $error_response, 'message' => $message);
        wp_send_json($response);
        wp_die();
    }
}

function limit_dishes_main($count, $isChecked, $main_service_id) 
{
    $service_name = trim($main_service_id);
    $limits = array(
        '3' => array('min' => 22, 'max' => 23),
        '4' => array('min' => 17, 'max' => 18),
        '9' => array('min' => 12, 'max' => 13),
        '7' => array('min' => 19, 'max' => 20),
    );

    if ($isChecked == 'true') 
    {
        if ($count >= $limits[$service_name]['max']) 
        {
            return 'limiterror_extra';
        }else if($count < $limits[$service_name]['min']){
            return 'limiterror_add';
        }
    } 
    else 
    {
        if ($count <= $limits[$service_name]['min'] + 1) 
        {
            return 'limiterror_add';
        }
    }

    return 'limitsuccess';
}

/***************** insert daily menu services **********************/ 
add_action('wp_ajax_insert_daily_menu_services', 'insert_daily_menu_services_callback');
add_action('wp_ajax_nopriv_insert_daily_menu_services', 'insert_daily_menu_services_callback');
function insert_daily_menu_services_callback()
{
    global $wpdb; 
    $menu_table = $wpdb->prefix . 'trion_daily_menu_service_tbl_meta';
    $service_table = $wpdb->prefix . 'trion_service_tbl_meta';

    if(isset($_POST['daily_menu_service_id']) && isset($_POST['daily_menu_id']))
    {
        $daily_menu_service_id = $_POST['daily_menu_service_id'];
        $daily_menu_id = $_POST['daily_menu_id'];

        $sql = $wpdb->prepare("SELECT * FROM $service_table WHERE id = %d", $daily_menu_service_id);
        $result = $wpdb->get_row($sql);
       
        $daily_menu_service_name = $result->service_name;
        $daily_menu_service_des = $result->service_description;

        $menu_table_count = $wpdb->get_var("SELECT COUNT(*) FROM $menu_table");
    
        if ($menu_table_count >= 3) {
            $result = 'limiterror'; // Set $result to false if the count is 3 or more
        } 
        else 
        {
            $result = $wpdb->insert($menu_table, array(
                'id' => $daily_menu_service_id , 
                'service_name' => $daily_menu_service_name , 
                'service_description' => $daily_menu_service_des , 
                'parent_menu' => $daily_menu_id
            ));
        }

        if ($result === false) 
        {
            $response = array('success' => false , 'error' => $wpdb->last_error);
        } 
        else if($result === 'limiterror')
        {
            $response = array('success' => 'limiterror', 'error' => $wpdb->last_error);
        }
        else 
        {
            $response = array('success' => true);
        }
    }
    else
    {
        $response = array('success' => false,  'error' => $wpdb->last_error);
    }

    echo json_encode($response);
    wp_die();
}


/******************* delete  daily menu service ************************/ 
add_action('wp_ajax_daily_menu_delete_service', 'daily_menu_delete_service_callback');
add_action('wp_ajax_nopriv_daily_menu_delete_service', 'daily_menu_delete_service_callback');
function daily_menu_delete_service_callback()
{
    global $wpdb; 
    $daily_service_table = $wpdb->prefix . 'trion_daily_menu_service_tbl_meta';

    if(isset($_POST['update_daily_service_menu_id']))
    {
        $update_daily_service_menu_id = $_POST['update_daily_service_menu_id'];
        $wpdb->delete(
            $daily_service_table,
            array('id' => $update_daily_service_menu_id),
        );
        if ($wpdb->rows_affected > 0) 
        {
            $response = array('status' => 'success', 'message' => 'Service deleted successfully');
        } 
        else 
        {
            $response = array('status' => 'error', 'message' => 'Failed to delete service');
        }

        wp_send_json($response);
    }
    else 
    {
        $response = array('status' => 'error', 'message' => 'Invalid data received');
        wp_send_json($response);
    }
       
    wp_die();
}

/******************** insert/update daily services dishes ***********************/ 
add_action('wp_ajax_insert_daily_service_dishes', 'insert_daily_service_dishes_callback');
add_action('wp_ajax_nopriv_insert_daily_service_dishes', 'insert_daily_service_dishes_callback');
function insert_daily_service_dishes_callback()
{
    global $wpdb;
    $message = '';

    if(isset($_POST['daily_dish_id']) && isset($_POST['daily_service_id']) && isset($_POST['isChecked']))
    {
        $daily_dish_id = $_POST['daily_dish_id'];
        $daily_service_id = $_POST['daily_service_id'];
        $isChecked = $_POST['isChecked'];
        $daily_dish_meta_key = '_daily_dish';
    
        $table_name = $wpdb->prefix . 'trion_daily_service_tbl_dish_meta'; 
        $service_table = $wpdb->prefix . 'trion_service_tbl_meta'; 

        $result = $wpdb->get_row(
            $wpdb->prepare(
                "select * from $table_name WHERE daily_dish_meta_value ='$daily_dish_id' AND daily_service_id = '$daily_service_id'",
            ), ARRAY_A
        );

        if($result)
        {
            $daily_menu_dish_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE daily_service_id = $daily_service_id AND daily_dish_status = 'true'");
            
            $daily_service_name = $wpdb->get_var("SELECT service_name FROM $service_table WHERE id = $daily_service_id");

            $error_response = limit_dishes_daily($daily_menu_dish_count, $isChecked, $daily_service_id);

            if($error_response === 'limiterror_extra')
            {
                $isChecked = 'false';
                $message  = 'Please remove the dish if you want to add another dish because this service dishes limit is exceed.';
            }
            else if($error_response === 'limiterror_add')
            {
                $message  = 'Please add the dish because this service dishes limit is not fulfill.';
            }
            else
            {
                $error_response = 'limitsuccess';
                $message  = 'Dishes limit is fullfilled for this service.';
            }

            $wpdb->update(
                $table_name,
                array('daily_dish_status' => $isChecked),
                array('daily_dish_meta_value' => $daily_dish_id, 'daily_service_id' => $daily_service_id),
            );
        }
        else
        {
            $data = array(
                'daily_service_id' => $daily_service_id,
                'daily_dish_meta_key' => $daily_dish_meta_key,
                'daily_dish_meta_value' => $daily_dish_id,
                'daily_dish_status' => $isChecked,
            );
            $wpdb->insert($table_name, $data, $format);
        }

        
        $response = array('success' => $error_response, 'message' => $message );
        // Send JSON response
        wp_send_json($response);
    
       wp_die();
    }
}

function limit_dishes_daily($count, $isChecked, $daily_service_id) 
{
    $service_name = trim($daily_service_id);
    $limits = array(
        '1' => array('min' => 24, 'max' => 25),
        '2' => array('min' => 24, 'max' => 25),
        '6' => array('min' => 15, 'max' => 16),
    );

    if ($isChecked == 'true') 
    {
        if ($count >= $limits[$service_name]['max']) 
        {
            return 'limiterror_extra';
        }
        else if($count < $limits[$service_name]['min'])
        {
            return 'limiterror_add';
        }
    } 
    else 
    {
        if ($count <= $limits[$service_name]['min'] + 1) 
        {
            return 'limiterror_add';
        }
    }

    return 'limitsuccess';
}

/******************** insert special menu services ************************/ 
add_action('wp_ajax_insert_special_menu_services', 'insert_special_menu_services_callback');
add_action('wp_ajax_nopriv_insert_special_menu_services', 'insert_special_menu_services_callback');
function insert_special_menu_services_callback()
{
    global $wpdb; 
    $menu_table = $wpdb->prefix . 'trion_special_menu_service_tbl_meta';
    $service_table = $wpdb->prefix . 'trion_service_tbl_meta';

    if(isset($_POST['special_menu_service_id']) && isset($_POST['special_menu_id']))
    {
        $special_menu_service_id = $_POST['special_menu_service_id'];
        $special_menu_id = $_POST['special_menu_id'];

        $sql = $wpdb->prepare("SELECT * FROM $service_table WHERE id = %d", $special_menu_service_id);
        $result = $wpdb->get_row($sql);
       
        $special_menu_service_name = $result->service_name;
        $special_menu_service_des = $result->service_description;
    
         $menu_table_count = $wpdb->get_var("SELECT COUNT(*) FROM $menu_table");

        if ($menu_table_count >= 3) {
            $result = 'limiterror'; // Set $result to false if the count is 4 or more
        }
        else
        {
            $result = $wpdb->insert($menu_table, array(
                'id' => $special_menu_service_id , 
                'service_name' => $special_menu_service_name , 
                'service_description' => $special_menu_service_des , 
                'parent_menu' => $special_menu_id
            ));
        }
       

        if ($result === false) 
        {
            $response = array('status' => false , 'error' => $wpdb->last_error);
        } 
        else if($result === 'limiterror')
        {
            $response = array('status' => 'limiterror', 'error' => $wpdb->last_error);
        } 
        else 
        {
            $response = array( 'status' => true);
        }
    }
    else
    {
        $response = array('status' => false,  'error' => $wpdb->last_error);
    }

    echo json_encode($response);
    wp_die();
}


/*************** insert dishes **********************/
add_action('wp_ajax_insert_dishes', 'insert_dishes_callback');
add_action('wp_ajax_nopriv_insert_dishes', 'insert_dishes_callback');

function insert_dishes_callback()
{
    global $wpdb; 

    $response = array(); // Initialize the response array

    if(isset($_POST['dish_price']) && isset($_POST['dish_description']) && isset($_POST['selected_services']))
    {
        $dish_name_eng = $_POST['dish_name_eng'];
        $dish_name_es = $_POST['dish_name_es'];
        $dish_name_eus = $_POST['dish_name_eus'];
        $dish_name_fr = $_POST['dish_name_fr'];
        $dish_price = $_POST['dish_price'];
        $dish_description = $_POST['dish_description'];
        $selected_services = $_POST['selected_services'];

        // Determine the language based on the dish name

        $serialized_services = serialize($selected_services);

        $dish_table = $wpdb->prefix . 'trion_dish_tbl_meta';

        $result = $wpdb->insert($dish_table, array(
                'dish_name_eng' => $dish_name_eng,
                'dish_name_es' => $dish_name_es,
                'dish_name_eus' => $dish_name_eus,
                'dish_name_fr' => $dish_name_fr,
                'dish_description' => $dish_description,
                'dish_pricing' => $dish_price,
                'parent_service' => $serialized_services
                //'parent_service' => $selected_services
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );

        if ($result === false) 
        {
            $response['main'] = array('success' => false , 'error' => $wpdb->last_error);
        } 
        else 
        {
            // Get the last inserted dish ID
            $dish_id = $wpdb->insert_id;
            $response['main'] = array('success' => true, 'dish_id' => $dish_id);

            if (isset($_POST['other_data_id']) && isset($_POST['selected_menus'])) 
            {
                if ($_POST['other_data_id'] == 'other_menu') 
                {
                    $selected_menus = $_POST['selected_menus'];
                    $decoded_menus = json_decode(stripslashes($selected_menus), true);

                    $response['other'] = array(); // Initialize the response array for other data

                    foreach ($decoded_menus as $menu_data) 
                    {
                        $other_menu_id = $menu_data['menu_id'];

                        // Assuming you have other data to insert
                        $other_dish_meta_key = '_other_dish'; 
                        $other_dish_meta_value = $dish_id;

                        $other_menu_table = $wpdb->prefix . 'trion_other_menu_dish_meta';

                        $result = $wpdb->insert($other_menu_table, array(
                            'other_menu_id' => $other_menu_id,
                            'other_dish_meta_key' => $other_dish_meta_key,
                            'other_dish_meta_value' => $other_dish_meta_value,
                            'other_dish_status' => 'false',
                        ));

                        if ($result === false) 
                        {
                            $response['other'][] = array('success' => false, 'error' => $wpdb->last_error);
                        } 
                        else 
                        {
                            $response['other'][] = array('success' => true);
                        }
                    }
                }
            }
        }
    }
    else
    {
        $response['main'] = array('success' => false,  'error' => $wpdb->last_error);
    }

    echo json_encode($response);
    wp_die();
}

/************ update dishes ***************/ 
add_action('wp_ajax_update_dishes', 'update_dishes_callback');
add_action('wp_ajax_nopriv_update_dishes', 'update_dishes_callback');

function update_dishes_callback()
{
    global $wpdb;
    $dish_table = $wpdb->prefix . 'trion_dish_tbl_meta';
    $service_table = $wpdb->prefix . 'trion_service_tbl_meta';
    $special_service_tbl_dish_meta = $wpdb->prefix . 'trion_special_service_tbl_dish_meta';
    $trion_other_menu_dish_meta = $wpdb->prefix . 'trion_other_menu_dish_meta';

    if(isset($_POST['dishes_id']) && isset($_POST['update_dish_price']) && isset($_POST['update_dishes_des']))
    {
        $dishes_id = $_POST['dishes_id'];
        $update_dish_name_eng = $_POST['update_dish_name_eng'];
        $update_dish_name_es = $_POST['update_dish_name_es'];
        $update_dish_name_eus = $_POST['update_dish_name_eus'];
        $update_dish_name_fr = $_POST['update_dish_name_fr'];
        $update_dish_price = $_POST['update_dish_price'];
        $update_dish_description = $_POST['update_dishes_des'];   
        $selected_services = $_POST['selected_services'];
        $service_data = $_POST['service_data'];
        $unchecked_services = $_POST['unchecked_services'];
		$unchecked_services_array = json_decode(stripslashes($_POST['unchecked_services']), true);

        /********* other menus *********/ 
        $selected_menus = $_POST['update_menus'];
        $menu_data = $_POST['menu_data'];
        $unchecked_menu = $_POST['unchecked_menus'];
		$unchecked_menus_array = json_decode(stripslashes($_POST['unchecked_menus']), true);

        /*************** unchecked menus data ********************/ 
        $all_uncheck_menus = array();
        foreach($unchecked_menus_array as $uncheck_menu_data)
        {
          	$all_uncheck_menus[] = $uncheck_menu_data['menu_id'];
        }
		
		if(!empty($all_uncheck_menus))
		{
			foreach ($all_uncheck_menus as $unchecked_menu_id) 
			{
				// Check if the combination of service_id and dishes_id exists in the special_service_tbl_dish_meta table
				$existing_data = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT * FROM $trion_other_menu_dish_meta WHERE other_menu_id = %d AND other_dish_meta_value = %d",
						$unchecked_menu_id,
						$dishes_id
					),
					ARRAY_A
				);

				if ($existing_data) 
				{
					// Update dish_status to false
					// $result = $wpdb->update(
					// 	$trion_other_menu_dish_meta,
					// 	array('other_dish_status' => 'false'),
					// 	array('id' => $existing_data['id'])
					// );

                     // Delete the row from $trion_other_menu_dish_meta
                    $result = $wpdb->delete(
                        $trion_other_menu_dish_meta,
                        array('id' => $existing_data['id'])
                    );

                    if($result !== false)
                    {
                        // echo $result;
                        $response = array('success' => true);
                        // print_r($response); 
                    }
                    else
                    {
                    
                        $response = array('success' => false);
                    }

				}

    		}
		}
        
        /************* check menu data ********************/ 
        $other_menu_id = array();
        foreach($menu_data as $menus)
        {
           $other_menu_id[] =  $menus['menu_id'];
        }

        if(!empty($other_menu_id))
        {
            $placeholderss = implode(', ', array_fill(0, count($other_menu_id), '%d'));
        
            $query = $wpdb->prepare("SELECT * FROM $trion_other_menu_dish_meta WHERE other_menu_id IN ($placeholderss) AND other_dish_meta_value = %d", array_merge($other_menu_id, [$dishes_id]));

            // Execute the query
            $all_menus_data = $wpdb->get_results($query, ARRAY_A);

            foreach ($other_menu_id as $data)
            {
                // Check if menu ID exists in $all_menus_data
                $found_menu = false;
                foreach ($all_menus_data as $menu_data)
                {
                    if ($menu_data['other_menu_id'] == $data)
                    {
                        $found_menu = true;
                        // Update dish_status to true
                        $result = $wpdb->update(
                            $trion_other_menu_dish_meta,
                            array('other_dish_status' => 'true'),
                            array('id' => $menu_data['id'])
                        );

                        if($result === false)
                        {
                            $response = array('success' => false);
                            break; // exit the loop if update fails
                        }
                        else
                        {
                            $response = array('success' => true);
                        }
                    }
                }

                // If menu ID not found, insert new data
                if (!$found_menu)
                {
                    $insert_result = $wpdb->insert(
                        $trion_other_menu_dish_meta,
                        array(
                            'other_menu_id' => $data, 
                            'other_dish_meta_key' => '_other_dish',
                            'other_dish_meta_value' => $dishes_id,
                            'other_dish_status' => 'false',
                        )
                    );

                    if ($insert_result === false) 
                    {
                        $response = array('success' => false);
                        break; // exit the loop if insert fails
                    } 
                    else 
                    {
                        $response = array('success' => true);
                    }
                }
            }
        }
        else
        {
            $response = array('success' => true);
        }

        // echo json_encode($response);
        // wp_die();
        
    

        /************* unchecked services data *********************/ 
		$all_uncheck_services = array();
        foreach($unchecked_services_array as $uncheck_ser_data)
        {
          	$all_uncheck_services[] = $uncheck_ser_data['service_id'];
        }
		
		if(!empty($all_uncheck_services))
		{
			foreach ($all_uncheck_services as $unchecked_service_id) 
			{
				// Check if the combination of service_id and dishes_id exists in the special_service_tbl_dish_meta table
				$existing_data = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT * FROM $special_service_tbl_dish_meta WHERE service_id = %d AND dish_meta_value = %d",
						$unchecked_service_id,
						$dishes_id
					),
					ARRAY_A
				);

				if ($existing_data) 
				{
					// Update dish_status to false
					$wpdb->update(
						$special_service_tbl_dish_meta,
						array('dish_status' => 'false'),
						array('id' => $existing_data['id'])
					);
				}
    		}
		}
        // else
        // {
		//     echo "asdasd";
		// }
         /************* unchecked services data sec end  *********************/


        /************* get services for special meta table *************************/ 

        $serialized_services = serialize($selected_services);

        // Create data array for updating
        $data = array(
            'dish_name_eng' => $update_dish_name_eng,
            'dish_name_es' => $update_dish_name_es,
            'dish_name_eus' => $update_dish_name_eus,
            'dish_name_fr' => $update_dish_name_fr,
            'dish_pricing' => $update_dish_price,
            'dish_description' => $update_dish_description,
            'dish_status' => 'true',
            'daily_dish_status' => 'true',
            'parent_service' => $serialized_services
            //'parent_service' => $selected_services
        );
        $where = array('id' => $dishes_id);
        
        // Perform the update
        $result = $wpdb->update($dish_table, $data, $where);

        if ($result !== false) 
        {
            $response = array('success' => true);
        } 
        else
        {
            // Update failed
            $response = array('success' => false, 'error' => $wpdb->last_error);
        }

        $special_services = array();
        foreach($service_data as $s_data)
        {
           $special_services[] = $s_data['service_id'];
        }

        // Ensure the $special_services array is not empty
        if (!empty($special_services)) 
        {
            // Create a placeholder string with as many %d as there are elements in $special_services
            $placeholders = implode(', ', array_fill(0, count($special_services), '%d'));

            // Prepare the SQL query using IN clause
            $query = $wpdb->prepare("SELECT * FROM $service_table WHERE id IN ($placeholders)", $special_services);

            // Execute the query
            $all_services_data = $wpdb->get_results($query, ARRAY_A);

            // Output the result
            foreach($all_services_data as $data)
            {

                if($data['id'] == 8 || $data['id'] == 5 || $data['id'] == 10)
                {
                    $special_ser_id = $data['id'];

                    $special_menu_id = 3;

                     /******************* insert/update data in the special dish table ************************/ 

                     // Check if the combination of service_id and dishes_id already exists in the special_service_tbl_dish_meta table
                    $existing_data = $wpdb->get_row(
                        $wpdb->prepare(
                            "SELECT * FROM $special_service_tbl_dish_meta WHERE service_id = %d AND dish_meta_value = %d AND special_menu_id = %d",
                            $special_ser_id,
                            $dishes_id,
                            $special_menu_id
                        ),
                        ARRAY_A
                    );

                    if ($existing_data) 
                    {
                        // Update dish_status to true
                        $wpdb->update(
                            $special_service_tbl_dish_meta,
                            array('dish_status' => 'true'),
                            array('id' => $existing_data['id'])
                        );
                    } 
                    else 
                    {
                        // Insert new data
                        $wpdb->insert(
                            $special_service_tbl_dish_meta,
                            array(
                                'special_menu_id' => $special_menu_id,
                                'service_id' => $special_ser_id,
                                'dish_meta_key' => '_dish', 
                                'dish_meta_value' => $dishes_id, 
                                'dish_status' => 'false', 
                            )
                        );
                    }

                } 
            } 

            $serialized_services = serialize($selected_services);

            // Create data array for updating
            $data = array(
                'dish_name_eng' => $update_dish_name_eng,
                'dish_name_es' => $update_dish_name_es,
                'dish_name_eus' => $update_dish_name_eus,
                'dish_name_fr' => $update_dish_name_fr,
                'dish_pricing' => $update_dish_price,
                'dish_description' => $update_dish_description,
                'dish_status' => 'true',
                'daily_dish_status' => 'true',
                'parent_service' => $serialized_services
                //'parent_service' => $selected_services
            );
            $where = array('id' => $dishes_id);

            // Perform the update
            $result = $wpdb->update($dish_table, $data, $where);

            if ($result !== false) 
            {
                $response = array('success' => true);
            } 
            else
            {
                // Update failed
                $response = array('success' => false, 'error' => $wpdb->last_error);
            }
        } 
        else 
        {
            echo 'No special services to retrieve.';
        }  
    }
    else
    {
        $response = array('success' => false, 'error' => 'Missing data');
    }

    echo json_encode($response);
    wp_die();
}


/************* delete dishes ****************/ 
add_action('wp_ajax_delete_dishes', 'delete_dishes_callback');
add_action('wp_ajax_nopriv_delete_dishes', 'delete_dishes_callback');

function delete_dishes_callback()
{
    global $wpdb;
    $dish_table = $wpdb->prefix . 'trion_dish_tbl_meta';
    
    if(isset($_POST['dishes_id']))
    {
        $dishes_id = $_POST['dishes_id'];
        $where = array('id' => $dishes_id);
        $result = $wpdb->delete($dish_table, $where);
        if ($result !== false)
        {
            // Delete from the second table
            $special_menu_service_table = $wpdb->prefix . 'trion_special_service_tbl_dish_meta';
            $result_special_menu = $wpdb->delete($special_menu_service_table, array('dish_meta_value' => $dishes_id));

            // Delete from the third table
            $main_menu_service_table = $wpdb->prefix . 'trion_main_service_tbl_dish_meta';
            $result_main_menu = $wpdb->delete($main_menu_service_table, array('main_dish_meta_value' => $dishes_id));

            // Delete from the fourth table
            $daily_menu_service_table = $wpdb->prefix . 'trion_daily_service_tbl_dish_meta';
            $result_daily_menu = $wpdb->delete($daily_menu_service_table, array('daily_dish_meta_value' => $dishes_id));

            // Delete from the fifth table
            $other_menu_service_table = $wpdb->prefix . 'trion_other_menu_dish_meta';
            $result_other_menu = $wpdb->delete($other_menu_service_table, array('other_dish_meta_value' => $dishes_id));

            $response = array('success' => true);
        }
        else
        {
            // Delete failed
            $response = array('success' => false, 'error' => $wpdb->last_error);
        }
    }
    else
    {
        $response = array('success' => false, 'error' => 'Missing data');
    }

    // Send JSON response
    echo json_encode($response);
    wp_die();
     
}

/*************** insert category ******************/ 
add_action('wp_ajax_insert_category', 'insert_category_callback');
add_action('wp_ajax_nopriv_insert_category', 'insert_category_callback');
function insert_category_callback()
{
    global $wpdb; 
    $category_table = $wpdb->prefix . 'trion_category_tbl_meta';

    if(isset($_POST['category_name']) && isset($_POST['category_description']))
    {
        $category_name = $_POST['category_name'];
        $category_description = $_POST['category_description'];
        $slug = sanitize_title($category_name);
        $existing_slug = $wpdb->get_var($wpdb->prepare("SELECT slug FROM $category_table WHERE slug = %s", $slug));

        if (!$existing_slug) 
        {
            $result = $wpdb->insert($category_table, array(
                'category_name' => $category_name , 
                'category_description' => $category_description, 
                'slug' => $slug, 
                'status' => 0
            ));

            if ($result === false) 
            {
                $response = array('success' => false , 'error' => $wpdb->last_error);
            } 
            else 
            {
                $response = array('success' => true);
            }
        }
        else
        {
            $response = array('success' => false, 'error' => "Category with the same name already exists");
        }
    }
    else
    {
        $response = array('success' => false,  'error' => $wpdb->last_error);
    }

    echo json_encode($response);
    wp_die();
}

/************** update category ********************/ 
add_action('wp_ajax_update_category', 'update_category_callback');
add_action('wp_ajax_nopriv_update_category', 'update_category_callback');
function update_category_callback()
{
    global $wpdb;
    $category_table = $wpdb->prefix . 'trion_category_tbl_meta';

    if(isset($_POST['cat_id']) && isset($_POST['update_category_name']) && isset($_POST['update_category_des']))
    {
        $cat_id = $_POST['cat_id'];
        $update_category_name = $_POST['update_category_name'];
        $update_category_des = $_POST['update_category_des'];   
        
        // Create data array for updating
        $data = array(
            'category_name' => $update_category_name,
            'category_description' => $update_category_des
        );
        $where = array('id' => $cat_id);

        // Perform the update
        $result = $wpdb->update($category_table, $data, $where);

        if ($result !== false) 
        {
            $response = array('success' => true);
        } 
        else
        {
            // Update failed
            $response = array('success' => false, 'error' => $wpdb->last_error);
        }
    }
    else
    {
        $response = array('success' => false, 'error' => 'Missing data');
    }

    echo json_encode($response);
    wp_die();
}
/************* delete category ****************/ 
add_action('wp_ajax_delete_category', 'delete_category_callback');
add_action('wp_ajax_nopriv_delete_category', 'delete_category_callback');

function delete_category_callback()
{
    global $wpdb;
    $category_table = $wpdb->prefix . 'trion_category_tbl_meta';
    
    if(isset($_POST['cats_id']))
    {
        $cats_id = $_POST['cats_id'];
        $where = array('id' => $cats_id);
        $result = $wpdb->delete($category_table, $where);
        if ($result !== false)
        {
            $response = array('success' => true);
        }
        else
        {
            // Delete failed
            $response = array('success' => false, 'error' => $wpdb->last_error);
        }
    }
    else
    {
        $response = array('success' => false, 'error' => 'Missing data');
    }

    // Send JSON response
    echo json_encode($response);
    wp_die();
     
}

/**************** insert menus **********************/ 
add_action('wp_ajax_insert_menus', 'insert_menus_callback');
add_action('wp_ajax_nopriv_insert_menus', 'insert_menus_callback');
function insert_menus_callback()
{
    global $wpdb; 
    $menu_table = $wpdb->prefix . 'trion_menu_tbl_meta';

    if(isset($_POST['menu_name']) && isset($_POST['menu_description']) && isset($_POST['category_id']))
    {
        $menu_name = $_POST['menu_name'];
        $menu_name_eng = $_POST['menu_name_eng'];
        $menu_name_eus = $_POST['menu_name_eus'];
        $menu_name_fr = $_POST['menu_name_fr'];
        $menu_description = $_POST['menu_description'];
        $category_id = $_POST['category_id'];

         // Check if the category_slug is 'special'
        $category_slug = isset($_POST['category_slug']) ? $_POST['category_slug'] : '';
        
       // if($category_slug === 'special' && isset($_POST['menu_price']))
        if (($category_slug === 'special' || $category_slug === 'other-cat') && isset($_POST['menu_price'])) 
        {
            $menu_price = $_POST['menu_price'];
            $menu_price_eng = $_POST['menu_price_eng'];
            $menu_price_eus = $_POST['menu_price_eus'];
            $menu_price_fr = $_POST['menu_price_fr'];

            $result = $wpdb->insert($menu_table, array(
                'menu_name' => $menu_name,
                'menu_name_eng' => $menu_name_eng,
                'menu_name_eus' => $menu_name_eus,
                'menu_name_fr' => $menu_name_fr,
                'menu_description' => $menu_description, 
                'category_id' => $category_id,
                'menu_pricing'  => $menu_price,
                'menu_pricing_eng'  => $menu_price_eng,
                'menu_pricing_eus'  => $menu_price_eus,
                'menu_pricing_fr'  => $menu_price_fr
            ));
        }
        else
        {
            $result = $wpdb->insert($menu_table, array(
                'menu_name' => $menu_name, 
                'menu_name_eng' => $menu_name_eng, 
                'menu_name_eus' => $menu_name_eus, 
                'menu_name_fr' => $menu_name_fr, 
                'menu_description' => $menu_description, 
                'category_id' => $category_id
            ));
        }
        
        if ($result === false) 
        {
            $response = array('success' => false , 'error' => $wpdb->last_error);
        } 
        else 
        {
            $response = array('success' => true);
        }
    }
    else
    {
        $response = array('success' => false,  'error' => $wpdb->last_error);
    }

    echo json_encode($response);
    wp_die();
}

/************* update menus *****************/ 
add_action('wp_ajax_update_menus', 'update_menus_callback');
add_action('wp_ajax_nopriv_update_menus', 'update_menus_callback');
function update_menus_callback()
{
    global $wpdb;
    $menu_table = $wpdb->prefix . 'trion_menu_tbl_meta';
    $cat_table_name = $wpdb->prefix . 'trion_category_tbl_meta';

    if(isset($_POST['get_cat_id']) && isset($_POST['update_menu_name']) && isset($_POST['update_menu_des']))
    {
        $get_cat_id = $_POST['get_cat_id'];
        $update_menu_name = $_POST['update_menu_name'];
        $update_menu_name_eng = $_POST['update_menu_name_eng'];
        $update_menu_name_eus = $_POST['update_menu_name_eus'];
        $update_menu_name_fr = $_POST['update_menu_name_fr'];
        $update_menu_des = $_POST['update_menu_des'];   
        $update_menu_id = $_POST['update_menu_id'];  
        
         // Check if the category slug is 'special'
         //$cat_items = $wpdb->get_row($wpdb->prepare("SELECT * FROM $cat_table_name WHERE slug = %s", 'special'));
         $cat_items = $wpdb->get_row($wpdb->prepare("SELECT * FROM $cat_table_name WHERE slug IN (%s, %s, %s)", array('daily','special', 'other-cat')));

        if ($cat_items && isset($_POST['update_menu_price'])) 
        {
             // Category slug is 'special,' and menu price is set, so update menu price
             $update_menu_price = $_POST['update_menu_price'];
             $update_menu_price_eng = $_POST['update_menu_price_eng'];
             $update_menu_price_eus = $_POST['update_menu_price_eus'];
             $update_menu_price_fr = $_POST['update_menu_price_fr'];
             $data = array(
                 'menu_name' => $update_menu_name,
                 'menu_name_eng' => $update_menu_name_eng,
                 'menu_name_eus' => $update_menu_name_eus,
                 'menu_name_fr' => $update_menu_name_fr,
                 'menu_description' => $update_menu_des,
                 'category_id' => $get_cat_id,
                 'menu_pricing' => $update_menu_price,
                 'menu_pricing_eng'  => $update_menu_price_eng,
                 'menu_pricing_eus'  => $update_menu_price_eus,
                 'menu_pricing_fr'  => $update_menu_price_fr
             );
        }
        else 
        {
            // Category slug is not 'special,' or menu price is not set, so update without menu price
             $data = array(
                 'menu_name' => $update_menu_name,
                 'menu_name_eng' => $update_menu_name_eng,
                 'menu_name_eus' => $update_menu_name_eus,
                 'menu_name_fr' => $update_menu_name_fr,
                 'menu_description' => $update_menu_des,
                 'category_id' => $get_cat_id,
             );
        } 

        $where = array('id' => $update_menu_id);

        // Perform the update
        $result = $wpdb->update($menu_table, $data, $where);

        if ($result !== false) 
        {
            $response = array('success' => true);
        } 
        else
        {
            // Update failed
            $response = array('success' => false, 'error' => $wpdb->last_error);
        }
    }
    else
    {
        $response = array('success' => false, 'error' => 'Missing data');
    }

    echo json_encode($response);
    wp_die();
}
/************* delete menus ****************/ 
add_action('wp_ajax_delete_menus', 'delete_menus_callback');
add_action('wp_ajax_nopriv_delete_menus', 'delete_menus_callback');

function delete_menus_callback()
{
    global $wpdb;
    $menu_table = $wpdb->prefix . 'trion_menu_tbl_meta';
    
    if(isset($_POST['menu_id']))
    {
        $menu_id = $_POST['menu_id'];
        $where = array('id' => $menu_id);
        $result = $wpdb->delete($menu_table, $where);
        if ($result !== false)
        {
            $response = array('success' => true);
        }
        else
        {
            // Delete failed
            $response = array('success' => false, 'error' => $wpdb->last_error);
        }
    }
    else
    {
        $response = array('success' => false, 'error' => 'Missing data');
    }

    // Send JSON response
    echo json_encode($response);
    wp_die();
     
}

/*************** upadte main dish status *******************/ 
add_action('wp_ajax_update_dish_status', 'update_dish_status_callback');
add_action('wp_ajax_nopriv_update_dish_status', 'update_dish_status_callback');
function update_dish_status_callback()
{
    global $wpdb;
    $dish_table = $wpdb->prefix . 'trion_dish_tbl_meta';

    if(isset($_POST['dishId']) && isset($_POST['isChecked']))
    {
        $dishId = $_POST['dishId'];
        $isChecked = $_POST['isChecked'];  
        
        // Create data array for updating
        $data = array(
            'dish_status' => $isChecked
        );
        $where = array('id' => $dishId);

        // Perform the update
        $result = $wpdb->update($dish_table, $data, $where);

        if ($result !== false) 
        {
            $response = array('success' => true);
        } 
        else
        {
            // Update failed
            $response = array('success' => false, 'error' => $wpdb->last_error);
        }
    }
    else
    {
        $response = array('success' => false, 'error' => 'Missing data');
    }

    echo json_encode($response);
    wp_die();
}

/*************** upadte daily dish status *******************/ 
add_action('wp_ajax_update_daily_dish_status', 'update_daily_dish_status_callback');
add_action('wp_ajax_nopriv_update_daily_dish_status', 'update_daily_dish_status_callback');
function update_daily_dish_status_callback()
{
    global $wpdb;
    $dish_table = $wpdb->prefix . 'trion_dish_tbl_meta';

    if(isset($_POST['dishId']) && isset($_POST['isChecked']))
    {
        $dishId = $_POST['dishId'];
        $isChecked = $_POST['isChecked'];  
        
        // Create data array for updating
        $data = array(
            'daily_dish_status' => $isChecked
        );
        $where = array('id' => $dishId);

        // Perform the update
        $result = $wpdb->update($dish_table, $data, $where);

        if ($result !== false) 
        {
            $response = array('success' => true);
        } 
        else
        {
            // Update failed
            $response = array('success' => false, 'error' => $wpdb->last_error);
        }
    }
    else
    {
        $response = array('success' => false, 'error' => 'Missing data');
    }

    echo json_encode($response);
    wp_die();
}

/******************** insert services ************************/ 
add_action('wp_ajax_insert_services', 'insert_services_callback');
add_action('wp_ajax_nopriv_insert_services', 'insert_services_callback');
function insert_services_callback()
{
    global $wpdb; 
    $service_table = $wpdb->prefix . 'trion_service_tbl_meta';

    if(isset($_POST['service_description']))
    {
        $service_name = $_POST['service_name'];
        $service_name_eng = $_POST['service_name_eng'];
        $service_name_eus = $_POST['service_name_eus'];
        $service_name_fr = $_POST['service_name_fr'];
        $service_description = $_POST['service_description'];

        $result = $wpdb->insert($service_table, array(
            'service_name' => $service_name,
            'service_name_eng' => $service_name_eng,
            'service_name_eus' => $service_name_eus,
            'service_name_fr' => $service_name_fr, 
            'service_description' => $service_description
        ));

        if ($result === false) 
        {
            $response = array('success' => false , 'error' => $wpdb->last_error);
        } 
        else 
        {
            $response = array('success' => true);
        }
    }
    else
    {
        $response = array('success' => false,  'error' => $wpdb->last_error);
    }

    echo json_encode($response);
    wp_die();
}

/************* Edit special service *********************/ 
add_action('wp_ajax_edit_service_modal', 'edit_service_modal_callback');
add_action('wp_ajax_nopriv_edit_service_modal', 'edit_service_modal_callback');
function edit_service_modal_callback()
{
    global $wpdb; 
    $service_table = $wpdb->prefix . 'trion_special_menu_service_tbl_meta';

    if(isset($_POST['service_id']))
    {
        $service_id = $_POST['service_id'];

        $service_query = $wpdb->get_results("SELECT * FROM $service_table WHERE id = '" . $service_id . "'", ARRAY_A);

        foreach($service_query as $service_data)
        {
            $data = array(
                            'id' => $service_data['id'],
                            'service_name' => $service_data['service_name'],
                            'service_name_eng' => $service_data['service_name_eng'],
                            'service_name_eus' => $service_data['service_name_eus'],
                            'service_name_fr' => $service_data['service_name_fr'],
                            'service_description' => $service_data['service_description'],
                            'service_pricing' => $service_data['service_pricing'],
                            'parent_menu' => $service_data['parent_menu'],
            );
                         
            $response = array('status' => 'success' , 'data' => $data);
        }
        echo json_encode($response);
        wp_die();

    }
}

/************* Update service *********************/ 
add_action('wp_ajax_update_service_modal', 'update_service_modal_callback');
add_action('wp_ajax_nopriv_update_service_modal', 'update_service_modal_callback');

function update_service_modal_callback()
{
    global $wpdb; 
    $service_table = $wpdb->prefix . 'trion_service_tbl_meta';
    $special_menu_service_table = $wpdb->prefix . 'trion_special_menu_service_tbl_meta';
    $main_menu_service_table = $wpdb->prefix . 'trion_main_menu_service_tbl_meta';
    $daily_menu_service_table = $wpdb->prefix . 'trion_daily_menu_service_tbl_meta';

    if(isset($_POST['service_id']) && isset($_POST['update_service_des']))
    {
        $service_id = $_POST['service_id'];
        $update_service_name = sanitize_text_field($_POST['update_service_name']);
        $update_service_name_eng = sanitize_text_field($_POST['update_service_name_eng']);
        $update_service_name_eus = sanitize_text_field($_POST['update_service_name_eus']);
        $update_service_name_fr =sanitize_text_field( $_POST['update_service_name_fr']);

        $update_service_des = sanitize_textarea_field($_POST['update_service_des']);

        // Update data in the database
        $wpdb->update(
            $service_table,
            array(
                'service_name' => $update_service_name,
                'service_name_eng' => $update_service_name_eng,
                'service_name_eus' => $update_service_name_eus,
                'service_name_fr' => $update_service_name_fr,
                'service_description' => $update_service_des
            ),
            array('id' => $service_id),
        );

        // Check if the id exists in the wp_trion_special_menu_service_tbl_meta table
        $special_menu_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $special_menu_service_table WHERE id = %d", $service_id));

        if ($special_menu_exists) 
        {
            // Update data in the wp_trion_special_menu_service_tbl_meta table
            $wpdb->update(
                $special_menu_service_table,
                array(
                    'service_name' => $update_service_name,
                    // Add other fields to update if needed
                ),
                array('id' => $service_id),
            );
        }

        // Check if the id exists in the wp_trion_main_menu_service_tbl_meta table
        $main_menu_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $main_menu_service_table WHERE id = %d", $service_id));

        if ($main_menu_exists) 
        {
            // Update data in the wp_trion_main_menu_service_tbl_meta table
            $wpdb->update(
                $main_menu_service_table,
                array(
                    'service_name' => $update_service_name,
                    // Add other fields to update if needed
                ),
                array('id' => $service_id),
            );
        }

        // Check if the id exists in the wp_trion_daily_menu_service_tbl_meta table
        $daily_menu_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $daily_menu_service_table WHERE id = %d", $service_id));

        if ($daily_menu_exists) 
        {
            // Update data in the wp_trion_daily_menu_service_tbl_meta table
            $wpdb->update(
                $daily_menu_service_table,
                array(
                    'service_name' => $update_service_name,
                    // Add other fields to update if needed
                ),
                array('id' => $service_id),
            );
        }

        // Prepare and send a response
        $response = array('status' => true, 'message' => 'Service updated successfully');
        wp_send_json($response);
    }
    else 
    {
        // Prepare and send an error response
        $response = array('status' => false, 'message' => 'Invalid data received');
        wp_send_json($response);
    }
    wp_die();
}



   
/********************** Delete Service *********************/ 
add_action('wp_ajax_delete_service', 'delete_service_callback');
add_action('wp_ajax_nopriv_delete_service', 'delete_service_callback');
function delete_service_callback()
{
    global $wpdb; 
    $service_table = $wpdb->prefix . 'trion_service_tbl_meta';

    if(isset($_POST['service_id']))
    {
        $service_id = $_POST['service_id'];
        $wpdb->delete(
            $service_table,
            array('id' => $service_id),
        );
        if ($wpdb->rows_affected > 0) 
        {
            $response = array('status' => true, 'message' => 'Service deleted successfully');
        } 
        else 
        {
            $response = array('status' => false, 'message' => 'Failed to delete service');
        }

        wp_send_json($response);
    }
    else 
    {
        $response = array('status' => false, 'message' => 'Invalid data received');
        wp_send_json($response);
    }
       
    wp_die();
}

/********************** Delete special  Service *********************/ 
add_action('wp_ajax_delete_service_special', 'delete_service_special_callback');
add_action('wp_ajax_nopriv_delete_service_special', 'delete_service_special_callback');
function delete_service_special_callback()
{
    global $wpdb; 
    $service_table = $wpdb->prefix . 'trion_special_menu_service_tbl_meta';

    if(isset($_POST['service_id']))
    {
        $service_id = $_POST['service_id'];
        $wpdb->delete(
            $service_table,
            array('id' => $service_id),
        );
        if ($wpdb->rows_affected > 0) 
        {
            $response = array('status' => true, 'message' => 'Service deleted successfully');
        } 
        else 
        {
            $response = array('status' => false, 'message' => 'Failed to delete service');
        }

        wp_send_json($response);
    }
    else 
    {
        $response = array('status' => false, 'message' => 'Invalid data received');
        wp_send_json($response);
    }
       
    wp_die();
}


/***************** insert/update special services ****************/ 
/***************** insert/update special services ****************/ 
add_action('wp_ajax_insert_special_service_dishes', 'insert_special_service_dishes_callback');
add_action('wp_ajax_nopriv_insert_special_service_dishes', 'insert_special_service_dishes_callback');
function insert_special_service_dishes_callback()
{
    global $wpdb;

    $message = '';

    if(isset($_POST['special_dish_id']) && isset( $_POST['special_service_id']) && isset( $_POST['isChecked']) && isset($_POST['special_menu_id']))
    {
        $special_dish_id = $_POST['special_dish_id'];
        $special_service_id = $_POST['special_service_id'];
        $special_menu_id = $_POST['special_menu_id'];
        $isChecked = $_POST['isChecked'];
        $special_dish_meta_key = '_dish';
        
        $table_name = $wpdb->prefix . 'trion_special_service_tbl_dish_meta'; 
        $service_table = $wpdb->prefix . 'trion_service_tbl_meta'; 

        // $result = $wpdb->get_row(
        //     $wpdb->prepare(
        //         "select * from $table_name WHERE dish_meta_value = $special_dish_id AND service_id = $special_service_id AND special_menu_id = $special_menu_id",
        //     ), ARRAY_A
        // );

        $error_response = '';
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE dish_meta_value = %d AND service_id = %d AND special_menu_id = %d",
                $special_dish_id,
                $special_service_id,
                $special_menu_id
            ),
            ARRAY_A
        );

        if($result)
        {

            $special_menu_dish_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE service_id = $special_service_id AND dish_status = 'true'");

            $service_name = $wpdb->get_var("SELECT service_name FROM $service_table WHERE id = $special_service_id");

            $error_response = limit_dishes_special($special_menu_dish_count, $isChecked, $special_service_id);

            if($error_response === 'limiterror_extra')
            {
                $isChecked = 'false';
                $message  = 'Please remove the dish if you want to add another dish because this service dishes limit is exceed.';
            }
            else if($error_response === 'limiterror_add')
            {
                $message  = 'Please add the dish because this service dishes limit is not fulfill.';
            }
            else
            {
                $error_response = 'limitsuccess';
                $message  = 'Dishes limit is fullfilled for this service.';
            }
            
            $wpdb->update(
                $table_name,
                array('dish_status' => $isChecked,),
                array('id' => $result['dish_meta_value']),
            );
        }
        else
        {
            $data = array(
                'special_menu_id' => $special_menu_id,
                'service_id' => $special_service_id,
                'dish_meta_key' => $special_dish_meta_key,
                'dish_meta_value' => $special_dish_id,
                'dish_status' => 'false',
            );
            // $wpdb->insert($table_name, $data, $format);
            $wpdb->insert($table_name, $data);
        }

        $response = array('success' => $error_response, 'message' => $message);
        wp_send_json($response);
        wp_die();
    }
}

function limit_dishes_special($count, $isChecked, $special_service_id) 
{
    $service_name = trim($special_service_id);
    $limits = array(
        '8' => array('min' => 4, 'max' => 5),
        '5' => array('min' => 17, 'max' => 18),
        '10' => array('min' => 4, 'max' => 5),
    );

    if ($isChecked == 'true') 
    {
        if ($count >= $limits[$service_name]['max']) 
        {
            return 'limiterror_extra';
        }
        else if($count < $limits[$service_name]['min'])
        {
            return 'limiterror_add';
        }
    } 
    else 
    {
        if ($count <= $limits[$service_name]['min'] + 1) 
        {
            return 'limiterror_add';
        }
    }

    return 'limitsuccess';
}    

/******************** insert services dishes ***********************/ 
add_action('wp_ajax_insert_service_dishes', 'insert_service_dishes_callback');
add_action('wp_ajax_nopriv_insert_service_dishes', 'insert_service_dishes_callback');
function insert_service_dishes_callback()
{
    global $wpdb;
    if(isset($_POST['dish_id']) && isset( $_POST['service_id']))
    {
        $dish_id = $_POST['dish_id'];
        $service_id = $_POST['service_id'];
        $dish_meta_key = '_dish';
    
        $table_name = $wpdb->prefix . 'trion_special_service_tbl_dish_meta'; 
        $data = array(
            'service_id' => $service_id,
            'dish_meta_key' => $dish_meta_key,
            'dish_meta_value' => $dish_id,
            'dish_status' => 'true',
        );
        $wpdb->insert($table_name, $data, $format);
    
        // Check if the insertion was successful
        if ($wpdb->last_error) 
        {
            $response = array('success' => false, 'message' => 'Error inserting data into the table.');
        } 
        else 
        {
            $response = array('success' => true, 'message' => 'Data inserted successfully.');
        }
    
        // Send JSON response
        wp_send_json($response);
    
       wp_die();
    }
}

/************************* update services dishes ********************/ 
add_action('wp_ajax_update_service_dishes', 'update_service_dishes_callback');
add_action('wp_ajax_nopriv_update_service_dishes', 'update_service_dishes_callback');
function update_service_dishes_callback()
{
    global $wpdb;
    $table_name1 = $wpdb->prefix . 'trion_special_service_tbl_dish_meta'; 
    
    if(isset($_POST['dish_id']) && isset( $_POST['service_id']))
    {
        $dish_id = $_POST['dish_id'];
        $service_id = $_POST['service_id'];

        $result = $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name1 SET dish_status = 'false' WHERE dish_meta_value ='$dish_id' AND service_id = '$service_id'",
            )
        );

        if ($result) 
        {
            $response = array('status' => 'success', 'message' => 'Dish updated successfully');
        } 
        else 
        {
            $response = array('status' => 'error', 'message' => 'Unable to update dish');
        }
    } 
    else 
    {
        $response = array('status' => 'error', 'message' => 'Unable to update dish');
    }

    wp_send_json($response);
    wp_die();
}

/******************** insert other nenu dishes ***********************/ 
// add_action('wp_ajax_insert_other_menu_dishes', 'insert_other_menu_dishes_callback');
// add_action('wp_ajax_nopriv_insert_other_menu_dishes', 'insert_other_menu_dishes_callback');
// function insert_other_menu_dishes_callback()
// {
//     global $wpdb;
//     if(isset($_POST['other_menu_id']) && isset( $_POST['other_dish_id']))
//     {
//         $other_menu_id = $_POST['other_menu_id'];
//         $other_dish_id = $_POST['other_dish_id'];
//         $other_dish_meta_key = '_other_dish';
    
//         $table_name = $wpdb->prefix . 'trion_other_menu_dish_meta'; 

//          // Check if the combination of other_menu_id and other_dish_id already exists
//          $existing_data = $wpdb->get_row(
//             $wpdb->prepare(
//                 "SELECT * FROM $table_name WHERE other_menu_id = %d AND other_dish_meta_value = %d",
//                 $other_menu_id,
//                 $other_dish_id
//             ),
//             ARRAY_A
//         );

//         if ($existing_data) 
//         {
//             // If the combination already exists, update the other_dish_status to 'true'
//             $result = $wpdb->update(
//                 $table_name,
//                 array('other_dish_status' => 'true'),
//                 array('id' => $existing_data['id'])
//             );

//             if ($result !== false) {
//                 $response = array('success' => true, 'message' => 'Data updated successfully.');
//             } else {
//                 $response = array('success' => false, 'message' => 'Error updating data.');
//             }
//         } 
//         else 
//         {
//             // If the combination doesn't exist, insert a new record
//             $data = array(
//                 'other_menu_id' => $other_menu_id,
//                 'other_dish_meta_key' => $other_dish_meta_key,
//                 'other_dish_meta_value' => $other_dish_id,
//                 'other_dish_status' => 'true',
//             );
//             $wpdb->insert($table_name, $data);

//             if ($wpdb->last_error) {
//                 $response = array('success' => false, 'message' => 'Error inserting data into the table.');
//             } else {
//                 $response = array('success' => true, 'message' => 'Data inserted successfully.');
//             }
//         }

//         // Send JSON response
//         wp_send_json($response);
//         wp_die();
//     }
// }


/***************** insert other menu dishes ************************/
add_action('wp_ajax_insert_other_menu_dishes', 'insert_other_menu_dishes_callback');
add_action('wp_ajax_nopriv_insert_other_menu_dishes', 'insert_other_menu_dishes_callback');
function insert_other_menu_dishes_callback()
{
    global $wpdb;
    if(isset($_POST['other_menu_id']) && isset($_POST['other_dish_id']))
    {
        $other_menu_id = $_POST['other_menu_id'];
        $other_dish_id = $_POST['other_dish_id'];
        $other_dish_meta_key = '_other_dish';
    
        $table_name = $wpdb->prefix . 'trion_other_menu_dish_meta'; 

        // Check the count of dishes for the other menu
        $menu_dish_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE other_menu_id = %d AND other_dish_status = 'true'",
                $other_menu_id
            )
        );

        // Check if the count exceeds the limit (5)
        if ($menu_dish_count >= 5) {
            $response = array('success' => false, 'message' => 'Maximum limit of dishes reached for this menu.');
            wp_send_json($response);
            wp_die();
        }

        // Check if the combination of other_menu_id and other_dish_id already exists
        $existing_data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE other_menu_id = %d AND other_dish_meta_value = %d",
                $other_menu_id,
                $other_dish_id
            ),
            ARRAY_A
        );

        if ($existing_data) 
        {
            // If the combination already exists, update the other_dish_status to 'true'
            $result = $wpdb->update(
                $table_name,
                array('other_dish_status' => 'true'),
                array('id' => $existing_data['id'])
            );

            if ($result !== false) {
                $response = array('success' => true, 'message' => 'Data updated successfully.');
            } else {
                $response = array('success' => false, 'message' => 'Error updating data.');
            }
        } 
        else 
        {
            // If the combination doesn't exist, insert a new record
            $data = array(
                'other_menu_id' => $other_menu_id,
                'other_dish_meta_key' => $other_dish_meta_key,
                'other_dish_meta_value' => $other_dish_id,
                'other_dish_status' => 'true',
            );
            $wpdb->insert($table_name, $data);

            if ($wpdb->last_error) {
                $response = array('success' => false, 'message' => 'Error inserting data into the table.');
            } else {
                $response = array('success' => true, 'message' => 'Data inserted successfully.');
            }
        }

        // Send JSON response
        wp_send_json($response);
        wp_die();
    }
}



/******************* update other menu dish meta **************************/ 
add_action('wp_ajax_update_other_menu_meta_dishes', 'update_other_menu_meta_dishes_callback');
add_action('wp_ajax_nopriv_update_other_menu_meta_dishes', 'update_other_menu_meta_dishes_callback');
function update_other_menu_meta_dishes_callback()
{
    global $wpdb;
    $table_name1 = $wpdb->prefix . 'trion_other_menu_dish_meta'; 
    
    if(isset($_POST['other_menu_id']) && isset($_POST['other_dish_id']))
    {
        $other_dish_id = $_POST['other_dish_id'];
        $other_menu_id = $_POST['other_menu_id'];

        $result = $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name1 SET other_dish_status = 'false' WHERE other_dish_meta_value = '$other_dish_id' AND other_menu_id = '$other_menu_id'",
            )
        );

        if ($result) 
        {
            $response = array('status' => 'success', 'message' => 'Dish updated successfully');
        } 
        else 
        {
            $response = array('status' => 'error', 'message' => 'Unable to update dish');
        }
    } 
    else 
    {
        $response = array('status' => 'error', 'message' => 'Unable to update dish');
    }

    wp_send_json($response);
    wp_die();
}

/******************** get menu data **************************/ 
add_action('wp_ajax_generate_pdf_ajax_action', 'generate_pdf_ajax_action_callback');
add_action('wp_ajax_nopriv_generate_pdf_ajax_action', 'generate_pdf_ajax_action_callback');
function generate_pdf_ajax_action_callback()
{
    global $wpdb;
    $menu_table = $wpdb->prefix . 'trion_menu_tbl_meta'; 
    
    if(isset($_POST['menu_id']))
    {
        $menu_id = $_POST['menu_id'];
        $menu_lang_id = $_POST['menu_lang_id'];

        $response  = trion_menu_craft_plugin_generate_pdf($menu_id, $menu_lang_id);
       // $response  = trion_menu_craft_plugin_generate_pdf($menu_id);
        
        wp_send_json($response);
    }

  wp_die();
}
	 
/********************** Delete pdf data  *********************/ 
add_action('wp_ajax_delete_pdf_data', 'delete_pdf_data_callback');
add_action('wp_ajax_nopriv_delete_pdf_data', 'delete_pdf_data_callback');
function delete_pdf_data_callback()
{
    global $wpdb; 
    $pdf_table = $wpdb->prefix . 'trion_pdf_tbl_data';

    if (isset($_POST['pdf_id'])) 
    {
        $pdf_id = $_POST['pdf_id'];
        $pdf_data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $pdf_table WHERE id = %d",
                $pdf_id
            ),
            ARRAY_A
        );

        if ($pdf_data) 
        {
            // Get the PDF file path from the database
            $pdfFilePath = $pdf_data['pdf_path'];

            // Delete the database entry
            $wpdb->delete(
                $pdf_table,
                array('id' => $pdf_id)
            );

            if ($wpdb->rows_affected > 0)
            {
                if (file_exists($pdfFilePath) && unlink($pdfFilePath)) 
                {
                    $response = array('status' => 'success', 'message' => 'PDF and database entry deleted successfully');
                } 
                else 
                {
                    $response = array('status' => 'success', 'message' => 'Database entry deleted, but PDF file not found or unable to delete');
                }
            } 
            else 
            {
                $response = array('status' => 'error', 'message' => 'Failed to delete database entry');
            }
        } 
        else 
        {
            $response = array('status' => 'error', 'message' => 'PDF data not found in the database');
        }

        wp_send_json($response);
    } 
    else 
    {
        $response = array('status' => 'error', 'message' => 'Invalid data received');
        wp_send_json($response);
    }

       
    wp_die();
}

/******************** drag and drop services save *************************/ 
add_action('wp_ajax_save_drag_drop', 'save_drag_drop_callback');
add_action('wp_ajax_nopriv_save_drag_drop', 'save_drag_drop_callback');
function save_drag_drop_callback()
{
    global $wpdb;
    $main_menu_ser_table = $wpdb->prefix . 'trion_main_menu_service_tbl_meta'; 
    $daily_menu_ser_table = $wpdb->prefix . 'trion_daily_menu_service_tbl_meta'; 
    $special_menu_service_tbl = $wpdb->prefix . 'trion_special_menu_service_tbl_meta'; 
    $special_menu_ser_table = $wpdb->prefix . 'trion_service_tbl_meta'; 
    $menu_table = $wpdb->prefix . 'trion_menu_tbl_meta'; 
    $cat_table = $wpdb->prefix . 'trion_category_tbl_meta';


    if(isset($_POST['main_menuId']) && isset($_POST['dataMenuIdArray']))
    {
        $main_menuId = $_POST['main_menuId'];
        $dataMenuIdArray = $_POST['dataMenuIdArray'];

        /********* check menu ******************/ 
        $query_menu_table = $wpdb->get_results("SELECT * FROM $menu_table WHERE id = " . $main_menuId, ARRAY_A);

        foreach($query_menu_table as $menu_data)
        {
            $cat_id = $menu_data['category_id'];

            $query_cat_table = $wpdb->get_row("SELECT slug FROM $cat_table WHERE id = " . $cat_id, ARRAY_A);

            $cat_slug = $query_cat_table['slug'];

           if($cat_slug == 'main')
           {
                foreach($dataMenuIdArray as $key => $value)
                {
                    /************ update order *****************/ 
                    $main_menu_ser_query =  $wpdb->query($wpdb->prepare("UPDATE $main_menu_ser_table SET main_service_ordering = '$key' WHERE id = '$value'"));

                    if ($main_menu_ser_query) 
                    {
                        $response = array('status' => 'success', 'message' => 'Service order updated successfully');
                    } 
                    else 
                    {
                        $response = array('status' => 'error', 'message' => 'Unable to update service order');
                    }
                }
           }
           if($cat_slug == 'daily')
           {
                foreach($dataMenuIdArray as $key => $value)
                {
                    /************ update order *****************/ 
                    $daily_menu_ser_query =  $wpdb->query($wpdb->prepare("UPDATE $daily_menu_ser_table SET daily_service_ordering = '$key' WHERE id = '$value'"));

                    if ($daily_menu_ser_query) 
                    {
                        $response = array('status' => 'success', 'message' => 'Service order updated successfully');
                    } 
                    else 
                    {
                        $response = array('status' => 'error', 'message' => 'Unable to update service order');
                    }
                }
           }
           if($cat_slug == 'special')
           {
                foreach($dataMenuIdArray as $key => $value)
                {
                    /************ update order *****************/ 
                    $special_menu_ser_query =  $wpdb->query($wpdb->prepare("UPDATE $special_menu_service_tbl SET specail_service_ordering = '$key' WHERE id = '$value'"));

                    if ($special_menu_ser_query) 
                    {
                        $response = array('status' => 'success', 'message' => 'Service order updated successfully');
                    } 
                    else 
                    {
                        $response = array('status' => 'error', 'message' => 'Unable to update service order');
                    }
                }
           }
            
        }

        wp_send_json($response);
        wp_die();
    }
}

/******************** drag and drop dishes save *************************/ 
add_action('wp_ajax_save_dish_drag_drop', 'save_dish_drag_drop_callback');
add_action('wp_ajax_nopriv_save_dish_drag_drop', 'save_dish_drag_drop_callback');
function save_dish_drag_drop_callback()
{
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    global $wpdb;
    $main_menu_dish_table = $wpdb->prefix . 'trion_main_service_tbl_dish_meta'; 
    $daily_menu_dish_table = $wpdb->prefix . 'trion_daily_service_tbl_dish_meta'; 
    $special_menu_dish_table = $wpdb->prefix . 'trion_special_service_tbl_dish_meta'; 
    $ser_table = $wpdb->prefix . 'trion_service_tbl_meta'; 
    $menu_table = $wpdb->prefix . 'trion_menu_tbl_meta'; 
    $cat_table = $wpdb->prefix . 'trion_category_tbl_meta';

    if(isset($_POST['main_menuId']) && isset($_POST['main_menu_serviceId']) && isset($_POST['dataDishIdArray']))
    {
        $main_menuId         = $_POST['main_menuId'];
        $main_menu_serviceId = $_POST['main_menu_serviceId'];
        $dataDishIdArray     = $_POST['dataDishIdArray'];

         /********* check menu ******************/ 
        $query_menu_table = $wpdb->get_results("SELECT * FROM $menu_table WHERE id = " . $main_menuId, ARRAY_A);
        foreach($query_menu_table as $menu_data)
        {
            $cat_id = $menu_data['category_id'];
            $query_cat_table = $wpdb->get_row("SELECT slug FROM $cat_table WHERE id = " . $cat_id, ARRAY_A);
            $cat_slug = $query_cat_table['slug'];

            $get_service_query = $wpdb->get_results("SELECT * FROM $ser_table WHERE id = " . $main_menu_serviceId, ARRAY_A);
            foreach($get_service_query as $ser_data)
            {
               $service_name = $ser_data['service_name'];

               if($cat_slug == 'main' && $service_name)
               {
                    foreach($dataDishIdArray as $key => $value)
                    {   
                        $key = $key + 1;
                        /************ update order *****************/ 
                        $main_menu_dish_query =  $wpdb->query($wpdb->prepare("UPDATE $main_menu_dish_table SET main_dish_ordering = '$key' WHERE main_dish_meta_value = '$value' AND main_service_id = '$main_menu_serviceId'"));

                        if ($main_menu_dish_query) 
                        {
                            $response = array('status' => 'success', 'message' => 'Dish order updated successfully');
                        } 
                        else 
                        {
                            $response = array('status' => 'error', 'message' => 'Unable to update dish order');
                        }
                    }
                   
                }
                if($cat_slug == 'daily' && $service_name)
                {
                    foreach($dataDishIdArray as $key => $value)
                    {
                        $key = $key + 1;
                        /************ update order *****************/ 
                        $daily_menu_dish_query =  $wpdb->query($wpdb->prepare("UPDATE $daily_menu_dish_table SET daily_dish_ordering = '$key' WHERE daily_dish_meta_value = '$value' AND daily_service_id = '$main_menu_serviceId'"));

                        if ($daily_menu_dish_query) 
                        {
                            $response = array('status' => 'success', 'message' => 'Dish order updated successfully');
                        } 
                        else 
                        {
                            $response = array('status' => 'error', 'message' => 'Unable to update dish order');
                        }
                    }
                   
                }
                if($cat_slug == 'special' && $service_name)
                {
                    foreach($dataDishIdArray as $key => $value)
                    {
                        /************ update order *****************/ 
                        $special_menu_dish_query =  $wpdb->query($wpdb->prepare("UPDATE $special_menu_dish_table SET special_dish_ordering = '$key' WHERE dish_meta_value = '$value' AND service_id = '$main_menu_serviceId'"));

                        if ($special_menu_dish_query) 
                        {
                            $response = array('status' => 'success', 'message' => 'Dish order updated successfully');
                        } 
                        else 
                        {
                            $response = array('status' => 'error', 'message' => 'Unable to update dish order');
                        }
                    }
                   
                }
           
            } 
        }

        wp_send_json($response);
        wp_die();
    }
}

/******************** drag and drop other dishes save *************************/ 
add_action('wp_ajax_save_drag_drop_other_dish', 'save_drag_drop_other_dish_callback');
add_action('wp_ajax_nopriv_save_drag_drop_other_dish', 'save_drag_drop_other_dish_callback');
function save_drag_drop_other_dish_callback()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    global $wpdb;
    $main_menu_dish_table = $wpdb->prefix . 'trion_main_service_tbl_dish_meta'; 
    $daily_menu_dish_table = $wpdb->prefix . 'trion_daily_service_tbl_dish_meta'; 
    $special_menu_dish_table = $wpdb->prefix . 'trion_special_service_tbl_dish_meta'; 
    $ser_table = $wpdb->prefix . 'trion_service_tbl_meta'; 
    $menu_table = $wpdb->prefix . 'trion_menu_tbl_meta'; 
    $cat_table = $wpdb->prefix . 'trion_category_tbl_meta';
    $other_menu_dish_meta = $wpdb->prefix . 'trion_other_menu_dish_meta';

    $response = array(); // Initialize the response array


    if(isset($_POST['main_menuId']) && isset($_POST['dataMenuIdArray']))
    {
        $main_menuId         = $_POST['main_menuId'];
        $dataMenuIdArray = $_POST['dataMenuIdArray'];

         /********* check menu ******************/ 
        $query_menu_table = $wpdb->get_results("SELECT * FROM $menu_table WHERE id = " . $main_menuId, ARRAY_A);
        foreach($query_menu_table as $menu_data)
        {
            $cat_id = $menu_data['category_id'];
            $query_cat_table = $wpdb->get_row("SELECT slug FROM $cat_table WHERE id = " . $cat_id, ARRAY_A);
            $cat_slug = $query_cat_table['slug'];

            if($cat_slug == 'other-cat')
            {
                foreach($dataMenuIdArray as $key => $value)
                {
                    /************ update order *****************/ 
                    $main_menu_dish_query =  $wpdb->query($wpdb->prepare("UPDATE $other_menu_dish_meta SET other_dish_ordering = '$key' WHERE other_dish_meta_value = '$value' AND other_menu_id = '$main_menuId'"));

                    if ($main_menu_dish_query) 
                    {
                        $response = array('status' => 'success', 'message' => 'Dish order updated successfully');
                    } 
                    else 
                    {
                        $response = array('status' => 'error', 'message' => 'Unable to update dish order');
                    }
                }

                wp_send_json($response);
                wp_die();
                
            }
        
        }

        // wp_send_json($response);
        // wp_die();
    }
}


	