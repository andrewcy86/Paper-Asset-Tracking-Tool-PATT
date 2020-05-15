<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$GLOBALS['id'] = $_GET['id'];
$GLOBALS['pid'] = $_GET['pid'];

include_once WPPATT_ABSPATH . 'includes/class-wppatt-functions.php';
$load_styles = new wppatt_Functions();
$load_styles->addStyles();

$general_appearance = get_option('wpsc_appearance_general_settings');

$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';

?>


<div class="bootstrap-iso">
<?php
			$folderfile_details = $wpdb->get_row(
				"SELECT 
            wpqa_wpsc_epa_folderdocinfo.box_id,
            wpqa_wpsc_epa_folderdocinfo.title, 
            wpqa_wpsc_epa_folderdocinfo.date, 
            wpqa_wpsc_epa_folderdocinfo.author, 
            wpqa_wpsc_epa_folderdocinfo.record_type,
            wpqa_wpsc_epa_folderdocinfo.site_name, 
            wpqa_wpsc_epa_folderdocinfo.site_id, 
            wpqa_wpsc_epa_folderdocinfo.close_date,
            wpqa_wpsc_epa_folderdocinfo.epa_contact_email,
            wpqa_wpsc_epa_folderdocinfo.access_type,
            wpqa_wpsc_epa_folderdocinfo.source_format,
            wpqa_wpsc_epa_folderdocinfo.rights, 
            wpqa_wpsc_epa_folderdocinfo.contract_number,  
            wpqa_wpsc_epa_folderdocinfo.grant_number,
            wpqa_wpsc_epa_folderdocinfo.file_location,
            wpqa_wpsc_epa_folderdocinfo.file_name
            FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '" . $GLOBALS['id'] . "'"
			);

			$folderfile_boxid = $folderfile_details->box_id;
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
			/*$box_details = $wpdb->get_row(
				"SELECT wpqa_wpsc_epa_boxinfo.id, wpqa_wpsc_ticket.request_id as request_id, wpqa_wpsc_epa_boxinfo.box_id as box_id, wpqa_wpsc_epa_boxinfo.ticket_id as ticket_id, wpqa_wpsc_epa_boxinfo.index_level as index_level, wpqa_wpsc_epa_boxinfo.location as location, wpqa_wpsc_epa_boxinfo.bay as bay, wpqa_wpsc_epa_boxinfo.shelf as shelf, wpqa_epa_record_schedule.Record_Schedule_Number as rsnum, wpqa_wpsc_epa_program_office.acronym as program_office
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_epa_record_schedule ON wpqa_wpsc_epa_boxinfo.record_schedule_id = wpqa_epa_record_schedule.id
INNER JOIN wpqa_wpsc_epa_program_office ON wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.id
INNER JOIN wpqa_wpsc_ticket ON wpqa_wpsc_epa_boxinfo.ticket_id = wpqa_wpsc_ticket.id
WHERE wpqa_wpsc_epa_boxinfo.id = '" . $folderfile_boxid . "'"
			);*/
			
			$box_details = $wpdb->get_row("SELECT wpqa_wpsc_epa_boxinfo.id, wpqa_wpsc_ticket.request_id as request_id, wpqa_wpsc_epa_boxinfo.box_id as box_id, wpqa_wpsc_epa_boxinfo.ticket_id as ticket_id, wpqa_wpsc_epa_folderdocinfo.index_level as index_level, wpqa_wpsc_epa_storage_location.digitization_center as location, wpqa_wpsc_epa_storage_location.aisle as aisle, wpqa_wpsc_epa_storage_location.bay as bay, wpqa_wpsc_epa_storage_location.shelf as shelf, wpqa_wpsc_epa_storage_location.position as position, wpqa_epa_record_schedule.Record_Schedule_Number as rsnum, wpqa_wpsc_epa_program_office.acronym as program_office

FROM wpqa_epa_record_schedule, wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_folderdocinfo, wpqa_wpsc_epa_program_office, wpqa_wpsc_epa_storage_location, wpqa_wpsc_ticket

WHERE wpqa_epa_record_schedule.id = wpqa_wpsc_epa_boxinfo.record_schedule_id AND wpqa_wpsc_epa_program_office.id = wpqa_wpsc_epa_boxinfo.program_office_id AND wpqa_wpsc_ticket.id = wpqa_wpsc_epa_boxinfo.ticket_id AND wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id AND wpqa_wpsc_epa_storage_location.id = wpqa_wpsc_epa_boxinfo.storage_location_id
AND wpqa_wpsc_epa_boxinfo.id = '" . $folderfile_boxid . "'");

			$box_boxid = $box_details->box_id;
			$box_ticketid = $box_details->ticket_id;
			$box_requestid = $box_details->request_id;
			$box_rs = $box_details->rsnum;
			$box_po = $box_details->program_office;
			$request_id = substr($box_boxid, 0, 7);
			$box_il = $box_details->index_level;
			$box_location = $box_details->location;
			$box_aisle = $box_details->aisle;
			$box_bay = $box_details->bay;
			$box_shelf = $box_details->shelf;
			$box_position = $box_details->position;
?>

<?php
			$box_il_val = '';
			if ($box_il == 1) {
?>
  <h3>Folder Details</h3>
<?php
			} else {
?>
  <h3>File Details</h3>
<?php
			}
?>

 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
	<div class="col-sm-12">
    	<button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=wpsc-tickets';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
    	
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'requestdetails') {
?>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="location.href='admin.php?page=wpsc-tickets&id=<?php echo $box_requestid ?>';" style="<?php echo $action_default_btn_css?>"><i class="fas fa-chevron-circle-left"></"></i> Back to Request</button>
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'boxsearch') {
?>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="location.href='admin.php?page=boxes';" style="<?php echo $action_default_btn_css?>"><i class="fas fa-chevron-circle-left"></"></i> Back to Box Dashboard</button>
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'docsearch') {
?>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="location.href='admin.php?page=folderfile';" style="<?php echo $action_default_btn_css?>"><i class="fas fa-chevron-circle-left"></"></i> Back to Folder/File Dashboard</button>
<?php
}
?>   	
  </div>
	
</div>

<div class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">

<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id'])) {
?>
  <div class="col-sm-8 col-md-9 wpsc_it_body">
    <div class="row wpsc_it_subject_widget">


<?php
			$box_il_val = '';
			if ($box_il == 1) {
?>
      <h3>
	 	 <?php if(apply_filters('wpsc_show_hide_ticket_subject',true)){?>
        	[Folder ID # <?php
            echo $GLOBALS['id']; ?>]
		  <?php } ?>		
      </h3>
<?php
			} else {
?>
      <h3>
	 	 <?php if(apply_filters('wpsc_show_hide_ticket_subject',true)){?>
        	[File ID # <?php
            echo $GLOBALS['id']; ?>]
		  <?php } ?>		
      </h3>
<?php
			}
?>
    </div>
<form method="POST" action="#">
<?php
            $agent_permissions = $wpscfunction->get_current_agent_permissions();
            $agent_permissions['label'];
            
			echo "<strong>Program Office:</strong> " . $box_po . "<br />";
            echo "<strong>Record Schedule:</strong> " . $box_rs ."<br />";
            
			//only admins/agents have the ability to edit folder/file details			
			if (!empty($folderfile_title)) {
				echo "<strong>Title:</strong> " . $folderfile_title . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
			        echo "<input type='text' id='title' placeholder= 'Enter title...'></br></br>";
			    }
			}
			//make a calendar picker
			if (!empty($folderfile_date)) {
				echo "<strong>Date:</strong> " . $folderfile_date . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    			    echo "<input type='date' id='date' placeholder= 'mm/dd/yyyy'></br></br>"; 
    			}
			}
			if (!empty($folderfile_author)) {
				echo "<strong>Author:</strong> " . $folderfile_author . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='text' id='author' placeholder= 'Enter author...'></br></br>"; 
    			}
			}
			if (!empty($folderfile_record_type)) {
				echo "<strong>Record Type:</strong> " . $folderfile_record_type . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='text' id='record_type' placeholder= 'Enter record type...'></br></br>"; 
    			}
			}
			if (!empty($folderfile_site_name)) {
				echo "<strong>Site Name:</strong> " . $folderfile_site_name . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='text' id='site_name' placeholder= 'Enter site name...'></br></br>"; 
    			}
			}
			if (!empty($folderfile_site_id)) {
				echo "<strong>Site ID #:</strong> " . $folderfile_site_id . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='text' id='site_id' placeholder= 'Enter site ID...'></br></br>"; 
    			}
			}
			if (!empty($folderfile_close_date)) {
				echo "<strong>Close Date:</strong> " . $folderfile_close_date . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='date' id='close_date' placeholder= 'Enter close date...'></br></br>"; 
    			}
			}
			if (!empty($folderfile_epa_contact_email)) {
				echo "<strong>Contact Email:</strong> " . $folderfile_epa_contact_email . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='text' id='contact_email' placeholder= 'Enter contact email...'></br></br>"; 
    			}
			}
			if (!empty($folderfile_access_type)) {
				echo "<strong>Access Type:</strong> " . $folderfile_access_type . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='text' id='access_type' placeholder= 'Enter access type...'></br></br>"; 
    			}
			}
			if (!empty($folderfile_source_format)) {
				echo "<strong>Source Format:</strong> " . $folderfile_source_format . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='text' id='source_format' placeholder= 'Enter source format...'></br></br>"; 
    			}
			}
			if (!empty($folderfile_rights)) {
				echo "<strong>Rights:</strong> " . $folderfile_rights . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='text' id='rights' placeholder= 'Enter folder/file rights...'></br></br>"; 
    			}
			}
			if (!empty($folderfile_contract_number)) {
				echo "<strong>Contract #:</strong> " . $folderfile_contract_number . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='text' id='contract_number' placeholder= 'Enter contract number...'></br></br>"; 
    			}
			}
			if (!empty($folderfile_grant_number)) {
				echo "<strong>Grant #:</strong> " . $folderfile_grant_number . "<br />";
				if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
    				echo "<input type='text' id='grant_number' placeholder= 'Enter grant number...'></br></br>"; 
    			}
			}
			
			if (!empty($folderfile_file_location) || !empty($folderfile_file_name)) {
				echo '<strong>Link to File:</strong> <a href="' . $folderfile_file_location . '" target="_blank">' . $folderfile_file_name . '</a><br />';
			}
?>

<br />
<!--Submit and Reset Buttons-->
<?php
if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
{
    echo '<input type="hidden" id="folderfileid" name="folderfileid" value="'.$GLOBALS['id'].'">';
    echo '<button type="submit" value="Submit" id="wpsc_create_ticket_submit" class="btn" style="background-color:#419641 !important;color:#FFFFFF !important;border-color:#C3C3C3 !important;" onclick="wpsc_edit_folder_file_details();">Submit</button>';
    echo '<button type="reset" value ="Reset" id="wpsc_create_ticket_reset" class="btn" style="background-color:#FFFFFF !important;color:#000000 !important;border-color:#C3C3C3 !important;">Reset</button>';
}
?>
</form>
<br />

<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>
<script>
 jQuery(document).ready(function() {
	 jQuery('#toplevel_page_wpsc-tickets').removeClass('wp-not-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-has-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-menu-open'); 
	 jQuery('#toplevel_page_wpsc-tickets a:first').removeClass('wp-not-current-submenu');
	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-has-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-menu-open');
	 jQuery('#menu-dashboard').removeClass('current');
	 jQuery('#menu-dashboard a:first').removeClass('current');

<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'requestdetails') {
?>
	 jQuery('.wp-first-item').addClass('current'); 
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'boxsearch') {
?>
	 jQuery('.wp-submenu li:nth-child(3)').addClass('current');
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}-[0-9]{2}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'docsearch') {
?>
	 jQuery('.wp-submenu li:nth-child(4)').addClass('current');
<?php
}
?>
} );

function wpsc_edit_folder_file_details(){		
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_folder_file_details.php',{
postvarsffid: jQuery("#folderfileid").val(),
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
      window.location.replace("/wordpress3/wp-admin/admin.php?page=boxdetails&pid=boxsearch&id=<?php echo $box_boxid; ?>");
   });
} 

</script>


  </div>
 
	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">

							<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					      <h4 class="widget_header"><i class="fa fa-arrow-circle-right"></i> Location
					      </h4>
								<hr class="widget_divider">
								<div class="wpsp_sidebar_labels"><strong>Request ID:</strong> 
	                            <?php 
	                                echo "<a href='admin.php?page=wpsc-tickets&id=" . $box_requestid . "'>" . $box_requestid . "</a>";
	                            ?>
	                            </div>
	                            <div class="wpsp_sidebar_labels"><strong>Box ID:</strong> 
	                            <?php 
	                            if (!empty($box_boxid)) {
	                                if ($GLOBALS['pid'] == 'requestdetails') {
	                                echo "<a href='admin.php?pid=requestdetails&page=boxdetails&id=" . $box_boxid . "'>" . $box_boxid . "</a>";
	                                }
	                                if ($GLOBALS['pid'] == 'boxsearch') {
	                                echo "<a href='admin.php?pid=boxsearch&page=boxdetails&id=" . $box_boxid . "'>" . $box_boxid . "</a>";
	                                }
	                                if ($GLOBALS['pid'] == 'docsearch') {
	                                echo "<a href='admin.php?pid=docsearch&page=boxdetails&id=" . $box_boxid . "'>" . $box_boxid . "</a>";
	                                }
	                                } ?>
	                            </div>
	                            <div class="wpsp_sidebar_labels"><strong>Digitization Center:</strong> 
	                            <?php
	                            echo $box_location . "<br />";
	                            ?>
	                            </div>
								<!--switch out aisle, bay, shelf. and position with storage location table-->
								<div class="wpsp_sidebar_labels"><strong>Aisle:</strong>
							    <?php
	                            echo $box_aisle . "<br />";
	                            ?>
	                            </div>
								<div class="wpsp_sidebar_labels"><strong>Bay:</strong>
							    <?php
	                            echo $box_bay . "<br />";
	                            ?>
	                            </div>
								<div class="wpsp_sidebar_labels"><strong>Shelf:</strong>
								<?php
	                            echo $box_shelf . "<br />";
	                            ?>
								</div>
								<div class="wpsp_sidebar_labels"><strong>Position:</strong>
							    <?php
	                            echo $box_position . "<br />";
	                            ?>
	                            </div>
			    		</div>
	
	</div>
<?php
} else {

echo '<span style="padding-left: 10px">Please pass a valid Folder/File ID</span>';

}
?>
</div>
</div>
</div>
