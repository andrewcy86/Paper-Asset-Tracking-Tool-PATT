<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsfolderdocid'])
){


$folderdocid_string = $_POST['postvarsfolderdocid'];
$folderdocid_arr = explode (",", $folderdocid_string);  
$page_id = $_POST['postvarpage'];
$box_id = $_POST['boxid'];

$table_name = 'wpqa_wpsc_epa_folderdocinfo';

$destruction_reversal = 0;

if($page_id == 'boxdetails') {
foreach($folderdocid_arr as $key) {    
$get_destruction = $wpdb->get_row("SELECT unauthorized_destruction FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$key."'");
$get_destruction_val = $get_destruction->unauthorized_destruction;

$get_request_id = substr($key, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

if ($get_destruction_val == 1){
$destruction_reversal = 1;
$data_update = array('unauthorized_destruction' => 0);
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_unauthorized_destruction_unflag', $ticket_id, $key);
}

if ($get_destruction_val == 0){
$data_update = array('unauthorized_destruction' => 1);
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_unauthorized_destruction', $ticket_id, $key);
}

}
}

if($page_id == 'filedetails') {

$get_destruction = $wpdb->get_row("SELECT unauthorized_destruction FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$folderdocid_string."'");

$get_destruction_val = $get_destruction->unauthorized_destruction;

$get_request_id = substr($folderdocid_string, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

if ($get_destruction_val == 1){
$destruction_reversal = 1;
$data_update = array('unauthorized_destruction' => 0);
$data_where = array('folderdocinfo_id' => $folderdocid_string);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_unauthorized_destruction_unflag', $ticket_id, $folderdocid_string);
}

if ($get_destruction_val == 0){
$data_update = array('unauthorized_destruction' => 1);
$data_where = array('folderdocinfo_id' => $folderdocid_string);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_unauthorized_destruction', $ticket_id, $folderdocid_string);
}

}

$get_destruction_sum = $wpdb->get_row("SELECT sum(unauthorized_destruction) as sum FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = '".$box_id."'");

$get_destruction_sum_val = $get_destruction_sum->sum;


if ($page_id == 'boxdetails') {
if ($get_destruction_sum_val > 0) {
    
if ($destruction_reversal == 1) {
echo "Unauthorized destruction has been updated. A unauthorized destruction has been reversed.";
} else {
echo "Unauthorized destruction has been updated";
}

} else {
echo "All unathorized destruction flags removed";
}
}

if ($page_id == 'filedetails') {
if ($destruction_reversal == 1) {
echo "Unauthorized destruction has been updated. A unauthorized destruction has been reversed.";
} else {
echo "Unauthorized destruction has been updated";
}
}


} else {
   echo "Please select one or more items to mark as unauthorized destruction.";
}
?>
