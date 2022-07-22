<?php
require ABSPATH . '/wp-load.php';

function installDatabaseTables() {
  global $wpdb;

  $pre_registration_email = $wpdb->base_prefix . 'user_management_pre_registration_email';
  $place_table = $wpdb->base_prefix . 'user_management_place';
  $timeslot_table = $wpdb->base_prefix . 'user_management_timeslots';
  $users_table = $wpdb->base_prefix . 'user_management_users';
  $charset_collate = 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
  // $wpdb->query( "DROP TABLE IF EXISTS $timeslot_table" );
  // $wpdb->query( "DROP TABLE IF EXISTS $users_table" );
  // $wpdb->query( "DROP TABLE IF EXISTS $place_table" );
  // $wpdb->query( "DROP TABLE IF EXISTS $pre_registration_email" );

  $user_management_place = "CREATE TABLE IF NOT EXISTS $place_table (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `max_number` int(20) NOT NULL,
    `place` varchar(100) NOT NULL,
    `address` varchar(256) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB $charset_collate";

  $user_management_pre_registration_email = "CREATE TABLE IF NOT EXISTS $pre_registration_email (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(100) NOT NULL,
    `zip` int(15) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB $charset_collate";

  $user_management_timeslots = "CREATE TABLE IF NOT EXISTS $timeslot_table (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tree_number` int(11) NOT NULL,
    `place_id` int(11) NOT NULL,
    `time` varchar(100) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`place_id`) REFERENCES $place_table(id) ON DELETE CASCADE 
  ) ENGINE=InnoDB $charset_collate";

  $user_management_users = "CREATE TABLE IF NOT EXISTS $users_table (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `submit_date` varchar(25) NOT NULL,
    `timeslot` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `phone` varchar(30) NOT NULL, 
    `email` varchar(100) NOT NULL,
    `uploaded_image_url` varchar(255) NOT NULL,
    `planted_image_url` varchar(255) NOT NULL,
    `reason` varchar(300) NOT NULL,
    `place_id` int(11) NOT NULL,
    `picked_time` varchar(100) NOT NULL,
    `status` varchar(30) NOT NULL,
    `address` varchar(120) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`timeslot`) REFERENCES $timeslot_table(id) ON DELETE CASCADE,
    FOREIGN KEY (`place_id`) REFERENCES $place_table(id) ON DELETE CASCADE 
  ) ENGINE=InnoDB $charset_collate";

  require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
  dbDelta($user_management_pre_registration_email);
  dbDelta($user_management_place);
  dbDelta($user_management_timeslots);
  dbDelta($user_management_users);
}
?>
