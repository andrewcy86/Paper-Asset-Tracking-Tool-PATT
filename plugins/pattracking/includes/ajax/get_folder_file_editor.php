<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

if (!isset($_SESSION)) {
    session_start();    
}

$doc_id = $_POST["doc_id"];
        
ob_start();

			$folderfile_details = $wpdb->get_row(
				"SELECT *
            FROM wpqa_wpsc_epa_folderdocinfo WHERE id = '" . $doc_id . "'"
			);

            $folderfile_id = $folderfile_details->id;
			$folderfile_boxid = $folderfile_details->box_id;
			$folderfile_folderdocinfoid = $folderfile_details->folderdocinfo_id;
			$folderfile_il = $folderfile_details->index_level;
			$folderfile_title = $folderfile_details->title;
			$folderfile_date = $folderfile_details->date;
			$folderfile_author = $folderfile_details->author;
			$folderfile_record_type = $folderfile_details->record_type;
			$folderfile_site_name = $folderfile_details->site_name;
			$folderfile_site_id = $folderfile_details->site_id;
			$folderfile_close_date = $folderfile_details->close_date;
			$folderfile_epa_contact_email = $folderfile_details->epa_contact_email;
			$folderfile_access_type = $folderfile_details->access_type;
			$folderfile_source_format = $folderfile_details->source_format;
			$folderfile_rights = $folderfile_details->rights;
			$folderfile_contract_number = $folderfile_details->contract_number;
			$folderfile_grant_number = $folderfile_details->grant_number;
			$folderfile_file_location = $folderfile_details->file_location;
			$folderfile_file_name = $folderfile_details->file_name;

?>

<form>
<strong>Index Level:</strong><br />
<select id="il" name="il">
  <option value="1">Folder</option>
  <option value="2">File</option>
</select></br></br>
<strong>Title:</strong><br /><input type='text' id='title' placeholder= 'Enter title...'></br></br>
<strong>Date:</strong><br /><input type='date' id='date' placeholder= 'mm/dd/yyyy'></br></br>
<strong>Author:</strong><br /><input type='text' id='author' placeholder= 'Enter author...'></br></br>
<strong>Record Type:</strong><br /><input type='text' id='record_type' placeholder= 'Enter record type...'></br></br>
<strong>Site Name:</strong><br /><input type='text' id='site_name' placeholder= 'Enter site name...'></br></br>
<strong>Site ID:</strong><br /><input type='text' id='site_id' placeholder= 'Enter site ID...'></br></br>
<strong>Close Date:</strong><br /><input type='date' id='close_date' placeholder= 'Enter close date...'></br></br>
<strong>Contact Email:</strong><br /><input type='text' id='contact_email' placeholder= 'Enter contact email...'></br></br>
<strong>Access Type:</strong><br /><input type='text' id='access_type' placeholder= 'Enter access type...'></br></br>
<strong>Source Format:</strong><br /><input type='text' id='source_format' placeholder= 'Enter source format...'></br></br>
<strong>Rights:</strong><br /><input type='text' id='rights' placeholder= 'Enter folder/file rights...'></br></br>
<strong>Contract Number:</strong><br /><input type='text' id='contract_number' placeholder= 'Enter contract number...'></br></br>
<strong>Grant Number:</strong><br /><input type='text' id='grant_number' placeholder= 'Enter grant number...'>
<input type="hidden" id="folderfileid" name="folderfileid" value="<?php echo $folderfile_id; ?>">
<input type="hidden" id="pattdocid" name="pattdocid" value="<?php echo $folderfile_folderdocinfoid; ?>">
</form>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_edit_folder_file_details();"><?php _e('Save','supportcandy');?></button>
<script>
function wpsc_edit_folder_file_details(){		
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_folder_file_details.php',{
postvarsffid: jQuery("#folderfileid").val(),
postvarspdid: jQuery("#pattdocid").val(),
postvarsil: jQuery("#il").val(),
postvarsrs: jQuery("#record_schedule").val(),
postvarstitle: jQuery("#title").val(),
postvarsdate: jQuery("#date").val(),
postvarsauthor: jQuery("#author").val(),
postvarsrt: jQuery("#record_type").val(),
postvarssn: jQuery("#site_name").val(),
postvarssid: jQuery("#site_id").val(),
postvarscd: jQuery("#close_date").val(),
postvarsce: jQuery("#contact_email").val(),
postvarsat: jQuery("#access_type").val(),
postvarssf: jQuery("#source_format").val(),
postvarsrights: jQuery("#rights").val(),
postvarscn: jQuery("#contract_number").val(),
postvarsgn: jQuery("#grant_number").val()
}, 
   function (response) {
      if(!alert(response)){window.location.reload();}
      window.location.replace("/wordpress3/wp-admin/admin.php?pid=boxsearch&page=filedetails&id=<?php echo $folderfile_folderdocinfoid; ?>");
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
