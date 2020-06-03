<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarspo']) ||
!empty($_POST['postvarsrs'])
){
   $box_id = $_POST['postvarsboxid'];
   $pattboxid = $_POST['postvarspattboxid'];
   $po = $_POST['postvarspo'];
   $rs = $_POST['postvarsrs'];

$table_name = 'wpqa_wpsc_epa_boxinfo';

if(!empty($po)) {
$data_update = array('program_office_id' => $po);
$data_where = array('id' => $box_id);
$wpdb->update($table_name , $data_update, $data_where);
}


 echo "Box ID #: " . $pattboxid . " has been updated.";
 
} else {
   echo "Please make an edit.";
}
?>