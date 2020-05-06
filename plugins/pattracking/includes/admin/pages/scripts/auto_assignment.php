<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvartktid']) && isset($_POST['postvardcname']) && $_POST['postvardcname'] != '620'){
   $tkid = $_POST['postvartktid'];
   $dc = $_POST['postvardcname'];

//Conver $dc

$ticket_details = $wpdb->get_row(
"SELECT ticket_status 
FROM wpqa_wpsc_ticket 
WHERE
id = '" . $tkid . "'");

$ticket_details_status = $ticket_details->ticket_status;

  
// Check to see if tkid is a comma delimited list.

$ticketid_array = explode(',', $tkid);

if (sizeof($ticketid_array) > 1) {
// If comma delimited list, parse out into array and run a for-each loop
  echo 'Multiple Tickets selected';
  print_r($ticketid_array);
  
} else {

  //Select count on boxes that have not been auto assigned
  // Get boxes associated to passed ticket id.
// Check to see if auto assignments have been made.
// Is the value NOT blank for digitization center and are there NO 0s present for the boxes assigned to the ticket in wpqa_wpsc_epa_storage_location?
// If auto assignments need to be made, Count total number of boxes
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

// Add DC to query
$find_gaps = $wpdb->get_results("SELECT SUBSTRING_INDEX(shelf_id, '_', 2) as aisle_bay, GROUP_CONCAT(SUBSTRING_INDEX(shelf_id, '_', -1)) as shelf, GROUP_CONCAT(remaining) as remaining FROM wpqa_wpsc_epa_storage_status WHERE occupied = 1 AND remaining BETWEEN 1 AND 3 GROUP BY aisle_bay ORDER BY id ASC");

            foreach ($find_gaps as $info) {
				$findgaps_id = $info->aisle_bay;
				$findgaps_location = $info->shelf;
				$findgaps_bay = $info->remaining;
				
				echo $findgaps_id.':::';
				echo $findgaps_location.':::';
				echo $findgaps_bay.':::......';
				}
				
$sarray = array(1,2,5);
$position_array = array();
for ($c = 0; $c < count($sarray); $c++){
    $next_arr_pos = $c+1;

    if ($c == (count($sarray) -1)){
        $cons_check = $sarray[$c];
    }
    else
    {
        $cons_check = $sarray[$next_arr_pos];
        $gap_calc = $cons_check-$sarray[$c];
    }

    if ($sarray[$c] < $cons_check && $gap_calc == 1){

        echo $sarray[$c].' Is consecutive with '.$cons_check.' <br />';
        echo 'position '.array_search($sarray[$c], $sarray). ' position '.array_search($cons_check, $sarray).'<br/>';
        array_push($position_array, array_search($sarray[$c], $sarray));
        array_push($position_array, array_search($cons_check, $sarray));
    }
}

$location_of_unique_array = array_unique($position_array);
print_r($location_of_unique_array);

$rarray = array(3,1,2);

$sum = 0;
foreach ($location_of_unique_array as &$value) {
    $sum+= $rarray[$value];
}
echo 'Boxes Available in Gap: ' .$sum.'<br />';


if($ticket_details_status == 3 && $box_details_count > 0) {
   echo 'Single Ticket selected, # of unassigned boxes = ' . $box_details_count . ' ';
   echo "Ticket ID #: " . $tkid . " Digitization Center: " .$dc. " Status: ".$ticket_details_status;
}

}

// Apply inherited digitization center to wpqa_wpsc_epa_storage_location via update statement
// REMOVE location dropdown from the change request status popup when the ticket is > New status.
// Determine if boxes will be placed in the first available gap that is large enought to accomodate the request
// OR if boxes need to be placed in the next sequence of consecutive empty shelfs using wpqa_wpsc_epa_storage_status
// Order by Last number of shelf ID (1_1_1<<), Filter by Occupied = 1, Check remaining column and go to the first availble non 0 number, count number up to when the reamining becomes 0
// This becomes gap (4). IF Count total number of boxes =< 4 then make assignment to that Aisle, Bay, Shelf and sequentially assign Position
// If a bigger gap is needed, continue to identify next gap.
// If no gaps are available then we need to find the next sequence of consecutive empty shelfs.
// To find the next sequence, Identify where there are 2 consective shelfs with a occupied of 0 and a remaining of 4
// Update wpqa_wpsc_epa_storage_location
// Update wpqa_wpsc_epa_storage_status after assignment

// Only look for consecutive position locations


// Update location_update.php to update wpqa_wpsc_epa_storage_status
// IF unassigned, 1 update to the wpqa_wpsc_epa_storage_status is needed
// IF assigned, 2 updates to the wpqa_wpsc_epa_storage_status is needed

/*IF REQUEST STATUS IS NEW AND REQUEST PRIORITY IS NOT ASSIGNED
THEN 
DISPLAY REQUEST LOCATION

REMOVE LOCATION FROM WIDGET

Introduced Multiple to Location column on request dashboard
*/



//$table_name = 'wpqa_wpsc_epa_storage_location';

//$data_update = array('aisle' => $aisle ,'bay'=>$bay,'shelf'=>$shelf,'position'=>$position);
//$data_where = array('id' => $box_storage_location_id);
//$wpdb->update($table_name , $data_update, $data_where);

} else {
   echo "No automatic assignments made.";
}
?>
