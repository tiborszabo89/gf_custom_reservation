<?php

$main_form_id = MAIN_FORM;
$filename = round(microtime(true));
add_filter( 'gform_pre_render_'.$main_form_id, 'populate_posts' );
add_filter( 'gform_pre_validation_'.$main_form_id, 'populate_posts' );
add_filter( 'gform_pre_submission_filter_'.$main_form_id, 'populate_posts' );
add_filter( 'gform_admin_pre_render_'.$main_form_id, 'populate_posts' );
function populate_posts( $form ) {
	foreach ( $form['fields'] as &$field ) {
		if ( $field->type != 'select' || strpos( $field->cssClass, 'populate-places' ) === false ) {
			continue;
		}
		$choices = array();
		global $wpdb;
		$times = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_timeslots", OBJECT ); 
		$places = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_place WHERE max_number > 0", ARRAY_A ); 

		if ($times) {
			foreach ($places as $p) {
				$choices[] = array( 
					'text' => $p['place'],
					'value' => $p['id'] ,
				);
			}
			$field->placeholder = 'Wähle einen Ort aus';
			$field->choices = $choices;
		}
	}
	return $form;
}

add_action("gform_post_submission", "rename_file", 10, 2);
function rename_file($entry, $form){
    //---------------------------------------------------------
    //---------------------------------------------------------
    //REPLACE THESE THREE VARIABLES BASED ON YOUR ACTUAL IDs
		global $main_form_id;
		global $filename;
    $form_id = $main_form_id;
    $fileupload_field_id = "5";
    $email_field_id = "3";
		$name_field_id = '1';
    //---------------------------------------------------------
    //---------------------------------------------------------
    //ignore other forms
    if($form["id"] != $form_id)
        return;

    $upload_info = wp_upload_dir();

    //original name/path
    $original_file_url = $entry[$fileupload_field_id];
    $original_file_path = str_replace($upload_info["baseurl"], $upload_info["basedir"], $original_file_url);
    $original_file_info = pathinfo($original_file_url);

    //New File Name (without extension).

    $new_name = $filename;

    //adding same extension as original
    $new_name .= "." . $original_file_info["extension"];

    $new_file_url = str_replace($original_file_info["basename"], $new_name, $original_file_url);
    $new_file_path = str_replace($original_file_info["basename"], $new_name, $original_file_path);

    //rename file
    //$is_success = rename(str_replace('http://', '', $original_file_path), str_replace('http://', '', $new_file_path));
		$is_success = rename($original_file_path, $new_file_path);

    //if file was renamed successfully, updating entry so that it points to the new file
    if($is_success){
        global $wpdb;
        $wpdb->update(RGFormsModel::get_lead_details_table_name(), array("value" => $new_file_url), array("lead_id" => $entry["id"], "value" => $original_file_url));
    }
}

//Appointments do DB
add_action( 'gform_pre_submission_'.$main_form_id, 'pre_submission_handler_2' );
function pre_submission_handler_2( $form ) {
  global $wpdb;
	global $main_form_id;
	global $filename;
	$chosen_place = $_POST['input_6'];
	$chosen_timeslot = $_POST['input_7'];


  $time_slots_final = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_timeslots WHERE place_id='$chosen_place' AND time='$chosen_timeslot'", OBJECT );
	$user_table = $wpdb->prefix.'user_management_users';
	$user_full_name = $_POST['input_1_3'] . " " . $_POST['input_1_6'];
	$timeslot_foreign_key = $time_slots_final[0]->id;	
	$temp = explode(".", $_SESSION['input_5']['name']);

	$newfilename = round(microtime(true)) . '.' . end($temp);
	$submit_date = strval(date('Y.m.d h:i:s'));

	$upload_path = GFFormsModel::get_upload_url(intval($main_form_id));
	$file_url = $upload_path.'/'.date('Y').'/'.date('m').'/'.$filename.'.'.end($temp);
	$full_address = $_POST['input_4_3'] . ' ' . $_POST['input_4_1'] . ' ' . $_POST['input_4_2'];
	$data = array(
		'submit_date' => $submit_date,
		'timeslot' =>  $timeslot_foreign_key,
		'name' =>  $user_full_name,
		'phone' => $_POST['input_2'],
		'email' => $_POST['input_3'],
		'uploaded_image_url' => $file_url,
		'reason' => $_POST['input_10'],
		'planted_image_url' => "",
		'place_id' => $chosen_place,
		'picked_time' => $_POST['input_7'],
		'status' => 'incoming',
		'address' => $full_address,
		);

	$format = array('%s','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s');
	if ($wpdb->insert($user_table,$data,$format)) {
		unset($_SESSION['input_5']);
	}
	
	$time_slots_table = $wpdb->prefix.'user_management_timeslots';
	$place_table = $wpdb->prefix . 'user_management_place';

	$get_current_tree_number =  intval($wpdb->get_results( "SELECT tree_number FROM {$wpdb->prefix}user_management_timeslots WHERE id='$timeslot_foreign_key'", OBJECT )[0]->tree_number);

	$get_current_place = intval($wpdb->get_row( "SELECT max_number FROM {$wpdb->prefix}user_management_place WHERE id='$chosen_place'")->max_number);
	$final_place_max_number = $get_current_place -1;

	if($get_current_tree_number > 0 && $get_current_place > 0) {
		$time_data = array(
			'tree_number' => $get_current_tree_number - 1,
		);
		$time_where = array(
			'id' => $timeslot_foreign_key,
		);
		if ($wpdb->update($time_slots_table,$time_data,$time_where)) {
			$wpdb->update( $place_table , ['max_number' => $final_place_max_number] , ['id' => $chosen_place] );
		}
	} 
		
}

//Dynamic category dropdown
add_filter("gform_pre_render_".$main_form_id, "monitor_single_dropdown");
function monitor_single_dropdown($form){ ?>
	<?php global $main_form_id; ?>
	<style>
		.d-none {
			display: none !important;
		}
		.hide-first .gf_placeholder {
			display:none !important;
		}
	</style>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script type="text/javascript">
	
	jQuery(document).ready(function(){
		jQuery(document).on('gform_post_render', function(e, form_id) { 
    if ( jQuery('div.gform_validation_error').length > 0 ) {
							jQuery(`#input_${formid}_6`).change()
					}
			});
		var formid = <?= $main_form_id; ?>;
		var hiddenInput = jQuery(`#field_${formid}_7`);
		var selectInputka = jQuery(`#input_${formid}_6`);
		hiddenInput.addClass('d-none');
		jQuery(document).on('change',`#input_${formid}_6`,function() {
			var mainCat=jQuery(`#input_${formid}_6`).val();
			hiddenInput.removeClass('d-none');
			selectInputka.addClass('hide-first');
			jQuery(`#input_${formid}_7`).empty();
				jQuery.ajax({
					url:"<?php echo admin_url('admin-ajax.php'); ?>",
					type:'POST',
						data: ({
							action: 'timeslot_fill_dropdown',
							timeslot_place: mainCat
						}),
						success:function(results) {
							//console.log(results)
							const parsedResults = jQuery.parseJSON(results)
							if (parsedResults) {
								//console.log(parsedResults[0]);
								jQuery(`#input_${formid}_8`).attr('value', parsedResults[0][0]['address'])
								Array.prototype.forEach.call(parsedResults, (res) => {
									if(res['data-id']) {
										jQuery(`#input_${formid}_7`).append(`<option data-id="${res['data-id']}" value="${res['value']}">
											${res['text']}
										</option>`);
									}
								})
									
							} 
						},
						error:function(e) {
							console.log(e)
						}
				});
			}
		);

		var submitBtn = document.getElementById(`gform_submit_button_${formid}`)

		if (submitBtn) {
			submitBtn.addEventListener('click', function(e) {
				e.preventDefault()
				var timeslotId = document.getElementById(`input_${formid}_7`).options[document.getElementById(`input_${formid}_7`).selectedIndex].getAttribute('data-id')
				jQuery.ajax({
					url:"<?php echo admin_url('admin-ajax.php'); ?>",
					type:'POST',
					data: ({
						action: 'timeslot_free_value',
						timeslot_id: timeslotId
					}),
					success:function(results) {
						const response = jQuery.parseJSON(results)
						if (response.status) {
							document.getElementById(`gform_${formid}`).submit()
						} else {
							jQuery(`#input_${formid}_6`).change()
							alert('Dieses Zeitfenster ist bereits belegt!')
						}
					},
					error:function(e) {
						console.log(e)
					}
				})
			})
		}
	});
	</script>

	<?php
	return $form;
}

add_filter( 'gform_validation_'.$main_form_id, 'custom_validation_timeslot_email_equal' );
function custom_validation_timeslot_email_equal( $validation_result ) {
    global $wpdb;
    global $main_form_id;
    $timeslot_email_equal = $wpdb->prefix.'user_management_pre_registration_email';
		$user_table = $wpdb->prefix.'user_management_users';
    $form = $validation_result['form'];
    $email = $_POST['input_3'];
		//var_dump($_SESSION);
		if(!$_SESSION['input_5'] && !empty($_FILES['input_5']['name'])) {
			$_SESSION['input_5'] = $_FILES['input_5'];
			//var_dump($_FILES);
		}
    if (empty($wpdb->get_var( "SELECT * FROM {$wpdb->prefix}user_management_pre_registration_email WHERE email='$email'"))) {
        $validation_result['is_valid'] = false;
         foreach( $form['fields'] as &$field ) {
             if ( $field->id == '3' ) {
                $field->failed_validation = true;
                $field->validation_message = 'E-Mail ist nicht im System!';
                break;
            }
        }
    }
		if (!empty($wpdb->get_var( "SELECT * FROM {$wpdb->prefix}user_management_users WHERE email='$email'"))) {
			$validation_result['is_valid'] = false;
			 foreach( $form['fields'] as &$field ) {
					 if ( $field->id == '3' ) {
							$field->failed_validation = true;
							$field->validation_message = 'Für diese E-Mail Adresse liegt uns schon eine Bewerbung vor. Bitte beachte, dass du dich für nur einen Klimabaum bewerben darfst. ';
							break;
					}
			}
	}
    $validation_result['form'] = $form;
    return $validation_result;
}

function timeslot_fill_dropdown($form) {
$parentCat=$_POST['timeslot_place'];

global $wpdb;
$timeslots_table = $wpdb->base_prefix . 'user_management_timeslots';
$place_table = $wpdb->base_prefix . 'user_management_place';

$timeslot_filter = $wpdb->get_results("SELECT * FROM $timeslots_table WHERE place_id='$parentCat'");
$place_filter = $wpdb->get_results("SELECT address FROM $place_table WHERE id='$parentCat'");
$items = array();
 if ($timeslot_filter) {
	  $items[0] = $place_filter;
	 	foreach ( $timeslot_filter as $timeslot_filt ){
			if(!intval($timeslot_filt->tree_number) < 1) {
				$items[] = array("value" => $timeslot_filt->time, "data-id" => $timeslot_filt->id , "text" => $timeslot_filt->time );
			}
		}
 }
	echo json_encode($items);
	wp_die();
}

add_action( 'wp_ajax_nopriv_timeslot_fill_dropdown', 'timeslot_fill_dropdown');
add_action( 'wp_ajax_timeslot_fill_dropdown', 'timeslot_fill_dropdown');

add_action( 'wp_ajax_nopriv_timeslot_free_value', 'timeslot_free_value');
add_action('wp_ajax_timeslot_free_value', 'timeslot_free_value');

function timeslot_free_value($form) {
	$timeslotId = $_POST['timeslot_id'];
	$status = false;
	global $wpdb;
	$timeslots_table = $wpdb->base_prefix . 'user_management_timeslots';
	
	if ($wpdb->get_var("SELECT * FROM $timeslots_table WHERE  id = '$timeslotId' AND tree_number > 0")) {
		$status = true;
	}
	echo json_encode(['status' => $status]);
	
	wp_die();
}

