<?php function timeslot_page_content() { ?>
<?php
  global $wpdb;
  $fromTime = $_POST['time-from'];
  $toTime = $_POST['time-to'];
  if (isset($fromTime) && isset($toTime)) {
      $wpdb->insert(
      $wpdb->prefix.'user_management_place',
      array(
        'place' => $_POST['place'],
        'max_number' => $_POST['max_tree'],
        'address' => $_POST['address']
      )
    );
    $lastid = $wpdb->insert_id;

    $startTime = strtotime($fromTime);
    $endTime = strtotime($toTime);
    if ($startTime <= $endTime) {
      do {
        $wpdb->insert(
          $wpdb->prefix.'user_management_timeslots',
          array(
            'place_id' => $lastid,
            'tree_number' => $_POST['tree_numbers'],
            'time' => date('H:i', $startTime)
          )
        );
        $startTime = strtotime('+3 minutes', $startTime);
      } while ($startTime <= $endTime);
    } else {
      echo '"Time from" cannot be less than "Time to" and place use valid time format!';
    }
  }
  if (isset($_POST['delete-places'])) {
    if ($wpdb->delete(
      $wpdb->prefix.'user_management_timeslots',
      array( 
        'place_id' => $_POST['delete-places']
      )
    )
  ) {
    $wpdb->delete(
      $wpdb->prefix.'user_management_place',
      array( 
        'id' => $_POST['delete-places']
      )
    );
  }
    

  }
  $times = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_timeslots", OBJECT ); 
  $places = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_management_place", ARRAY_A ); 
  ?>
  <div class="um-container timeslot">
    <div class="form-wrapper">
      <form action="" method="post">
        <label for="place">
          <span>Place</span>
          <input name="place" type="text">
        </label>
        <label for="address">
          <span>Address</span>
          <input name="address" type="text">
        </label>
        <label for="max_tree">
          <span>Maximun tree numbers per place</span>
          <input name="max_tree" type="number">
        </label>
        <label for="tree_numbers">
          <span>Tree numbers per time slot</span>
          <input name="tree_numbers" type="number">
        </label>
        <label for="time-from">
          <span>Time from</span>
          <input min="1" max="500" name="time-from" type="time">
        </label>
        <label for="time-to">
          <span>Time to</span>
          <input name="time-to" type="time">
        </label>
        <input disabled type="submit" value="Update">
      </form>
    </div>
    <?php if ($times): ?>
      <div class="info-wrapper">
        <div class="list-place-wrap">
          <form action="" method="post">
            <h3>List all timeslots</h3>
            <select name="place-list" id="">
              <?php foreach ($places as $t): ?>
                <option <?= ($_POST['place-list'] === $t['id']) ? 'selected' : '';?> value="<?= $t['id']; ?>">
                  <?= $t['place']; ?>
                </option>
              <?php endforeach; ?>
              </select>
            <input type="submit" value="List">
          </form>
        </div>
        <div class="result-place-wrap">
          <?php if (isset($_POST['place-list'])): ?>
            <?php
              $place_id = $_POST['place-list'];
              $place_address = $wpdb->get_var( "SELECT address FROM {$wpdb->prefix}user_management_place WHERE id='$place_id'");
              $place_max_number = $wpdb->get_var( "SELECT max_number FROM {$wpdb->prefix}user_management_place WHERE id='$place_id'"); 
            ?>
            <h3>Timeslots for "<?= $place_id . ' Address: ' .  $place_address . ' Max number: ' . $place_max_number ?>"</h3>
            <div class="place-wrap">
              <?php foreach ($times as $t): ?>
                <?php if ($t->place_id === $_POST['place-list']): ?>
                  <div class="time">
                    <?= $t->time . ' (' . $t->tree_number . ')' ?>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
            <form style="margin-top: 30px;" action="" method="post">
              <input type="hidden" name="delete-places" value="<?= $_POST['place-list']; ?>">
              <input disabled type="submit" value="Delete all from '<?= $_POST['place-list']; ?>' ">
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php } ?>
