<?php

$pre_registration_form_id = PRE_FORM;

add_action( 'gform_pre_submission_'.$pre_registration_form_id, 'pre_submission_handler_registration_email' );
function pre_submission_handler_registration_email( $form ) {
    global $wpdb;
	global $pre_registration_form_id;

    $email = $_POST['input_1'];
    $zip = $_POST['input_3'];
    $pre_registration_email = $wpdb->prefix.'user_management_pre_registration_email';

    if (!$wpdb->get_var( "SELECT * FROM {$wpdb->prefix}user_management_pre_registration_email WHERE email='$email'")) {
        $data = array(
            'email' => $email,
            'zip' => $zip,
        );
    
        $format = array('%s','%s');
        $wpdb->insert($pre_registration_email,$data,$format);
    }
}

add_filter( 'gform_validation_'.$pre_registration_form_id, 'custom_validation_pre_registration_email' );
function custom_validation_pre_registration_email( $validation_result ) {
    global $wpdb;
    global $pre_registration_form_id;
    $pre_registration_email = $wpdb->prefix.'user_management_pre_registration_email';
    $form = $validation_result['form'];
    $email = $_POST['input_1'];
    
    if ($wpdb->get_var( "SELECT * FROM {$wpdb->prefix}user_management_pre_registration_email WHERE email='$email'")) {
        $validation_result['is_valid'] = false;
         foreach( $form['fields'] as &$field ) {
             if ( $field->id == '1' ) {
                $field->failed_validation = true;
                $field->validation_message = 'Für diese E-Mail Adresse liegt uns schon eine Bewerbung vor. Bitte beachte, dass du dich für nur einen Klimabaum bewerben darfst.';
                break;
            }
        }
    }
    $validation_result['form'] = $form;
    return $validation_result;
}