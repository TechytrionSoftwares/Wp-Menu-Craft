(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
 
	/**************** services section********************/ 
	jQuery(document).ready(function($)
	{
		$('#add_new_service_btn').on('click', function()
		{
			$('.add-new-service-sec').toggle(500);
		});

		$('#cancel_new_service_btn').on('click', function()
		{
			$('.add-new-service-sec').toggle(500);
		});

		$('#cancel_update_service_btn').on('click', function()
		{
			$('.update-new-service-sec').toggle(500);
		});

		/************* insert services ***************/ 
		$('#rest_add_services').submit(function(event) 
		{
			event.preventDefault();
			var service_name = $('.service_name').val();
			var service_name_eng = $('.service_name_eng').val();
			var service_name_eus = $('.service_name_eus').val();
			var service_name_fr = $('.service_name_fr').val();
			var service_description = $('.servicees_des').val();
		
			if(service_description == "" )
			{
				Swal.fire({
					title: "Error",
					text: "All fields is required",
					icon: "error",
					buttons: false,
					timer: 2000 
				})
			}
			else
			{
				$.ajax({
					url: ajax_object.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: {
							 action: 'insert_services',
							'service_name' : service_name,
							'service_name_eng' : service_name_eng,
							'service_name_eus' : service_name_eus,
							'service_name_fr' : service_name_fr,
							'service_description' : service_description,
						  },

					success: function(response) 
					{
						console.log(response);
	
					   if(response.success === true)
					   {
							Swal.fire({
								title: "Success",
								text: "Service added sccessfully",
								icon: "success",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
					   }
					   else
					   {
							Swal.fire({
								title: "Error",
								text: "Unable to Add Services",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
					   }	
	
					},
					error: function(xhr, status, error) 
					{
						console.log(xhr.responseText);
						// Swal.fire({
						// 	title: "Error",
						// 	text: "An error occurred while processing the request",
						// 	icon: "error",
						// 	buttons: false,
						// 	timer: 2000 
						// }).then(function() 
						// {
						// 	location.reload();
						// },200);
					}
					
				});
			}
		});

		/*************** Update services *******************/ 
		$('#update_services').on('submit', function(event)
		{
			event.preventDefault();
			var update_service_name = $('.update_service_name').val();
			var update_service_name_eng = $('.update_service_name_eng').val();
			var update_service_name_eus = $('.update_service_name_eus').val();
			var update_service_name_fr = $('.update_service_name_fr').val();
			var update_service_des = $('.update_service_des').val();
			var servicees_id = $('.servicees_id').val();
			
			$.ajax({
				url: ajax_object.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: {
						 action: 'update_service_modal',
						'update_service_name' : update_service_name,
						'update_service_name_eng' : update_service_name_eng,
						'update_service_name_eus' : update_service_name_eus,
						'update_service_name_fr' : update_service_name_fr,
						'update_service_des' : update_service_des,
						'service_id' : servicees_id,
					  },

				success: function(response) 
				{
					console.log(response);

					if(response.status === true)
					{
						Swal.fire({
							title: "Success",
							text: "Service updated sccessfully",
							icon: "success",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							//location.reload();
							// window.location.href = 'http://localhost/restaurant/wp-admin/admin.php?page=trion-menu-craft-plugin-services';

							// window.location.href = 'https://dirkconsulting.com/oianume/wp-admin/admin.php?page=trion-menu-craft-plugin-services';

							var base_url = window.location.origin + window.location.pathname;
							window.location.href = base_url+'?page=trion-menu-craft-plugin-services';
						},200);
					}
					else
					{
						Swal.fire({
							title: "Error",
							text: "Unable to update Services",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}	

				},
				error: function(xhr, status, error) 
				{
					console.log(xhr.responseText);
					Swal.fire({
						title: "Error",
						text: "An error occurred while processing the request",
						icon: "error",
						buttons: false,
						timer: 2000 
					}).then(function() 
					{
						location.reload();
					},200);
				}
			});
			
		});

		/***************** delete services *************************/ 
		$('.delete_services').on('click', function()
		{
			var service_id = $(this).data('id');
			$.ajax({
				url: ajax_object.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: {
						 action: 'delete_service',
						 'service_id' : service_id,
					  },
				success: function(response) 
				{
					console.log(response);

					if(response.status === true)
					{
						Swal.fire({
							title: "Success",
							text: "Service deleted sccessfully",
							icon: "success",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}
					else
					{
						Swal.fire({
							title: "Error",
							text: "Unable to delete service",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}	

				},
				error: function(xhr, status, error) 
				{
					console.log(xhr.responseText);
					Swal.fire({
						title: "Error",
						text: "An error occurred while processing the request",
						icon: "error",
						buttons: false,
						timer: 2000 
					}).then(function() 
					{
						location.reload();
					},200);
				}
			});
		});
	});


	/* ++++++++++++++++++++ Dish Section +++++++++++++++++++++++++*/ 
	jQuery(document).ready(function($)
	{
		$('#add_new_dish_btn').on('click', function()
		{
			$('.add-new-dish-sec').toggle(500);
		});

		$('#cancel_new_dish_btn').on('click', function()
		{
			$('.add-new-dish-sec').toggle(500);
		});

		$('#cancel_update_dish_btn').on('click', function()
		{
			$('.update-new-dish-sec').toggle(500);
		});

		/***************************  dishes section ******************************/ 
		/************* insert dishes ***************/ 
		$('#rest_add_dishess').submit(function(event) 
		{
			event.preventDefault();
			var dish_name_eng = $('.dish_name_eng').val();
			var dish_name_es = $('.dish_name_es').val();
			var dish_name_eus = $('.dish_name_eus').val();
			var dish_name_fr = $('.dish_name_fr').val();
			var dish_price = $('.dish_price').val();
			var dish_description = $('.dishes_des').val();

			var selected_services = [];
			
			$('.selected_servicess:checked').each(function() 
			{
				selected_services.push({
					service_id: $(this).val(),
					// menu_id: menu_id,
					// menu_name: menu_name,
					// menu_id: $(this).data('id')
				});
			});
			
			var serializedData = JSON.stringify(selected_services);

			/*********** other-menu ***************/ 
			var other_data_id  = $('.selected_menus').data('id');
			var selected_menus = [];
			$('.selected_menus:checked').each(function() 
			{
				selected_menus.push({
					menu_id: $(this).val(),
				});
			});
			var serializedMenuData = JSON.stringify(selected_menus);
	

			if(dish_price == "" && dish_description == "" && serializedData == "") 
			{
				Swal.fire({
					title: "Error",
					text: "All fields is required",
					icon: "error",
					buttons: false,
					timer: 2000 
				})
			}
			else
			{
				$.ajax({
					url: ajax_object.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: {
							 action: 'insert_dishes',
							'dish_name_eng' : dish_name_eng,
							'dish_name_es' : dish_name_es,
							'dish_name_eus' : dish_name_eus,
							'dish_name_fr' : dish_name_fr,
							'dish_price' : dish_price,
							'dish_description' : dish_description,
							'selected_services': serializedData,
							'selected_menus':serializedMenuData,
							'other_data_id':other_data_id,
						  },
					success: function(response) 
					{
						console.log(response);
	
					   if(response.main.success === true)
					   {
							Swal.fire({
								title: "Success",
								text: "Dish added sccessfully",
								icon: "success",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
					   }
					   else
					   {
							Swal.fire({
								title: "Error",
								text: response.main.error ||"Unable to Add Dish",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
					   }
					   
					   // Handle additional response if needed
					   if (response.other) {
						console.log(response.other);
						// Handle the 'other' response here
					}
	
					},
					error: function(xhr, status, error) 
					{
						console.log(xhr.responseText);
						Swal.fire({
							title: "Error",
							text: "An error occurred while processing the request",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}
					
				});
			}
		});

		/*************** Update Dishes *******************/ 
		$('#update_dishess').on('submit', function(event)
		{
			event.preventDefault();
			var update_dish_name_eng = $('.update_dish_name_eng').val();
			var update_dish_name_es = $('.update_dish_name_es').val();
			var update_dish_name_eus = $('.update_dish_name_eus').val();
			var update_dish_name_fr = $('.update_dish_name_fr').val();
			var update_dish_price = $('.update_dish_price').val();
			var update_dishes_des = $('.update_dishes_des').val();
			var dishes_id = $('.dishes_id').val();
			var selected_services = [];
			var unchecked_services = [];

			var update_menus = [];
			var unchecked_menus = [];



			$('.selected_servicess').each(function() 
			{
				if ($(this).is(':checked')) 
				{
					selected_services.push({
						service_id: $(this).val(),
					});
				} 
				else 
				{
					unchecked_services.push({
						service_id: $(this).val(),
					});
				}
			});
			
			var serializedData = JSON.stringify(selected_services);

			$('.update_menus').each(function()
			{
				if ($(this).is(':checked')) 
				{
					update_menus.push({
						menu_id : $(this).val(),
					});
				}
				else 
				{
					unchecked_menus.push({
						menu_id: $(this).val(),
					});
				}
			});
			var serializedMenuData = JSON.stringify(update_menus);

			$.ajax({
				url: ajax_object.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: {
						action: 'update_dishes',
						'update_dish_name_eng' : update_dish_name_eng,
						'update_dish_name_es' : update_dish_name_es,
						'update_dish_name_eus' : update_dish_name_eus,
						'update_dish_name_fr' : update_dish_name_fr,
						'update_dish_price' : update_dish_price,
						'update_dishes_des' : update_dishes_des,
						'dishes_id' : dishes_id,
						'selected_services': serializedData,
						'unchecked_services': JSON.stringify(unchecked_services),
						'service_data': selected_services,
						'update_menus': serializedMenuData,
						'unchecked_menus': JSON.stringify(unchecked_menus),
						'menu_data': update_menus,

					  },
				success: function(response) 
				{
					console.log(response.success);

					if(response.success == true)
					{
						Swal.fire({
							title: "Success",
							text: "Dish updated sccessfully",
							icon: "success",
							buttons: false,
							timer: 4000 
						}).then(function() 
						{
							var base_url = window.location.origin + window.location.pathname;
							window.location.href = base_url+'?page=trion-menu-craft-plugin-dish';
						},200);
					}
					else
					{
						Swal.fire({
							title: "Error",
							text: "Unable to update Dish",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}	

				},
				error: function(xhr, status, error) 
				{
					console.log(xhr.responseText);
					Swal.fire({
						title: "Error",
						text: "An error occurred while processing the request",
						icon: "error",
						buttons: false,
						timer: 2000 
					}).then(function() 
					{
						location.reload();
					},200);
				}
			});
			
		});

		/***************** delete dishes *************************/ 
		$('.delete_dishes').on('click', function()
		{
			//var dishes_id = $('.dishes_id').val();
			var dishes_id = $(this).data('id');
			$.ajax({
				url: ajax_object.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: {
						 action: 'delete_dishes',
						 'dishes_id' : dishes_id,
					  },
				success: function(response) 
				{
					console.log(response);

					if(response.success === true)
					{
						Swal.fire({
							title: "Success",
							text: "Dish deleted sccessfully",
							icon: "success",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}
					else
					{
						Swal.fire({
							title: "Error",
							text: "Unable to delete Dish",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}	

				},
				error: function(xhr, status, error) 
				{
					console.log(xhr.responseText);
					Swal.fire({
						title: "Error",
						text: "An error occurred while processing the request",
						icon: "error",
						buttons: false,
						timer: 2000 
					}).then(function() 
					{
						location.reload();
					},200);
				}
			});
		});

		
		/****************** Category Section *************************/ 
		$('#add_new_category_btn').on('click', function()
		{
			$('.add-new-category-sec').toggle(500);
		});

		$('#cancel_new_category_btn').on('click', function()
		{
			$('.add-new-category-sec').toggle(500);
		});

		$('#cancel_update_category_btn').on('click', function()
		{
			$('.update-new-category-sec').toggle(500);
		});

		/********* insert category ***********/ 
		$('#rest_add_category').on('submit', function(event)
		{
			event.preventDefault();
			var category_name = $('.category_name').val();
		    var category_description = $('.category_descriptions').val();

			$.ajax({
						url: ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: {
								action: 'insert_category',
								category_name : category_name,
								category_description : category_description
							},
						success: function(response) 
						{
							console.log(response);
							if(response.success === true)
							{
								Swal.fire({
									title: "Success",
									text: "Category added sccessfully",
									icon: "success",
									buttons: false,
									timer: 2000 
								}).then(function() 
								{
									location.reload();
								},200);
							}
							else
							{
								Swal.fire({
									title: "Error",
									text: "Unable to insert Category",
									icon: "error",
									buttons: false,
									timer: 2000 
								}).then(function() 
								{
									location.reload();
								},200);
							}	
						},
						error: function(xhr, status, error) 
						{
							console.log(xhr.responseText);
							Swal.fire({
								title: "Error",
								text: "An error occurred while processing the request",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								//location.reload();
							},200);
						}
				});
		});

		/************** update category ********************/ 
		$('#update_categories').on('submit', function(event)
		{
			event.preventDefault();
			var update_category_name = $('.update_category_name').val();
			var update_category_des = $('.update_category_des').val();
			var cat_id = $('.category_idd').val();
			$.ajax({
				url: ajax_object.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: {
						 action: 'update_category',
						'update_category_name' : update_category_name,
						'update_category_des' : update_category_des,
						'cat_id' : cat_id,
					  },
				success: function(response) 
				{
					console.log(response);

					if(response.success === true)
					{
						Swal.fire({
							title: "Success",
							text: "Category updated sccessfully",
							icon: "success",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							//location.reload();
							// window.location.href = 'http://localhost/restaurant/wp-admin/admin.php?page=trion-menu-craft-plugin-category';

							// window.location.href = 'https://dirkconsulting.com/oianume/wp-admin/admin.php?page=trion-menu-craft-plugin-category';

							var base_url = window.location.origin + window.location.pathname;
							window.location.href = base_url+'?page=trion-menu-craft-plugin-category';
						},200);
					}
					else
					{
						Swal.fire({
							title: "Error",
							text: "Unable to update Category",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}	

				},
				error: function(xhr, status, error) 
				{
					console.log(xhr.responseText);
					Swal.fire({
						title: "Error",
						text: "An error occurred while processing the request",
						icon: "error",
						buttons: false,
						timer: 2000 
					}).then(function() 
					{
						location.reload();
					},200);
				}
			});
			
		});

	});
	
	/**************** delete category ********************/ 
	$(document).on('click', function(event)
	{
		var target = $(event.target);
		if (target.hasClass('delete_category')) 
		{
			//var cats_id = $(this).data('id');
			var cats_id = target.data('id');

			$.ajax({
				url: ajax_object.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: {
							action: 'delete_category',
							'cats_id' : cats_id,
						},
				success: function(response) 
				{
					console.log(response);

					if(response.success === true)
					{
						Swal.fire({
							title: "Success",
							text: "Category deleted sccessfully",
							icon: "success",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}
					else
					{
						Swal.fire({
							title: "Error",
							text: "Unable to delete Category",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}	

				},
				error: function(xhr, status, error) 
				{
					console.log(xhr.responseText);
					Swal.fire({
						title: "Error",
						text: "An error occurred while processing the request",
						icon: "error",
						buttons: false,
						timer: 2000 
					}).then(function() 
					{
						location.reload();
					},200);
			   }
			});
		}
	});

	/********************** Add Menu Section *****************************/ 
	/************ get cat ***********/ 
	jQuery(document).ready(function()
	{
		$('#get_cat_id').on('change', function()
		{
			var cat_id = $(this).val();
			if(cat_id != "")
			{
				$('#category_id').val(cat_id);

				/************ get selected val ***************/ 

				/********* Show menu price div ***********/ 
				var selectedSlug = $(this).find('option:selected').data('slug');
				$('#category_slug').val(selectedSlug);

				if (selectedSlug === 'daily' || selectedSlug === 'special' || selectedSlug === 'other-cat') 
				{
					$('.menu_outer').show();
				} 
				else 
				{
					$('.menu_outer').hide();
				}

				$('#add_new_menu_btn').css('display', 'block');
			}
		});

		/******* toggle btns ********/ 
		$('#add_new_menu_btn').on('click', function() 
		{
			$('.add-new-menu-sec').toggle(500);
		});

		$('#cancel_new_menu_btn').on('click', function() 
		{
			$('.add-new-menu-sec').toggle(500);
		});

		$('#cancel_update_menu_btn').on('click', function() 
		{
			$('.update-menu-sec').toggle(500);
		});

		/*******insert ******/ 
		$('#add_menus').on('submit', function(event)
		{
			event.preventDefault();
			var menu_name = $('.menu_name').val();
			var menu_name_eng = $('.menu_name_eng').val();
			var menu_name_eus = $('.menu_name_eus').val();
			var menu_name_fr = $('.menu_name_fr').val();
			var menu_description = $('.menu_description').val();
			var category_id = $('#category_id').val();
			var category_slug = $('#category_slug').val();
			var menu_price = $('.menu_price').val();
			var menu_price_eng = $('.menu_price_eng').val();
			var menu_price_eus = $('.menu_price_eus').val();
			var menu_price_fr = $('.menu_price_fr').val();

			$.ajax({
					url: ajax_object.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: {
							action: 'insert_menus',
							menu_name : menu_name,
							menu_name_eng : menu_name_eng,
							menu_name_eus : menu_name_eus,
							menu_name_fr : menu_name_fr,
							menu_description : menu_description,
							category_id  : category_id,
							category_slug : category_slug,
							menu_price : menu_price,
							menu_price_eng : menu_price_eng,
							menu_price_eus : menu_price_eus,
							menu_price_fr : menu_price_fr
						},
					success: function(response) 
					{
						console.log(response);
						if(response.success === true)
						{
							Swal.fire({
								title: "Success",
								text: "Menu added sccessfully",
								icon: "success",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}
						else
						{
							Swal.fire({
								title: "Error",
								text: "Unable to Menu Category",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}	
					},
					error: function(xhr, status, error) 
					{
						console.log(xhr.responseText);
						Swal.fire({
							title: "Error",
							text: "An error occurred while processing the request",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}
				});
		});

		/******* update *******/ 
		$('#update_menus').on('submit', function(event)
		{
			event.preventDefault();
			var update_menu_name = $('.update_menu_name').val();
			var update_menu_name_eng = $('.update_menu_name_eng').val();
			var update_menu_name_eus = $('.update_menu_name_eus').val();
			var update_menu_name_fr = $('.update_menu_name_fr').val();
			var update_menu_des = $('.update_menu_des').val();
			var get_cat_id = $('#get_update_cat_id').val();
			var update_menu_id = $('.update_menu_id').val();
			var update_menu_price = $('.update_menu_price').val();
			var update_menu_price_eng = $('.update_menu_price_eng').val();
			var update_menu_price_eus = $('.update_menu_price_eus').val();
			var update_menu_price_fr = $('.update_menu_price_fr').val();


			$.ajax({
				url: ajax_object.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: {
						action: 'update_menus',
						'update_menu_name' : update_menu_name,
						'update_menu_name_eng' : update_menu_name_eng,
						'update_menu_name_eus' : update_menu_name_eus,
						'update_menu_name_fr' : update_menu_name_fr,
						'update_menu_des' : update_menu_des,
						'get_cat_id' : get_cat_id,
						'update_menu_id': update_menu_id,
						'update_menu_price': update_menu_price,
						'update_menu_price_eng' : update_menu_price_eng,
						'update_menu_price_eus' : update_menu_price_eus,
						'update_menu_price_fr' : update_menu_price_fr
					},
				success: function(response) 
				{
					console.log(response);

					if(response.success === true)
					{
						Swal.fire({
							title: "Success",
							text: "Menus updated sccessfully",
							icon: "success",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							//location.reload();
							// window.location.href = 'http://localhost/restaurant/wp-admin/admin.php?page=trion-menu-craft-plugin-menus';

							// window.location.href = 'https://dirkconsulting.com/oianume/wp-admin/admin.php?page=trion-menu-craft-plugin-menus';
							
							var base_url = window.location.origin + window.location.pathname;
							window.location.href = base_url+'?page=trion-menu-craft-plugin-menus';
						},200);
					}
					else
					{
						Swal.fire({
							title: "Error",
							text: "Unable to update Menus",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}	

				},
				error: function(xhr, status, error) 
				{
					console.log(xhr.responseText);
					Swal.fire({
						title: "Error",
						text: "An error occurred while processing the request",
						icon: "error",
						buttons: false,
						timer: 2000 
					}).then(function() 
					{
						location.reload();
					},200);
				}
			});
		});

		/**************delete menus******************/ 
		$('.del_menu').on('click', function()
		{
			var menu_id = $(this).data('id');
			$.ajax({
				url: ajax_object.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: {
							action: 'delete_menus',
							'menu_id' : menu_id,
						},
				success: function(response) 
				{
					console.log(response);

					if(response.success === true)
					{
						Swal.fire({
							title: "Success",
							text: "Menus deleted sccessfully",
							icon: "success",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}
					else
					{
						Swal.fire({
							title: "Error",
							text: "Unable to delete Menus",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}	

				},
				error: function(xhr, status, error) 
				{
					console.log(xhr.responseText);
					Swal.fire({
						title: "Error",
						text: "An error occurred while processing the request",
						icon: "error",
						buttons: false,
						timer: 2000 
					}).then(function() 
					{
						location.reload();
					},200);
			   }
			});
		});

		$(document).on('click', function(e){
			console.log(e.target.className);
		});

		/*+++++++++++++++++++++++++++ Menu List Sec ++++++++++++++++++++++++++++++++++++++*/ 

		/***************** MAIN MENU SECTION ***************** */ 

		/************** Show main menu service modal ************************/ 
		$('.main_menu_service').on('click', function(event)
		{
			event.preventDefault();
			var menu_id = $(this).data('id');
			
			$("#main_menu_service_id").val(menu_id);
			$('.main_servicess').modal('show');

		})

		/**********close main menu modal btn ***********/
		$('.close').on('click', function()
		{
			setTimeout(function () {
				$('.main_servicess').modal('hide');
			}, 300); 
		})

		/**************insert main menu services ****************/ 
		
		$('#res_custom_main_menu_service_form').on('submit', function(event)
		{
			event.preventDefault();
			var main_menu_service_id = $(this).find('.select_ser_sec').val();
			var main_menu_id = $("#main_menu_service_id").val();

			$.ajax({
					url :ajax_object.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: { 
							action: 'insert_main_menu_services',
							main_menu_service_id: main_menu_service_id, 
							main_menu_id: main_menu_id, 
						},
					success: function(response) 
					{
						console.log(response);
						if(response.status === true)
						{
							Swal.fire({
								title: "Success",
								text: "Add service sccessfully",
								icon: "success",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}
						else if(response.status === 'limiterror')
						{
							Swal.fire({
								title: "Error",
								text: "Not add More than 4 Services.",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}
						else
						{
							Swal.fire({
								title: "Error",
								text: "Unable to add service",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}	
				    },
					error: function(xhr, status, error) 
					{
						console.log(xhr.responseText);
						Swal.fire({
							title: "Error",
							text: "An error occurred while processing the request",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}
			    });
		});
 
		/**************** Edit Main Service sec ******************/ 
		$('.main_service_edit').on('click', function(event)
		{
			event.preventDefault();
			var main_service_id = $(this).data('id');

			$.ajax({
				url :ajax_object.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: { 
						action: 'edit_main_service_modal',
						main_service_id: main_service_id
					  },
				success: function(response) 
				{
					//console.log(response);
					if (response.status === 'success') 
					{
						var data = response.data;
						$('#edit_res_custom_main_service_form #update_main_service_menu_id').val(data.id);
						$('#edit_res_custom_main_service_form #update_main_service_name').val(data.service_name);
						$('#edit_res_custom_main_service_form #update_main_service_description').val(data.service_description);

						$('#edit_main_services_modal').removeClass('main_hidemodall');
						$('#edit_main_services_modal').modal('show');
		
					}
				}
			});
		});

		/********** Main Service modal btn close***********/
		$('.close').on('click', function()
		{
			setTimeout(function () {
				$('#edit_main_services_modal').modal('hide');
			}, 300); 
		})

		/******************* delete main service sec **********************/ 
		$(".main_service_delete").on('click', function() 
		{
			var main_menu_service_id = $(this).data('id');
		
			Swal.fire({
				title: 'Are you sure?',
				text: 'You won\'t be able to revert this!',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					// User confirmed deletion, send AJAX request
					$.ajax({
						url: ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: {
							action: 'delete_main_menu_service',
							main_menu_service_id: main_menu_service_id,
						},
						success: function(response) {
							console.log(response);
							if (response.status === 'success') {
								Swal.fire({
									title: "Success",
									text: "Service deleted successfully",
									icon: "success",
									showConfirmButton: false,
									timer: 2000
								}).then(function() {
									location.reload();
								});
							} else {
								Swal.fire({
									title: 'Error',
									text: 'Failed to delete service.',
									icon: 'error',
									showConfirmButton: true
								});
							}
						}
					});
				}
			});
		});

		/************* Main service dish meta *****************/ 
		$(".main_service_dish").on('click', function() 
		{
            var _this = $(this);
			var main_dish_id = $(this).val();
			var main_service_id = $(this).data('service-id');
			var isChecked = $(this).prop('checked'); 

			$.ajax({
						url :ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: { 
								action: 'insert_main_service_dishes',
								main_dish_id: main_dish_id,
								main_service_id: main_service_id,
								isChecked: isChecked
							},
							success: function(response) {
							
							if (response.success === 'limiterror_add') 
							{
                                // Swal.fire({
                                //     title: "Info",
                                //     text: response.message,
                                //     icon: "info",
                                //     toast: true,
                                //     position: "top",
                                //     showConfirmButton: false,
                                //     timer: 3500
                                // })
                                $(_this).removeClass('no-content');

                            }
                            else if (response.success === 'limiterror_extra') 
							{
                                Swal.fire({
                                    title: "Info",
                                    text: response.message,
                                    icon: "info",
                                    toast: true,
                                    position: "top",
                                    showConfirmButton: false,
                                    timer: 3500
                                })
                                $(_this).addClass('no-content');
                            }
                            else if(response.success === 'limitsuccess')
                            {
                                Swal.fire({
                                    title: "Success",
                                    text: response.message,
                                    icon: "success",
                                    toast: true,
                                    position: "top",
                                    showConfirmButton: false,
                                    timer: 2000
                                })
                                $(_this).removeClass('no-content');
                            } 
                        }
							
					});
		});

		/******************** DAILY MENU SEC ************************* */ 

		/************ show daily menu service modal***************************/ 

		/*********** open service moodal ************/ 
		$('.add_daily_service').on('click', function(event)
		{
			event.preventDefault();
			var menu_id = $(this).data('id');
			//console.log("Menu ID:", menu_id); 

			$("#daily_service_menu_id").val(menu_id);
			$('.daily_service1').modal('show');

		})

		/**********close daily menu  modal btn ***********/
		$('.close').on('click', function()
		{
			setTimeout(function () {
				$('.daily_service1').modal('hide');
			}, 300); 
		})

		/**************** Insert daily menu Service sec ******************/ 
		$('#res_custom_daily_menu_service_form').on('submit', function(event)
		{
			event.preventDefault();
			var daily_menu_service_id = $(this).find('.select_ser_sec').val();
			var daily_menu_id = $("#daily_service_menu_id").val();
			
			$.ajax({
					url :ajax_object.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: { 
							action: 'insert_daily_menu_services',
							daily_menu_service_id: daily_menu_service_id, 
							daily_menu_id: daily_menu_id,
						},
					success: function(response) 
					{
						console.log(response);
						if(response.success === true)
						{
							Swal.fire({
								title: "Success",
								text: "Add service sccessfully",
								icon: "success",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}
						else if(response.success === 'limiterror')
						{
							Swal.fire({
								title: "Error",
								text: "Not add More than 3 Services.",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}
						else
						{
							Swal.fire({
								title: "Error",
								text: "Unable to add service",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}	
				    },
					error: function(xhr, status, error) 
					{
						console.log(xhr.responseText);
						Swal.fire({
							title: "Error",
							text: "An error occurred while processing the request",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}
			    });
		});


		/**********close daily edit modal btn ***********/
		$('.close').on('click', function()
		{
			setTimeout(function () {
				$('#edit_daily_services_modal').modal('hide');
			}, 300); 
		})


		/*************** delete daily menu service ************************/ 
		$(".daily_service_delete").on('click', function() 
		{
			var update_daily_service_menu_id = $(this).data('id');
		
			Swal.fire({
				title: 'Are you sure?',
				text: 'You won\'t be able to revert this!',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					// User confirmed deletion, send AJAX request
					$.ajax({
						url: ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: {
							action: 'daily_menu_delete_service',
							update_daily_service_menu_id: update_daily_service_menu_id,
						},
						success: function(response) {
							console.log(response);
							if (response.status === 'success') {
								Swal.fire({
									title: "Success",
									text: "Service deleted successfully",
									icon: "success",
									showConfirmButton: false,
									timer: 2000
								}).then(function() {
									location.reload();
								});
							} else {
								Swal.fire({
									title: 'Error',
									text: 'Failed to delete service.',
									icon: 'error',
									showConfirmButton: true
								});
							}
						}
					});
				}
			});
		});

		/************* daily service dish meta *****************/ 
		$(".daily_service_dishes").on('click', function() 
		{
			var _this = $(this);
			var daily_dish_id = $(this).val();
			var daily_service_id = $(this).data('service-id');
			var isChecked = $(this).prop('checked'); 


			$.ajax({
						url :ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: { 
								action: 'insert_daily_service_dishes',
								daily_dish_id: daily_dish_id,
								daily_service_id: daily_service_id,
								isChecked: isChecked
							},
						success: function(response) 
						{
							console.log(response);
							if (response.success === 'limiterror_add') 
							{
								
								// Swal.fire({
								// 	title: "Info",
								// 	text: response.message,
								// 	icon: "info",
								// 	toast: true,
								// 	position: "top",
								// 	showConfirmButton: false,
								// 	timer: 3500
								// })
								$(_this).removeClass('no-content');
							}
							else if (response.success === 'limiterror_extra') 
							{
                                Swal.fire({
                                    title: "Info",
                                    text: response.message,
                                    icon: "info",
                                    toast: true,
                                    position: "top",
                                    showConfirmButton: false,
                                    timer: 3500
                                })
                                $(_this).addClass('no-content');
                            }
							else if(response.success === 'limitsuccess')
							{
								Swal.fire({
									title: "Success",
									text: response.message,
									icon: "success",
									toast: true,
									position: "top",
									showConfirmButton: false,
									timer: 2000
								})
								$(_this).removeClass('no-content');
							} 
						}
					});
		});

		/************************ SERVICE SECTION **************************/ 
		/*********** open service moodal ************/ 
		$('.add_service').on('click', function(event)
		{
			event.preventDefault();
			var menu_id = $(this).data('id');
			//console.log("Menu ID:", menu_id); 

			$("#service_menu_id").val(menu_id);
			$('.service').modal('show');

		})

		/**********close modal btn ***********/
		$('.close').on('click', function()
		{
			setTimeout(function () {
				$('.service').modal('hide');
			}, 300); 
		})

		/**************** Insert Service sec ******************/ 
		$('#res_custom_service_form').on('submit', function(event)
		{
			event.preventDefault();
			var serviceName = $("#service_name").val();
			var serviceDescription = $("#service_description").val();
			// var servicePrice = $("#service_price").val();
			var service_menu_id = $("#service_menu_id").val();
			
			$.ajax({
					url :ajax_object.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: { 
							action: 'insert_services',
							serviceName: serviceName, 
							serviceDescription: serviceDescription,
							// servicePrice: servicePrice,
							service_menu_id: service_menu_id,
						},
					success: function(response) 
					{
						console.log(response);
						if(response.success === true)
						{
							Swal.fire({
								title: "Success",
								text: "Add service sccessfully",
								icon: "success",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
								//window.location.href = '';
							},200);
						}
						else
						{
							Swal.fire({
								title: "Error",
								text: "Unable to add service",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}	
				    },
					error: function(xhr, status, error) 
					{
						console.log(xhr.responseText);
						Swal.fire({
							title: "Error",
							text: "An error occurred while processing the request",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}
			    });
		});

		/**************** Edit Service sec ******************/ 
		$('.service_edit').on('click', function(event)
		{
			event.preventDefault();
			var service_id = $(this).data('id');

			$.ajax({
				url :ajax_object.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: { 
						action: 'edit_service_modal',
						service_id: service_id
					  },
				success: function(response) 
				{
					//console.log(response);
					if (response.status === 'success') 
					{
						var data = response.data;
						$('#edit_res_custom_service_form #service_id').val(data.id);
						$('#edit_res_custom_service_form #update_service_name').val(data.service_name);
						$('#edit_res_custom_service_form #update_service_description').val(data.service_description);
						// $('#edit_res_custom_service_form #update_service_price').val(data.service_pricing);

						$('#edit_services_modal').removeClass('hidemodall');
						$('#edit_services_modal').modal('show');
		
					}
				}
			});
		});

		/**********close modal btn ***********/
		$('.close').on('click', function()
		{
			setTimeout(function () {
				$('.edit_service').modal('hide');
			}, 300); 
		})
		
		/****************** update service sec ********************/ 
		$("#edit_res_custom_service_form").on('submit', function(event)
		{
			event.preventDefault();
			var service_id = $("#special_service_id").val();
			var update_service_name = $("#update_service_name").val();
			var update_service_description = $("#update_service_description").val();

			$.ajax({
						url :ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: { 
								action: 'update_special_service_modal',
								service_id: service_id,
								update_service_name: update_service_name,
								update_service_description: update_service_description
							},
						success: function(response) 
						{
							console.log(response);
							// if (response.status === 'success') 
							// {
							// 	Swal.fire({
							// 		title: "Success",
							// 		text: "Update service sccessfully",
							// 		icon: "success",
							// 		buttons: false,
							// 		timer: 2000 
							// 	}).then(function() 
							// 	{
							// 		location.reload();
							// 	}, 200);
							// }
						}
				  });
		});

		/******************* delete service sec **********************/ 
		$(".service_delete").on('click', function() 
		{
			var service_id = $(this).data('id');
		
			Swal.fire({
				title: 'Are you sure?',
				text: 'You won\'t be able to revert this!',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					// User confirmed deletion, send AJAX request
					$.ajax({
						url: ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: {
							action: 'delete_service',
							service_id: service_id,
						},
						success: function(response) {
							console.log(response);
							if (response.status === 'success') {
								Swal.fire({
									title: "Success",
									text: "Service deleted successfully",
									icon: "success",
									showConfirmButton: false,
									timer: 2000
								}).then(function() {
									location.reload();
								});
							} else {
								Swal.fire({
									title: 'Error',
									text: 'Failed to delete service.',
									icon: 'error',
									showConfirmButton: true
								});
							}
						}
					});
				}
			});
		});

		/******************* delete special service sec **********************/ 
		$(".special_service_delete").on('click', function() 
		{
			var service_id = $(this).data('id');
		
			Swal.fire({
				title: 'Are you sure?',
				text: 'You won\'t be able to revert this!',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					// User confirmed deletion, send AJAX request
					$.ajax({
						url: ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: {
							action: 'delete_service_special',
							service_id: service_id,
						},
						success: function(response) {
							console.log(response);
							if (response.status === true) {
								Swal.fire({
									title: "Success",
									text: "Service deleted successfully",
									icon: "success",
									showConfirmButton: false,
									timer: 2000
								}).then(function() {
									location.reload();
								});
							} else {
								Swal.fire({
									title: 'Error',
									text: 'Failed to delete service.',
									icon: 'error',
									showConfirmButton: true
								});
							}
						}
					});
				}
			});
		});

		/************ select dishes according to the services ************/ 
		// $(".service_dishes").on('click', function() 
		// {
		// 	var dish_id = $(this).val();
		// 	var service_id = $(this).data('service-id');
		// 	var isChecked = $(this).prop('checked'); 
		// 	//alert(isChecked);

		// 	if(isChecked == true)
		// 	{
		// 		$.ajax({
		// 					url :ajax_object.ajaxurl,
		// 					method: 'POST',
		// 					dataType: 'json',
		// 					data: { 
		// 							action: 'insert_service_dishes',
		// 							dish_id: dish_id,
		// 							service_id: service_id,
		// 						},
		// 					success: function(response) 
		// 					{
		// 						console.log(response);
		// 						if (response.success) 
		// 						{
									
		// 						}
		// 						else
		// 						{
									
		// 						}
		// 					}
		// 	 			});
		// 	}
		// 	else
		// 	{
		// 		$.ajax({
		// 			url :ajax_object.ajaxurl,
		// 			method: 'POST',
		// 			dataType: 'json',
		// 			data: { 
		// 					action: 'update_service_dishes',
		// 					dish_id: dish_id,
		// 					service_id: service_id
		// 				},
		// 			success: function(response) 
		// 			{
		// 				console.log(response);
						
		// 			}
		// 		 });
		// 	}
		
		// });

		$(".service_dishes").on('click', function() 
		{
			var _this = $(this);
			var special_dish_id = $(this).val();
			var special_service_id = $(this).data('service-id');
			var special_menu_id = $(this).data('menu-id');
			var isChecked = $(this).prop('checked'); 

			$.ajax({
						url :ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: { 
								action: 'insert_special_service_dishes',
								special_dish_id: special_dish_id,
								special_service_id: special_service_id,
								special_menu_id: special_menu_id,
								isChecked: isChecked
							},
							success: function(response) 
							{
								console.log(response.success);
							
								if (response.success === 'limiterror_add') 
								{
									// Swal.fire({
									// 	title: "Info",
									// 	text:  response.message,
									// 	icon: "info",
									// 	toast: true,
									// 	position: "top",
									// 	showConfirmButton: false,
									// 	// buttons: false,
									// 	timer: 3500
									// })
									$(_this).removeClass('no-content');
								}
								else if(response.success === 'limiterror_extra')
								{
									Swal.fire({
										title: "Info",
										text: response.message,
										icon: "info",
										toast: true,
										position: "top",
										showConfirmButton: false,
										timer: 3500
									})
									$(_this).addClass('no-content');
								} 
								else if(response.success === 'limitsuccess')
								{
									Swal.fire({
										title: "Success",
										text: response.message,
										icon: "success",
										toast: true,
										position: "top",
										showConfirmButton: false,
										timer: 2000
									})
									$(_this).removeClass('no-content');
								} 
							}
							
					});
		});


		/******************** other dishes *************************/ 
		$(".other_dish").on('click', function()
		{
			var other_menu_id = $(this).data('id');
			var other_dish_id = $(this).val();
			var isChecked = $(this).prop('checked'); 
			//alert(isChecked);
			if(isChecked == true)
			{
				$.ajax({
							url :ajax_object.ajaxurl,
							method: 'POST',
							dataType: 'json',
							data: { 
									action: 'insert_other_menu_dishes',
									other_menu_id: other_menu_id,
									other_dish_id: other_dish_id
								},
							success: function(response) 
							{
								console.log(response);
								if (response.success) 
								{
									// Swal.fire({
									// 	title: "Success",
									// 	text: "Add Dish in menu sccessfully",
									// 	icon: "success",
									// 	buttons: false,
									// 	timer: 2000 
									// }).then(function() 
									// {
									// 	//location.reload();
									// },200);
								}
								else
								{
									Swal.fire({
										title: "Info",
										text: response.message,
										icon: "info",
										toast: true,
										position: "top",
										showConfirmButton: false,
										timer: 3500
									})
								}
							}
			 			});
			}
			else
			{
				$.ajax({
					url :ajax_object.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: { 
							action: 'update_other_menu_meta_dishes',
							other_menu_id: other_menu_id,
							other_dish_id: other_dish_id
						},
					success: function(response) 
					{
						console.log(response);
					}
				 });
			}
		})
	});

	/********************** pdf sectipon *******************************/ 
	/******** get data from menus ************/ 
	jQuery(document).ready(function($)
	{
		$('#get_menu_pdf').on('change', function()
		{
			$('.gen_pdf_outer').css('display', 'block');
			$('.gen_pdf_lang_outer').css('display', 'block');
			
		});
		/*************** generate pdf ******************/ 
		$('.generate_pdf').on('click', function()
		{
			var menu_name = $('#get_menu_pdf option:selected').text();
			var menu_id = $('#get_menu_pdf option:selected').val();
			var menu_lang_id = $('#get_language_pdf option:selected').val();
	
			$.ajax({
					url :ajax_object.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: { 
							action: 'generate_pdf_ajax_action',
							menu_id: menu_id,
							menu_lang_id: menu_lang_id
						 },
					success: function(response) 
					{
						if (response.success) 
						{
							console.log(response);
							Swal.fire({
								title: "Success",
								text: response.message,
								icon: "success",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}
						else
						{
							Swal.fire({
								title: "Error",
								text: response.message,
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
							},200);
						}
					}
				});
		});
	})
	

	/**************** delete pdf **********************/ 
	jQuery(document).ready(function($)
	{
		$(".delete_pdff").on('click', function() 
		{
			var pdf_id = $(this).data('id');
			Swal.fire({
				title: 'Are you sure?',
				text: 'You won\'t be able to revert this!',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => 
			{
				if (result.isConfirmed) 
				{
					$.ajax({
						url: ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: {
							action: 'delete_pdf_data',
							pdf_id: pdf_id,
						},
						success: function(response) 
						{
							console.log(response);
							if (response.status === 'success') 
							{
								Swal.fire({
									title: "Success",
									text: "Pdf deleted successfully",
									icon: "success",
									showConfirmButton: false,
									timer: 2000
								}).then(function() {
									location.reload();
								});
							} 
							else 
							{
								Swal.fire({
									title: 'Error',
									text: 'Failed to delete pdf.',
									icon: 'error',
									showConfirmButton: true
								});
							}
						}
					});
				}
			});
		});
	});

	/************* copied pdf link ****************/ 
	$(document).ready(function() 
	{
		$('.copy_pdff').click(function(event) {
			event.preventDefault();
	
			// Get the href attribute of the clicked anchor element
			var linkToCopy = $(this).attr('href');
	
			// Create a temporary input element to copy the link
			var tempInput = $('<input>');
			$('body').append(tempInput);
			tempInput.val(linkToCopy).select();
	
			// Copy the link to the clipboard
			document.execCommand('copy');
	
			// Remove the temporary input element
			tempInput.remove();
	
			// Optionally, provide some feedback to the user
			alert('Link copied to clipboard: ' + linkToCopy);
		});
	});

	/****************** drag drop functionality *************************/ 
	jQuery(document).ready(function($)
	{
		$('.droppable_area_service li').on('click', function() 
		{
			// Get the data-slug attribute of the parent ul
			var parentUlSlug = $(this).closest('.droppable_area_service').data('slug');

			// console.log("Parent UL Slug:", parentUlSlug);
	
			if (parentUlSlug === undefined || parentUlSlug.trim() === "") {
				// console.log("Calling initOthers");
				initOthers();
			}
			
		});
	
		var modernAccordion = $('.mdn-accordion');
		if( modernAccordion.length > 0 ) 
		{
			modernAccordion.each(function()
			{
				var each_accordion = $(this);
				$('.accordion-toggle:checked').siblings('ul').attr('style', 'display:none;').stop(true,true).slideDown(300);
				each_accordion.on('change', '.accordion-toggle', function()
				{
					var toggleAccordion = $(this);
					if (toggleAccordion.is(":radio")) 
					{
						toggleAccordion.closest('.mdn-accordion').find('input[name="' + $(this).attr('name') + '"]').siblings('ul')
						.attr('style', 'display:block;').stop(true,true).slideUp(300); 
						toggleAccordion.siblings('ul').attr('style', 'display:none;').stop(true,true).slideDown(300);									
					} 
					else 
					{				
						(toggleAccordion.prop('checked')) ? toggleAccordion.siblings('ul')
						.attr('style', 'display:none;').stop(true,true).slideDown(300) : toggleAccordion.siblings('ul')
						.attr('style', 'display:block;').stop(true,true).slideUp(300); 
					}
				});
			});
		}	

		// ++++++++++++++++ drag and drop functionality services +++++++++++++++
		$(initServices);
	
		function initServices() 
		{
			$(".droppable_area_service").sortable({
				connectWith: ".connected-sortable",
				stack: '.connected-sortable ul',

				/******** get current item ********/
				stop: function(event, ui) 
				{
					var currentItem = ui.item;

					var main_menuId = currentItem.parent().data("menu");

					var dataMenuIdArray = [];
					currentItem.parent().find(".accordion-title").each(function () 
					{
						dataMenuIdArray.push($(this).data("menu-id"));
					});

					//console.log(dataMenuIdArray);
					saveDragDropPosition(main_menuId, dataMenuIdArray);
				}
			}).disableSelection();
		}

		// Save the drag and drop position 
		function saveDragDropPosition(main_menuId, dataMenuIdArray) 
		{
			$.ajax({
						url :ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: { 
								action: 'save_drag_drop',
								main_menuId: main_menuId,
								dataMenuIdArray: dataMenuIdArray
							},
						success: function(response) 
						{
						}
				   });
		}

		/************* drag and drop functionality for dish  **************/ 
		$(initDishes);
		
		function initDishes() 
		{
			$(".droppable_area_dish").sortable({
				connectWith: ".connected-sortable",
				stack: '.connected-sortable ul',

				/******** get current item ********/
				stop: function(event, ui) 
				{
					var currentItem = ui.item;

					var main_menuId = currentItem.parent().data("menu");
					var main_menu_serviceId = currentItem.parent().data("id");

					var dataDishIdArray = [];
					currentItem.parent().find(".dish-level").each(function () 
					{
						dataDishIdArray.push($(this).val());
					});

					console.log(dataDishIdArray);
					saveDragDropDishPosition(main_menuId, main_menu_serviceId, dataDishIdArray);
				}
			}).disableSelection();
		}

		// Save the drag and drop position 
		function saveDragDropDishPosition(main_menuId, main_menu_serviceId, dataDishIdArray) 
		{
			$.ajax({
						url :ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: { 
								action: 'save_dish_drag_drop',
								main_menuId: main_menuId,
								main_menu_serviceId: main_menu_serviceId,
								dataDishIdArray: dataDishIdArray
							},
						success: function(response) 
						{
						}
				});
		}

		// // ++++++++++++++++ drag and drop functionality for others +++++++++++++++
		// $(initOthers);

		function initOthers() 
		{
			$(".droppable_area_service").sortable({
				connectWith: ".connected-sortable",
				stack: '.connected-sortable ul',

				/******** get current item ********/
				stop: function(event, ui) 
				{
					var currentItem = ui.item;

					var main_menuId = currentItem.parent().data("menu");

					var dataMenuIdArray = [];
					currentItem.parent().find(".dish-level").each(function () 
					{
						dataMenuIdArray.push($(this).val());
					});

					console.log(dataMenuIdArray);
					saveDragDropOtherDishPosition(main_menuId, dataMenuIdArray);
				}
			}).disableSelection();
		}

		// Save the drag and drop position 
		function saveDragDropOtherDishPosition(main_menuId, dataMenuIdArray) 
		{
			$.ajax({
						url :ajax_object.ajaxurl,
						method: 'POST',
						dataType: 'json',
						data: { 
								action: 'save_drag_drop_other_dish',
								main_menuId: main_menuId,
								dataMenuIdArray: dataMenuIdArray
							},
						success: function(response) 
						{
						}
					});
		}

	});

	
	/**************** serach dishes *******************/ 
	jQuery(document).ready(function($)
	{
		$('.searchInput').on('keyup', function() 
		{
			let searchQuery = $(this).val().toLowerCase();
			console.log(searchQuery);

			let allNames = $('.dish-level');
			let noDishesFound = $('#no-dishes-found');

			let foundDishes = false;
	
			allNames.each(function() {
				let currentName = $(this).text().toLowerCase();
	
				if (currentName.includes(searchQuery)) {
					$(this).css('display', 'block');
					foundDishes = true;
				} else {
					$(this).css('display', 'none');
				}
			});

			if (foundDishes) {
				noDishesFound.hide();
			} else {
				noDishesFound.show();
			}
		});

	});

	/**************** Insert Special Service sec ******************/ 
	jQuery(document).ready(function($)
	{
		$('#res_custom_special_service_form').on('submit', function(event)
		{
			event.preventDefault();
			var special_menu_service_id = $(this).find('.select_ser_sec').val();
			var special_menu_id = $("#service_menu_id").val();

			$.ajax({
					url :ajax_object.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: { 
							action: 'insert_special_menu_services',
							special_menu_service_id: special_menu_service_id, 
							special_menu_id: special_menu_id, 
						},
					success: function(response) 
					{
						console.log(response);
						if(response.status === true)
						{
							Swal.fire({
								title: "Success",
								text: "Add service sccessfully",
								icon: "success",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}
						else if(response.status === 'limiterror')
						{
							Swal.fire({
								title: "Error",
								text: "Not add More than 3 Services.",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}
						else
						{
							Swal.fire({
								title: "Error",
								text: "Unable to add service",
								icon: "error",
								buttons: false,
								timer: 2000 
							}).then(function() 
							{
								location.reload();
							},200);
						}	
					},
					error: function(xhr, status, error) 
					{
						console.log(xhr.responseText);
						Swal.fire({
							title: "Error",
							text: "An error occurred while processing the request",
							icon: "error",
							buttons: false,
							timer: 2000 
						}).then(function() 
						{
							location.reload();
						},200);
					}
				});
		});
	})
	
	
	/************ datatables tables *********************/ 
	jQuery(document).ready(function($)
	{
		$('#dish_table').DataTable();
		$('#cat_table').DataTable();
		$('#menu_table').DataTable();
		$('#menuPDFDetails').DataTable();
		$('#service_table').DataTable();
	});

})( jQuery );
