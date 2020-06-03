 <?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

$subfolder_path = site_url( '', 'relative'); 

if (!isset($_SESSION)) {
    session_start();    
}

$box_id = $_POST["box_id"];
        
ob_start();

  $box_details = $wpdb->get_row("
SELECT 
wpqa_wpsc_epa_boxinfo.box_id as box_id, 
wpqa_wpsc_epa_program_office.acronym as acronym, 
wpqa_epa_record_schedule.Record_Schedule_Number as record_schedule_number
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_program_office ON wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.id
INNER JOIN wpqa_epa_record_schedule ON wpqa_wpsc_epa_boxinfo.record_schedule_id = wpqa_epa_record_schedule.id
WHERE wpqa_wpsc_epa_boxinfo.id = '" . $box_id . "'");

    $patt_box_id = $box_details->box_id;
    $program_office = $box_details->acronym;
    $record_schedule = $box_details->record_schedule_number;

?>

<!--converted program office dropdown to a datalist-->
<form autocomplete='off'>
<strong>Program Office:</strong><br />
<?php
if(!empty($program_office)) 
{
  $po_array = Patt_Custom_Func::fetch_program_office_array(); ?>
    <input type="search" list="po" name="program_office" placeholder='$program_office'/>
    <datalist id = 'po'>
     <?php foreach($po_array as $key => $value) { ?>
        <option value='<?php echo $value; ?>'><?php echo preg_replace("/\([^)]+\)/","",$value); ?></option>
     <?php } ?>
     </datalist>
<?php }
else {
    $po_array = Patt_Custom_Func::fetch_program_office_array(); ?>
    <input type="search" list="po" name="program_office" placeholder='Enter program office'/>
    <datalist id = 'po'>
     <?php foreach($po_array as $key => $value) { ?>
        <option value='<?php echo $value; ?>'><?php echo preg_replace("/\([^)]+\)/","",$value); ?></option>
     <?php } ?>
     </datalist>
<?php } ?>

<br></br>

<strong>Record Schedule:</strong><br />
<?php
if(!empty($record_schedule)) 
{
  $rs_array = Patt_Custom_Func::fetch_record_schedule_array(); ?>
    <input type="search" list="rs" name="record_schedule" placeholder='$record_schedule'/>
    <datalist id = 'rs'>
     <?php foreach($rs_array as $key => $value) { ?>
        <option value='<?php echo $value; ?>'></option>
     <?php } ?>
     </datalist>;
<?php }
else {
    $rs_array = Patt_Custom_Func::fetch_record_schedule_array(); ?>
    <input type="search" list="rs" name="record_schedule" placeholder='Enter record schedule'/>
    <datalist id = 'rs'>
     <?php foreach($rs_array as $key => $value) { ?>
        <option value='<?php echo $value; ?>'?></option>
     <?php } ?>
     </datalist>
<?php } ?>

<input type="hidden" id="boxid" name="boxid" value="<?php echo $box_id; ?>">
<input type="hidden" id="pattboxid" name="pattboxid" value="<?php echo $patt_box_id; ?>">
</form>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_edit_box_details();"><?php _e('Save','supportcandy');?></button>
<script>
function wpsc_edit_box_details(){		
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_box_details.php',{
postvarspattboxid: jQuery("#pattboxid").val(),
postvarsboxid: jQuery("#boxid").val(),
postvarspo: jQuery("#po").val(),
postvarsrs: jQuery("#rs").val()
}, 
   function (response) {
      if(!alert(response)){window.location.reload();}
      window.location.replace("<?php echo $subfolder_path; ?>/wp-admin/admin.php?pid=boxsearch&page=boxdetails&id=<?php echo $patt_box_id; ?>");
   });
}
</script>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);
