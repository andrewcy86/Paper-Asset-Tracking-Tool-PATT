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

$get_ticket_id = $wpdb->get_row("
SELECT ticket_id
FROM wpqa_wpsc_epa_boxinfo
WHERE
box_id = '" . $pattboxid . "'
");

$ticket_id = $get_ticket_id->ticket_id;

   $metadata_array = array();

$table_name = 'wpqa_wpsc_epa_boxinfo';

//get exact match of program office acronym and take the id from that, insert id into box table
if(!empty($po)) {
$get_old_acronym = $wpdb->get_row("SELECT b.office_acronym as office_acronym FROM wpqa_wpsc_epa_boxinfo a LEFT JOIN wpqa_wpsc_epa_program_office b ON a.program_office_id = b.office_code WHERE a.box_id = '" . $pattboxid . "'");
$po_old_acronym = $get_old_acronym->office_acronym;

//update box table with program office foreign key
$data_update = array('program_office_id' => $po);
$data_where = array('id' => $box_id);
$wpdb->update($table_name , $data_update, $data_where);

$get_new_acronym = $wpdb->get_row("SELECT office_acronym FROM wpqa_wpsc_epa_program_office WHERE office_code = '" . $po . "'");
$po_new_acronym = $get_new_acronym->office_acronym;
array_push($metadata_array,'Program Office: '.$po_old_acronym.' > '.$po_new_acronym);
}

//get exact match of record_schedule_number and take the id from that, insert id into box table
if(!empty($rs)) {
$get_old_rs = $wpdb->get_row("SELECT b.Record_Schedule_Number as Record_Schedule_Number FROM wpqa_wpsc_epa_boxinfo a LEFT JOIN wpqa_epa_record_schedule b ON a.record_schedule_id = b.id WHERE a.box_id = '" . $pattboxid . "'");
$rs_old_num = $get_old_rs->Record_Schedule_Number;

//update box table with record schedule foreign key
$data_update = array('record_schedule_id' => $rs);
$data_where = array('id' => $box_id);
$wpdb->update($table_name , $data_update, $data_where);

$get_new_rs = $wpdb->get_row("SELECT Record_Schedule_Number FROM wpqa_epa_record_schedule WHERE id = '" . $rs . "'");
$rs_new_num = $get_new_rs->Record_Schedule_Number;
array_push($metadata_array,'Record Schedule: '.$rs_old_num.' > '.$rs_new_num);
}

$metadata = implode (", ", $metadata_array);

do_action('wpppatt_after_box_metadata', $ticket_id, $metadata, $pattboxid);
 //echo 'Program office: ' . $po;
 //echo 'Record schedule: ' . $rs;
 echo "Box ID #: " . $pattboxid . " has been updated.";
 
} else {
   echo "Please make an edit.";
}
?>
