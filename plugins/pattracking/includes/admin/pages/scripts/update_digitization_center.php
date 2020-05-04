<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvarsboxidname'])){
   $box_id = $_POST['postvarsboxidname'];

   $box_details = $wpdb->get_row(
"SELECT wpqa_wpsc_epa_boxinfo.storage_location_id as storage_location_id, wpqa_wpsc_epa_boxinfo.id as id, wpqa_wpsc_epa_boxinfo.box_id as box_id, wpqa_wpsc_epa_storage_location.digitization_center as digitization_center
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
WHERE wpqa_wpsc_epa_boxinfo.id = '" . $box_id . "'"
			);
			

			$box_storage_location_id = $box_details->storage_location_id;
			$box_storage_digitization_center = $box_details->digitization_center;
			$box_id_val = $box_details->box_id;
			
			$box_storage_digitization_center_val = '';

            if ($box_storage_digitization_center == 'West') {
            $box_storage_digitization_center_val = 'East';
            } else if ($box_storage_digitization_center == 'East') {
            $box_storage_digitization_center_val = 'West';
            }
            
$table_name = 'wpqa_wpsc_epa_storage_location';


$data_update = array('digitization_center' => $box_storage_digitization_center_val, 'aisle' => '0' ,'bay'=>'0','shelf'=>'0','position'=>'0');
$data_where = array('id' => $box_storage_location_id);
$wpdb->update($table_name , $data_update, $data_where);

   echo "Box ID #: " . $box_id_val . " has been updated.\nAssigned Digitization Center: " .$box_storage_digitization_center_val;
} else {
   echo "Update not successful.";
}
?>
