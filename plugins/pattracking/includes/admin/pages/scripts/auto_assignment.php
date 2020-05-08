<?php
global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/', '', __DIR__);
include ($path . 'wp-load.php');
include ($path . 'wp-content/plugins/pattracking/includes/class-wppatt-custom-function.php');

if (isset($_POST['postvartktid']) && isset($_POST['postvardcname']) && $_POST['postvardcname'] != '620') {
	$tkid = $_POST['postvartktid'];
	$dc = $_POST['postvardcname'];

    //Convert $dc Place in where clause of each select statement
	$dc_final = '';

	if ($dc == 62) {
		$dc_final = 'East';
	} else if ($dc == 2) {
		$dc_final = 'West';
	}
    
// Finds Shelf ID of next available sequence
	$find_sequence = $wpdb->get_row(
		"
WITH 
cte1 AS
(
SELECT id, 
       CASE WHEN     occupied  = LAG(occupied) OVER (ORDER BY id)
                 AND remaining = LAG(remaining) OVER (ORDER BY id)
            THEN 0
            ELSE 1 
            END values_differs
FROM wpqa_wpsc_epa_storage_status
WHERE digitization_center = '" . $dc_final . "'
),
cte2 AS 
(
SELECT id,
       SUM(values_differs) OVER (ORDER BY id) group_num
FROM cte1
ORDER BY id
)
SELECT MIN(id) as id
FROM cte2
GROUP BY group_num
ORDER BY COUNT(*) DESC LIMIT 1;
");

	$sequence_shelfid = $find_sequence->id;

	$ticket_details = $wpdb->get_row("
SELECT ticket_status 
FROM wpqa_wpsc_ticket 
WHERE
id = '" . $tkid . "'
");

	$ticket_details_status = $ticket_details->ticket_status;

        //Select count on boxes that have not been auto assigned
        // Get boxes associated to passed ticket id.
        // Check to see if auto assignments have been made.
        // Is the value NOT blank for digitization center and are there NO 0s present for the boxes assigned to the ticket in wpqa_wpsc_epa_storage_location?
        // If auto assignments need to be made, Count total number of boxes
		$box_details = $wpdb->get_row("
SELECT wpqa_wpsc_epa_boxinfo.id, count(wpqa_wpsc_epa_boxinfo.id) as count 
FROM wpqa_wpsc_epa_boxinfo 
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id 
WHERE
wpqa_wpsc_epa_storage_location.digitization_center IS NOT NULL AND
wpqa_wpsc_epa_storage_location.aisle = 0 AND 
wpqa_wpsc_epa_storage_location.bay = 0 AND 
wpqa_wpsc_epa_storage_location.shelf = 0 AND 
wpqa_wpsc_epa_storage_location.position = 0 AND
wpqa_wpsc_epa_boxinfo.ticket_id = '" . $tkid . "'
");

		$box_details_id = $box_details->id;
		$box_details_count = $box_details->count;

    // Check to see if tkid is a comma delimited list.
	$ticketid_array = explode(',', $tkid);

	if (sizeof($ticketid_array) > 1) {
        // If comma delimited list, parse out into array and run a for-each loop
		echo 'Multiple Tickets selected';
		print_r($ticketid_array);
	} else {
        // Is the box count <= 3?:: Continuous shelf space not requried. Find first available gap.
		if ($box_details_count <= 3) {

            // Find first available slot for requests with boxes under 3
			$shelf_not_continuous = $wpdb->get_row("
SELECT shelf_id, min(remaining) as remaining
FROM wpqa_wpsc_epa_storage_status
WHERE occupied = 1 AND
remaining <> 0 AND
remaining = '" . $box_details_count . "' AND
digitization_center = '" . $dc_final . "'
GROUP BY shelf_id
ORDER BY id asc
limit 1
");

			$not_continuous_shelf_id = $shelf_not_continuous->shelf_id;

			echo $not_continuous_shelf_id . ' ';

            // When Continuing shelf space space is required

		} else if ($box_details_count <= Patt_Custom_Func::calc_max_gap_val($dc_final)) {
			$find_gaps = $wpdb->get_results("
WITH 
cte1 AS
(
SELECT shelf_id, remaining, SUM(remaining = 0) OVER (ORDER BY id) group_num
FROM wpqa_wpsc_epa_storage_status
WHERE digitization_center = '" . $dc_final . "' AND
id BETWEEN 1 AND '" . $sequence_shelfid . "'
)
SELECT GROUP_CONCAT(shelf_id) as shelf_id,
       GROUP_CONCAT(remaining) as remaining,
       SUM(remaining) as total
FROM cte1
WHERE remaining != 0
GROUP BY group_num
");

			$findgaps_array = array();

			$counter = 0;

			foreach ($find_gaps as $info) {
				$findgaps_shelfid = $info->shelf_id;
				$findgaps_remaining = $info->remaining;
				$findgaps_total = $info->total;
				array_push($findgaps_array, $findgaps_shelfid);
				if ($box_details_count <= $findgaps_total) {
					break;
				}
				$counter++;
			}

			$shelfid_gaps_array = explode(",", $findgaps_array[$counter]);

			//print_r($shelfid_gaps_array);
		    $missing_gap_array = array();
		    $position_gap_array = array();		    
			foreach ($shelfid_gaps_array as &$value) {

// Explode into variables
				[$aisle, $bay, $shelf] = explode("_", $value);

// Get all positions in an array to determine available positions
				$position_gap_details = $wpdb->get_results("
SELECT position FROM wpqa_wpsc_epa_storage_location 
WHERE aisle = '" . $aisle . "' 
AND bay = '" . $bay . "' 
AND shelf = '" . $shelf . "' 
AND digitization_center = '" . $dc_final . "'
");
	
				foreach ($position_gap_details as $info) {
					$position_gap_position = $info->position;
					array_push($position_gap_array, $position_gap_position);
				}
                // Determine missing positions and push to an array         
				$missing = array_diff(range(1, 4), $position_gap_array);
				//print_r($missing);
				
				foreach ($missing as &$missing_val) {
				    $shelf_position_id_val = $value.'_'.$missing_val;
				array_push($missing_gap_array, $shelf_position_id_val);
				}
				

            
			}
			
$gap_aisle_bay_shelf_position = array_slice($missing_gap_array, 0, $box_details_count);
				
$gap_box_id_assignment = Patt_Custom_Func::get_unassigned_boxes($tkid);

//print_r($gap_aisle_bay_shelf_position);
//print_r($gap_box_id_assignment);

foreach($gap_aisle_bay_shelf_position as $key => $value){
    
[$gap_aisle, $gap_bay, $gap_shelf, $gap_position] = explode("_", $value);

$table_name = 'wpqa_wpsc_epa_storage_location';
$data_update = array('aisle' => $gap_aisle ,'bay'=>$gap_bay,'shelf'=>$gap_shelf,'position'=>$gap_position,'digitization_center'=>$dc_final);
$data_where = array('id' => $gap_box_id_assignment[$key]);

$wpdb->update($table_name , $data_update, $data_where);

}


			
		} else {
            // Calculate Upper Limit
			$sequence_upperlimit = $sequence_shelfid + ceil($box_details_count / 4) - 1;

			$find_sequence_details = $wpdb->get_results("
SELECT shelf_id FROM wpqa_wpsc_epa_storage_status 
WHERE ID BETWEEN '" . $sequence_shelfid . "' AND '" . $sequence_upperlimit . "' AND
digitization_center = '" . $dc_final . "'
");

			$sequence_array = array();

			foreach ($find_sequence_details as $info) {
				$find_sequence_shelfid = $info->shelf_id;
				array_push($sequence_array, $find_sequence_shelfid);
			}
			print_r($sequence_array);
		}

		if ($ticket_details_status == 3 && $box_details_count > 0) {
			echo 'Single Ticket selected, # of unassigned boxes = ' . $box_details_count . ' ';
			echo "Ticket ID #: " . $tkid . " Digitization Center: " . $dc_final . " Status: " . $ticket_details_status;
		}
	}

    // Apply inherited digitization center to wpqa_wpsc_epa_storage_location via update statement
    // REMOVE location dropdown from the change request status popup when the ticket is > New status.
    // Determine if boxes will be placed in the first available gap that is large enought to accomodate the request
    // OR if boxes need to be placed in the next sequence of consecutive empty shelfs using wpqa_wpsc_epa_storage_status
    // Order by Last number of shelf ID (1_1_1<<), Filter by Occupied = 1, Check remaining column and go to the first availble non 0 number, count number up to when the reamining becomes 0
    // This becomes gap (4). IF Count total anumber of boxes =< 4 then make assignment to that Aisle, Bay, Shelf and sequentially assign Position
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

    //When destory is selected as status need to delete associated entry in the wpqa_wpsc_epa_storage_location table
    //Need to update wpqa_wpsc_epa_storage_status table and make shelf available again
    //$table_name = 'wpqa_wpsc_epa_storage_location';
    //$data_update = array('aisle' => $aisle ,'bay'=>$bay,'shelf'=>$shelf,'position'=>$position);
    //$data_where = array('id' => $box_storage_location_id);
    //$wpdb->update($table_name , $data_update, $data_where);

} else {
	echo "No automatic assignments made.";
}
?>
