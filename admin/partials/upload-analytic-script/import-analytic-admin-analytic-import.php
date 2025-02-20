<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

global $wpdb;

$dirPath = __DIR__;

require_once $dirPath . '/Classes/PHPExcel.php';
require_once $dirPath . '/Classes/PHPExcel/IOFactory.php';

$table_name = $wpdb->prefix . 'moove_activity_log';


if (isset($_POST["import"]) == 'submit') 
{
    if ($_FILES["excel_file"]) 
    {
        $excelFilePath = $_FILES["excel_file"]["tmp_name"];
    
        $allowedFileTypes = array(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/xml',
            'text/csv'
        );
    
        if (in_array($_FILES["excel_file"]["type"], $allowedFileTypes)) 
        {
            try 
            {
                $spreadsheet = PHPExcel_IOFactory::load($excelFilePath);
                
                if ($_FILES["excel_file"]["type"] === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') 
                {
                    $objWorksheet = $spreadsheet->getActiveSheet();
                    $data = array(); // Initialize the $data array
    
                    $skipHeader = true;
                    foreach ($objWorksheet->getRowIterator() as $row) 
                    {
                        if ($skipHeader) 
                        {
                            $skipHeader = false;
                            continue;
                        }
                        $rowData = [];
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);
                        foreach ($cellIterator as $cell) {
                            $rowData[] = $cell->getValue();
                        }
                        // Append the rowData to the data array
                        $data[] = $rowData;
                    }

                    /************* batches *****************/ 
                    $batchSize = 100;
                    $totalRows = count($data);
                    
                    for ($i = 0; $i < $totalRows; $i += $batchSize) 
                    {
                        $batchData = array_slice($data, $i, $batchSize);
                        $counter_check = 0;
                        foreach($batchData as $sheetItem)
                        {
                            $dish_table = $wpdb->prefix . 'trion_dish_tbl_meta';
                            $menu_table = $wpdb->prefix . 'trion_menu_tbl_meta';
                            $service_table = $wpdb->prefix . 'trion_service_tbl_meta';
                            $service_meta_table = $wpdb->prefix . 'trion_special_service_tbl_dish_meta';
                            $main_meta_table = $wpdb->prefix . 'trion_main_service_tbl_dish_meta';
                            $daily_meta_table = $wpdb->prefix . 'trion_daily_service_tbl_dish_meta';
                            $other_table = $wpdb->prefix . 'trion_other_menu_dish_meta';


                            /*********** get data from the excel sheet ****************/ 

                            $dish_name_eng = $sheetItem[0];
                            $dish_name_es = $sheetItem[1];
                            $dish_name_eus = $sheetItem[2];
                            $dish_name_fr = $sheetItem[3];
                            $dish_pricing = $sheetItem[4];
                            $dish_description = $sheetItem[5];
                            $dish_main_status = $sheetItem[6];
                            $dish_daily_status = $sheetItem[7];
                            $special_menu_name = $sheetItem[8];
                            $special_menu_price = $sheetItem[9];
                            $service_name = $sheetItem[10];
                            $other_menu_name = $sheetItem[11];
                            $other_menu_price = $sheetItem[12];
                            

                            

                            /************** main menu status **************/ 
                            if($dish_main_status == 1)
                            {
                                $dish_main_status = 'true';
                                }
                                else
                                {
                                $dish_main_status = 'false';
                            }

                            /************** daily menu  status **************/ 

                            if($dish_daily_status == 1)
                            {
                                $dish_daily_status = 'true';
                                }
                                else
                                {
                                $dish_daily_status = 'false';
                            }

                            /******************** DISh TABLE DATA ****************************/ 
                            $service_name = trim($service_name);
                            $service_name_array = explode(',', $service_name);
                            $service_name_array = array_unique(array_map('trim', $service_name_array)); // Remove duplicates and trim whitespace

                            $service_ids = array();

                            foreach($service_name_array as $ser_names)
                            {
                               $services_name = trim($ser_names);

                               if(!empty($services_name))
                               {
                                    // Check if the dish with the same name exists
                                    $service_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $service_table WHERE service_name = %s", $services_name));

                                    if ($service_id !== null)
                                    {  
                                        
                                    } 
                                    else 
                                    {
                                        // Service with the same name doesn't exist, so insert a new row
                                        $service_insert_update['service_name'] = $services_name; 
                                        $wpdb->insert($service_table, $service_insert_update);
                                        $service_id = $wpdb->insert_id;
                                    }

                                        // Store the service IDs in an array
                                        $service_ids[] = array('service_id' => $service_id);

                                }
                        
                            }

                            

                            /********* serialized service ids *************/ 
                            $json_service_ids = json_encode($service_ids);
                            $escaped_json_service_ids = addslashes($json_service_ids);
                            $serialized_service_ids = 's:' . strlen($escaped_json_service_ids) . ':"' . $escaped_json_service_ids . '";';

                            // Define the data to be inserted or updated
                            $data_to_insert_update = array(
                                'dish_name_es' => $dish_name_es,
                                'dish_name_eus' => $dish_name_eus,
                                'dish_name_fr' => $dish_name_fr,
                                'dish_description' => $dish_description,
                                'dish_pricing' => $dish_pricing,
                                'parent_service' => $serialized_service_ids,
                                'dish_status' => $dish_main_status,
                                'daily_dish_status' => $dish_daily_status,
                            );
                            

                            // Check if the dish with the same name exists
                            $dish_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $dish_table WHERE dish_name_eng = %s", $dish_name_eng));

                            // echo'dish_id_______________________>' .$dish_id;
                            // echo'<br>';

                            if ($dish_id !== null) 
                            {
                                // Dish with the same name exists, so update the existing row
                                $wpdb->update($dish_table, $data_to_insert_update, array('id' => $dish_id));
                            } 
                            else 
                            {
                                $data_to_insert_dishes = array(
                                    'dish_name_eng' => $dish_name_eng,
                                    'dish_name_es' => $dish_name_es,
                                    'dish_name_eus' => $dish_name_eus,
                                    'dish_name_fr' => $dish_name_fr,
                                    'dish_description' => $dish_description,
                                    'dish_pricing' => $dish_pricing,
                                    'parent_service' => $serialized_service_ids,
                                    'dish_status' => $dish_main_status,
                                    'daily_dish_status' => $dish_daily_status,
                                );

                                $wpdb->insert($dish_table, $data_to_insert_dishes);
                                $dish_id = $wpdb->insert_id;

                                // echo'dish_name_______________________>' .$data_to_insert_update['dish_name'];
                                // echo'<br>';
                            }

                            /************* main data / daily data *************/ 
                            foreach($service_ids as $service_idd)
                            {

                                $result_main = $wpdb->get_var($wpdb->prepare("SELECT id FROM $main_meta_table WHERE main_service_id = %d AND main_dish_meta_value = %d", $service_idd['service_id'], $dish_id));

                                if($result_main){
                                    $main_data_update = array(
                                        'main_dish_status' => $dish_main_status,
                                    );
                                    $wpdb->update($main_meta_table, $main_data_update, array('id' => $result_main));
                                    

                                }else{

                                    $main_data_update = array(
                                        'main_service_id' => $service_idd['service_id'],
                                        'main_dish_meta_key' => '_main_dish',
                                        'main_dish_meta_value' => $dish_id,
                                        'main_dish_status' => $dish_main_status,
                                    );

                                    $wpdb->insert($main_meta_table, $main_data_update);
                                }


                                $result_daily = $wpdb->get_var($wpdb->prepare("SELECT id FROM $daily_meta_table WHERE daily_service_id = %d AND daily_dish_meta_value = %d", $service_idd['service_id'], $dish_id));

                                if($result_daily){
                                    $daily_data_update = array(
                                        'daily_dish_status' => $dish_daily_status,
                                    );
                                    $wpdb->update($daily_meta_table, $daily_data_update, array('id' => $result_daily));
                                    

                                }else{

                                    $daily_data_update = array(
                                        'daily_service_id' => $service_idd['service_id'],
                                        'daily_dish_meta_key' => '_daily_dish',
                                        'daily_dish_meta_value' => $dish_id,
                                        'daily_dish_status' => $dish_daily_status,
                                    );

                                    $wpdb->insert($daily_meta_table, $daily_data_update);
                                }

                            }
                            

                            //echo $wpdb->last_query;
                            /****** ok ******/ 

                            /********************** SPECIAL  MENU TABLE DATA  *******************************/ 

                            if($special_menu_price)
                            {
                                 // Define the data to be inserted or updated
                                $special_menu_price_update = array(
                                'menu_pricing' => $special_menu_price,
                                'category_id' => 3
                                );
                            }
                            else
                            {
                                $special_menu_price_update = array(
                                    'category_id' => 3
                                );
                            }
                           

                            if($special_menu_name)
                            {
                                $special_menu_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $menu_table WHERE menu_name = %s", $special_menu_name));
                                
                                if ($special_menu_id !== null) 
                                {
                                    $result = $wpdb->update($menu_table, $special_menu_price_update, array('id' => $special_menu_id));
                                }
                                else
                                {
                                    $special_menu_insert = array(
                                        'menu_name' => $special_menu_name,
                                        'menu_pricing' => $special_menu_price,
                                        'category_id' => 3,
                                    );

                                    $wpdb->insert($menu_table, $special_menu_insert);
                                    $special_menu_id = $wpdb->insert_id;
                                }
                            }
                            
        
                            /********************** SERVICE TABLE *****************************/ 

                            if($special_menu_name && $special_menu_id)
                            {
                                foreach($service_ids as $service_iddd){

                                    $result_special = $wpdb->get_var($wpdb->prepare("SELECT id FROM $service_meta_table WHERE special_menu_id = %d AND service_id = %d AND dish_meta_value = %d", $special_menu_id, $service_iddd['service_id'], $dish_id));

                                    if($result_special){
                                        $service_data_update = array(
                                            'dish_status' => 'true',
                                        );
                                        $wpdb->update($service_meta_table, $service_data_update, array('id' => $result_special));
                                        

                                    }else{

                                        $service_data_update = array(
                                            'special_menu_id' => $special_menu_id,
                                            'service_id' => $service_iddd['service_id'],
                                            'dish_meta_key' => '_dish',
                                            'dish_meta_value' => $dish_id,
                                            'dish_status' => 'true',
                                        );

                                        $wpdb->insert($service_meta_table, $service_data_update);
                                    }

                                }
                            }
                            /**************** Other tables ************** */

                            if($other_menu_name)
                            {
                                $other_menu_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $menu_table WHERE menu_name = %s", $other_menu_name));
                               

                                if($other_menu_price)
                                {
                                    $other_menu_cat_update = array(
                                        'category_id' => 4,
                                        'menu_pricing' => $other_menu_price
                                    );
                                }
                                else
                                {
                                    $other_menu_cat_update = array(
                                        'category_id' => 4
                                    );
                                }
                                
                                if ($other_menu_id !== null) 
                                {
                                    $result = $wpdb->update($menu_table, $other_menu_cat_update, array('id' => $other_menu_id));
                                }
                                else
                                {
                                    $other_menu_insert = array(
                                        'menu_name' => $other_menu_name,
                                        'category_id' => 4,
                                        'menu_pricing' => $other_menu_price
                                    );

                                    $wpdb->insert($menu_table, $other_menu_insert);
                                    $other_menu_id = $wpdb->insert_id;
                                }

                                 // Check if a record with the same `other_menu_id` and `dish_id` exists in the other table.

                                // $result_other = $wpdb->get_var($wpdb->prepare("SELECT id FROM $other_table WHERE other_menu_id = %d  AND dish_meta_value = %d", $other_menu_id, $dish_id));

                                $result_other = $wpdb->get_var(
                                    $wpdb->prepare(
                                        "SELECT id FROM $wpdb->prefix"."trion_other_menu_dish_meta WHERE other_menu_id = %d AND other_dish_meta_value LIKE %s",
                                        $other_menu_id,
                                        '%' . $wpdb->esc_like($dish_id) . '%'
                                    )
                                );

                                if($result_other !== null)
                                {
                                    $other_data_update = array(
                                        'other_dish_status' => 'true',
                                    );
                                    $wpdb->update($other_table, $other_data_update, array('id' => $result_other));

                                }
                                else
                                {
                                    $other_data_insert = array(
                                        'other_menu_id' => $other_menu_id,
                                        'other_dish_meta_key' => '_other_dish',
                                        'other_dish_meta_value' => $dish_id,
                                        'other_dish_status' => 'true',
                                    );

                                    $wpdb->insert($other_table, $other_data_insert);

                                }

                            }
                            
                            $counter_check++;

                        }
                      
                    }
                    ?>
                        <script defer>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    title: 'Thank You',
                                    text: "Sheet Uploaded successfully",
                                    icon: 'Success',
                                    showCancelButton: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        
                                    }
                                });
                            });
                        </script>
                        <?php
                } 
                elseif ($_FILES["excel_file"]["type"] === 'text/xml') 
                {
                    // Handle XML file
                    $objWorksheet = $spreadsheet->getActiveSheet();
                    $data = array(); // Initialize the $data array
    
                    $skipHeader = true;
                    foreach ($objWorksheet->getRowIterator() as $row) 
                    {
                        if ($skipHeader) 
                        {
                            $skipHeader = false;
                            continue;
                        }
                        $rowData = [];
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);
                        foreach ($cellIterator as $cell) {
                            $rowData[] = $cell->getValue();
                        }
                        // Append the rowData to the data array
                        $data[] = $rowData;
                    }

                    /************* batches *****************/ 
                    $batchSize = 100;
                    $totalRows = count($data);
                    
                    for ($i = 0; $i < $totalRows; $i += $batchSize) 
                    {
                        $batchData = array_slice($data, $i, $batchSize);
                        $counter_check = 0;
                        foreach($batchData as $sheetItem)
                        {
                            $dish_table = $wpdb->prefix . 'trion_dish_tbl_meta';
                            $menu_table = $wpdb->prefix . 'trion_menu_tbl_meta';
                            $service_table = $wpdb->prefix . 'trion_service_tbl_meta';
                            $service_meta_table = $wpdb->prefix . 'trion_special_service_tbl_dish_meta';
                            $main_meta_table = $wpdb->prefix . 'trion_main_service_tbl_dish_meta';
                            $daily_meta_table = $wpdb->prefix . 'trion_daily_service_tbl_dish_meta';
                            $other_table = $wpdb->prefix . 'trion_other_menu_dish_meta';


                            /*********** get data from the excel sheet ****************/ 

                            $dish_name_eng = $sheetItem[0];
                            $dish_name_es = $sheetItem[1];
                            $dish_name_eus = $sheetItem[2];
                            $dish_name_fr = $sheetItem[3];
                            $dish_pricing = $sheetItem[4];
                            $dish_description = $sheetItem[5];
                            $dish_main_status = $sheetItem[6];
                            $dish_daily_status = $sheetItem[7];
                            $special_menu_name = $sheetItem[8];
                            $special_menu_price = $sheetItem[9];
                            $service_name = $sheetItem[10];
                            $other_menu_name = $sheetItem[11];
                            $other_menu_price = $sheetItem[12];
                        

                            /************** main menu status **************/ 
                            if($dish_main_status == 1)
                            {
                                $dish_main_status = 'true';
                                }
                                else
                                {
                                $dish_main_status = 'false';
                            }

                            /************** daily menu  status **************/ 

                            if($dish_daily_status == 1)
                            {
                                $dish_daily_status = 'true';
                                }
                                else
                                {
                                $dish_daily_status = 'false';
                            }

                            /******************** DISh TABLE DATA ****************************/ 
                            $service_name = trim($service_name);
                            $service_name_array = explode(',', $service_name);
                            $service_name_array = array_unique(array_map('trim', $service_name_array)); // Remove duplicates and trim whitespace

                            $service_ids = array();

                            foreach($service_name_array as $ser_names)
                            {
                               $services_name = trim($ser_names);

                               if(!empty($services_name))
                               {
                                    // Check if the dish with the same name exists
                                    $service_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $service_table WHERE service_name = %s", $services_name));

                                    if ($service_id !== null)
                                    {  
                                        
                                    } 
                                    else 
                                    {
                                        // Service with the same name doesn't exist, so insert a new row
                                        $service_insert_update['service_name'] = $services_name; 
                                        $wpdb->insert($service_table, $service_insert_update);
                                        $service_id = $wpdb->insert_id;
                                    }

                                        // Store the service IDs in an array
                                        $service_ids[] = array('service_id' => $service_id);

                                }
                        
                            }

                            

                            /********* serialized service ids *************/ 
                            $json_service_ids = json_encode($service_ids);
                            $escaped_json_service_ids = addslashes($json_service_ids);
                            $serialized_service_ids = 's:' . strlen($escaped_json_service_ids) . ':"' . $escaped_json_service_ids . '";';

                            // Define the data to be inserted or updated
                            $data_to_insert_update = array(
                                'dish_name_es' => $dish_name_es,
                                'dish_name_eus' => $dish_name_eus,
                                'dish_name_fr' => $dish_name_fr,
                                'dish_description' => $dish_description,
                                'dish_pricing' => $dish_pricing,
                                'parent_service' => $serialized_service_ids,
                                'dish_status' => $dish_main_status,
                                'daily_dish_status' => $dish_daily_status,
                            );
                            

                            // Check if the dish with the same name exists
                            $dish_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $dish_table WHERE dish_name_eng = %s", $dish_name_eng));

                            // echo'dish_id_______________________>' .$dish_id;
                            // echo'<br>';

                            if ($dish_id !== null) 
                            {
                                // Dish with the same name exists, so update the existing row
                                $wpdb->update($dish_table, $data_to_insert_update, array('id' => $dish_id));
                            } 
                            else 
                            {
                                $data_to_insert_dishes = array(
                                    'dish_name_eng' => $dish_name_eng,
                                    'dish_name_es' => $dish_name_es,
                                    'dish_name_eus' => $dish_name_eus,
                                    'dish_name_fr' => $dish_name_fr,
                                    'dish_description' => $dish_description,
                                    'dish_pricing' => $dish_pricing,
                                    'parent_service' => $serialized_service_ids,
                                    'dish_status' => $dish_main_status,
                                    'daily_dish_status' => $dish_daily_status,
                                );

                                $wpdb->insert($dish_table, $data_to_insert_dishes);
                                $dish_id = $wpdb->insert_id;

                                // echo'dish_name_______________________>' .$data_to_insert_update['dish_name'];
                                // echo'<br>';
                            }

                            foreach($service_ids as $service_idd){

                                $result_main = $wpdb->get_var($wpdb->prepare("SELECT id FROM $main_meta_table WHERE main_service_id = %d AND main_dish_meta_value = %d", $service_idd['service_id'], $dish_id));

                                if($result_main){
                                    $main_data_update = array(
                                        'main_dish_status' => $dish_main_status,
                                    );
                                    $wpdb->update($main_meta_table, $main_data_update, array('id' => $result_main));
                                    

                                }else{

                                    $main_data_update = array(
                                        'main_service_id' => $service_idd['service_id'],
                                        'main_dish_meta_key' => '_main_dish',
                                        'main_dish_meta_value' => $dish_id,
                                        'main_dish_status' => $dish_main_status,
                                    );

                                    $wpdb->insert($main_meta_table, $main_data_update);
                                }


                                $result_daily = $wpdb->get_var($wpdb->prepare("SELECT id FROM $daily_meta_table WHERE daily_service_id = %d AND daily_dish_meta_value = %d", $service_idd['service_id'], $dish_id));

                                if($result_daily){
                                    $daily_data_update = array(
                                        'daily_dish_status' => $dish_daily_status,
                                    );
                                    $wpdb->update($daily_meta_table, $daily_data_update, array('id' => $result_daily));
                                    

                                }else{

                                    $daily_data_update = array(
                                        'daily_service_id' => $service_idd['service_id'],
                                        'daily_dish_meta_key' => '_daily_dish',
                                        'daily_dish_meta_value' => $dish_id,
                                        'daily_dish_status' => $dish_daily_status,
                                    );

                                    $wpdb->insert($daily_meta_table, $daily_data_update);
                                }

                            }
                            

                            // echo $wpdb->last_query;
                            /****** ok ******/ 

                            /********************** SPECIAL  MENU TABLE DATA  *******************************/ 

                            if($special_menu_price)
                            {
                                 // Define the data to be inserted or updated
                                $special_menu_price_update = array(
                                'menu_pricing' => $special_menu_price,
                                'category_id' => 3
                                );
                            }
                            else
                            {
                                $special_menu_price_update = array(
                                    'category_id' => 3
                                );
                            }
                           

                            if($special_menu_name)
                            {
                                $special_menu_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $menu_table WHERE menu_name = %s", $special_menu_name));
                                
                                if ($special_menu_id !== null) 
                                {
                                    $result = $wpdb->update($menu_table, $special_menu_price_update, array('id' => $special_menu_id));
                                }
                                else
                                {
                                    $special_menu_insert = array(
                                        'menu_name' => $special_menu_name,
                                        'menu_pricing' => $special_menu_price,
                                        'category_id' => 3,
                                    );

                                    $wpdb->insert($menu_table, $special_menu_insert);
                                    $special_menu_id = $wpdb->insert_id;
                                }
                            }
                            
        
                            /********************** SERVICE TABLE *****************************/ 

                            if($special_menu_name && $special_menu_id)
                            {
                                foreach($service_ids as $service_iddd){

                                    $result_special = $wpdb->get_var($wpdb->prepare("SELECT id FROM $service_meta_table WHERE special_menu_id = %d AND service_id = %d AND dish_meta_value = %d", $special_menu_id, $service_iddd['service_id'], $dish_id));

                                    if($result_special){
                                        $service_data_update = array(
                                            'dish_status' => 'true',
                                        );
                                        $wpdb->update($service_meta_table, $service_data_update, array('id' => $result_special));
                                        

                                    }else{

                                        $service_data_update = array(
                                            'special_menu_id' => $special_menu_id,
                                            'service_id' => $service_iddd['service_id'],
                                            'dish_meta_key' => '_dish',
                                            'dish_meta_value' => $dish_id,
                                            'dish_status' => 'true',
                                        );

                                        $wpdb->insert($service_meta_table, $service_data_update);
                                    }

                                }
                            }
                            /**************** Other tables ************** */

                            if($other_menu_name)
                            {
                                $other_menu_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $menu_table WHERE menu_name = %s", $other_menu_name));
                               

                                if($other_menu_price)
                                {
                                    $other_menu_cat_update = array(
                                        'category_id' => 4,
                                        'menu_pricing' => $other_menu_price
                                    );
                                }
                                else
                                {
                                    $other_menu_cat_update = array(
                                        'category_id' => 4
                                    );
                                }
                                
                                if ($other_menu_id !== null) 
                                {
                                    $result = $wpdb->update($menu_table, $other_menu_cat_update, array('id' => $other_menu_id));
                                }
                                else
                                {
                                    $other_menu_insert = array(
                                        'menu_name' => $other_menu_name,
                                        'category_id' => 4,
                                        'menu_pricing' => $other_menu_price
                                    );

                                    $wpdb->insert($menu_table, $other_menu_insert);
                                    $other_menu_id = $wpdb->insert_id;
                                }

                                 // Check if a record with the same `other_menu_id` and `dish_id` exists in the other table.

                                // $result_other = $wpdb->get_var($wpdb->prepare("SELECT id FROM $other_table WHERE other_menu_id = %d  AND dish_meta_value = %d", $other_menu_id, $dish_id));

                                $result_other = $wpdb->get_var(
                                    $wpdb->prepare(
                                        "SELECT id FROM $wpdb->prefix"."trion_other_menu_dish_meta WHERE other_menu_id = %d AND other_dish_meta_value LIKE %s",
                                        $other_menu_id,
                                        '%' . $wpdb->esc_like($dish_id) . '%'
                                    )
                                );

                                if($result_other !== null)
                                {
                                    $other_data_update = array(
                                        'other_dish_status' => 'true',
                                    );
                                    $wpdb->update($other_table, $other_data_update, array('id' => $result_other));

                                }
                                else
                                {
                                    $other_data_insert = array(
                                        'other_menu_id' => $other_menu_id,
                                        'other_dish_meta_key' => '_other_dish',
                                        'other_dish_meta_value' => $dish_id,
                                        'other_dish_status' => 'true',
                                    );

                                    $wpdb->insert($other_table, $other_data_insert);

                                }

                            }
                            
                            $counter_check++;

                        }
                      
                    }
                    ?>
                        <script defer>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    title: 'Thank You',
                                    text: "Sheet Uploaded successfully",
                                    icon: 'Success',
                                    showCancelButton: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        
                                    }
                                });
                            });
                        </script>
                        <?php
                } 
                elseif ($_FILES["excel_file"]["type"] === 'text/csv') 
                {
                    // Handle CSV file
                    $objWorksheet = $spreadsheet->getActiveSheet();
                    $data = array(); // Initialize the $data array
    
                    $skipHeader = true;
                    foreach ($objWorksheet->getRowIterator() as $row) 
                    {
                        if ($skipHeader) 
                        {
                            $skipHeader = false;
                            continue;
                        }
                        $rowData = [];
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);
                        foreach ($cellIterator as $cell) {
                            $rowData[] = $cell->getValue();
                        }
                        // Append the rowData to the data array
                        $data[] = $rowData;
                    }

                    /************* batches *****************/ 
                    $batchSize = 100;
                    $totalRows = count($data);
                    
                    for ($i = 0; $i < $totalRows; $i += $batchSize) 
                    {
                        $batchData = array_slice($data, $i, $batchSize);
                        $counter_check = 0;
                        foreach($batchData as $sheetItem)
                        {
                            $dish_table = $wpdb->prefix . 'trion_dish_tbl_meta';
                            $menu_table = $wpdb->prefix . 'trion_menu_tbl_meta';
                            $service_table = $wpdb->prefix . 'trion_service_tbl_meta';
                            $service_meta_table = $wpdb->prefix . 'trion_special_service_tbl_dish_meta';
                            $main_meta_table = $wpdb->prefix . 'trion_main_service_tbl_dish_meta';
                            $daily_meta_table = $wpdb->prefix . 'trion_daily_service_tbl_dish_meta';
                            $other_table = $wpdb->prefix . 'trion_other_menu_dish_meta';


                            /*********** get data from the excel sheet ****************/ 

                            $dish_name_eng = $sheetItem[0];
                            $dish_name_es = $sheetItem[1];
                            $dish_name_eus = $sheetItem[2];
                            $dish_name_fr = $sheetItem[3];
                            $dish_pricing = $sheetItem[4];
                            $dish_description = $sheetItem[5];
                            $dish_main_status = strtolower($sheetItem[6]);
                            $dish_daily_status = strtolower($sheetItem[7]);
                            $special_menu_name = $sheetItem[8];
                            $special_menu_price = $sheetItem[9];
                            $service_name = $sheetItem[10];
                            $other_menu_name = $sheetItem[11];
                            $other_menu_price = $sheetItem[12];
                        

                            // /************** main menu status **************/ 
                            // if($dish_main_status == 1)
                            // {
                            //     $dish_main_status = 'true';
                            // }
                            // else
                            // {
                            //     $dish_main_status = 'false';
                            // }

                            // /************** daily menu  status **************/ 

                            // if($dish_daily_status == "")
                            // {
                            //     $dish_daily_status = 'true';
                            // }
                            // else
                            // {
                            //     $dish_daily_status = 'false';
                            // }

                            /******************** DISh TABLE DATA ****************************/ 
                            $service_name = trim($service_name);
                            $service_name_array = explode(',', $service_name);
                            $service_name_array = array_unique(array_map('trim', $service_name_array)); // Remove duplicates and trim whitespace

                            $service_ids = array();

                            foreach($service_name_array as $ser_names)
                            {
                               $services_name = trim($ser_names);

                               if(!empty($services_name))
                               {
                                    // Check if the dish with the same name exists
                                    $service_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $service_table WHERE service_name = %s", $services_name));

                                    if ($service_id !== null)
                                    {  
                                        
                                    } 
                                    else 
                                    {
                                        // Service with the same name doesn't exist, so insert a new row
                                        $service_insert_update['service_name'] = $services_name; 
                                        $wpdb->insert($service_table, $service_insert_update);
                                        $service_id = $wpdb->insert_id;
                                    }

                                        // Store the service IDs in an array
                                        $service_ids[] = array('service_id' => $service_id);

                                }
                        
                            }

                            

                            /********* serialized service ids *************/ 
                            $json_service_ids = json_encode($service_ids);
                            $escaped_json_service_ids = addslashes($json_service_ids);
                            $serialized_service_ids = 's:' . strlen($escaped_json_service_ids) . ':"' . $escaped_json_service_ids . '";';

                            // Define the data to be inserted or updated
                            $data_to_insert_update = array(
                                'dish_name_es' => $dish_name_es,
                                'dish_name_eus' => $dish_name_eus,
                                'dish_name_fr' => $dish_name_fr,
                                'dish_description' => $dish_description,
                                'dish_pricing' => $dish_pricing,
                                'parent_service' => $serialized_service_ids,
                                'dish_status' => $dish_main_status,
                                'daily_dish_status' => $dish_daily_status,
                            );

                          
                            

                            // Check if the dish with the same name exists
                            $dish_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $dish_table WHERE dish_name_eng = %s", $dish_name_eng));

                            // echo'dish_id_______________________>' .$dish_id;
                            // echo'<br>';

                            if ($dish_id !== null) 
                            {
                                // Dish with the same name exists, so update the existing row
                                $wpdb->update($dish_table, $data_to_insert_update, array('id' => $dish_id));
                            } 
                            else 
                            {
                                $data_to_insert_dishes = array(
                                    'dish_name_eng' => $dish_name_eng,
                                    'dish_name_es' => $dish_name_es,
                                    'dish_name_eus' => $dish_name_eus,
                                    'dish_name_fr' => $dish_name_fr,
                                    'dish_description' => $dish_description,
                                    'dish_pricing' => $dish_pricing,
                                    'parent_service' => $serialized_service_ids,
                                    'dish_status' => $dish_main_status,
                                    'daily_dish_status' => $dish_daily_status,
                                );

                                

                                $wpdb->insert($dish_table, $data_to_insert_dishes);
                                $dish_id = $wpdb->insert_id;

                                // echo'dish_name_______________________>' .$data_to_insert_update['dish_name'];
                                // echo'<br>';
                            }

                            /**********************main menu/ daily menu****************/ 

                            foreach($service_ids as $service_idd){

                                $result_main = $wpdb->get_var($wpdb->prepare("SELECT id FROM $main_meta_table WHERE main_service_id = %d AND main_dish_meta_value = %d", $service_idd['service_id'], $dish_id));

                                if($result_main){
                                    $main_data_update = array(
                                        'main_dish_status' => $dish_main_status,
                                    );
                                    $wpdb->update($main_meta_table, $main_data_update, array('id' => $result_main));
                                    

                                }else{

                                    $main_data_update = array(
                                        'main_service_id' => $service_idd['service_id'],
                                        'main_dish_meta_key' => '_main_dish',
                                        'main_dish_meta_value' => $dish_id,
                                        'main_dish_status' => $dish_main_status,
                                    );

                                    $wpdb->insert($main_meta_table, $main_data_update);
                                }


                                $result_daily = $wpdb->get_var($wpdb->prepare("SELECT id FROM $daily_meta_table WHERE daily_service_id = %d AND daily_dish_meta_value = %d", $service_idd['service_id'], $dish_id));

                                if($result_daily){
                                    $daily_data_update = array(
                                        'daily_dish_status' => $dish_daily_status,
                                    );
                                    $wpdb->update($daily_meta_table, $daily_data_update, array('id' => $result_daily));
                                    

                                }else{

                                    $daily_data_update = array(
                                        'daily_service_id' => $service_idd['service_id'],
                                        'daily_dish_meta_key' => '_daily_dish',
                                        'daily_dish_meta_value' => $dish_id,
                                        'daily_dish_status' => $dish_daily_status,
                                    );

                                    $wpdb->insert($daily_meta_table, $daily_data_update);
                                }

                            }
                            

                            // echo $wpdb->last_query;
                            /****** ok ******/ 

                            /********************** SPECIAL  MENU TABLE DATA  *******************************/ 

                            if($special_menu_price)
                            {
                                 // Define the data to be inserted or updated
                                $special_menu_price_update = array(
                                'menu_pricing' => $special_menu_price,
                                'category_id' => 3
                                );
                            }
                            else
                            {
                                $special_menu_price_update = array(
                                    'category_id' => 3
                                );
                            }
                           

                            if($special_menu_name)
                            {
                                $special_menu_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $menu_table WHERE menu_name = %s", $special_menu_name));
                                
                                if ($special_menu_id !== null) 
                                {
                                    $result = $wpdb->update($menu_table, $special_menu_price_update, array('id' => $special_menu_id));
                                }
                                else
                                {
                                    $special_menu_insert = array(
                                        'menu_name' => $special_menu_name,
                                        'menu_pricing' => $special_menu_price,
                                        'category_id' => 3,
                                    );

                                    $wpdb->insert($menu_table, $special_menu_insert);
                                    $special_menu_id = $wpdb->insert_id;
                                }
                            }
                            
        
                            /********************** SERVICE TABLE *****************************/ 

                            if($special_menu_name && $special_menu_id)
                            {
                                foreach($service_ids as $service_iddd){

                                    $result_special = $wpdb->get_var($wpdb->prepare("SELECT id FROM $service_meta_table WHERE special_menu_id = %d AND service_id = %d AND dish_meta_value = %d", $special_menu_id, $service_iddd['service_id'], $dish_id));

                                    if($result_special){
                                        $service_data_update = array(
                                            'dish_status' => 'true',
                                        );
                                        $wpdb->update($service_meta_table, $service_data_update, array('id' => $result_special));
                                        

                                    }else{

                                        $service_data_update = array(
                                            'special_menu_id' => $special_menu_id,
                                            'service_id' => $service_iddd['service_id'],
                                            'dish_meta_key' => '_dish',
                                            'dish_meta_value' => $dish_id,
                                            'dish_status' => 'true',
                                        );

                                        $wpdb->insert($service_meta_table, $service_data_update);
                                    }

                                }
                            }
                            /**************** Other tables ************** */

                            if($other_menu_name)
                            {
                                $other_menu_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $menu_table WHERE menu_name = %s", $other_menu_name));
                               

                                if($other_menu_price)
                                {
                                    $other_menu_cat_update = array(
                                        'category_id' => 4,
                                        'menu_pricing' => $other_menu_price
                                    );
                                }
                                else
                                {
                                    $other_menu_cat_update = array(
                                        'category_id' => 4
                                    );
                                }
                                
                                if ($other_menu_id !== null) 
                                {
                                    $result = $wpdb->update($menu_table, $other_menu_cat_update, array('id' => $other_menu_id));
                                }
                                else
                                {
                                    $other_menu_insert = array(
                                        'menu_name' => $other_menu_name,
                                        'category_id' => 4,
                                        'menu_pricing' => $other_menu_price
                                    );

                                    $wpdb->insert($menu_table, $other_menu_insert);
                                    $other_menu_id = $wpdb->insert_id;
                                }

                                 // Check if a record with the same `other_menu_id` and `dish_id` exists in the other table.

                                // $result_other = $wpdb->get_var($wpdb->prepare("SELECT id FROM $other_table WHERE other_menu_id = %d  AND dish_meta_value = %d", $other_menu_id, $dish_id));

                                $result_other = $wpdb->get_var(
                                    $wpdb->prepare(
                                        "SELECT id FROM $wpdb->prefix"."trion_other_menu_dish_meta WHERE other_menu_id = %d AND other_dish_meta_value LIKE %s",
                                        $other_menu_id,
                                        '%' . $wpdb->esc_like($dish_id) . '%'
                                    )
                                );

                                if($result_other !== null)
                                {
                                    $other_data_update = array(
                                        'other_dish_status' => 'true',
                                    );
                                    $wpdb->update($other_table, $other_data_update, array('id' => $result_other));

                                }
                                else
                                {
                                    $other_data_insert = array(
                                        'other_menu_id' => $other_menu_id,
                                        'other_dish_meta_key' => '_other_dish',
                                        'other_dish_meta_value' => $dish_id,
                                        'other_dish_status' => 'true',
                                    );

                                    $wpdb->insert($other_table, $other_data_insert);

                                }

                            }
                            
                            $counter_check++;

                        }
                      
                    }
                    ?>
                        <script defer>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    title: 'Thank You',
                                    text: "Sheet Uploaded successfully",
                                    icon: 'Success',
                                    showCancelButton: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        
                                    }
                                });
                            });
                        </script>
                        <?php
                }
            } 
            catch (Exception $e) 
            {
                ?>
                <script defer>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Failed',
                            text: 'Invalid file type. Only the following types are allowed: ' . implode(', ', $allowedFileTypes),
                            icon: 'error',
                            showCancelButton: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                
                            }
                        });
                    });
                </script>
                <?php
            }
        } 
        else 
        {
            ?>
            <script defer>
                document.addEventListener('DOMContentLoaded', function() 
                {
                    Swal.fire({
                        title: 'Failed',
                        text: 'Invalid file type. Only the following types are allowed: ' . implode(', ', $allowedFileTypes),
                        icon: 'error',
                        showCancelButton: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            
                        }
                    });
                });
            </script>
            <?php
        }
    } 
    else 
    {
        ?>
        <script defer>
            document.addEventListener('DOMContentLoaded', function() 
            {
                Swal.fire({
                    title: 'Failed',
                    text: 'File upload error. Error code: ' . $_FILES["file"]["error"],
                    icon: 'error',
                    showCancelButton: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        
                    }
                });
            });
        </script>
        <?php
    }
}


?>