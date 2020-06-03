<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

    if(isset($_POST['postvarsboxid']) && isset($_POST['postvarslocation'])){
    
        /* Add the placeholders to the variables */
        $box_id = $_POST['postvarsboxid'];
        $location = $_POST['postvarslocation'];  
        $stagingarea_id = "";
        $scanning_id = "";
        $aisle = "";
        $bay = "";  
        $shelf = "";  
        $position = "";  
        $digitization_center = "";
        $dyn_insert[] = "";
    
        /* Identify the table */
        $table_name = 'wpqa_wpsc_epa_scan_list';
        
        /* Update each box id with each location in the array */
        foreach($box_id as $val_box_id){
            
            foreach($location as $val_loc){
            
            /* Use switch statement and pregmatch regex to sort the values in the location into
               the appropriate table columns while dynamically building the statement*/
                switch($val_loc) {
                    
                    case preg_match("/\b(SCN-\d\d-e|SCN-\d\d-w)\b/i", $val_loc, $matches):
                      $scanning_id = $matches;
                      $dyn_insert[] = " `scanning_id` => '".mysql_real_escape_string($scanning_id)."'";
                      continue;
                    case preg_match("/\b(sa-e|sa-w)\b/i", $val_loc, $matches):
                      $stagingarea_id = $matches;
                      $dyn_insert[] = " `stagingarea_id` => '".mysql_real_escape_string($stagingarea_id)."'";
                      continue;
                    case preg_match("/(\d{1,3}a\z)/i", $val_loc, $matches):
                       $aisle = $matches;
                       $dyn_insert[] = " `aisle` => '".mysql_real_escape_string($aisle)."'";
                      continue;
                    case preg_match("/(\d{1,3}b\z)/i", $val_loc, $matches):
                      $bay = $matches;
                      $dyn_insert[] = " `bay` => '".mysql_real_escape_string($bay)."'";
                      continue;
                    case preg_match("/(\d{1,3}s\z)/i", $val_loc, $matches):
                      $shelf = $matches;
                      $dyn_insert[] = " `shelf` => '".mysql_real_escape_string($shelf)."'";
                      continue;
                    case preg_match("/(\d{1,3}p\z)/i", $val_loc, $matches):
                      $position = $matches;
                      $dyn_insert[] = " `position` => '".mysql_real_escape_string($position)."'";
                      continue;  
                    case preg_match("/(\d{1,3}p\z)/i", $val_loc, $matches):
                      $digitizationCenter = $matches;
                      $dyn_insert[] = " `digitization_center` => '".mysql_real_escape_string($digitization_center)."'";
                      continue;  
                    default:
                       continue;
                      
                }
            
                /* In the update() method, construct the UPDATE statement with named placeholders */
                $data_update = array(implode(", ", $dyn_insert));
                $data_where = array('box_id' => $val_box_id);
        
            
                /* Use a prepared statement to prepare the UPDATE statement for the execution and execute it with an array argument */
                $wpdb->update($table_name , $data_update, $data_where);
            }
        }
        
        /* Debug 
        print_r($box_id);
        print_r($location);*/
     
    } else {
        
        echo "Either the BoxID(s) are missing or there are no Location Scan(s) to submit.  Please try again.";
       
    }
    
?>