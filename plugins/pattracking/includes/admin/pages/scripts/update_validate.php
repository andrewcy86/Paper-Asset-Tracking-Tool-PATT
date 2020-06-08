<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsfolderdocid'])
){


$folderdocid_string = $_POST['postvarsfolderdocid'];
$get_userid = $_POST['potvarsuserid'];
$folderdocid_arr = explode (",", $folderdocid_string);  

$table_name = 'wpqa_wpsc_epa_folderdocinfo';

$validation_reversal = 0;

foreach($folderdocid_arr as $key) {    
$get_validation = $wpdb->get_row("SELECT validation FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$key."'");

$get_validation_val = $get_validation->validation;

if ($get_validation_val == 1){
$validation_reversal = 1;
$data_update = array('validation' => 0, 'validation_user_id'=>'');
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
}

if ($get_validation_val == 0){
$data_update = array('validation' => 1, 'validation_user_id'=>$get_userid);
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
}

//echo $get_validation_val;
}

if ($validation_reversal == 1) {
//print_r($folderdocid_arr);
echo "Validation has been updated. A validation has been reversed.";
} else {
echo "Validation has been updated";
}


} else {
   echo "Please select one or more items to validate.";
}
?>
