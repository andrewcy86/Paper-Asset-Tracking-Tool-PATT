<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

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

<form>
<strong>Program Office:</strong><br />
<input type='text' id='po' placeholder= '<?php echo $program_office; ?>'><br /><br />
<strong>Record Schedule:</strong><br />
<input type='text' id='rs' placeholder= '<?php echo $record_schedule; ?>'><br /><br />
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
      window.location.replace("/wordpress3/wp-admin/admin.php?pid=boxsearch&page=boxdetails&id=<?php echo $patt_box_id; ?>");
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
