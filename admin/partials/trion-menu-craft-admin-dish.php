<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://https://anesta.ancorathemes.com/
 * @since      1.0.0
 *
 * @package    Event_Custom_Addon
 * @subpackage Event_Custom_Addon/admin/partials
 */
?>

<!--****************** Add Dish Form  ***************-->
<div class="trion-menu-craft-main">
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'trion_dish_tbl_meta';

    $daily_menu_service_tbl_meta = $wpdb->prefix . 'trion_daily_menu_service_tbl_meta';
    $service_tbl_meta = $wpdb->prefix . 'trion_service_tbl_meta';
    $menu_table_name = $wpdb->prefix . 'trion_menu_tbl_meta';
    $main_menu_service_tbl_meta = $wpdb->prefix . 'trion_main_menu_service_tbl_meta';
    $trion_category_tbl_meta = $wpdb->prefix . 'trion_category_tbl_meta';
    $trion_other_menu_dish_meta =  $wpdb->prefix . 'trion_other_menu_dish_meta';


    ?>
    <div class="trion-menu-craft-outer-admin">
         <!--************* add dishes ***************-->
         <div class="trion-menu-craft-inner-admin">
            <button type="button" class="craft_button_addon craft_green" id="add_new_dish_btn">Agregar nuevo plato</button>
        </div>
        
        <!-- ************* dishes form *************** -->
        <div class="trion-dish-craft-inner-admin add-new-dish-sec">
            <div class="add_dish_sec1">
                <form method="POST" id="rest_add_dishess">
                    <div class="trion-dish-craft-inner-content-admin">
                        <!--dish name eng  -->
                        <div class="trion-dish-craft-admin-label">
                            <label for="trion-dish-craft-lbl">Ingrese el nombre del plato ENG</label>
                        </div>
                        <div class="trion-dish-craft-input">
                            <input class="dish_name_eng" type="text" name="dish_name_eng"></input>
                        </div>

                        <!--dish name es  -->
                        <div class="trion-dish-craft-admin-label">
                            <label for="trion-dish-craft-lbl">Introduzca el nombre del plato ES</label>
                        </div>
                        <div class="trion-dish-craft-input">
                            <input class="dish_name_es" type="text" name="dish_name_es"></input>
                        </div>

                        <!--dish name eus  -->
                        <div class="trion-dish-craft-admin-label">
                            <label for="trion-dish-craft-lbl">Ingrese el nombre del plato EUS</label>
                        </div>
                        <div class="trion-dish-craft-input">
                            <input class="dish_name_eus" type="text" name="dish_name_eus"></input>
                        </div>

                        <!--dish name fr  -->
                          <div class="trion-dish-craft-admin-label">
                            <label for="trion-dish-craft-lbl">Ingrese el nombre del plato FR</label>
                        </div>
                        <div class="trion-dish-craft-input">
                            <input class="dish_name_fr" type="text" name="dish_name_fr"></input>
                        </div>

                         <!--dish Price  -->
                        <div class="trion-dish-craft-admin-label">
                            <label for="trion-dish-craft-lbl">Ingrese el precio del plato</label>
                        </div>
                        <div class="trion-dish-craft-input">
                            <input class="dish_price" type="text" name="dish_price"></input>
                        </div>

                        <!--dish des  -->
                        <div class="trion-dish-craft-admin-label">
                            <label for="trion-dish-craft-lbl">Ingrese la descripción del plato</label>
                        </div>
                        <div class="trion-dish-craft-input">
                            <textarea name="dish_description" rows="4" class="dishes_des"></textarea>
                        </div>
                        <!--*********************** select services  *******************-->
                        <div class = "select_services_outer">
                            <div class="select_services_inner">
                                <div class="trion-dish-craft-admin-label">
                                <label for="trion-dish-craft-lbl">Seleccionar Servicios :-</label>
                                </div>
                                <!-- get data from the tabales -->
                                <?php
                                $service_menu_query = $wpdb->get_results("SELECT * FROM $service_tbl_meta", ARRAY_A);                          

                                // Now you can loop through the merged data
                                foreach ($service_menu_query as $merged_data_item) 
                                {

                                    $service_id = $merged_data_item['id'];
                                    $service_name = $merged_data_item['service_name'];
                                    //$parent_menu = $merged_data_item['parent_menu'];

                                    // echo '<label><input type="checkbox" name="selected_services[]" class="selected_servicess" value="' . $service_id . '" data-id ="'.$parent_menu.'"> ' . $service_name . '</label><br>';

                                    echo '<label><input type="checkbox" name="selected_services[]" class="selected_servicess" value="' . $service_id . '"> ' . $service_name . '</label><br>';
                                }
                                ?>
                            </div>
                        </div>
                        <!-- *********** end select ser sec *********** -->


                        <!--********************* select other menu  **************************-->
                        <div class = "select_services_outer">
                            <div class="select_services_inner">
                                <div class="trion-dish-craft-admin-label">
                                <label for="trion-dish-craft-lbl">Seleccione otro menú :-</label>
                                </div>
                                <!-- get data from the tabales -->
                                <?php
                                $other_menu_query = $wpdb->get_results("SELECT * FROM $trion_category_tbl_meta WHERE slug = 'other-cat'", ARRAY_A);

                                // Now you can loop through the merged data
                                foreach ($other_menu_query as $other_menu_data) 
                                {
                                     $other_menu_id = $other_menu_data['id'];

                                     $menu_query = $wpdb->get_results("SELECT * FROM $menu_table_name WHERE category_id = $other_menu_id", ARRAY_A);

                                     foreach($menu_query as $data)
                                     {
                                        $menu_name = $data['menu_name'];
                                        $menu_id = $data['id'];

                                        echo '<label><input type="checkbox" data-id ="other_menu" name="selected_other_menu[]" class="selected_menus" value="' . $menu_id . '"> ' . $menu_name . '</label><br>';
                                     }

                                   
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                    <div class="craft_error_message"></div>
                    <div class="craft_success_message"></div>
                    <div class="trion-dish-craft-admin-foter">
                        <input type="submit" class="craft_button_addon craft_green" value="Agregar platos">
                        <button type="button" class="craft_button_addon craft_green"
                            id="cancel_new_dish_btn">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
      

        <!-- ************** edit dishes form ********************* -->
        <?php 
        $dish_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $edit_action = isset($_GET['action']) == 'edit_dish' ? $style="display: block;" : $style="display: none;";

        $dish_item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $dish_id));
         
        ?>
        <div class="trion-dish-craft-inner-admin update-new-dish-sec" style="<?php echo $style; ?>">
            <form method="POST" id="update_dishess">
                <div class="trion-dish-craft-inner-content-admin">

                <input type="hidden" name="dishes_idd" class="dishes_id" value="<?php echo $dish_id; ?>">
                    <!-- dish name eng -->
                    <div class="trion-dish-craft-admin-label">
                        <label for="trion-dish-craft-lbl">Actualizar nombre del plato ENG</label>
                    </div>
                    <div class="trion-dish-craft-input">
                        <input class="update_dish_name_eng" type="text" name="update_dish_name_eng" value="<?php echo $dish_item->dish_name_eng; ?>"></input>
                    </div>
                    <!-- dish name es -->
                    <div class="trion-dish-craft-admin-label">
                        <label for="trion-dish-craft-lbl">Actualizar nombre del plato ES</label>
                    </div>
                    <div class="trion-dish-craft-input">
                        <input class="update_dish_name_es" type="text" name="update_dish_name_es" value="<?php echo $dish_item->dish_name_es; ?>"></input>
                    </div>
                    <!-- dish name eus -->
                    <div class="trion-dish-craft-admin-label">
                        <label for="trion-dish-craft-lbl">Actualizar nombre del plato EUS</label>
                    </div>
                    <div class="trion-dish-craft-input">
                        <input class="update_dish_name_eus" type="text" name="update_dish_name_eus" value="<?php echo $dish_item->dish_name_eus; ?>"></input>
                    </div>
                    <!-- dish name fr -->
                    <div class="trion-dish-craft-admin-label">
                        <label for="trion-dish-craft-lbl">Actualizar nombre del plato FR</label>
                    </div>
                    <div class="trion-dish-craft-input">
                        <input class="update_dish_name_fr" type="text" name="update_dish_name_fr" value="<?php echo $dish_item->dish_name_fr; ?>"></input>
                    </div>

                    <div class="trion-dish-craft-admin-label">
                        <label for="trion-dish-craft-lbl">Actualizar precio del plato</label>
                    </div>
                    <div class="trion-dish-craft-input">
                        <input class="update_dish_price" type="text" name="update_dish_price"  value="<?php echo $dish_item->dish_pricing; ?>"></input>
                    </div>

                    <div class="trion-dish-craft-admin-label">
                        <label for="trion-dish-craft-lbl">Actualizar descripción del plato</label>
                    </div>
                    <div class="trion-dish-craft-input">
                        <textarea name="update_dish_description" rows="4" class="update_dishes_des" value="<?php echo $dish_item->dish_description; ?>"><?php echo $dish_item->dish_description; ?></textarea>
                    </div>

                        <!--*********************** select services  *******************-->
                        <div class = "select_services_outer">
                            <div class="select_services_inner">
                                <div class="trion-dish-craft-admin-label">
                                <label for="trion-dish-craft-lbl">Seleccionar Servicios</label>
                                </div>
                                <!-- get data from the tabales -->
                                <?php
                                //selected service
                                $selected_services = unserialize($dish_item->parent_service);

                                $jsonData = stripslashes($selected_services);
                                $decodedData = json_decode($jsonData, true);

                                $service_menu_query = $wpdb->get_results("SELECT * FROM $service_tbl_meta", ARRAY_A);

                                foreach ($service_menu_query as $merged_data_item) 
                                {
                                    $service_id = $merged_data_item['id'];
                                    $service_name = $merged_data_item['service_name'];
                                    // $parent_menu = $merged_data_item['parent_menu'];
                                    
                                    $checked = '';
                                    if ($decodedData !== null) 
                                    {
                                        foreach($decodedData as $checkdata)
                                        {
                                            // if($checkdata['service_id'] == $service_id && $checkdata['menu_id'] == $parent_menu)
                                            if($checkdata['service_id'] == $service_id)
                                            {
                                                $checked = 'checked';
                                            }   
                                        }
                                    }

                                    echo '<label><input type="checkbox" name="selected_services[]" class="selected_servicess" value="' . $service_id . '" ' . $checked . '> ' . $service_name . '</label><br>';


                                }  
                                
                                ?>
                            </div>
                        </div>

                        <!--********************* update other menus **************** -->
                        <div class = "select_menus_outer">
                            <div class="select_menu_inner">
                                <div class="trion-dish-craft-admin-label">
                                    <label for="trion-dish-craft-lbl">Seleccione otro menú :</label>
                                </div>
                                <!-- get data from the tabales -->
                                <?php
                                $other_menu_query = $wpdb->get_results("SELECT * FROM $trion_category_tbl_meta WHERE slug = 'other-cat'", ARRAY_A);

                                // Now you can loop through the merged data
                                foreach ($other_menu_query as $other_menu_data) 
                                {
                                    $other_menu_id = $other_menu_data['id'];

                                    $menu_query = $wpdb->get_results("SELECT * FROM $menu_table_name WHERE category_id = $other_menu_id", ARRAY_A);

                                    foreach($menu_query as $data)
                                    {
                                        $menu_name = $data['menu_name'];
                                        $menu_id = $data['id'];

                                        // Check if conditions are met in your other_menu_status_check_query
                                        $other_menu_status_check_query = $wpdb->get_row($wpdb->prepare("SELECT * FROM $trion_other_menu_dish_meta WHERE other_menu_id = %d AND other_dish_meta_value = %d", $menu_id, $dish_id)
                                        );

                                        $checked = '';
                                        if ($other_menu_status_check_query && ($other_menu_status_check_query->other_dish_status === 'true' || $other_menu_status_check_query->other_dish_status === 'false')) {
                                            $checked = 'checked';
                                        } 
                                        

                                        echo '<label><input type="checkbox" data-id="update_other_menu" name="update_other_menu[]" class="update_menus" value="' . $menu_id . '" ' . $checked . '> ' . $menu_name . '</label><br>';  
                                    }
                                }
                            ?>
                            </div>
                        </div>

                    <!--*********************** select services sec end   *******************-->

                </div>
                <div class="craft_error_message"></div>
                <div class="craft_success_message"></div>
                <div class="trion-dish-craft-admin-foter">
                    <input type="submit" class="craft_button_addon craft_green" value="Actualizar platos">
                    <button type="button" class="craft_button_addon craft_green"
                        id="cancel_update_dish_btn">Cancelar</button>
                </div>
            </form>
        </div>
        <?php 
        ?>
    </div>
</div>

<!--************** Fetch table data  **************-->
<?php
$res_dishes = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
?>

<div class="trion-menu-craft-outer-admin">
    <div class="trion-menu-craft-inner-admin">
        <div class="trion-menu-craft-inner-content-admin">
            <div class="trion-menu-craft-admin-label">
                <label for="trion-menu-craft-lbl">Platos</label>
            </div>
            <div class="trion-menu-craft-admin-invite-emails"> 
                <table id="dish_table">
                    <thead>
                        <tr>
                            <th>Identificación</th>
                            <th>Nombre del plato ing.</th>
                            <th>Nombre del plato ES</th>
                            <th>Nombre del plato Eus</th>
                            <th>Nombre del plato</th>
                            <th>Precio del plato</th>
                            <th>Descripción del plato</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>  
                        <?php
                        $i = 1;
                        if (count($res_dishes) > 0) 
                        {
                            foreach ($res_dishes as $res_dishes_data) 
                            {?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $res_dishes_data['dish_name_eng']; ?></td>
                                    <td><?php echo $res_dishes_data['dish_name_es']; ?></td>
                                    <td><?php echo $res_dishes_data['dish_name_eus']; ?></td>
                                    <td><?php echo $res_dishes_data['dish_name_fr']; ?></td>
                                    <td><?php echo $res_dishes_data['dish_pricing']; ?></td>
                                    <td><?php echo $res_dishes_data['dish_description']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-success">
                                            <a class="trion-menu-craft-edit-btn" id="edit_dishes" href="<?php echo esc_url(admin_url('admin.php?page=trion-menu-craft-plugin-dish&action=edit_dish&id='. $res_dishes_data['id'])); ?>">Editar</a>
                                        </button>

                                        <!-- <button type="button" class="btn btn-danger">
                                            <a class="trion-menu-craft-delete-btn" id="delete_dishes" href="<?php //echo esc_url(admin_url('admin.php?page=trion-menu-craft-plugin-dish&action=delete_dish&id=' . $res_dishes_data['id'])); ?>">Delete</a>
                                        </button>  -->

                                        <button type="button" class="btn btn-danger">
                                            <a href="#" class="trion-menu-craft-delete-btn delete_dishes" id="delete_dish" data-id="<?php echo $res_dishes_data['id']; ?>">Borrar</a>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            }
                        } 
                        else 
                        {
                            echo '<tr><td>No se encontró ningún plato.</td></tr>';
                        } ?>
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
</div>
