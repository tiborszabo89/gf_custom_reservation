<?php

$final_form_id = FINAL_FORM;
$pre_registration_form_id = PRE_FORM;
$filename = round(microtime(true));

add_action("gform_post_submission", "rename_file_again", 10, 2);
function rename_file_again($entry, $form){
    //---------------------------------------------------------
    //---------------------------------------------------------
    //REPLACE THESE THREE VARIABLES BASED ON YOUR ACTUAL IDs
		global $final_form_id;
		global $filename;
    $form_id = $final_form_id;
    $fileupload_field_id = "3";
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



add_action( 'gform_pre_submission_'.$final_form_id, 'pre_submission_handler_final' );
function pre_submission_handler_final( $form ) {
  global $wpdb;
  global $final_form_id;
  global $filename;

  $email = $_POST['input_1'];
  $final_upload = $_FILES['input_3'];
  
  $user_table = $wpdb->prefix.'user_management_users';
  $temp = explode(".", $_SESSION['input_3']['name']);

  $newfilename = round(microtime(true)) . '.' . end($temp);

  $upload_path = GFFormsModel::get_upload_url(intval($final_form_id));
  $file_url = $upload_path.'/'.date('Y').'/'.date('m').'/'.$filename.'.'.end($temp);

  if ($user_id = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}user_management_users WHERE email='$email'")) {
      $data =$file_url;
      $wpdb->update($user_table,['planted_image_url'=>$file_url],['id'=>$user_id]);
  }
}
if ($wpdb->update($user_table,$data,$format)) {
    unset($_SESSION['input_3']);
}
add_filter( 'gform_validation_'.$final_form_id, 'custom_validation_final_form' );
function custom_validation_final_form( $validation_result ) {
    global $wpdb;
    global $pre_registration_form_id;
    global $final_form_id;
    $pre_registration_email = $wpdb->prefix.'user_management_users';
  
    $form = $validation_result['form'];
    $email = $_POST['input_1'];
    $user_final = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_users WHERE email='$email'", ARRAY_A);
    if (!$user_final) {
        $validation_result['is_valid'] = false;
         foreach( $form['fields'] as &$field ) {
             if ( $field->id == '1' ) {
                $field->failed_validation = true;
                $field->validation_message = 'E-Mail ist nicht im System!';
                break;
            }
        }
    }
    foreach($user_final[0] as $key => $value) {
        if ($key == 'planted_image_url') {
            $planted_image_url = $value;
        }        
    }
    if (!empty($planted_image_url)) {
        $validation_result['is_valid'] = false;
         foreach( $form['fields'] as &$field ) {
                 if ( $field->id == '2' ) {
                    $field->failed_validation = true;
                    $field->validation_message = 'Bild ist bereits hochgeladen';
                    break;
                }
        }
    }
    if(!$_SESSION['input_3'] && !empty($_FILES['input_3']['name'])) {
        $_SESSION['input_3'] = $_FILES['input_3'];
        //var_dump($_FILES);
    }

    $validation_result['form'] = $form;
    return $validation_result;
}