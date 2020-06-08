<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarspo']) ||
!empty($_POST['postvarsrs'])
){
   //id in box table (e.g. 1)
   $box_id = $_POST['postvarsboxid'];
   //box_id in box table (e.g. 0000001-1)
   $pattboxid = $_POST['postvarspattboxid'];
   $po = $_POST['postvarspo'];
   $rs = $_POST['postvarsrs'];

$table_name = 'wpqa_wpsc_epa_boxinfo';

//get exact match of program office acronym and take the id from that, insert id into box table
if(!empty($po)) {
//update box table with program office foreign key
$data_update = array('program_office_id' => $po);
$data_where = array('id' => $box_id);
$wpdb->update($table_name , $data_update, $data_where);
}

//get exact match of record_schedule_number and take the id from that, insert id into box table
if(!empty($rs)) {
//update box table with record schedule foreign key
$data_update = array('record_schedule_id' => $rs);
$data_where = array('id' => $box_id);
$wpdb->update($table_name , $data_update, $data_where);
}
 
} else {
   echo "Please make an edit.";
}
?>
