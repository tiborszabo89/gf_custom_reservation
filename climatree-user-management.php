<?php
/**
 * Plugin Name: ClimaTree User Management plugin
 * Description: User management for registered users
 * Version: 1.0
 * Author: Anonymus
*/
include_once('parts/db_management.php');
register_activation_hook( __FILE__, 'installDatabaseTables' );

if ( !defined( 'ABSPATH' ) ) exit;
function register_my_session()
{
  if( !session_id() )
  {
    session_start();
  }
}

add_action('init', 'register_my_session');
function register_admin_dashboard_menu() {
  add_menu_page(
    __( 'Baumpaten', 'climatree' ),
    __( 'Baumpaten', 'climatree' ),
    'manage_options',
    'user-management-page',
    'admin_page_content',
    'dashicons-palmtree',
    3
  );

  add_submenu_page(
    'user-management-page',
    __( 'Timeslots', 'climatree' ),
    __( 'Timeslots', 'climatree' ),
    'manage_options',
    'user-management-timeslot',
    'timeslot_page_content',
    3
  );
}
add_action( 'admin_menu', 'register_admin_dashboard_menu' );



// Enqueue scripts and styles
function user_management_scripts($hook) {  
  $plugin_url = plugin_dir_url( __FILE__ );
  if ($hook === 'toplevel_page_user-management-page' || $hook === 'baumpaten_page_user-management-timeslot') {
    wp_enqueue_script( 'user_management_script', $plugin_url . '/assets/js/app.js' );
    wp_enqueue_style( 'user_management_style',  $plugin_url . "/assets/css/style.css");
  }
}
add_action('admin_enqueue_scripts', 'user_management_scripts');

function frontend_user_management_scripts() {  
  $plugin_url = plugin_dir_url( __FILE__ );
  wp_enqueue_script( 'user_management_script_frontend', $plugin_url . '/assets/js/frontend.js' );
  wp_localize_script( 'user_management_script_frontend', 'main_form_id_def', array('MAIN_FORM'=> MAIN_FORM, 'PRE_FORM'=> PRE_FORM, 'FINAL_FORM'=> FINAL_FORM ));
}
add_action('wp_enqueue_scripts', 'frontend_user_management_scripts');

// Includes
add_action( 'plugins_loaded', 'admin_page_override' );
 
function admin_page_override() {
  include_once('parts/admin_page.php');
}
include_once('parts/basic_functions.php');
include_once('parts/timeslot_page.php');
include_once('parts/pre_registration_form.php');
include_once('parts/final_form.php');