<?php require ABSPATH . '/wp-load.php'; ?>
<?php global $wpdb ; ?>
<?php function admin_page_content() { 
$pagenum = intval($_GET['pagenum']);
$offset = 10;

$offsetnum = ($pagenum * $offset) - $offset;
if ($pagenum < 2) {
  $offsetnum = 0;
}
global $wpdb;
$all_appointments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_users", OBJECT );
$appointments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_users ORDER BY id DESC LIMIT $offset OFFSET $offsetnum", OBJECT );
$current_place = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_place", ARRAY_A );
$pre_table = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_pre_registration_email", ARRAY_A );

$get_filter_value = $_GET['filter-status'];
if ($get_filter_value) {
  $appointments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_users WHERE status='$get_filter_value' ORDER BY id DESC LIMIT $offset OFFSET $offsetnum", OBJECT );
  $all_appointments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_users WHERE status='$get_filter_value'", OBJECT );
}

$prev_query = $_GET;
$prev_query['pagenum'] = ($pagenum < 1) ? 1 : $pagenum-1;
$prev_page = http_build_query($prev_query);

$next_query = $_GET;
$next_query['pagenum'] = ($pagenum < 1) ? 2 : $pagenum+1;
$next_page = http_build_query($next_query);

if ($appointments): ?>

  <div class="main-appointment-wrapper" style="display:flex;flex-wrap:wrap;padding: 50px 40px 0 20px;">
      <div class="links-wrapper" style="display:flex;justify-content:space-between;align-items:center;flex:0 0 100%;max-width:100%;margin-bottom:20px;">
        <div class="filter-wrapper" style="display:flex;align-items:center;">
          <div style="margin-right: 10px;">Filter:</div>
          <form action="" method="get" style="margin-right:50px;display:flex;align-items:center;">
          <input type="hidden" name="page" value="user-management-page">
          <input type="hidden" name="pagenum" value="1">
            <select style="border:solid 1px black;padding:1px 20px 0 10px;border-radius:0;margin-right:10px;" name="filter-status" id="">
              <option value="">All</option>
              <option value="incoming" <?= ($get_filter_value === 'incoming') ? 'selected': ''; ?>>Incoming</option>
              <option value="approved" <?= ($get_filter_value === 'approved') ? 'selected': ''; ?>>Approved</option>
            </select>
            <input style="cursor:pointer;border:solid 1px black;padding:5px 10px;text-decoration:none;color:#000;background:#fff;" type="submit" value="Apply">
          </form>
          <div class="export-wrapper">
            <a style="margin-left:10px;border:solid 1px black;padding:6px 10px;text-decoration:none;color:#000;background:#fff;" target="_blank" href="<?= plugins_url() . '/climatree-user-management/parts/exportdata.php'; ?>">Export to Cleverreach</a>
          </div>
          <div class="export-wrapper">
            <a style="margin-left:10px;border:solid 1px black;padding:6px 10px;text-decoration:none;color:#000;background:#fff;" target="_blank" href="<?= plugins_url() . '/climatree-user-management/parts/export_all.php'; ?>">Export All Entries</a>
          </div>
        </div>
        <div class="links">
          <?php if ($pagenum > 1): ?>
            <a style="border:solid 1px black;padding:5px 10px;text-decoration:none;color:#000;background:#fff;margin-right:20px;" href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $prev_page; ?>">Previous page</a>
          <?php endif; ?>
          <?php if (count($all_appointments) > ($pagenum * $offset)): ?>
            <a style="border:solid 1px black;padding:5px 10px;text-decoration:none;color:#000;background:#fff;" href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $next_page; ?>">Next page</a>
          <?php endif; ?>
        </div>
      </div>
      <div class="admin-appointment-wrapper" style="flex: 0 0 100%;max-width:100%;">
        <div class="appointment-row" style="display:flex;margin:8px 0;">
          <div style="font-size:13px;flex: 0 0 2%;max-width:2%;font-weight:bold;" >ID</div>
          <div style="font-size:13px;flex: 0 0 5%;max-width:5%;font-weight:bold;" >Status</div>
          <div style="font-size:13px;flex: 0 0 8%;max-width:8%;font-weight:bold;" >Registration</div>
          <div style="font-size:13px;flex: 0 0 7%;max-width:7%;font-weight:bold;" >Full name</div>
          <div style="font-size:13px;flex: 0 0 9%;max-width:9%;font-weight:bold;" >Mail</div>
          <div style="font-size:13px;flex: 0 0 10%;max-width:10%;font-weight:bold;" >Reason</div>
          <div style="font-size:13px;flex: 0 0 5%;max-width:5%;font-weight:bold;" >ZIP</div>
          <div style="font-size:13px;flex: 0 0 5%;max-width:5%;font-weight:bold;" >Address</div>
          <div style="font-size:13px;flex: 0 0 7%;max-width:7%;font-weight:bold;" >Phone</div>
          <div style="font-size:13px;flex: 0 0 7%;max-width:7%;font-weight:bold;" >First image</div>
          <div style="font-size:13px;flex: 0 0 7%;max-width:7%;font-weight:bold;" >Planted image</div>
          <div style="font-size:13px;flex: 0 0 10%;max-width:10%;font-weight:bold;" >Place</div>
          <div style="text-align:center; font-size:13px;flex: 0 0 6%;max-width:6%;font-weight:bold;" >Picked Time</div>
          <div style="font-size:13px;flex: 0 0 11%;max-width:11%;font-weight:bold;" >Actions</div>
        </div>
        <?php foreach ($appointments as $appointment): ?>
          <div class="appointment-row" style="display:flex;align-items:center;margin:8px 0;padding:7px 0;border-top:solid 1px black;">
            <div style="font-size:11px;flex: 0 0 2%;max-width:2%" class="id">
              <?= $appointment->id; ?>
            </div>
            <div style="font-size:11px;flex: 0 0 5%;max-width:5%" class="status">
            <?php $status_color = ''; ?>
              <?php if ($appointment->status === 'incoming') {
                $status_color = '#000';
              } else if ($appointment->status === 'approved') {
                $status_color = '#43f901';
               } ?>
              <span style="display:block;text-align:center;max-width:85%;color:#fff;background-color:<?= $status_color; ?>">
                <?= $appointment->status; ?>
              </span>
            </div>
            <div style="font-size:11px;flex: 0 0 8%;max-width:8%" class="reg-date">
              <?= $appointment->submit_date; ?>
            </div>
            <div style="font-size:11px;flex: 0 0 7%;max-width:7%" class="name">
              <?= $appointment->name; ?>
            </div>
            <div style="overflow-y:auto;font-size:11px;flex: 0 0 9%;max-width:9%;margin-right:3px;" class="email">
              <?= $appointment->email; ?>
            </div>
            <div style="overflow-y:auto;max-height:200px;font-size:11px;flex: 0 0 10%;max-width:10%;text-align:center" class="reason">
              <?= $appointment->reason; ?>
            </div>
            <?php $main_email = $appointment->email ; ?>
            <div style="overflow-y:auto;max-height:200px;font-size:11px;flex: 0 0 5%;max-width:5%" class="zip">
              <?php $pre_zip = $wpdb->get_var("SELECT zip FROM {$wpdb->prefix}user_management_pre_registration_email WHERE email='$main_email'") ; ?>
              <?php if($pre_zip) : ?>
                <span>
                  <?php echo $pre_zip; ?>
                </span>
              <?php endif ; ?>
            </div>
            <div style="overflow-y:auto;max-height:200px;font-size:11px;flex: 0 0 5%;max-width:5%" class="address">
              <?= $appointment->address; ?>
            </div>
            <div style="font-size:11px;flex: 0 0 7%;max-width:7%" class="phone">
              <?= $appointment->phone; ?>
            </div>
            <div style="font-size:11px;flex: 0 0 7%;max-width:7%" class="uploaded_image_url">
              <a style="display:block; max-width:100%" target="blank" href="<?= $appointment->uploaded_image_url; ?>">
                <span style="display:block;max-width:93%;overflow-x:auto;overflow-y:hidden;">Open image</span>
              </a>
            </div>
            <div style="font-size:11px;flex: 0 0 7%;max-width:7%;max-height:200px;overflow-y:auto;" class="planted_image_url">
              <?php if($appointment->planted_image_url) : ?>
              <a style="display:block; max-width:100%" target="blank" href="<?= $appointment->planted_image_url; ?>">
                <span style="display:block;max-width:93%;overflow-x:auto;overflow-y:hidden;">Open image</span>
              </a>
              <?php else : ?>
                <span>No image yet</span>
              <?php endif ; ?>
            </div>
            <div style="font-size:11px;flex: 0 0 10%;max-width:10%" class="place">
            <?php if ( !empty($current_place) ) : ?>
              <?php foreach ($current_place as $place) : ?>
                <?php if ($appointment->place_id === $place['id']) : ?>
                  <span>
                    <?php echo $place['address'] ; ?>
                  </span>
                <?php endif ; ?>
              <?php endforeach ; ?>
            <?php endif ; ?>
            </div>
            <div style="text-align:center;font-size:11px;flex: 0 0 6%;max-width:6%" class="picked-time">
              <?= $appointment->picked_time; ?>
            </div>
            <div style="display:flex;font-size:11px;flex: 0 0 11%;max-width:11%" class="actions">

            <form style="margin-right: 20px" name="update-<?= $appointment->id?>" method="post" action="">
                <input name="approve-appointment" type="hidden" value="<?= $appointment->id?>">
                <input name="approve-mailfield" type="hidden" value="<?= $appointment->email; ?>">
                <input name="approve-time" type="hidden" value="<?= $appointment->picked_time; ?>">
                <input name="approve-place" type="hidden" value="<?= $appointment->place_id; ?>">
                <label>
                  <?php if($appointment->status === "incoming") : ?>
                    
                    <?php if($current_place[0]['max_number'] == 0) : ?>
                      <input disabled style="display:block;cursor:pointer;" type="submit" value="Approve">
                      <span style="color:red;font-size:15px;">No more trees</span>
                    <?php else: ?>
                  <input style="cursor:pointer;" type="submit" value="Approve">
                  <?php endif ;?>
                  <?php else : ?>
                    <span style="display:inline-block;color:green;font-size:15px;">Approved</span>
                  <?php endif; ?>
                </label>
              </form>
              <form name="delete-<?= $appointment->id?>" method="post" action="">
                <input name="delete-appointment" type="hidden" value="<?= $appointment->id?>">
                <input name="delete-mailfield" type="hidden" value="<?= $appointment->email; ?>">
                <input name="timeslot-id" type="hidden" value="<?= $appointment->timeslot; ?>">
                <input name="place-id" type="hidden" value="<?php echo $appointment->place_id; ?>">
                <label>
                <?php if($appointment->status === "incoming") { ?>
                  <input style="cursor:pointer;" type="submit" value="Reject">
                  <?php } ?>
                </label>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif;
  }
  
  // delete record
  if (isset($_POST['delete-appointment'])) {
    $wpdb->delete(
      $wpdb->prefix.'user_management_users',
      array( 
        'id' => $_POST['delete-appointment']
      ),
      array(
        'id' => $_POST['delete-appointment']
      )
    );
    $wpdb->delete(
      $wpdb->prefix.'user_management_pre_registration_email',
      array( 
        'email' => $_POST['delete-mailfield']
      )
    );
    $timeslot_id = $_POST['timeslot-id'];
    $place_id = $_POST['place-id'];
    $curr_tree_num = intval($wpdb->get_results( "SELECT tree_number FROM {$wpdb->prefix}user_management_timeslots WHERE id='$timeslot_id'", OBJECT )[0]->tree_number)+1;
    $curr_place_num = intval($wpdb->get_results( "SELECT max_number FROM {$wpdb->prefix}user_management_place WHERE id='$place_id'", OBJECT )[0]->max_number)+1;

    $wpdb->update(
      $wpdb->prefix.'user_management_timeslots',
      array( 
        'tree_number' => $curr_tree_num
      ),
      array(
        'id' => $timeslot_id
      )
    );
    $wpdb->update(
      $wpdb->prefix.'user_management_place',
      array( 
        'max_number' => $curr_place_num
      ),
      array(
        'id' => $place_id
      )
    );
    $headers[] = 'Reply-To: Klimabäume <noreply@klimabaeume.ruhr>';
    wp_mail(
      $_POST['delete-mailfield'],
'Absage Baumpatenschaft',
'Liebe/r Bewerber/in, 

leider können wir Dir keine Baumpatenschaft anbieten. 
Deine Bewerbung war entweder unvollständig, hat nicht alle Kriterien unserer 
Teilnahmebedingungen erfüllt oder alle Bäume sind bereits vergeben. 

Viele Grüße
Das Projektteam Klimabäume


Regionalverband Ruhr | | Kronprinzenstraße 35 | | Deutschland | 45128 Essen | +49 (0) 201 2069-0 | www.klimabaeume.ruhr |

Klimabäume
https://www.klimabaeume.ruhr
', $headers
    );
  }
  if (isset($_POST['approve-appointment'])) {
    $now_place = $_POST["approve-place"];
    $current_address = $wpdb->get_var( "SELECT address FROM {$wpdb->prefix}user_management_place WHERE id='$now_place'");

    $wpdb->update(
      $wpdb->prefix.'user_management_users',
      array( 
        'status' => 'approved'
      ),
      array(
        'id' => $_POST['approve-appointment']
      )
    );
   $final_time = $_POST['approve-time'];
   $final_place = $current_address;
   $headers[] = 'Reply-To: Klimabäume <noreply@klimabaeume.ruhr>';

    wp_mail(
      $_POST['approve-mailfield'],
'Zusage Baumpatenschaft',

'Liebe/r Bewerber/in,
herzlichen Glückwunsch - Du wirst Baumpate/in! Gemeinsam machen wir das Klima in der Metropole Ruhr besser und die Region grüner!

Bitte hole Deinen Baum am Samstag, 30. Oktober 2021 
an der von Dir gewählten Ausgabestelle: ' . $final_place . ' 
zu dem von Dir gewählten Zeitpunkt um ' . $final_time . ' Uhr ab. 

Zur Abholung ist es notwendig, dass Du diese Bestätigungs-Mail in digitaler oder gedruckter Version mitbringst. 

Wir freuen uns, dass Du dabei bist!

Viele Grüße
Dein Projektteam Klimabäume

Regionalverband Ruhr | | Kronprinzenstraße 35 | | Deutschland | 45128 Essen | +49 (0) 201 2069-0 | www.klimabaeume.ruhr |

Klimabäume
https://www.klimabaeume.ruhr

', $headers
    );
    return;
  }
?>
