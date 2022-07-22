<?php
require '../../../../wp-load.php'; 
// Fetch records from database 
global $wpdb;
$query = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_users WHERE status='approved'", ARRAY_A );
$place_query = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_place", ARRAY_A );

if($query){ 
    $delimiter = ","; 
    $filename = "climatree_registered_users-" . date('Y-m-d') . ".csv"; 
     
    // Create a file pointer 
    $f = fopen('php://memory', 'w');
     
    // Set column headers 
    $fields = array('ID', 'Submit date', 'Name', 'Phone', 'Email', 'Reason', 'Zip', 'City', 'Address','Image', 'Final image' , 'Place', 'Picked time'); 
    fputcsv($f, $fields, $delimiter); 
     
    // Output each row of the data, format line as csv and write to file pointer 
    foreach ($query as $row) {
      foreach($place_query as $place) {
        if($row['place_id'] === $place['id']) {
          $timeslotId = $row['timeslot'];
          $timeslot_time = $timeslotId;
          if (!empty($timeslotId)) {
            $timeslot_time = $wpdb->get_var( "SELECT time FROM {$wpdb->prefix}user_management_timeslots WHERE id='$timeslotId'");
          }
          $zip = '';
          $email = $row['email'];
          
          $full_address = $row['address'];
          list($city, $address) = explode(' ', $full_address, 2);
          
          if (!empty($email)) {
            $zip = $wpdb->get_var( "SELECT zip FROM {$wpdb->prefix}user_management_pre_registration_email WHERE email='$email'");
          }
          $lineData = array($row['id'], $row['submit_date'], $row['name'], $row['phone'], $email, $row['reason'], $zip, $city, $address, $row['uploaded_image_url'], $row['planted_image_url'], $place['place'], $row['picked_time']); 
        }
      }
      
      fputcsv($f, $lineData, $delimiter); 
    }
     
    // Move back to beginning of file 
    fseek($f, 0); 
     
    // Set headers to download file rather than displayed 
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="' . $filename . '";'); 
     
    //output all remaining data on a file pointer 
    fpassthru($f); 
  } 
?>