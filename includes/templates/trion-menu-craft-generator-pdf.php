<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL & ~E_DEPRECATED);


    // Start output buffering
    ob_start(); 

    require_once('tcpdf/tcpdf.php');
    //require_once('tcpdf/examples/tcpdf_include.php');

    /**
     *
     * Call translater function of autotranslate text
     *
     */
    require_once('pdf-translater/Tradutor.php');


    //  include plugin_dir_path(__FILE__) .  'pdf-translater/Tradutor.php';

    $lang = $menu_lang_id;
    $pdfFilename = '';
    /***************** Modified function with language and label parameters ***********/
    function tsl_lg($label, $language) {

        $tradutor = new Tradutor();

        // Translate the label based on the provided language
        $translated_label = $tradutor->traduzLang(null, $language, $label);

        // Use the translated label in your code
        // Ensure the text does not exceed 42 characters
        $limited_text = substr($translated_label, 0, 42);

        return $limited_text;
    }
    /* capitalize */
    function capitalizeFirstWord($str) 
    {
        return ucfirst(strtolower($str));
    }


    /**************** Tcpdf ***********************/ 
    class MYPDF extends TCPDF 
    {   
        public function Footer() 
        {
            $logoPath = PLUGIN_URL .'logo-black-tp.jpg';

            $this->SetY(-15);

            $this->SetFont('helvetica', 'I', 8);

            $this->Image($logoPath, 25, 275, 40, '', 'JPG', '', 'B', false, 300, '', false, false, 0, false, false, false);
        }

    }

    /************* 42 character limit *********************/ 
    // Function to trim and limit characters
    function trimAndLimitCharacters($text, $character_limit) 
    {
        $trimmed_text = '';
        $char_count = 0;

        // Iterate through each character and add it to the trimmed text until the character limit is reached
        for ($i = 0; $i < mb_strlen($text, 'UTF-8') && $char_count < $character_limit; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');

            // Add the character to the trimmed text
            $trimmed_text .= $char;

            // Increment the character count if the character is not a space
            if ($char !== ' ') {
                $char_count++;
            }
        }

        return $trimmed_text;
    }
    /************* end sec 42 character limit *********************/ 

    // Create a new TCPDF instance
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nicola Asuni');
    $pdf->SetTitle('TCPDF Example 002');
    $pdf->SetSubject('TCPDF Tutorial');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

    $logoPath = PLUGIN_URL .'logo-black-tp.jpg';

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //local 
    // $root_directory = str_replace('wp-content\plugins\wp-menu-craft/', '', PLUGIN_PATH);

    //live website 
    $root_directory = str_replace('wp-content/plugins/wp-menu-craft/', '', PLUGIN_PATH);


    require_once $root_directory . 'wp-load.php';

    // $logoPath = PLUGIN_URL .'logo-black.jpeg';

    // $pdfContent = '';

    /********** menu id *************/ 
    $menu_idd = $menu_id;

    /************ pdf land id **********/ 
    $pdf_lang = $menu_lang_id;

    /******************* get menu data **********************/ 
    global $wpdb;
    
    $menu_table                     = $wpdb->prefix . 'trion_menu_tbl_meta';
    $dish_table                     = $wpdb->prefix . 'trion_dish_tbl_meta';
    $service_table                  = $wpdb->prefix . 'trion_service_tbl_meta';
    $service_meta_table             = $wpdb->prefix . 'trion_special_service_tbl_dish_meta';
    $service_menu_meta_table        = $wpdb->prefix . 'trion_special_menu_service_tbl_meta';
    $category_tbl_meta              = $wpdb->prefix . 'trion_category_tbl_meta';
    $main_menu_service_tbl_meta     = $wpdb->prefix . 'trion_main_menu_service_tbl_meta';
    $main_service_tbl_dish_meta     = $wpdb->prefix . 'trion_main_service_tbl_dish_meta';
    $daily_menu_service_tbl_meta    = $wpdb->prefix . 'trion_daily_menu_service_tbl_meta';
    $daily_service_tbl_dish_meta    = $wpdb->prefix . 'trion_daily_service_tbl_dish_meta';
    $other_menu_dish_meta           = $wpdb->prefix . 'trion_other_menu_dish_meta';




    $menus = $wpdb->get_results("SELECT * FROM $menu_table WHERE id =".$menu_idd, ARRAY_A);

    if (count($menus) > 0) 
    {
        foreach ($menus as $menu_data) 
        {
            /*echo "<pre>";
            print_r($menu_data);
            echo "</pre>";*/
            
            $menu_id          = $menu_data['id'];
            $menu_name        = $menu_data['menu_name'];
            $menu_description = $menu_data['menu_description'];
            if($pdf_lang === 'ES')
            {
                $menu_pricing = $menu_data['menu_pricing'];
            }
            else if($pdf_lang == 'EU')
            {
                $menu_pricing = $menu_data['menu_pricing_eus'];
            }
            else if($pdf_lang == 'FR')
            {
                $menu_pricing = $menu_data['menu_pricing_fr'];
            }
            else
            {
                $menu_pricing = $menu_data['menu_pricing_eng'];
            }

            $category_id      = $menu_data['category_id'];


            $pdf_menu_name = str_replace(' ', '_', $menu_name);

            if($pdf_lang === 'ES')
            {
                $pdfFilename = $pdf_menu_name."_ES_oianume.pdf";
            }
            else if($pdf_lang == 'EU')
            {
                $pdfFilename = $pdf_menu_name."_EU_oianume.pdf";
            }
            else if($pdf_lang == 'FR')
            {
                $pdfFilename = $pdf_menu_name."_FR_oianume.pdf";
            }
            else
            {
                $pdfFilename = $pdf_menu_name."_EN_oianume.pdf";
            }


            if($pdf_lang === 'ES')
            {
                $menu_name    = $menu_data['menu_name'];
            }
            else if($pdf_lang == 'EU')
            {
                $menu_name = $menu_data['menu_name_eus'];
            }
            else if($pdf_lang == 'FR')
            {
                $menu_name = $menu_data['menu_name_fr'];
            }
            else
            {
                $menu_name = $menu_data['menu_name_eng'];
            }


            // Print dynamic content in the PDF using Write()
            //$pdf->Write(0, $menu_name, '', 0, 'L', true, 0, false, false, 0);

            $category = $wpdb->get_results("SELECT * FROM $category_tbl_meta WHERE id =".$category_id, ARRAY_A);

            foreach($category as $category_data)
            {
                $cat_slug = $category_data['slug'];
                $cat_id   = $category_data['id'];

                /*************** main menu dishes *****************/ 
                if($cat_slug == 'main')
                {  
                    $pdf->SetFont('helvetica', '', 15);
                    $pdf->AddPage('L','A3');
                    $pdf->SetMargins(27, 25, 28 ,25);
                    $pdf->resetColumns();
                    $pdf->setEqualColumns(3, 117);
                    $pdf->setPrintFooter(true);
                    
                    /************** main menu service  **********************/  
                    // $main_services = $wpdb->get_results("SELECT * FROM $main_menu_service_tbl_meta WHERE parent_menu =".$cat_id, ARRAY_A);
                    $main_services = $wpdb->get_results("SELECT * FROM ".$main_menu_service_tbl_meta." WHERE parent_menu = ".$cat_id." ORDER BY main_service_ordering ASC", ARRAY_A);
                    if (count($menus) > 0) 
                    {
                        $countservice = 0;
                        foreach($main_services as $main_services_data)
                        {

                            $main_service_id = $main_services_data['id'];
                            $main_services_names_arr = $wpdb->get_results("SELECT * FROM ".$service_table." WHERE id = ".$main_service_id, ARRAY_A);
                            $main_services_names = $main_services_names_arr[0];

                            if($pdf_lang === 'ES')
                            {
                                $main_service_name1 = $main_services_names['service_name'];
                            }
                            else if($pdf_lang == 'EU')
                            {
                                $main_service_name1 = $main_services_names['service_name_eus'];
                            }
                            else if($pdf_lang == 'FR')
                            {
                                $main_service_name1 = $main_services_names['service_name_fr'];
                            }
                            else
                            {
                                $main_service_name1 = $main_services_names['service_name_eng'];
                            }

                            $main_service_id          = $main_services_data['id'];
                            $main_service_description = $main_services_data['service_description'];

                            $exploded_ser_main = explode(':', $main_service_name1);
                            $main_service_name = trim($exploded_ser_main[1]);
                        
                            if($countservice >   0)
                            {
                                $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                            }
                            $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts/';
                            $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                            $pdf->SetFont('Cormorant', '', 18); 
                            $pdf->SetTextColor(0, 0, 0);
                            //$pdf->Write(0, tsl_lg($main_service_name, $lang), '', 0, 'L', true, 0, false, false, 0);

                            $pdf->Write(0, ucwords($main_service_name), '', 0, 'L', true, 0, false, false, 0);
                            
                            /************** main service dish meta *****************/    
                            $main_dishes = $wpdb->get_results("SELECT * FROM $main_service_tbl_dish_meta WHERE main_service_id = ".$main_service_id." AND main_dish_status = 'true' ORDER BY main_dish_ordering IS NULL, CAST(main_dish_ordering AS SIGNED),main_dish_ordering ASC", ARRAY_A);  
                            
                            //  echo '<pre>';
                            //  print_r($main_dishes);
                            //  echo '</pre>';

                            foreach($main_dishes as $dishes)
                            {
                                $main_dish_id = $dishes['main_dish_meta_value'];

                                /**************** get service  dishes *******************/ 
                                $dishes_data = $wpdb->get_row("SELECT * FROM $dish_table WHERE id = ".$main_dish_id, ARRAY_A);

                                if($pdf_lang === 'ES')
                                {
                                    $main_dish_name = $dishes_data['dish_name_es'];
                                }
                                else if($pdf_lang == 'EU')
                                {
                                    $main_dish_name = $dishes_data['dish_name_eus'];
                                }
                                else if($pdf_lang == 'FR')
                                {
                                    $main_dish_name = $dishes_data['dish_name_fr'];
                                }
                                else
                                {
                                    $main_dish_name = $dishes_data['dish_name_eng'];
                                }

                                // echo '<pre>';
                                // print_r($main_dish_name);
                                // echo '</pre>';
                            

                                // Format the price using a regular expression
                                $formatted_price = preg_replace('/(\d{3})(\d{2})$/', '$1,$2', $dishes_data['dish_pricing']);
                                $main_dish_price = $formatted_price . ' €';

                                if ($pdf->GetY() >= $pdf->GetPageHeight() - 30) 
                                {
                                    $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts/';
                                    $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                                    $pdf->SetFont('Cormorant', '', 18); 
                                    $pdf->SetTextColor(0, 0, 0);

                                    if($pdf_lang === 'ES')
                                    {
                                        $pdf->Write(0, ucwords($main_service_name), '', 0, 'L', true, 0, false, false, 0);
                                    }
                                    else
                                    {
                                        $pdf->Write(0, ucwords($main_service_name), '', 0, 'L', true, 0, false, false, 0);
                                    }
                                
                                }
                                $pdf->SetFont('helvetica', '', 12);

                                $pdf->Ln(2);

                                
                                // $pdf->Cell(0, 0, substr($main_dish_name, 0, 42), 0, 0, 'L', false);

                                /***************** show 42 charcater dish ***************************/

                                // Set the desired character limit
                                $character_limit = 42;
                                $pdf->Cell(0, 0, trimAndLimitCharacters($main_dish_name, $character_limit), 0, 0, 'L', false);
                            
                                /***************** end show 42 charcater dish ***************************/ 


                                $pdf->Cell(0, 0, '', 0, 0, 'C', false);
                                if($dishes_data['dish_pricing']){
                                    $pdf->Cell(0, 0, $main_dish_price, 0, 1, 'R', false);
                                }else{
                                    $pdf->Cell(0, 0, '', 0, 1, 'R', false);
                                }

                            }
                            
                            $countservice++;
                        }

                        // die;

                        /*************** other menu dish meta data **********************/

                        $get_menus_table_data = $wpdb->get_results("SELECT * FROM $menu_table WHERE category_id = 4", ARRAY_A);

                        $secondForeachContent = '';
                        $thirdForeachContent = '';
                        $countOther = 0;
                        
                        foreach ($get_menus_table_data as $get_menus_item) 
                        {
                            $other_menu_id = $get_menus_item['id'];
                            $other_menu_name = $get_menus_item['menu_name'];

                            if($pdf_lang === 'ES')
                            {
                                $other_menu_name = $get_menus_item['menu_name'];
                            }
                            else if($pdf_lang == 'EU')
                            {
                                $other_menu_name = $get_menus_item['menu_name_eus'];
                            }
                            else if($pdf_lang == 'FR')
                            {
                                $other_menu_name = $get_menus_item['menu_name_fr'];
                            }
                            else
                            {
                                $other_menu_name = $get_menus_item['menu_name_eng'];
                            }

                            $other_menu_price = $get_menus_item['menu_pricing'];

                            if($pdf_lang === 'ES')
                            {
                                $other_menu_price = $get_menus_item['menu_pricing'];
                            }
                            else if($pdf_lang == 'EU')
                            {
                                $other_menu_price = $get_menus_item['menu_pricing_eus'];
                            }
                            else if($pdf_lang == 'FR')
                            {
                                $other_menu_price = $get_menus_item['menu_pricing_fr'];
                            }
                            else
                            {
                                $other_menu_price = $get_menus_item['menu_pricing_eng'];
                            }
                        
                            $pdf->SetDrawColor(50, 0, 0, 0);
                            $pdf->SetFillColor(100, 0, 0, 0);

                            $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts/';
                            $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                            $pdf->SetFont('Cormorant', '', 18); 

                            $pdf->Ln(7);
                        
                            if ($countOther == 0) {
                                $secondForeachContent .= "<h3>".$other_menu_name . "</h3><br />";
                                $secondForeachContent .= " <br />";
                            } else {
                                $thirdForeachContent .= "<h3>".$other_menu_name . "</h3><br />";
                                $thirdForeachContent .= " <br />";
                            }
                        
                            $others_dishes = $wpdb->get_results("SELECT * FROM $other_menu_dish_meta WHERE other_menu_id = " . $other_menu_id." AND other_dish_status = 'true'  ORDER BY other_dish_ordering IS NULL, CAST(other_dish_ordering AS SIGNED),other_dish_ordering ASC", ARRAY_A);

                       
                            foreach ($others_dishes as $other_dish_data) 
                            {
                                $other_menu_id = $other_dish_data['other_menu_id'];
                                $other_dish_meta_key = $other_dish_data['other_dish_meta_key'];
                                $other_dish_meta_value = $other_dish_data['other_dish_meta_value'];
                                $other_dish_status = $other_dish_data['other_dish_status'];
                        
                                /*********** other dish name ************/
                                if($pdf_lang === 'ES')
                                {
                                    $other_dish_name = $wpdb->get_var("SELECT dish_name_es FROM $dish_table WHERE id = " . $other_dish_meta_value);
                                }
                                else if($pdf_lang ==='EU')
                                {
                                    $other_dish_name = $wpdb->get_var("SELECT dish_name_eus FROM $dish_table WHERE id = " . $other_dish_meta_value);
                                }
                                else if($pdf_lang === 'FR')
                                {
                                    $other_dish_name = $wpdb->get_var("SELECT dish_name_fr FROM $dish_table WHERE id = " . $other_dish_meta_value);
                                }
                                else
                                {
                                    $other_dish_name = $wpdb->get_var("SELECT dish_name_eng FROM $dish_table WHERE id = " . $other_dish_meta_value);
                                }
                        
                                // Accumulate content for the second foreach loop
                                if ($countOther == 0) 
                                {
                                    // $secondForeachContent .= substr($other_dish_name, 0, 42) . "<br />";
                                    $secondForeachContent .= trimAndLimitCharacters($other_dish_name, $character_limit) . "<br />";
                                } 
                                else 
                                {
                                    // $thirdForeachContent .= substr($other_dish_name, 0, 42) . "<br />";
                                    $thirdForeachContent .= trimAndLimitCharacters($other_dish_name, $character_limit) . "<br />";
                                }
                                
                            }
                        
                            if ($other_menu_price) 
                            {
                                
                                $other_menu_price = "<b>" . $other_menu_price . "</b>";
                                if ($countOther == 0) {
                                    $secondForeachContent .= $other_menu_price . "<br />";
                                } else {
                                    $thirdForeachContent .= $other_menu_price . "<br />";
                                }
                            }
                        
                            $pdf->SetFont('helvetica', '', 13);
                            $countOther++;
                        }
                        
                        
                        $pdf->SetFillColor(230, 230, 230);
                        $pdf->SetDrawColor(0, 0, 0, 100);
                        $pdf->setCellPaddings(5, 4, 5, 3);
                        $pdf->SetFont('helvetica', '', 13);
                        $pdf->MultiCell(0, 10, $secondForeachContent, 1, 'L', true, 1, '', '', true, 0, true, true);
                        $pdf->Ln(12);
                        $pdf->MultiCell(0, 10, $thirdForeachContent, 1, 'L', true, 1, '', '', true, 0, true, true);
                        
                    }

                }
                /*************** daily menu dishes *********************/ 
                else if($cat_slug == 'daily')
                { 
                    $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts/';
                    $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                    $pdf->SetFont('Cormorant', '', 18); 
                    $pdf->SetTextColor(0, 0, 0);

                    $pdf->AddPage('P','A4');
                    $pdf->SetMargins(12, 5, 12 ,12);
                    $pdf->setEqualColumns(1, 197);
                    /************  Show pdf logo ********************/
                    $pdf->Image($logoPath, 85, 5, 40, '', 'JPG', '', 'B', false, 300, '', false, false, 0, false, false, false);   
                    $pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
                    
                    // $pdf->Write(0, ucwords(tsl_lg($menu_name, $lang)), '', 0, 'C', true, 0, false, false, 0);
                    $pdf->Write(0, $menu_name, '', 0, 'C', true, 0, false, false, 0);
                    $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                    $pdf->resetColumns();
                    $pdf->setEqualColumns(2, 85);

                    /************ daily menu services *************/ 
                    //  $daily_services = $wpdb->get_results("SELECT * FROM $daily_menu_service_tbl_meta WHERE parent_menu =".$cat_id, ARRAY_A);
                    $daily_services = $wpdb->get_results("SELECT * FROM ".$daily_menu_service_tbl_meta." WHERE parent_menu = ".$cat_id." ORDER BY daily_service_ordering ASC", ARRAY_A);
                    if (count($menus) > 0) 
                    {  
                        $countservice = 0;
                        foreach($daily_services as $daily_services_data)
                        {
                            $daily_service_id = $daily_services_data['id'];
                            $daily_services_names_arr = $wpdb->get_results("SELECT * FROM ".$service_table." WHERE id = ".$daily_service_id, ARRAY_A);
                            $daily_services_names = $daily_services_names_arr[0];

                            if($pdf_lang === 'ES')
                            {
                                $daily_service_name1 = $daily_services_names['service_name'];
                            }
                            else if($pdf_lang == 'EU')
                            {
                                $daily_service_name1 = $daily_services_names['service_name_eus'];
                            }
                            else if($pdf_lang == 'FR')
                            {
                                $daily_service_name1 = $daily_services_names['service_name_fr'];
                            }
                            else
                            {
                                $daily_service_name1 = $daily_services_names['service_name_eng'];
                            }


                            $daily_service_description = $daily_services_data['service_description'];

                            $exploded_ser_daily = explode(':', $daily_service_name1);
                            $daily_service_name = trim($exploded_ser_daily[1]);

                            $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts/';
                            $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                            $pdf->SetFont('Cormorant', '', 18); 
                            $pdf->SetTextColor(0, 0, 0);

                            if($countservice > 0)
                            {
                                $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                            }

                            //$pdf->Write(0, tsl_lg($daily_service_name, $lang), '', 0, 'L', true, 0, false, false, 0);

                            $pdf->Write(0, ucwords($daily_service_name), '', 0, 'L', true, 0, false, false, 0);
                            
                            /************** daily service dish meta *****************/    
                            $daily_dishes = $wpdb->get_results("SELECT * FROM $daily_service_tbl_dish_meta WHERE daily_service_id = ".$daily_service_id." AND daily_dish_status = 'true' ORDER BY daily_dish_ordering IS NULL, CAST(daily_dish_ordering AS SIGNED),daily_dish_ordering ASC", ARRAY_A);   

                            foreach($daily_dishes as $dishes)
                            {
                                $daily_dish_id = $dishes['daily_dish_meta_value'];

                                /**************** get service  dishes *******************/ 
                                $dishes_data = $wpdb->get_row("SELECT * FROM $dish_table WHERE id = ".$daily_dish_id, ARRAY_A);

                                if($pdf_lang == 'ES')
                                {
                                    $daily_dish_name = $dishes_data['dish_name_es'];
                                }
                                else if($pdf_lang == 'EU')
                                {
                                    $daily_dish_name = $dishes_data['dish_name_eus'];
                                }
                                else if($pdf_lang == 'FR')
                                {
                                    $daily_dish_name = $dishes_data['dish_name_fr'];
                                }
                                else
                                {
                                    $daily_dish_name = $dishes_data['dish_name_eng'];
                                }

                                $formatted_price = preg_replace('/(\d{3})(\d{2})$/', '$1,$2', $dishes_data['dish_pricing']);
                                $daily_dish_price = $formatted_price . ' €';

                                if ($pdf->GetY() >= $pdf->GetPageHeight() - 30) 
                                {
                                    $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts/';
                                    $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                                    $pdf->SetFont('Cormorant', '', 18); 
                                    $pdf->SetTextColor(0, 0, 0);

                                    if($pdf_lang == 'ES')
                                    {
                                        $pdf->Write(0, ucwords($daily_service_name), '', 0, 'L', true, 0, false, false, 0);
                                    }
                                    else
                                    {
                                        $pdf->Write(0, ucwords($daily_service_name), '', 0, 'L', true, 0, false, false, 0);
                                    }
                                
                                }
                                
                                $pdf->SetFont('helvetica', '', 12);
                                // $pdf->Ln(1);
                                $pdf->Ln(0.7);
                            
                                // $pdf->Cell(0, 0, substr($daily_dish_name, 0, 42), 0, 0, 'L', false);

                                $character_limit = 42;
                                $pdf->Cell(0, 0, trimAndLimitCharacters($daily_dish_name, $character_limit), 0, 0, 'L', false);

                                $pdf->Cell(0, 0, '', 0, 0, 'C', false);
                                // $pdf->Cell(0, 0, $daily_dish_price, 0, 1, 'R', false);
                                $pdf->Cell(0, 0,'', 0, 1, 'R', false);
                                
                            }
                            $countservice++;
                        }

                        /********* daily price sec *************/ 
                       
                        $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts/';
                        $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                        $pdf->SetFont('Cormorant', '', 16);
                        $pdf->SetTextColor(0, 0, 0);
                        $daily_menu_price = $menu_pricing;
                    
                        $d_menu_price = $daily_menu_price;
                        // Calculate the width of the text
                        $textWidth = $pdf->GetStringWidth($d_menu_price);

                        // Center the text horizontally
                        $xPosition = ($pdf->GetPageWidth() - $textWidth) / 2;

                        $pdf->Ln(30);
                        $pdf->SetX($xPosition); // Set X position to center

                        $pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
                        $pdf->Write(0, $d_menu_price, '', 0, 'C', true, 0, false, false, 0);
                    }
                }
                /******************** special menu services/dishes *********************/ 
                else if($cat_slug == 'special')
                {   
                    /************ Special pdf ****************/ 

                    $pdf->AddPage('P','A4');
                    $pdf->SetMargins(20, 25,20 ,25);
                    $pdf->resetColumns();
                    $pdf->setEqualColumns(1, 120);

                    /************  Show pdf logo ********************/
                    $pdf->Image($logoPath, 88, 4, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
                    
                    /************ special menu services *************/ 
                    // $special_services = $wpdb->get_results("SELECT * FROM $service_menu_meta_table   WHERE parent_menu = ".$category_id, ARRAY_A);
                    $special_services = $wpdb->get_results("SELECT * FROM ".$service_menu_meta_table." WHERE parent_menu = ".$cat_id." ORDER BY specail_service_ordering ASC", ARRAY_A);

                    if (count($menus) > 0) 
                    {   
                        $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts/';
                        $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                        $pdf->SetFont('Cormorant', '', 18);
                        $pdf->SetTextColor(0, 0, 0);

                        $pdf->Write(23, '', '', 0, 'C', true, 0, false, false, 0);

                        $pdf->Write(0, $menu_name, '', 0, 'C', true, 0, false, false, 0);
                        

                        $serviceLength = count($special_services);
                        $counte_check = 1;
                        foreach($special_services as $special_service_data)
                        {
                            $special_service_id = $special_service_data['id'];
                            $special_services_names_arr = $wpdb->get_results("SELECT * FROM ".$service_table." WHERE id = ".$special_service_id, ARRAY_A);
                            $special_services_names = $special_services_names_arr[0];

                            if($pdf_lang === 'ES')
                            {
                                $service_name1 = $special_services_names['service_name'];
                            }
                            else if($pdf_lang == 'EU')
                            {
                                $service_name1 = $special_services_names['service_name_eus'];
                            }
                            else if($pdf_lang == 'FR')
                            {
                                $service_name1 = $special_services_names['service_name_fr'];
                            }
                            else
                            {
                                $service_name1 = $special_services_names['service_name_eng'];
                            }

                            $service_id          = $special_service_data['id'];

                            $exploded_ser_special = explode(':', $service_name1);
                            $service_name = trim($exploded_ser_special[1]);

                            $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts/';
                            $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                            $pdf->SetFont('Cormorant', '', 18); 
                            $pdf->SetTextColor(0, 0, 0);
                            if($counte_check == $serviceLength)
                            {
                                // $pdf->Ln(-2);  
                                $pdf->Ln(-5);  
                            }
                            else
                            {
                                $pdf->Ln(0);  
                            }
                            $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);

                            $pdf->Write(0, ucwords($service_name), '', 0, 'C', true, 0, false, false, 0);

                            /************** service dish meta *****************/    
                            $special_dishes = $wpdb->get_results("SELECT * FROM $service_meta_table WHERE service_id = ".$service_id." AND dish_status = 'true' ORDER BY special_dish_ordering IS NULL, CAST(special_dish_ordering AS SIGNED),special_dish_ordering ASC", ARRAY_A);   


                            foreach($special_dishes as $dishes)
                            {
                                $special_dish_id = $dishes['dish_meta_value'];

                                /**************** get service  dishes *******************/ 
                                $s_dishes_data = $wpdb->get_row("SELECT * FROM $dish_table WHERE id = ".$special_dish_id, ARRAY_A);

                                if($pdf_lang == 'ES')
                                {
                                    $special_dish_name = $s_dishes_data['dish_name_es'];
                                }
                                else if($pdf_lang == 'EU')
                                {
                                    $special_dish_name = $s_dishes_data['dish_name_eus'];
                                }
                                else if($pdf_lang == 'FR')
                                {
                                    $special_dish_name = $s_dishes_data['dish_name_fr'];
                                }
                                else
                                {
                                    $special_dish_name = $s_dishes_data['dish_name_eng'];
                                }

                                $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts-new/';
                                $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                                $pdf->SetFont('helvetica', '', 12);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Ln(1);
                                $character_limit = 42;
                                $pdf->Write(0, trimAndLimitCharacters($special_dish_name, $character_limit), '', 0, 'C', true, 0, false, false, 0);

                                $formatted_price = preg_replace('/(\d{3})(\d{2})$/', '$1,$2', $s_dishes_data['dish_pricing']);
                                $special_dish_price = $formatted_price . ' €';

                            }
                            $counte_check++;
                        }

                        $fontDir = PLUGIN_PATH . 'includes/templates/tcpdf/fonts/';
                        $font = $pdf->AddFont('Cormorant', '', $fontDir . 'Cormorant.php');
                        $pdf->SetFont('Cormorant', '', 16);
                        $pdf->SetTextColor(0, 0, 0);
                        $special_menu_price = $menu_pricing;

                        $s_menu_price = $special_menu_price;

                        $pdf->Ln(1);
                        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                        $pdf->Write(0, $s_menu_price, '', 0, 'C', true, 0, false, false, 0);
                        
                    }
                }
                
            }    
        }
    }
    // Print dynamic content in the PDF using Write()
    // $pdf->Write(0, $pdfContent, '', 0, 'L', true, 0, false, false, 0);

    // Close output buffering and send PDF to the browser
    ob_end_flush();

    // Generate a unique identifier
    $uniqueIdentifier = uniqid();

    // Generate a random number (e.g., between 1 and 1000)
    $randomNumber = rand(1, 1000);

    if($pdfFilename == ''){
        $pdfFilename = "example_" . $uniqueIdentifier . "_" . str_pad($randomNumber, 3, '0', STR_PAD_LEFT) . ".pdf";
    }

    $upload_dir   = wp_upload_dir();

    // Specify the directory where you want to save the PDF file
    $pdfDirectory = PLUGIN_PATH . 'uploads/';

    // Combine the directory and filename to get the full path
    $pdfFilePath = $pdfDirectory . $pdfFilename;

    $pdf->Output($pdfFilePath, 'F'); // Save the PDF to the specified file path

    // Set HTTP headers for PDF download (optional)
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="example_002.pdf"');

    $pdf_url =  esc_url(PLUGIN_URL . "uploads/".$pdfFilename);
    $pdf_menu_id = $menu_idd;

    /***************************Insert/Update pdf data***********************************/ 
    global $wpdb;

    $pdf_table = $wpdb->prefix . 'trion_pdf_tbl_data'; 

    $result = $wpdb->get_row(
        $wpdb->prepare(
            "select * from $pdf_table WHERE pdf_menu_id ='$pdf_menu_id' AND pdf_lang = '$pdf_lang'",
        ), ARRAY_A
    );

    if($result)
    {

        $pdfFilePath = $result['pdf_path'];

        if (file_exists($pdfFilePath) && unlink($pdfFilePath)){} 
        
        $wpdb->update(
            $pdf_table,
            array(
                'pdf_link' => $pdf_url,
                'pdf_path' => $pdfFilePath,
            ),
            array('pdf_menu_id' => $menu_idd, 'pdf_lang' => $pdf_lang,),
        );
    }
    else
    {
        $data = array(
            'pdf_menu_id' => $pdf_menu_id,
            'pdf_link' => $pdf_url,
            'pdf_path' => $pdfFilePath,
            'pdf_lang' => $pdf_lang, 
        );
        $wpdb->insert($pdf_table, $data);
    }
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
