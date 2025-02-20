<!-- get menus  -->
<div class="trion-menu-craft-main">
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'trion_menu_tbl_meta';
    $cat_table = $wpdb->prefix . 'trion_category_tbl_meta';
    $menus = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    ?>
    <div class="trion-menu-craft-outer-admin">
        <div class="trion-menu-craft-menu-admin add_option_menu">
            <div class="trion-menu-craft-inner-content-menu-admin pdf-page-option">
                <div class="trion-menu-craft-inputs-outers">
                    <div class="trion-menu-craft-input">
                        <label>Seleccionar menús</label>
                        <select name="get_menu_pdf" id="get_menu_pdf">
                            <option value="">Seleccionar menús</option>
                            <?php
                            if(count($menus) > 0)
                            {
                                foreach($menus as $data)
                                { 
                                    $cat_query = $wpdb->get_row("SELECT * FROM $cat_table WHERE id = '".$data['category_id']."'", ARRAY_A);

                                    if(isset($cat_query['slug']) && $cat_query['slug']  != 'other-cat')
                                    {
                                    ?>
                                        <option value="<?php echo $data['id']; ?>" data-slug="<?php echo $data['menu_name']; ?>"><?php echo $data['menu_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                    
                                <?php }
                            }?>
                        </select>
                    </div>
                </div>

                <div class="trion-menu-craft-inputs-outers gen_pdf_lang_outer" style="display:none;">
                    <div class="trion-menu-craft-input">
                        <label>Seleccione el idioma</label>
                        <select name="get_language_pdf" id="get_language_pdf">
                            <option value="EN">EN</option>
                            <option value="ES">ES</option>
                            <option value="EU">EUS</option>
                            <option value="FR">FR</option>
                        </select>
                    </div>
                </div>

                <!--******* generate pdf btn  ***********-->
                <div class="gen_pdf_outer" style="display:none;">
                    <button type="button" class="btn btn-primary generate_pdf">Generar PDF</button>
                </div>
            </div>
        </div>
        <?php 
      $pdf_table_name =  $wpdb->prefix . 'trion_pdf_tbl_data';
        $pdf_data = $wpdb->get_results("SELECT id FROM $table_name", ARRAY_A);

        if($pdf_data)
        { ?>

        <!-- Show details -->
        <div class="pdf_details" >
            <table id="menuPDFDetails">
                <thead>
                    <tr>
                        <th>Identificación</th>
                        <th>Nombre del menú</th>
                        <th>EN PDF</th>
                        <th>EUS PDF</th>
                        <th>ES PDF</th>
                        <th>FR PDF</th>
                    </tr>
                </thead>
                <tbody>  
                    <?php
                    $i = 1;
                    if (count($pdf_data) > 0) 
                    {
                        foreach ($pdf_data as $pdf_item) 
                        {
  
                            $menus_info = $wpdb->get_row("SELECT menu_name FROM $table_name where id = ".$pdf_item['id']);

                            $results = $wpdb->get_results("SELECT * FROM $pdf_table_name WHERE pdf_menu_id = ".$pdf_item['id']);

                            if($results)
                            {
                                $menu_data = array(); // Initialize the result array

                                $languages = array('EN', 'EU', 'ES', 'FR');

                                // Initialize the $menu_data array with empty arrays for each language
                                $menu_data = array();
                                foreach ($languages as $language) {
                                    $menu_data[$language] = array();
                                }
                                
                                foreach ($results as $row) {
                                    $language = $row->pdf_lang;
                                    $pdf_link = $row->pdf_link;

                                    // Check if the language key exists in the result array, if not, create it
                                    if (!array_key_exists($language, $menu_data)) {
                                        $menu_data[$language] = array();
                                    }

                                    // Add the id and pdf link to the language array
                                    $menu_data[$language][] = array(
                                        'id' => $row->id,
                                        'pdf_link' => $pdf_link
                                    );
                                }         
                                ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $menus_info->menu_name; ?></td>
                                    <?php foreach($menu_data as $key => $pdf_item ){ ?>
                                        <?php if($key == 'EN' && $pdf_item){ ?>
                                            <td class="pdf_link">
                                                <div class="pdf_actions">
                                                    <a class="trion-menu-craft-copy-btn copy_pdff" href="<?php echo $pdf_item[0]['pdf_link']; ?>"><i class="fas fa-copy" aria-hidden="true"></i></a>

                                                    <a href="<?php echo $pdf_item[0]['pdf_link']; ?>" target="_blank"><i class="fas fa-eye" aria-hidden="true"></i></a>

                                                    <a href="#" class="trion-menu-craft-delete-btn delete_pdff" data-id="<?php echo $pdf_item[0]['id']; ?>"><i class="fas fa-trash" aria-hidden="true"></i></a>
                                                </div>
                                            </td>
                                        <?php }else if($key == 'FR' && $pdf_item){ ?>
                                            <td class="pdf_link">
                                                <div class="pdf_actions">
                                                    <a class="trion-menu-craft-copy-btn copy_pdff" href="<?php echo $pdf_item[0]['pdf_link']; ?>"><i class="fas fa-copy" aria-hidden="true"></i></a>

                                                    <a href="<?php echo $pdf_item[0]['pdf_link']; ?>" target="_blank"><i class="fas fa-eye" aria-hidden="true"></i></a>

                                                    <a href="#" class="trion-menu-craft-delete-btn delete_pdff" data-id="<?php echo $pdf_item[0]['id']; ?>"><i class="fas fa-trash" aria-hidden="true"></i></a>
                                                </div>
                                            </td>
                                        <?php }else if($key == 'ES' && $pdf_item){ ?>
                                            <td class="pdf_link">
                                                <div class="pdf_actions">
                                                    <a class="trion-menu-craft-copy-btn copy_pdff" href="<?php echo $pdf_item[0]['pdf_link']; ?>"><i class="fas fa-copy" aria-hidden="true"></i></a>

                                                    <a href="<?php echo $pdf_item[0]['pdf_link']; ?>" target="_blank"><i class="fas fa-eye" aria-hidden="true"></i></a>

                                                    <a href="#" class="trion-menu-craft-delete-btn delete_pdff" data-id="<?php echo $pdf_item[0]['id']; ?>"><i class="fas fa-trash" aria-hidden="true"></i></a>
                                                </div>
                                            </td>
                                        <?php }else if($key == 'EU' && $pdf_item){ ?>
                                            <td class="pdf_link">
                                                <div class="pdf_actions">
                                                    <a class="trion-menu-craft-copy-btn copy_pdff" href="<?php echo $pdf_item[0]['pdf_link']; ?>"><i class="fas fa-copy" aria-hidden="true"></i></a>

                                                    <a href="<?php echo $pdf_item[0]['pdf_link']; ?>" target="_blank"><i class="fas fa-eye" aria-hidden="true"></i></a>

                                                    <a href="#" class="trion-menu-craft-delete-btn delete_pdff" data-id="<?php echo $pdf_item[0]['id']; ?>"><i class="fas fa-trash" aria-hidden="true"></i></a>
                                                </div>
                                            </td>
                                        <?php }else{ ?>
                                            <td class="pdf_link">
                                                <div class="pdf_actions">___ ___ ___ ___ ___</div>
                                            </td>
                                        <?php 
                                    } ?>
                                    <?php } ?>
                                </tr>
                        <?php }
                        }
                    } 
                    else 
                    {
                        echo '<tr><td>Datos no encontrados.</td></tr>';
                    } ?>
                </tbody>
            </table>
        </div>

        <?php } ?>
    </div>
</div>