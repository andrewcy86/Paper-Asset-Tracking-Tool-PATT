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
   $pattdocid = $_POST['postvarspdid'];
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

//update index level on document
//if index level is updated, folderdocinfo_id has to be updated
if (isset($il)) {
        //updates index_level to either folder/file
        $data_update_il = array('index_level' => $il);
        
        //pattdocid = folderdocinfo_id
        $pattdocid_split = explode('-', $pattdocid);
        //rewrite the folderdocinfo_id
        if($data_update_il == 1) {
            $folderdocinfo_id_update = $pattdocid_split[0] . '-' . $pattdocid_split[1] . '-' . '01' . '-' . $pattdocid_split[3];
        } 
        else {
            $folderdocinfo_id_update = $pattdocid_split[0] . '-' . $pattdocid_split[1] . '-' . '02' . '-' . $pattdocid_split[3];
        }
        //assign new index level to folderdocinfo_id
        
        $data_update_folderdocinfo_id = array('folderdocinfo_id' => $folderdocinfo_id_update);
        $data_where = array('id' => $folderfileid);
        //update index_level
        $wpdb->update($table_name, $data_update_il, $data_where);
        //update folderdocinfo_id
        $wpdb->update($table_name, $data_update_folderdocinfo_id, $data_where);
}

//updates fields in folder-file-details modal window
if(!empty($title)) {
$data_update = array('title' => $title);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name , $data_update, $data_where);
}

if(!empty($date)) {
$data_update = array('date' => $date);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($author)) {
$data_update = array('author' => $author);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($record_type)) {
$data_update = array('record_type' => $record_type);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($site_name)) {
$data_update = array('site_name' => $site_name);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($site_id)) {
$data_update = array('site_id' => $site_id);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($close_date)) {
$data_update = array('close_date' => $close_date);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($site_id)) {
$data_update = array('site_id' => $site_id);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($contact_email)) {
$data_update = array('epa_contact_email' => $contact_email);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($access_type)) {
$data_update = array('access_type' => $access_type);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($source_format)) {
$data_update = array('source_format' => $source_format);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($rights)) {
$data_update = array('rights' => $rights);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($contract_number)) {
$data_update = array('contract_number' => $contract_number);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

if(!empty($grant_number)) {
$data_update = array('grant_number' => $grant_number);
$data_where = array('id' => $folderfileid);
$wpdb->update($table_name, $data_update, $data_where);
}

 echo "Document ID #: " . $pattdocid . " has been updated.";
 
} else {
   echo "Please make an edit.";
}
?>
