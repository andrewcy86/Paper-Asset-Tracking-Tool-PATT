<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');
/*
Observations
1) Need to update the wpqa_wpsc_epa_storage_location and wpqa_wpsc_epa_storage_status tables when setting the box shelf location.
Shelf location and digitization center can only be stored in wpqa_wpsc_epa_scan_list as part of a scan log.
2) Need to add an entry in the audit log (requires inserting a do action into this script)
3) Scanning.php does not correctly clear out past locations OR past box IDs that have been entered into the text field. 
There must be a one to one match with box and location scanned. This causes errors.
4) You cannot use mysql_real_escape_string. esc_sql (specific to WP) should be used instead.
5) Parsing shelf location information should occur within this script.
6) You should not loop through each box ID and initiate a new post request to this script. 
Both Box ID and Location information should be passed as arrays directly to this script. 
array_combine then merges both arrays into a single key->value pair array.
*/
    if(isset($_POST['postvarsboxid']) && isset($_POST['postvarslocation'])){
        

        /* Add the placeholders to the variables */
        $box_id = $_POST['postvarsboxid'];
        $location = $_POST['postvarslocation']; 
        
// the variables
$array_location = array($location);
$count = count($box_id);
$newArray_location = array();
// create a new array with AT LEAST the desired number of elements by joining the array at the end of the new array
while(count($newArray_location) <= $count){
    $newArray_location = array_merge($newArray_location, $array_location);
}
// reduce the new array to the desired length (as there might be too many elements in the new array)
$array_location = array_slice($newArray_location, 0, $count);

//print_r($array_location);
  
        $box_insert = array_combine($box_id, $array_location);
 
        /* Identify the table */
        $table_name = 'wpqa_wpsc_epa_scan_list';
        
        //Debug: TESTING
        //print_r($box_id);
        //print_r($location);
        //print_r($box_insert);
        
        $column_name = '';
        $error_flag = 0;
        $date = date('Y-m-d H:i:s');
        
        foreach ($box_insert as $key => $value) {
            
            if(preg_match('/\b(SCN-\d\d-e|SCN-\d\d-w)\b/i', $value)) {
                $column_name = 'scanning_id';
            }
            
            if(preg_match('/^\b(sa-e|sa-w)\b$/i', $value)) {
                
                $column_name = 'stagingarea_id';
            }
            
            if(preg_match('/(\bcid-\d\d-e\b|\bcid-\d\d-w\b)|(\bcid-\d\d-east\scui\b|\bcid-\d\d-west\scui\b)|(\bcid-\d\d-east\b|\bcid-\d\d-west\b)|(\bcid-\d\d-eastcui\b|\bcid-\d\d-westcui\b)/gim', $value)) {
                
                $column_name = 'cart_id';
            } 
            
            if(preg_match('/^\d{1,3}A_\d{1,3}B_\d{1,3}S_\d{1,3}P_(E|W|ECUI|WCUI)$/i', $value)) {
                $column_name = 'shelf_location';
            }
            
            
            /* Update the scanning table, scan_list. 
               Confirm the update and prep the message response for auditing. */

            if ($column_name != '') {
                $wpdb->insert($table_name, array(
                    'box_id' => esc_sql($key),
                    $column_name => esc_sql(strtoupper($value)),
                    'date_modified' => $date,
                ));
                
                $error_flag = 1;
            } else {
                
                $error_flag = 0;
            }
        }
        
        /* JM - 6/19/2020 - Must use the boxID to get the $ticket_id (Request ID)
                before adding an shelf location to wpqa_wpsc_epa_storage_location and wpqa_wpsc_epa_storage_status*/
        if($column_name == 'shelf_location'){
            
            /* JM - 6/19/2020 - Added code to update the location tables */
 
            /*  TODO:
            
            1.  The requestID is the padded version of the $ticket_id, must convert when obtaining the $ticket_id/databaseID 
                        
            2.     Andrew TODO:
            
            The term value must be inserted into the DB for the digitization center ($cneter_value) ex. 666 = not assigned 
            Create lookup (where) on terms table to determine if e = east    
            
            3.  Update wpqa_wpsc_epa_storage_location with new location that was selected by posting the following variables
                to location update.php instead of recreating the functionality here:
             */

                       $position_array = explode('_', $location);

                       $aisle = substr($position_array[0], 0, -1);
                       $bay = substr($position_array[1], 0, -1);
                       $shelf = substr($position_array[2], 0, -1);
                       $position = substr($position_array[3], 0, -1);
                       $dc = $position_array[4];
                       $center_term_id = term_exists($dc);
                       
                       //echo $aisle;
                       //echo $bay;
                       //echo $shelf;
                       //echo $position;
                       //echo $center_term_id;
                       
        
                $table_name = 'wpqa_wpsc_epa_storage_location';
                $data_update = array('aisle' => $aisle ,'bay'=>$bay,'shelf'=>$shelf,'position'=>$position, 'digitization_center'=>$center_term_id);
                $data_where = array('id' => $box_id);
                //$wpdb->update($table_name , $data_update, $data_where);
                
                /* Get the ticket ID aka Request ID */
                $get_ticket_id = $wpdb->get_row("
                                                    SELECT ticket_id
                                                    FROM wpqa_wpsc_epa_boxinfo
                                                    WHERE
                                                    box_id = '" . $boxid . "'
                                                ");
                
                $ticket_id = $get_ticket_id->ticket_id;
                
                /* Prepare the confirmation message */				
                $shelf_info = $aisle. 'A_' .$bay . 'B_' . $shelf .'S_'.$position.'P_'.$dc;
                $shelf_meta = "Box ID #: " . $box_id . " has been updated. New Scanned Location: " .$shelf_info;
                $error_flag = 1;
            }else{
                $shelf_meta = "The scanned shelf location update was not successful.";
                $error_flag = 0;
            }
        }
        
        if ($error_flag == 1) {
            
            echo 'The new scanned location(s) have been succesfully updated. ';   
             
             /* JM - 6/18/2020 - Determine which message to issue after update by referencing the 
             appropriate hook using IF structure  
             
             Must use the boxID to get the $ticket_id (Request ID) before adding an Audit log entry*/
            if($column_name == 'stagingarea_id'){

                $shelf_meta = "Box ID #: " . $box_id . " has been updated. New Scanned Staging Area: " .$stagingarea_id;
                do_action('wpppatt_after_shelf_location', $ticket_id, $box_id, $shelf_meta);  
            }
             
            if($column_name == 'cart_id'){

                $shelf_meta = "Box ID #: " . $box_id . " has been updated. New Scanned Cart ID: " .$stagingarea_id;
                do_action('wpppatt_after_shelf_location', $ticket_id, $box_id, $shelf_meta);  
            }
        }else{
            
            $shelf_meta = "Box ID #: " . $box_id . " was NOT updated. There was an issue updating box location.";
            do_action('wpppatt_after_shelf_location', $ticket_id, $box_id, $shelf_meta); 
            echo "There was an issue updating box location.";  
        }
    {
        
        echo "Either the BoxID(s) are missing or there are no Location Scan(s) to submit.  Please try again.";
       
    }
    
?>
