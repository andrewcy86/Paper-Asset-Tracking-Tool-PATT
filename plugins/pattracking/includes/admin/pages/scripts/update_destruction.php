<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsboxid'])
){


$boxid_string = $_POST['postvarsboxid'];
$boxid_arr = explode (",", $boxid_string);  
$page_id = $_POST['postvarpage'];

$table_name = 'wpqa_wpsc_epa_boxinfo';

$destruction_reversal = 0;

foreach($boxid_arr as $key) {    



$get_box_db_id = $wpdb->get_row("select id from wpqa_wpsc_epa_boxinfo where box_id = '".$key."'");
$box_db_id = $get_box_db_id->id;

$get_sum_total = $wpdb->get_row("select count(id) as sum_total_count from wpqa_wpsc_epa_folderdocinfo where box_id = '".$box_db_id."'");
$sum_total_val = $get_sum_total->sum_total_count;

$get_sum_validation = $wpdb->get_row("select sum(validation) as sum_validation from wpqa_wpsc_epa_folderdocinfo where validation = 1 AND box_id = '".$box_db_id."'");
$sum_validation = $get_sum_validation->sum_validation;

$get_status = $wpdb->get_row("select b.ticket_status as status from wpqa_wpsc_epa_boxinfo a INNER JOIN wpqa_wpsc_ticket b ON a.ticket_id = b.id where a.id = '".$box_db_id."'");
$request_status = $get_status->status;

if(($sum_total_val != $sum_validation) || ($request_status != 68)) {
    echo $key.' : ';
    echo 'Please ensure all documents are validated and the request status is approved for destruction before destroying the box.'. PHP_EOL;
} else {
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

if ($destruction_reversal == 1) {
echo $key.' : ';
echo "Box destruction has been updated. A box destruction has been reversed.". PHP_EOL;
} else {
echo $key.' : ';
echo "Box destruction has been updated". PHP_EOL;
}

}

}

} else {
   echo "Please select one or more boxes to mark for destruction.";
}
?>
