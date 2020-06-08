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
//get program_office_code from exact match of office_acronym
//$get_oc = $wpdb->get_row("SELECT office_code FROM wpqa_wpsc_epa_program_office WHERE office_acronym = '" . $po . "'");

//update box table with program office foreign key
$data_update = array('program_office_id' => $po);
$data_where = array('id' => $box_id);
$wpdb->update($table_name , $data_update, $data_where);
}

//get exact match of record_schedule_number and take the id from that, insert id into box table
if(!empty($rs)) {
//$get_rs_id = $wpdb->get_row("SELECT id FROM wpqa_epa_record_schedule WHERE Record_Schedule_Number = '" . $rs . "'");

//update box table with record schedule foreign key
$data_update = array('record_schedule_id' => $rs);
$data_where = array('id' => $box_id);
$wpdb->update($table_name , $data_update, $data_where);
}

 //echo 'Program office: ' . $po;
 //echo 'Record schedule: ' . $rs;
 echo "Box ID #: " . $pattboxid . " has been updated.";
 
} else {
   echo "Please make an edit.";
}
?>
