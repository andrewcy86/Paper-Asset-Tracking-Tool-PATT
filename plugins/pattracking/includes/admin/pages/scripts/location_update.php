<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvarspname']) && isset($_POST['postvaraname']) && isset($_POST['postvarbname']) && isset($_POST['postvarboxname']) && isset($_POST['postvarcentername'])){
   $shelf_position = $_POST['postvarspname'];
 
   $array = explode('_', $shelf_position);
   $shelf = $array[0];
   $position = $array[1];

   $aisle = $_POST['postvaraname'];
   $bay = $_POST['postvarbname'];
   $boxid = $_POST['postvarboxname'];
   $center = $_POST['postvarcentername'];
   
   $center_value = '';
   
   if ($center = 'East') {
     $center_value = 'E';
   } else if ($center = 'West'){
     $center_value = 'W';       
   }


			$box_details = $wpdb->get_row(
"SELECT storage_location_id
FROM wpqa_wpsc_epa_boxinfo
WHERE box_id = '" . $boxid . "'"
			);

			$box_storage_location_id = $box_details->storage_location_id;
			
$table_name = 'wpqa_wpsc_epa_storage_location';

$data_update = array('aisle' => $aisle ,'bay'=>$bay,'shelf'=>$shelf,'position'=>$position);
$data_where = array('id' => $box_storage_location_id);
$wpdb->update($table_name , $data_update, $data_where);

   echo "Box ID #: " . $boxid . " has been updated. New Location: " .$aisle. "A_" .$bay . "B_" . $shelf ."S_".$position."P_".$center_value;
} else {
   echo "Update not successful.";
}
?>
