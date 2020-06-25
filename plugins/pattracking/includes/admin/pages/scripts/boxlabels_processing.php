<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvarsboxids'])){
    
$box_ids = $_POST['postvarsboxids'];

$box_arr = explode(",", $box_ids);

$box_count = count($box_arr);

$count = 0;

foreach($box_arr as $key => $value) { 
    
$get_destroy_status = $wpdb->get_row("
SELECT box_destroyed FROM wpqa_wpsc_epa_boxinfo 
WHERE box_id = '" . $value . "'
");
$destroy_status = $get_destroy_status->box_destroyed;

if ($destroy_status == 1) {
$count++;
}

}


if ($box_count == $count) {
echo 'false';
}

if ($count < $box_count && $count != 0) {
echo 'warn';
}

if ($count < $box_count && $count == 0) {
echo 'true';
}

} else {
   echo "Update not successful.";
}
?>
