<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://dirkconsulting.com
 * @since      1.0.0
 *
 * @package    Trion_Menu_Craft
 * @subpackage Trion_Menu_Craft/admin/partials
 */
?>

<?php

// Custom sorting function
function customMainSort($a, $b) {
	if (empty($a['main_dish_ordering']) && empty($b['main_dish_ordering'])) {
		return 0;
	} elseif (empty($a['main_dish_ordering'])) {
		return 1;
	} elseif (empty($b['main_dish_ordering'])) {
		return -1;
	}
	return $a['main_dish_ordering'] - $b['main_dish_ordering'];
}

function customDailySort($a, $b) {
	if (empty($a['daily_dish_ordering']) && empty($b['daily_dish_ordering'])) {
		return 0;
	} elseif (empty($a['daily_dish_ordering'])) {
		return 1;
	} elseif (empty($b['daily_dish_ordering'])) {
		return -1;
	}
	return $a['daily_dish_ordering'] - $b['daily_dish_ordering'];
}

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<!-- wp menu craft page show menus -->
<body class="custom-bg">
    <?php
    global $wpdb;
    $service_table      		= $wpdb->prefix . 'trion_service_tbl_meta';
    $category_table				= $wpdb->prefix . 'trion_category_tbl_meta';
    $menus_table   				= $wpdb->prefix . 'trion_menu_tbl_meta';
    $dishes_table       		= $wpdb->prefix . 'trion_dish_tbl_meta';
    $special_service_tbl_dish_meta  = $wpdb->prefix . 'trion_special_service_tbl_dish_meta';
    $special_menu_service_tbl_meta  = $wpdb->prefix . 'trion_special_menu_service_tbl_meta';
    $other_meta_dishes_table  	= $wpdb->prefix . 'trion_other_menu_dish_meta';
	$main_menu_service_table 	= $wpdb->prefix . 'trion_main_menu_service_tbl_meta';
	$main_service_tbl_dish_meta = $wpdb->prefix . 'trion_main_service_tbl_dish_meta';
	$daily_menu_service_tbl_meta = $wpdb->prefix . 'trion_daily_menu_service_tbl_meta'; 
	$daily_service_tbl_dish_meta = $wpdb->prefix. 'trion_daily_service_tbl_dish_meta';
	// $special_menu_service_tbl_meta = $wpdb->prefix. 'trion_special_menu_service_tbl_meta';
    ?>
    <h1 class="heading-1">NUESTROS MENÚS</h1>
    <ul class="mdn-accordion indigo-accordion-theme">
        <?php
        /************ Show all menus ******************/ 
        $menus_data = $wpdb->get_results("SELECT * FROM $menus_table", ARRAY_A);

        if (count($menus_data) > 0) 
        {
            $count_menus = 0;
            foreach ($menus_data as $menu_item) 
            {   
              $category_id = $menu_item['category_id'];
              $category_slug = $wpdb->get_var("SELECT slug FROM $category_table where id = $category_id");
              ?>
                <!-- get menus name  -->
                <li class="sub-level menu-level">
                    <input class="accordion-toggle" type="checkbox" name="dish_ids[]" id="group-<?php echo $count_menus; ?>">

                    <label class="accordion-title" for="group-<?php echo $count_menus; ?>" data-menu-id="<?php echo $menu_item['id']; ?>" data-slug ="<?php echo $category_slug;?>"> 
					<i class="fa fa-bars"></i>
					<?php echo $menu_item['menu_name'] ?> 
					</label>

                    <!--*************** dish sec **********************-->
                    <ul class="droppable_area_service custom_ser_dish"  data-menu="<?php echo $menu_item['id']; ?>" data-slug ="<?php echo $category_slug;?>">
                        <!--********* get main menu sec  *************-->
                        <?php
						if($category_slug == 'main')
						{?>
							<li class="add_main_menu_service">
								<button type="button" class="main_menu_service" data-toggle="modal" data-target="#main_menu_service_modal" data-id ="<?php echo $menu_item['id']; ?>"><?php echo 'Seleccione Servicios del Menú Principal'; ?></button>
							</li>

							<!--*************** main menu services list ****************** -->
							<?php
							$main_menu_service_table_data = $wpdb->get_results("SELECT * FROM $main_menu_service_table WHERE parent_menu = '".$menu_item['id']."' ORDER BY main_service_ordering ASC", ARRAY_A);

							foreach ($main_menu_service_table_data as $main_service) 
							{?>
								<li class="sub-level menu-level service_title_area">
									<input class="accordion-toggle" type="checkbox" name="main_service[]" id="group-main-service-<?php echo $main_service['id']; ?>">

									<label class="accordion-title custom_service" for="group-main-service-<?php echo $main_service['id']; ?>" data-menu-id="<?php echo $main_service['id']; ?>"> 
										
										<span class="service_name">
											<i class="fa fa-bars"></i>
											<?php echo $main_service['service_name']  ?> 
										</span>
									</label>

									<!-- main menu service action -->
									<span class="main_service_actions">

										<span class="main_service_delete_action">
											<button class="main_service_delete" data-id="<?php echo $main_service['id'] ?>">
											<i class="fa fa-trash"></i>
											</button>
										</span>
									</span>

									<!-- main menu service dishes  -->
									<ul class="droppable_area_dish" data-menu="<?php echo $menu_item['id']; ?>" data-id="<?php echo $main_service['id'] ?>">

										<!-- *********** Search dishes sec ************ -->
										<div id="search">
											<label for="searchInput">Buscar platos</label>
											<input class="searchInput" type="text">
										</div>
										<div id="no-dishes-found" style="display: none;">No se encontraron platos</div>
										
										<!-- *********** Search dishes sec end ************ -->
										<?php

										$dishes_data = $wpdb->get_results("SELECT * FROM $dishes_table where dish_status = 'true'", ARRAY_A);


										$main_meta_dishes_result1 = $wpdb->get_results("SELECT * FROM $main_service_tbl_dish_meta where  main_service_id = ".$main_service['id']." ORDER BY main_dish_ordering ASC" , ARRAY_A);
										
										$mergedArray = array();
										foreach ($dishes_data as $dish) {
											$matchFound = false;
											foreach ($main_meta_dishes_result1 as $result) {
												if ($dish['id'] == $result['main_dish_meta_value']) {
													$mergedEntry = array_merge($dish, $result);
													$mergedArray[] = $mergedEntry;
													$matchFound = true;
												}
											}
											
											// If no match was found, add the $dish from $dishes_data to $mergedArray
											if (!$matchFound) {
												$dish['main_service_id'] = '';
												$dish['main_dish_meta_key'] = '_main_dish';
												$dish['main_dish_meta_value'] = '';
												$dish['main_dish_status'] = '';
												$dish['main_dish_ordering'] = '';
												$mergedArray[] = $dish;
											}
										}

										usort($mergedArray, 'customMainSort');

										$mergedArray = array_values($mergedArray);
	

										if (count($mergedArray) > 0) 
										{
											foreach ($mergedArray as $dishes_items) 
											{ 			

												$selected_services = unserialize($dishes_items['parent_service']);

												$jsonData = stripslashes($selected_services);
												$decodedData = json_decode($jsonData, true);
												
												$checked = false;
												if ($decodedData !== null) 
												{
													foreach($decodedData as $checkdata)
													{	
														if($checkdata['service_id'] == $main_service['id'])
														{
															$checked = true;														
														}
													}
												}
												
												if($checked)
												{
													if($dishes_items['main_dish_meta_value'] == '' || empty($dishes_items['main_dish_meta_value'])){
														$dishes_items['main_dish_meta_value'] = $dishes_items['id'];
													}
													?>
														<li class="dish-level" value="<?php echo $dishes_items['main_dish_meta_value']; ?>" data-id="group-dish-order-<?php echo $main_service['id']; ?>" data-dish-id="<?php echo $dishes_items['main_dish_meta_value']; ?>">
														
															<a href="#">
																<input type="checkbox" id="main_dishess" class="main_service_dish" name="main_dish_ids[]" value="<?php echo $dishes_items['main_dish_meta_value']; ?>" data-service-id="<?php echo $main_service['id']; ?>" <?php if ($dishes_items['main_dish_status'] === 'true') echo 'checked'; ?>>
																<i class="fas fa-utensils"></i>
																<?php echo $dishes_items['dish_name_es'] ?>
															</a>
														</li>
													<?php 
												}
											}
									    } ?>
									</ul>
								</li>
								<?php
							}						      
                        } 
                        /********** Daily menu sec ***********/ 
                        else if($category_slug == 'daily')
                        {?>
							<li class="add_daily_menu_servicess">
								<button type="button" class="add_daily_service" data-toggle="modal" data-target="#daily_services_modal" data-id ="<?php echo $menu_item['id']; ?>"><?php echo 'Seleccionar Servicio de Menú Diario'; ?></button>
							</li>

							<!-- daily services list  -->
							<?php
							$daily_service_table_data = $wpdb->get_results("SELECT * FROM $daily_menu_service_tbl_meta WHERE parent_menu = '".$menu_item['id']."' ORDER BY daily_service_ordering ASC", ARRAY_A);

							foreach ($daily_service_table_data as $daily_service) 
							{?>
								<li class="sub-level menu-level service_title_area">
									<input class="accordion-toggle" type="checkbox" name="service[]" id="group-daily-service-<?php echo $daily_service['id']; ?>">

									<label class="accordion-title custom_daily_service" for="group-daily-service-<?php echo $daily_service['id']; ?>" data-menu-id="<?php echo $daily_service['id']; ?>"> 
										
										<span class="daily_service_name">
											<i class="fa fa-bars"></i>
											<?php echo $daily_service['service_name']  ?> 
										</span>
									</label>

									<!-- daily service action -->
									<span class="daily_service_actions">

										<span class="daily_service_delete_action">
											<button class="daily_service_delete" data-id="<?php echo $daily_service['id'] ?>">
											<i class="fa fa-trash"></i>
											</button>
										</span>
									</span>

									<!--******************* daily Service Dishes ******************* -->
									<ul class="droppable_area_dish" data-menu="<?php echo $menu_item['id']; ?>" data-id="<?php echo $daily_service['id'] ?>">

										<!-- *********** Search dishes sec ************ -->
										<div id="search">
											<label for="searchInput">Buscar platos</label>
											<input class="searchInput" type="text">
										</div>
										<div id="no-dishes-found" style="display: none;">No se encontraron platos</div>
										<!-- *********** Search dishes sec end ************ -->

										<?php
									// 	$daily_dishes_data = $wpdb->get_results("
									// 	SELECT d.*, m.*
									// 	FROM $dishes_table d
									// 	LEFT JOIN $daily_service_tbl_dish_meta m
									// 	ON d.id = m.daily_dish_meta_value
									// 	WHERE m.daily_service_id = " . $daily_service['id'] . "
									// 	ORDER BY m.daily_dish_ordering ASC
									// ", ARRAY_A);

										$daily_dishes_data = $wpdb->get_results("SELECT * FROM $dishes_table where daily_dish_status = 'true'", ARRAY_A);

										$daily_meta_dishes_result1 = $wpdb->get_results("SELECT * FROM $daily_service_tbl_dish_meta where daily_service_id = ".$daily_service['id']." ORDER BY daily_dish_ordering ASC" , ARRAY_A);

										$mergedArray = array();

										foreach ($daily_dishes_data as $dish) 
										{
											$matchFound = false;
											foreach ($daily_meta_dishes_result1 as $result) 
											{
												if ($dish['id'] == $result['daily_dish_meta_value']) 
												{
													$mergedEntry = array_merge($dish, $result);
													$mergedArray[] = $mergedEntry;
													$matchFound = true;
												}
											}

											// If no match was found, add the $dish from $dishes_data to $mergedArray
											if (!$matchFound) 
											{
												$dish['daily_service_id'] = '';
												$dish['daily_dish_meta_key'] = '_daily_dish';
												$dish['daily_dish_meta_value'] = '';
												$dish['daily_dish_status'] = '';
												$dish['daily_dish_ordering'] = '';
												$mergedArray[] = $dish;
											}
										}

										usort($mergedArray, 'customDailySort');

										$mergedArray = array_values($mergedArray);

										if ($mergedArray !== null) 
										{										
											if (count($mergedArray) > 0) 
											{
												foreach ($mergedArray as $daily_dish) 
												{ 
													// $daily_meta_dishes_result = $wpdb->get_var("SELECT daily_dish_status FROM $daily_service_tbl_dish_meta WHERE daily_dish_meta_value = '".$daily_dish['id']."' AND daily_service_id = '".$daily_service['id']."'");

													$selected_services = unserialize($daily_dish['parent_service']);

													$jsonData = stripslashes($selected_services);
													$decodedData = json_decode($jsonData, true);
											
													$checked = false;
													if ($decodedData !== null) 
													{
														foreach($decodedData as $checkdata)
														{
															if($checkdata['service_id'] == $daily_service['id'])
															{
																$checked = true;
															}
														}
													}
													if($checked)
													{
														if($daily_dish['daily_dish_meta_value'] == '' || empty($daily_dish['daily_dish_meta_value']))
														{
															$daily_dish['daily_dish_meta_value'] = $daily_dish['id'];
														}
													?>
														<li class="dish-level" value="<?php echo $daily_dish['daily_dish_meta_value']; ?>" data-id="group-dish-order-<?php echo $daily_service['id']; ?>" data-dish-id="<?php echo $daily_dish['daily_dish_meta_value']; ?>">
															<a href="#">
																<input type="checkbox"  class="daily_service_dishes" name="daily_service_dishes_id[]" value="<?php echo $daily_dish['daily_dish_meta_value']; ?>"  data-service-id="<?php echo $daily_service['id']; ?>" <?php if ($daily_dish['daily_dish_status'] === 'true') echo 'checked'; ?>
																>
																<i class="fas fa-utensils"></i>
																<?php echo $daily_dish['dish_name_es'] ?>
															</a>
														</li>
												<?php }
												}
											}
										}?>
									</ul>
								</li>
							<?php
							}
						}
						/******************** Special menu sec **************************/ 
						else if($category_slug == 'special')
                        { ?>						
							<li class="add_servicess">
								<button type="button" class="add_service" data-toggle="modal" data-target="#services_modal" data-id ="<?php echo $menu_item['id']; ?>"><?php echo 'Seleccione Servicio de Menú Especial'; ?></button>
							</li>

							<!-- services list  -->
							<?php
							
							$service_table_data = $wpdb->get_results("SELECT * FROM $special_menu_service_tbl_meta WHERE parent_menu = '".$menu_item['id']."' ORDER BY specail_service_ordering ASC", ARRAY_A);

							foreach ($service_table_data as $service) 
							{ ?>
								
								<li class="sub-level menu-level service_title_area">
									<input class="accordion-toggle" type="checkbox" name="service[]" id="group-service-<?php echo $service['id']; ?>">

									<label class="accordion-title custom_service" for="group-service-<?php echo $service['id']; ?>" data-menu-id="<?php echo $service['id']; ?>"> 
										
										<span class="service_name">
											<i class="fa fa-bars"></i>
											<?php echo $service['service_name']  ?> 
										</span>
									</label>

									<!-- service action -->
									<span class="service_actions">
										<span class="service_delete_action">
											<button class="special_service_delete" data-id="<?php echo $service['id'] ?>">
											<i class="fa fa-trash"></i>
											</button>
										</span>
									</span>

									<!--******************* Service Dishes ******************* -->
									<ul class="droppable_area_dish" data-menu="<?php echo $menu_item['id']; ?>" data-id="<?php echo $service['id'] ?>">
										<!-- *********** Search dishes sec ************ -->
										<div id="search">
											<label for="searchInput">Buscar platos</label>
											<input class="searchInput" type="text">
										</div>
										<div id="no-dishes-found" style="display: none;">No se encontraron platos</div>
										<!-- *********** Search dishes sec end ************ -->

										<?php

										// $dishes_data = $wpdb->get_results("
										// SELECT d.*, m.*
										// FROM $dishes_table d
										// LEFT JOIN $special_service_tbl_dish_meta m
										// ON d.id = m.dish_meta_value
										// WHERE m.service_id = " . $service['id'] . "
										// ORDER BY m.special_dish_ordering ASC
										// ", ARRAY_A);

										$dishes_data = $wpdb->get_results("
										SELECT d.id, d.parent_service, d.dish_name_es, m.*
										FROM $dishes_table d
										LEFT JOIN $special_service_tbl_dish_meta m 
										ON d.id = m.dish_meta_value
										WHERE m.service_id = " . $service['id'] . "
										ORDER BY m.special_dish_ordering ASC
										", ARRAY_A);

										
										if (count($dishes_data) > 0) 
										{
											foreach ($dishes_data as $dishes_items) 
											{ 

												// $meta_dishes_result = $wpdb->get_var("SELECT dish_status FROM $meta_dishes_table WHERE dish_meta_value = '".$dishes_items['id']."' AND service_id = '".$service['id']."'");

												$selected_services = unserialize($dishes_items['parent_service']);
												$jsonData = stripslashes($selected_services);
												$decodedData = json_decode($jsonData, true);

												$checked = false;

												if($decodedData !== null)
												{
													foreach($decodedData as $checkdata)
													{
														// if($checkdata['service_id'] == $service['id'] && $checkdata['menu_id'] == $menu_item['id'])
														// {
														// 	$checked = true;
														
														// }
														if($checkdata['service_id'] == $service['id'])
														{
															$checked = true;
														
														}
													}
												}
																								
												if($checked)
												{
													?>								
													<li class="dish-level" value="<?php echo $dishes_items['dish_meta_value']; ?>" data-id="group-dish-order-<?php echo $service['id']; ?>"  data-dish-id="<?php echo $dishes_items['dish_meta_value']; ?>">
														<a href="#">
															<input type="checkbox"  class="service_dishes" name="service_dishes_id[]" value="<?php echo $dishes_items['id']; ?>"  data-service-id="<?php echo $service['id']; ?>" data-menu-id="<?php echo $menu_item['id']; ?>" <?php if ($dishes_items['dish_status'] === 'true') echo 'checked'; ?>
															>
															<i class="fas fa-utensils"></i>
															<?php echo $dishes_items['dish_name_es'] ?>
														</a>
													</li>
												<?php }
											}
										} ?>
									</ul>
								</li>
								
								<?php	
							}
						}
						/***************** other menu sec **************************/ 
						else
						{
							// $other_dishes_data = $wpdb->get_results("SELECT * FROM $dishes_table", ARRAY_A);

							$other_dishes_data = $wpdb->get_results("
										SELECT d.*, m.*
										FROM $dishes_table d
										LEFT JOIN $other_meta_dishes_table m
										ON d.id = m.other_dish_meta_value
										WHERE m.other_menu_id = " . $menu_item['id'] . "
										ORDER BY m.other_dish_ordering ASC
										", ARRAY_A);

							if (count($other_dishes_data) > 0) 
							{
                                foreach ($other_dishes_data as $other_dishes_items) 
                                { 
									// $other_dishes_result = $wpdb->get_var("SELECT id FROM $other_meta_dishes_table WHERE other_menu_id = ".$menu_item['id']." AND other_dish_meta_value = ".$other_dishes_items['id']);

									if($other_dishes_data)
									{
										// $other_meta_dishes_result = $wpdb->get_var("SELECT other_dish_status FROM $other_meta_dishes_table WHERE other_dish_meta_value = '".$other_dishes_items['id']."'");
										?>
										<li class="dish-level" data-id = "group-dish-order-<?php echo $menu_item['id']; ?>" value ="<?php echo $other_dishes_items['other_dish_meta_value']; ?>"  data-dish-id="<?php echo $other_dishes_items['id']; ?>">

											<a href="#">
												<input type="checkbox" id="other_dishess" class="other_dish" name="other_dish_ids[]" value="<?php echo $other_dishes_items['other_dish_meta_value']; ?>" data-id ="<?php echo $menu_item['id']; ?>"<?php if ($other_dishes_items['other_dish_status'] === 'true') echo 'checked'; ?>
												>
												<i class="fas fa-utensils"></i>
												<?php echo $other_dishes_items['dish_name_es'] ?>
											</a>
										</li>
								<?php }
								}
                            }       
						}
                        ?>
                    </ul>
                </li>
                <?php
                $count_menus++;
            }
        }
        ?>
    </ul>

	<!--*************************** add main menu sevices modal *********************-->
	<div class="modal fade main_servicess" id="main_menu_service_modal" tabindex="-1" role="dialog" aria-labelledby="main_menu_service_modal_label" aria-hidden="true">
		<?php
		global $wpdb;
		$service_table = $wpdb->prefix . 'trion_service_tbl_meta';
		$services = $wpdb->get_results("SELECT * FROM $service_table", ARRAY_A);
		?>
		<!-- ************* Services form *************** -->
		<form id="res_custom_main_menu_service_form" method="POST">
			<div class="modal-dialog modal-dialog-centered" role="document">			
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLongTitle">Seleccione Servicios del Menú Principal</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="main_menu_service_id" name="menu_service_id">
						<div class="main_menu_service_outer">
							<div class="form-group">
								<label for="main_menu_service_name">Seleccionar Servicio</label>
									<select class = "select_ser_sec">
									<?php 
										$check_menu_table = $wpdb->prefix . 'trion_main_menu_service_tbl_meta';
										$sql = $wpdb->prepare("SELECT id FROM $check_menu_table");
										$result = $wpdb->get_results($sql, ARRAY_A);
										$existingIds = array_column($result, 'id');
										
									foreach ($services as $service_data) 
									{
										if (in_array($service_data['id'], $existingIds)) { }else{
										?>
										<option value="<?php echo $service_data['id']; ?>"><?php echo $service_data['service_name']; ?>
										</option>
									<?php }
									} ?>
									</select>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary close" data-dismiss="modal">Cerca</button>
						<button type="submit" value="submit" class="btn btn-primary">Agregar servicio</button>
					</div>
				</div>
			</div>
		</form>
	</div>


	<!-- ******************** add daily menu service modal ********************************** -->
	<!-- add daily service modal  -->
	<div class="modal fade daily_service1" id="daily_menu_services_modal" tabindex="-1" role="dialog" aria-labelledby="services_modal_label" aria-hidden="true">
		<?php
		global $wpdb;
		$service_table = $wpdb->prefix . 'trion_service_tbl_meta';
		$services = $wpdb->get_results("SELECT * FROM $service_table", ARRAY_A);
		?>
		<!-- ************* daily Services form *************** -->
		<form id="res_custom_daily_menu_service_form" method="POST">
			<div class="modal-dialog modal-dialog-centered" role="document">			
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLongTitle">Seleccione Servicios de Menú Diario</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="daily_service_menu_id" name="daily_service_menu_id">
						<div class="main_service">
							<div class="form-group">
								<label for="daily_menu_service_name">Nombre del Servicio</label>
								<select class = "select_ser_sec">
								<?php 
										$check_menu_table = $wpdb->prefix . 'trion_daily_menu_service_tbl_meta';
										$sql = $wpdb->prepare("SELECT id FROM $check_menu_table");
										$result = $wpdb->get_results($sql, ARRAY_A);
										$existingIds = array_column($result, 'id');
		
										
									foreach ($services as $service_data) 
									{
										if (in_array($service_data['id'], $existingIds)) { }else{
										?>
										<option value="<?php echo $service_data['id']; ?>"><?php echo $service_data['service_name']; ?>
										</option>
									<?php }
									} ?>
								</select>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary close" data-dismiss="modal">Cerca</button>
						<button type="submit" value="submit" class="btn btn-primary">Agregar servicio</button>
					</div>
				</div>
			</div>
		</form>
	</div>

	
	<!--********************** add special menu service modal************************** -->
	<!-- add service modal  -->
	<div class="modal fade  service" id="services_modal" tabindex="-1" role="dialog" aria-labelledby="services_modal_label" aria-hidden="true">
		<?php
		global $wpdb;
		$service_table = $wpdb->prefix . 'trion_service_tbl_meta';
		$services = $wpdb->get_results("SELECT * FROM $service_table", ARRAY_A);
		?>
		<!-- ************* special Services form *************** -->
		<form id="res_custom_special_service_form" method="POST">
			<div class="modal-dialog modal-dialog-centered" role="document">			
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLongTitle">Seleccione Servicios de Menú Especial</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="service_menu_id" name="service_id">
						<div class="main_service">
							<div class="form-group">
								<label for="service_name">Seleccionar Servicio</label>
								<select class = "select_ser_sec">
								<?php 
										$check_menu_table = $wpdb->prefix . 'trion_special_menu_service_tbl_meta';
										$sql = $wpdb->prepare("SELECT id FROM $check_menu_table");
										$result = $wpdb->get_results($sql, ARRAY_A);

										$existingIds = array_column($result, 'id');
		
										
									foreach ($services as $service_data) 
									{
										if (in_array($service_data['id'], $existingIds)) { }else{
										?>
										<option value="<?php echo $service_data['id']; ?>"><?php echo $service_data['service_name']; ?>
										</option>
									<?php }
									} ?>
								</select>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary close" data-dismiss="modal">Cerca</button>
						<button type="submit" value="submit" class="btn btn-primary">Agregar servicio</button>
					</div>
				</div>
			</div>
		</form>
	</div>

</body>


