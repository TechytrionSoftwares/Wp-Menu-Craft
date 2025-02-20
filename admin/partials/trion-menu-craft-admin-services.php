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
    $main_menu_service_tbl_meta = $wpdb->prefix . 'trion_main_menu_service_tbl_meta';

    ?>
    <div class="trion-menu-craft-outer-admin">
         <!--************* add services ***************-->
         <div class="trion-menu-craft-inner-admin">
            <button type="button" class="craft_button_addon craft_green" id="add_new_service_btn">Agregar nuevo servicio</button>
        </div>
        
        <!-- ************* services form *************** -->
        <div class="trion-service-craft-inner-admin add-new-service-sec">
            <div class="add_service_sec1">
                <form method="POST" id="rest_add_services">
                    <div class="trion-service-craft-inner-content-admin">
                        <!-- ser name eng  -->
                        <div class="trion-service-craft-admin-label">
                            <label for="trion-service-craft-lbl">Ingrese el nombre del servicio</label>
                        </div>
                        <div class="trion-service-craft-input">
                            <input class="service_name" type="text" name="service_name"></input>
                        </div>
                        <!-- ser name es  -->
                        <div class="trion-service-craft-admin-label">
                            <label for="trion-service-craft-lbl">Ingrese el nombre del servicio ENG</label>
                        </div>
                        <div class="trion-service-craft-input">
                            <input class="service_name_eng" type="text" name="service_name_eng"></input>
                        </div>
                        <!-- ser name eus  -->
                        <div class="trion-service-craft-admin-label">
                            <label for="trion-service-craft-lbl">Ingrese el nombre del servicio EUS</label>
                        </div>
                        <div class="trion-service-craft-input">
                            <input class="service_name_eus" type="text" name="service_name_eus"></input>
                        </div>
                        <!-- ser name fr  -->
                        <div class="trion-service-craft-admin-label">
                            <label for="trion-service-craft-lbl">Ingrese el nombre del servicio FR</label>
                        </div>
                        <div class="trion-service-craft-input">
                            <input class="service_name_fr" type="text" name="service_name_fr"></input>
                        </div>
                        <!-- ser des -->
                        <div class="trion-service-craft-admin-label">
                            <label for="trion-service-craft-lbl">Ingrese la descripción del servicio</label>
                        </div>
                        <div class="trion-service-craft-input">
                            <textarea name="service_description" rows="4" class="servicees_des"></textarea>
                        </div>
                    </div>
                    
                    <div class="craft_error_message"></div>
                    <div class="craft_success_message"></div>

                    <div class="trion-services-craft-admin-foter">
                        <input type="submit" class="craft_button_addon craft_green" value="Agregar servicios">
                        <button type="button" class="craft_button_addon craft_green" id="cancel_new_service_btn">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
      

        <!-- ************** edit services form ********************* -->
        <?php 
        $service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $edit_action = isset($_GET['action']) == 'edit_services' ? $style="display: block;" : $style="display: none;";

        $service_item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $service_tbl_meta WHERE id = %d", $service_id));

        ?>
        <div class="trion-service-craft-inner-admin update-new-service-sec" style="<?php echo $style; ?>">
            <form method="POST" id="update_services">
                <div class="trion-service-craft-inner-content-admin">

                <input type="hidden" name="servicees_idd" class="servicees_id" value="<?php echo $service_id; ?>">
                    <!-- update ser name en -->
                    <div class="trion-service-craft-admin-label">
                        <label for="trion-service-craft-lbl">Actualizar nombre del servicio ES</label>
                    </div>
                    <div class="trion-service-craft-input">
                        <input class="update_service_name" type="text" name="update_service_name" value="<?php echo $service_item->service_name;?>"></input>                       
                    </div>
                      <!-- update ser name es -->
                    <div class="trion-service-craft-admin-label">
                        <label for="trion-service-craft-lbl">Actualizar nombre del servicio ENG</label>
                    </div>
                    <div class="trion-service-craft-input">
                        <input class="update_service_name_eng" type="text" name="update_service_name_eng" value="<?php echo $service_item->service_name_eng; ?>"></input>
                    </div>
                      <!-- update ser name eus -->
                    <div class="trion-service-craft-admin-label">
                        <label for="trion-service-craft-lbl">Actualizar nombre del servicio EUS</label>
                    </div>
                    <div class="trion-service-craft-input">
                        <input class="update_service_name_eus" type="text" name="update_service_name_eus" value="<?php echo $service_item->service_name_eus; ?>"></input>
                    </div>
                      <!-- update ser name fr -->
                    <div class="trion-service-craft-admin-label">
                        <label for="trion-service-craft-lbl">Actualizar nombre del servicio FR</label>
                    </div>
                    <div class="trion-service-craft-input">
                        <input class="update_service_name_fr" type="text" name="update_service_name_fr" value="<?php echo $service_item->service_name_fr; ?>"></input>
                    </div>

                    <div class="trion-service-craft-admin-label">
                        <label for="trion-service-craft-lbl">Descripción del servicio de actualización</label>
                    </div>
                    <div class="trion-service-craft-input">
                        <textarea name="update_service_description" rows="4" class="update_service_des" value="<?php echo $service_item->service_description; ?>"><?php echo $service_item->service_description; ?></textarea>
                    </div>

                </div>
                <div class="craft_error_message"></div>
                <div class="craft_success_message"></div>

                <div class="trion-service-craft-admin-foter">
                    <input type="submit" class="craft_button_addon craft_green" value="Descripción del servicio de actualización">
                    <button type="button" class="craft_button_addon craft_green" id="cancel_update_service_btn">Cancelar</button>
                </div>
            </form>
        </div>
        <?php 
        ?>
    </div>
</div>

<!--************** Fetch table data  **************-->
<?php
$res_services = $wpdb->get_results("SELECT * FROM $service_tbl_meta", ARRAY_A);
?>

<div class="trion-menu-craft-outer-admin">
    <div class="trion-menu-craft-inner-admin">
        <div class="trion-menu-craft-inner-content-admin">
            <div class="trion-menu-craft-admin-label">
                <label for="trion-menu-craft-lbl">Dishs</label>
            </div>
            <div class="trion-menu-craft-admin-invite-emails"> 
                <table id="service_table">
                    <thead>
                        <tr>
                            <th>Identificación</th>
                            <th>Nombre del Servicio</th>
                            <th>Descripción del servicio</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>  
                        <?php
                        $i = 1;
                        if (count($res_services) > 0) 
                        {
                            foreach ($res_services as $res_services_data) 
                            {?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $res_services_data['service_name']; ?></td>
                                    <td><?php echo $res_services_data['service_description']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-success">
                                            <a class="trion-menu-craft-edit-btn" id="edit_services" href="<?php echo esc_url(admin_url('admin.php?page=trion-menu-craft-plugin-services&action=edit_services&id='. $res_services_data['id'])); ?>">Editar</a>
                                        </button>

                                        <!-- <button type="button" class="btn btn-danger">
                                            <a href="#" class="trion-menu-craft-delete-btn delete_services" id="delete_service" data-id="<?php //echo $res_services_data['id']; ?>">Borrar</a>
                                        </button> -->
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
