<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvartktid']) && isset($_POST['postvardcname']) && $_POST['postvardcname'] != '620'){
   $tkid = $_POST['postvartktid'];
   $dc = $_POST['postvardcname'];
   
// Check to see if tkid is a comma delimited list.

$ticketid_array = explode(',', $tkid);

if (sizeof($ticketid_array) > 1) {
// If comma delimited list, parse out into array and run a for-each loop
  echo 'Multiple Tickets selected';
  print_r($ticketid_array);
} else {
  //Select count on boxes that have not been auto assigned
  
  $box_details = $wpdb->get_row(
"SELECT count(wpqa_wpsc_epa_boxinfo.id) as count 
FROM wpqa_wpsc_epa_boxinfo 
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id 
WHERE
wpqa_wpsc_epa_storage_location.digitization_center IS NOT NULL AND
wpqa_wpsc_epa_storage_location.aisle = 0 AND 
wpqa_wpsc_epa_storage_location.bay = 0 AND 
wpqa_wpsc_epa_storage_location.shelf = 0 AND 
wpqa_wpsc_epa_storage_location.position = 0 AND
wpqa_wpsc_epa_boxinfo.ticket_id = '" . $tkid . "'"
			);

	$box_details_count = $box_details->count;
  echo 'Single Ticket selected, # of unassigned boxes = ' . $box_details_count . ' ';
}

// Get boxes associated to passed ticket id.
// Check to see if auto assignments have been made.
// Is the value NOT blank for digitization center and are there NO 0s present for the boxes assigned to the ticket in wpqa_wpsc_epa_storage_location?
// If auto assignments need to be made, Count total number of boxes
// Apply inherited digitization center to wpqa_wpsc_epa_storage_location via update statement
// Determine if boxes will be placed in the first gap OR if boxes need to be placed in the next sequence using wpqa_wpsc_epa_storage_status
// Update wpqa_wpsc_epa_storage_location
// Update wpqa_wpsc_epa_storage_status after assignment

// Update location_update.php to update wpqa_wpsc_epa_storage_status


//$table_name = 'wpqa_wpsc_epa_storage_location';

//$data_update = array('aisle' => $aisle ,'bay'=>$bay,'shelf'=>$shelf,'position'=>$position);
//$data_where = array('id' => $box_storage_location_id);
//$wpdb->update($table_name , $data_update, $data_where);
   echo "Ticket ID #: " . $tkid . " Digitization Center: " .$dc;
} else {
   echo "No automatic assignments made.";
}
?>
