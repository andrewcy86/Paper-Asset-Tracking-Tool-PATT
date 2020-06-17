<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsboxid'])
){


$boxid_string = $_POST['postvarsboxid'];
$boxid_arr = explode (",", $folderdocid_string);  
$page_id = $_POST['postvarpage'];

$table_name = 'wpqa_wpsc_epa_boxinfo';

$destruction_reversal = 0;

foreach($boxid_arr as $key) {    
$get_destruction = $wpdb->get_row("SELECT box_destroyed FROM wpqa_wpsc_epa_boxinfo WHERE box_id = '".$key."'");
$get_destruction_val = $get_destruction->box_destroyed;

$get_request_id = substr($key, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

if ($get_destruction_val == 1){
$destruction_reversal = 1;
$data_update = array('box_destroyed' => 0);
$data_where = array('box_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_box_destruction_unflag', $ticket_id, $key);
}

if ($get_destruction_val == 0){
$data_update = array('box_destroyed' => 1);
$data_where = array('box_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_box_destruction', $ticket_id, $key);
}

}


if ($destruction_reversal == 1) {
echo "Box destruction has been updated. A box destruction has been reversed.";
} else {
echo "Box destruction has been updated";
}

} else {
   echo "Please select one or more boxes to mark for destruction.";
}
?>
