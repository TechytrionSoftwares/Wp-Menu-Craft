
<!--**************** Add menus *******************-->
<div class="trion-menu-craft-main">
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'trion_menu_tbl_meta';
    $cat_table_name = $wpdb->prefix . 'trion_category_tbl_meta';
    ?>
    <div class="trion-menu-craft-outer-admin">
        <!--*********** Add Category  ***************-->
        <div class="trion-menu-craft-menu-admin add_option_menu">
            <div class="trion-menu-craft-inner-content-menu-admin">
                <div class="trion-menu-craft-inputs-outers">
                    <div class="trion-menu-craft-input">
                        <label>Selecciona una categoría</label>
                        <select name="get_catt_id" id="get_cat_id">
                            <option value="">Selecciona una categoría</option>
                            <?php
                            $cat_data = $wpdb->get_results("SELECT * FROM $cat_table_name", ARRAY_A);
                            if(count($cat_data) > 0)
                            {
                                foreach($cat_data as $data)
                                {?>
                                    <option value="<?php echo $data['id']; ?>" data-slug="<?php echo $data['slug']; ?>"><?php echo $data['category_name']; ?></option>
                                <?php }
                            }?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="trion-menu-craft-inner-admin add-menu-new">
                <button type="button" class="craft_button_addon craft_green" id="add_new_menu_btn" style="display:none;">Agregar nuevo menú</button>
            </div>
            <!--************** add menu form  ************** -->
            <div class="trion-menu-craft-inner-admin add-new-menu-sec">
                <form method="POST" id="add_menus">
                    <div class="trion-menu-craft-inner-content-admin">

                        <!--************* cat_id ***********-->
                        <input type="hidden" name="category_id" id="category_id" value="">

                        <!-- cat slug  -->
                        <input type="hidden" name="category_slug" id="category_slug" value="">
                        
                        <!--********* menu name************* -->

                        <div class="trion-menu-craft-admin-label">
                            <label for="trion-menu-craft-lbl">Introduzca el nombre del menú ES</label>
                        </div>
                        <div class="trion-menu-craft-input">
                            <input class="menu_name" type="text" name="menu_name"></input>
                        </div>
                        <div class="trion-menu-craft-admin-label">
                            <label for="trion-menu-craft-lbl">Introduzca el nombre del menú ENG</label>
                        </div>
                        <div class="trion-menu-craft-input">
                            <input class="menu_name_eng" type="text" name="menu_name_eng"></input>
                        </div>
                        <div class="trion-menu-craft-admin-label">
                            <label for="trion-menu-craft-lbl">Introduzca el nombre del menú EUS</label>
                        </div>
                        <div class="trion-menu-craft-input">
                            <input class="menu_name_eus" type="text" name="menu_name_eus"></input>
                        </div>
                        <div class="trion-menu-craft-admin-label">
                            <label for="trion-menu-craft-lbl">Introduzca el nombre del menú FR</label>
                        </div>
                        <div class="trion-menu-craft-input">
                            <input class="menu_name_fr" type="text" name="menu_name_fr"></input>
                        </div>

                         <!--********* menu des************* -->

                        <div class="trion-menu-craft-admin-label">
                            <label for="trion-menu-craft-lbl">Ingresar descripción del menú</label>
                        </div>
                        <div class="trion-menu-craft-input">
                            <textarea name="menu_description" rows="4" class="menu_description"></textarea>
                        </div>  

                        <!--********* menu price************* -->

                        <div class="menu_outer">
                            <div class="trion-menu-craft-admin-label">
                                <label for="trion-menu-craft-lbl">Ingrese el precio del menú ES</label>
                            </div>
                            <div class="trion-menu-craft-input">
                                <input class="menu_price" type="text" name="menu_price"></input>
                            </div>
                            <div class="trion-menu-craft-admin-label">
                                <label for="trion-menu-craft-lbl">Ingrese el precio del menú ENG</label>
                            </div>
                            <div class="trion-menu-craft-input">
                                <input class="menu_price_eng" type="text" name="menu_price_eng"></input>
                            </div>
                            <div class="trion-menu-craft-admin-label">
                                <label for="trion-menu-craft-lbl">Ingrese el precio del menú EUS</label>
                            </div>
                            <div class="trion-menu-craft-input">
                                <input class="menu_price_eus" type="text" name="menu_price_eus"></input>
                            </div>
                            <div class="trion-menu-craft-admin-label">
                                <label for="trion-menu-craft-lbl">Ingrese el precio del menú FR</label>
                            </div>
                            <div class="trion-menu-craft-input">
                                <input class="menu_price_fr" type="text" name="menu_price_fr"></input>
                            </div>
                        </div>
                    </div>
                    <div class="craft_error_message"></div>
                    <div class="craft_success_message"></div>
                    <div class="trion-menu-craft-admin-foter">
                        <input type="submit" class="craft_button_addon craft_green" value="Agregar menú">
                        <button type="button" class="craft_button_addon craft_green"
                            id="cancel_new_menu_btn">Cancelar</button>
                    </div>

                </form>
            </div>
        </div>

        <!--**************** edit menus *********************-->
        <?php
        if(isset($_GET['action']))
        {
            if($_GET['action'] == 'edit_menu')
            {
                echo "<script>
                jQuery('.add_option_menu').css('display', 'none');
                </script>";
                $style = 'style="display: block;"'; 
                
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $menu_items = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

                /************ menu price show/hide *****************/ 
                $cat_slug = $wpdb->get_row($wpdb->prepare("SELECT slug FROM $cat_table_name WHERE id = %d", $menu_items->category_id)); 

                if ($cat_slug->slug == 'special' || $cat_slug->slug == 'other-cat' || $cat_slug->slug == 'daily') 
                {
                    $category_slug = $cat_slug->slug;
                    $showUpdateMenuPrice = 'block'; 
                } 
                else 
                {
    
                    $showUpdateMenuPrice = 'none';
                }
                ?>  

                 <!--*********** update Category  ***************-->
                <div class="trion-menu-craft-menu-admin update_option_menu">
                    <div class="trion-menu-craft-inner-content-menu-admin">
                        <div class="trion-menu-craft-inputs-outers">
                            <div class="trion-menu-craft-input">
                                <label>Categoría seleccionada</label>
                                <select name="get_update_cat_id" id="get_update_cat_id">
                                    <option value="">Categoría seleccionada</option>
                                    <?php
                                    $cat_data = $wpdb->get_results("SELECT * FROM $cat_table_name", ARRAY_A);
                                    if(count($cat_data) > 0)
                                    {
                                        foreach($cat_data as $data)
                                        {
                                            $category_id = $data['id'];
                                            $category_name = $data['category_name'];
                                            $selected_category_id= $menu_items->category_id;
                                            $selected = ($category_id == $selected_category_id) ? 'selected' : '';
                                        ?>
                                             <option value="<?php echo $category_id; ?>" <?php echo $selected; ?>><?php echo $category_name; ?></option>
                                        <?php }
                                    }?>
                                </select>
                            </div>
                        </div>
                    </div>

                <div class="trion-menu-craft-inner-admin update-menu-sec" <?php echo $style ;?>>
                    <form method="post" id="update_menus">
                        <div class="trion-menu-craft-inner-content-admin">
                           
                            <input type="hidden" name="update_menu_id" class="update_menu_id" value="<?php echo $id; ?>">

                            <div class="trion-menu-craft-admin-label">
                                <label for="trion-menu-craft-lbl">Actualizar nombre del menú</label>
                            </div>
                            <div class="trion-menu-craft-input">
                                <input class="update_menu_name" type="text" name="update_menu_name" value="<?php echo $menu_items->menu_name; ?>"></input>
                            </div>
                            <div class="trion-menu-craft-admin-label">
                                <label for="trion-menu-craft-lbl">Actualizar nombre del menú</label>
                            </div>
                            <div class="trion-menu-craft-input">
                                <input class="update_menu_name_eng" type="text" name="update_menu_name_eng" value="<?php echo $menu_items->menu_name_eng; ?>"></input>
                            </div>
                            <div class="trion-menu-craft-admin-label">
                                <label for="trion-menu-craft-lbl">Actualizar nombre del menú</label>
                            </div>
                            <div class="trion-menu-craft-input">
                                <input class="update_menu_name_eus" type="text" name="update_menu_name_eus" value="<?php echo $menu_items->menu_name_eus; ?>"></input>
                            </div>
                            <div class="trion-menu-craft-admin-label">
                                <label for="trion-menu-craft-lbl">Actualizar nombre del menú</label>
                            </div>
                            <div class="trion-menu-craft-input">
                                <input class="update_menu_name_fr" type="text" name="update_menu_name_fr" value="<?php echo $menu_items->menu_name_fr; ?>"></input>
                            </div>

                            <div class="trion-menu-craft-admin-label">
                                <label for="trion-menu-craft-lbl">Descripción del menú Actualizar</label>
                            </div>
                            <div class="trion-menu-craft-input">
                                <textarea name="update_menu_description" rows="4" class="update_menu_des"><?php echo $menu_items->menu_description; ?></textarea>
                            </div>

                            <!-- menu price update  -->
                            <div class="update_menu_price_outer" style="display: <?php echo $showUpdateMenuPrice; ?>;">
                                <div class="trion-menu-craft-admin-label">
                                    <label for="trion-menu-craft-lbl">Actualizar precio del menú</label>
                                </div>
                                <div class="trion-menu-craft-input">
                                    <input class="update_menu_price" type="text" name="update_menu_price"
                                        value="<?php echo $menu_items->menu_pricing; ?>"></input>
                                </div>
                                <div class="trion-menu-craft-admin-label">
                                    <label for="trion-menu-craft-lbl">Actualizar precio del menú</label>
                                </div>
                                <div class="trion-menu-craft-input">
                                    <input class="update_menu_price_eng" type="text" name="update_menu_price_eng"
                                        value="<?php echo $menu_items->menu_pricing_eng; ?>"></input>
                                </div>
                                <div class="trion-menu-craft-admin-label">
                                    <label for="trion-menu-craft-lbl">Actualizar precio del menú</label>
                                </div>
                                <div class="trion-menu-craft-input">
                                    <input class="update_menu_price_eus" type="text" name="update_menu_price_eus"
                                        value="<?php echo $menu_items->menu_pricing_eus; ?>"></input>
                                </div>
                                <div class="trion-menu-craft-admin-label">
                                    <label for="trion-menu-craft-lbl">Actualizar precio del menú</label>
                                </div>
                                <div class="trion-menu-craft-input">
                                    <input class="update_menu_price_fr" type="text" name="update_menu_price_fr"
                                        value="<?php echo $menu_items->menu_pricing_fr; ?>"></input>
                                </div>
                            </div>
                        </div>
                        <div class="trion-menu-craft-admin-foter">
                            <input type="submit" name="submit" class="craft_button_addon craft_green" value="Menú de actualización">
                            <button type="button" class="craft_button_addon craft_green"
                                id="cancel_update_menu_btn">Cancelar</button>
                        </div>
                    </form>
                </div>
            <?php  }
        } ?>
    </div>

    <!--******************* Fetch Menus data *******************-->
    <?php
    $menuITEms = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    ?>

    <div class="trion-menu-craft-outer-admin">
        <div class="trion-menu-craft-inner-admin">
            <div class="trion-menu-craft-inner-content-admin">
                <div class="trion-menu-craft-admin-label">
                    <label for="trion-menu-craft-lbl">Menús</label>
                </div>
                <div class="trion-menu-craft-admin-invite-emails"> 
                <table id="menu_table">
                    <thead>
                        <tr>
                            <th>Identificación</th>
                            <th>Nombre de la categoría</th>
                            <th>Nombre del menú</th>
                            <th>Descripción del menú</th>
                            <th>Precio del menú</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        if (count($menuITEms) > 0) 
                        {
                            foreach ($menuITEms as $menuITEm) 
                            {
                                $category_id = $menuITEm['category_id'];
                                $fetch_cat_data = $wpdb->get_results("SELECT * FROM $cat_table_name WHERE id= $category_id", ARRAY_A);
                            
                                foreach($fetch_cat_data as $cat_data)
                                {?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $cat_data['category_name']; ?></td>
                                        <td><?php echo $menuITEm['menu_name']; ?></td>
                                        <td><?php echo $menuITEm['menu_description']; ?></td>
                                        <td>
                                            <?php if($cat_data['slug'] === 'special' || $cat_data['slug'] === 'other-cat' || $cat_data['slug'] === 'daily')
                                            {
                                                echo $menuITEm['menu_pricing'];
                                            } 
                                            else
                                            {
                                                echo "-----";
                                            } ?>
                                        </td>
                                        <td>
                                             <?php if($cat_data['slug'] === 'main' || $cat_data['slug'] === 'special' || $cat_data['slug'] === 'other-cat' || $cat_data['slug'] === 'daily')
                                             {?>
                                                 <button type="button" class="btn btn-success">
                                                    <a class="trion-menu-craft-edit-btn m-btn" id="menu_edit_btn" href="<?php echo esc_url(admin_url('admin.php?page=trion-menu-craft-plugin-menus&action=edit_menu&id=' . $menuITEm['id'])); ?>">Editar</a>
                                                </button>

                                                <!-- <button type="button" class="btn btn-danger trion-menu-craft-delete-btn del_menu" data-id="<?php //echo $menuITEm['id'];?>">Borrar
                                                </button> -->
                                            <?php }
                                            else
                                            {
                                                echo "------";
                                            } ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                            }
                        } 
                        else 
                        {
                            echo '<tr><td colspan="3">No se encontró ningún menú.</td></tr>';
                        } ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>