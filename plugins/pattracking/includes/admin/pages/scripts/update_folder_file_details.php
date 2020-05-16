<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsil']) ||
!empty($_POST['postvarstitle']) ||
!empty($_POST['postvarsdate']) ||
!empty($_POST['postvarsauthor']) ||
!empty($_POST['postvarsrt']) ||
!empty($_POST['postvarssn']) ||
!empty($_POST['postvarssid']) ||
!empty($_POST['postvarscd']) ||
!empty($_POST['postvarsce']) ||
!empty($_POST['postvarsat']) ||
!empty($_POST['postvarssf']) ||
!empty($_POST['postvarsrights']) ||
!empty($_POST['postvarscn']) ||
!empty($_POST['postvarsgn'])
){
   $folderfileid = $_POST['postvarsffid'];
   $il = $_POST['postvarsil'];
   $title = $_POST['postvarstitle'];
   $date = $_POST['postvarsdate'];  
   $author = $_POST['postvarsauthor']; 
   $record_type = $_POST['postvarsrt']; 
   $site_name = $_POST['postvarssn']; 
   $site_id = $_POST['postvarssid']; 
   $close_date = $_POST['postvarscd']; 
   $contact_email = $_POST['postvarsce']; 
   $access_type = $_POST['postvarsat']; 
   $source_format = $_POST['postvarssf'];
   $rights = $_POST['postvarsrights']; 
   $contract_number = $_POST['postvarscn']; 
   $grant_number = $_POST['postvarsgn']; 

$table_name = 'wpqa_wpsc_epa_folderdocinfo';

if(!empty($title)) {
$data_update = array('title' => $title);
$data_where = array('folderdocinfo_id' => $folderfileid);
$wpdb->update($table_name , $data_update, $data_where);
}


 echo "Document ID #: " . $folderfileid . " has been updated.";
 
} else {
   echo "Please make an edit.";
}
?>
