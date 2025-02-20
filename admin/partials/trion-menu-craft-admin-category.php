
<!--****************** Add category Form  ***************-->
<div class="trion-menu-craft-main">
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'trion_category_tbl_meta';
    ?>
    <div class="trion-menu-craft-outer-admin">
         <!--************* add category ***************-->
         <div class="trion-menu-craft-inner-admin">
            <button type="button" class="craft_button_addon craft_green" id="add_new_category_btn">Añadir categoría</button>
        </div>
        
        <!-- ************* category form *************** -->
        <div class="trion-category-craft-inner-admin add-new-category-sec">
            <form method="POST" id="rest_add_category">
                <div class="trion-category-craft-inner-content-admin">
                    <div class="trion-category-craft-admin-label">
                        <label for="trion-category-craft-lbl">Ingrese el nombre de la categoría</label>
                    </div>
                    <div class="trion-category-craft-input">
                        <input class="category_name" type="text" name="category_name"></input>
                    </div>

                    <div class="trion-category-craft-admin-label">
                        <label for="trion-category-craft-lbl">Enter Category Description</label>
                    </div>
                    <div class="trion-category-craft-input">
                        <textarea name="category_description" rows="4" class="category_descriptions"></textarea>
                    </div>
                </div>
                <div class="craft_error_message"></div>
                <div class="craft_success_message"></div>
                <div class="trion-category-craft-admin-foter">
                    <input type="submit" class="craft_button_addon craft_green" value="Añadir categoría">
                    <button type="button" class="craft_button_addon craft_green" id="cancel_new_category_btn">Cancelar</button>
                </div>
            </form>
        </div>

        <!-- ************** edit categoryes form ********************* -->
        <?php 
        $category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $edit_action = isset($_GET['action']) == 'edit_category' ? $style="display: block;" : $style="display: none;";

        $category_item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $category_id));
         
        ?>
        <div class="trion-category-craft-inner-admin update-new-category-sec" style="<?php echo $style; ?>">
            <form method="POST" id="update_categories">
                <div class="trion-category-craft-inner-content-admin">

                <input type="hidden" name="category_idd" class="category_idd" value="<?php echo $category_id; ?>">

                    <div class="trion-category-craft-admin-label">
                        <label for="trion-category-craft-lbl">Actualizar nombre de categoría</label>
                    </div>
                    <div class="trion-category-craft-input">
                        <input class="update_category_name" type="text" name="update_category_name" value="<?php echo $category_item->category_name; ?>"></input>
                    </div>

                    <div class="trion-category-craft-admin-label">
                        <label for="trion-category-craft-lbl">Categoría de actualización Descripción</label>
                    </div>
                    <div class="trion-category-craft-input">
                        <textarea name="update_category_description" rows="4" class="update_category_des" value="<?php echo $category_item->category_description; ?>"><?php echo $category_item->category_description; ?></textarea>
                    </div>
                </div>
                <div class="craft_error_message"></div>
                <div class="craft_success_message"></div>
                <div class="trion-category-craft-admin-foter">
                    <input type="submit" class="craft_button_addon craft_green" value="Update category">
                    <button type="button" class="craft_button_addon craft_green"
                        id="cancel_update_category_btn">Cancelar</button>
                </div>
            </form>
        </div>
        <?php 
        ?>
    </div>
</div>

<!--**************** Fetch Category Data *******************-->
<?php
$res_cat = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

?>

<div class="trion-menu-craft-outer-admin">
    <div class="trion-menu-craft-inner-admin">
        <div class="trion-menu-craft-inner-content-admin">
            <div class="trion-menu-craft-admin-label">
                <label for="trion-menu-craft-lbl">Categorías</label>
            </div>
            <div class="trion-menu-craft-admin-invite-emails"> 
                <table id="cat_table">
                    <thead>
                        <tr>
                            <th>Identificación</th>
                            <th>Nombre de la categoría</th>
                            <th>Descripción de categoría</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>  
                        <?php
                        $i = 1;
                        if (count($res_cat) > 0) 
                        {
                            foreach ($res_cat as $res_cat_data) 
                            {?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $res_cat_data['category_name']; ?></td>
                                    <td><?php echo $res_cat_data['category_description']; ?></td>
                                    <?php 
                                    /*if($res_cat_data['status'] == 0)
                                    {?>
                                        <td>
                                        <button type="button" class="btn btn-success">
                                            <a class="trion-menu-craft-edit-btn" href="<?php echo esc_url(admin_url('admin.php?page=trion-menu-craft-plugin-category&action=edit_category&id=' . $res_cat_data['id'])); ?>">Editar</a>
                                        </button>

                                        <button type="button" class="btn btn-danger delete_category" data-id = "<?php echo $res_cat_data['id']; ?>">
                                        Borrar</button> 
                                        
                                    </td>
                                    <?php /*}
                                    else
                                    {*/?>
                                        <td>Ningún cambio</td>
                                    <?php /*} */?>
                                </tr>
                            <?php
                            }
                        } 
                        else 
                        {
                            echo '<tr><td>No se encontró ninguna categoría.</td></tr>';
                        } ?>
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
</div>