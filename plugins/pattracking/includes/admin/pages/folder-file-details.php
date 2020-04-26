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
			$box_details = $wpdb->get_row(
				"SELECT wpqa_wpsc_epa_boxinfo.id, wpqa_wpsc_ticket.request_id as request_id, wpqa_wpsc_epa_boxinfo.box_id as box_id, wpqa_wpsc_epa_boxinfo.ticket_id as ticket_id, wpqa_wpsc_epa_boxinfo.index_level as index_level, wpqa_wpsc_epa_boxinfo.location as location, wpqa_wpsc_epa_boxinfo.bay as bay, wpqa_wpsc_epa_boxinfo.shelf as shelf, wpqa_epa_record_schedule.Record_Schedule_Number as rsnum, wpqa_wpsc_epa_program_office.acronym as program_office
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_epa_record_schedule ON wpqa_wpsc_epa_boxinfo.record_schedule_id = wpqa_epa_record_schedule.id
INNER JOIN wpqa_wpsc_epa_program_office ON wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.id
INNER JOIN wpqa_wpsc_ticket ON wpqa_wpsc_epa_boxinfo.ticket_id = wpqa_wpsc_ticket.id
WHERE wpqa_wpsc_epa_boxinfo.id = '" . $folderfile_boxid . "'"
			);

			$box_boxid = $box_details->box_id;
			$box_ticketid = $box_details->ticket_id;
			$box_requestid = $box_details->request_id;
			$box_rs = $box_details->rsnum;
			$box_po = $box_details->program_office;
			$request_id = substr($box_boxid, 0, 7);
			$box_il = $box_details->index_level;
			$box_location = $box_details->location;
			$box_bay = $box_details->bay;
			$box_shelf = $box_details->shelf;
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
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="window.location.reload();" style="<?php echo $action_default_btn_css?>"><i class="fas fa-sync-alt"></i> <?php _e('Refresh','supportcandy')?></button>
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

<?php

			echo "<strong>Program Office:</strong> " . $box_po . "<br />";
			
			echo "<strong>Record Schedule:</strong> " . $box_rs ."<br />";
			if (!empty($folderfile_title)) {
				echo "<strong>Title:</strong> " . $folderfile_title . "<br />";
			}
			if (!empty($folderfile_date)) {
				echo "<strong>Date:</strong> " . $folderfile_date . "<br />";
			}
			if (!empty($folderfile_author)) {
				echo "<strong>Author:</strong> " . $folderfile_author . "<br />";
			}
			if (!empty($folderfile_record_type)) {
				echo "<strong>Record Type:</strong> " . $folderfile_record_type . "<br />";
			}
			if (!empty($folderfile_site_name)) {
				echo "<strong>Site Name:</strong> " . $folderfile_site_name . "<br />";
			}
			if (!empty($folderfile_site_id)) {
				echo "<strong>Site ID #:</strong> " . $folderfile_site_id . "<br />";
			}
			if (!empty($folderfile_close_date)) {
				echo "<strong>Close Date:</strong> " . $folderfile_close_date . "<br />";
			}
			if (!empty($folderfile_epa_contact_email)) {
				echo "<strong>Contact Email:</strong> " . $folderfile_epa_contact_email . "<br />";
			}
			if (!empty($folderfile_access_type)) {
				echo "<strong>Access Type:</strong> " . $folderfile_access_type . "<br />";
			}
			if (!empty($folderfile_source_format)) {
				echo "<strong>Source Format:</strong> " . $folderfile_source_format . "<br />";
			}
			if (!empty($folderfile_rights)) {
				echo "<strong>Rights:</strong> " . $folderfile_rights . "<br />";
			}
			if (!empty($folderfile_contract_number)) {
				echo "<strong>Contract #:</strong> " . $folderfile_contract_number . "<br />";
			}
			if (!empty($folderfile_grant_number)) {
				echo "<strong>Grant #:</strong> " . $folderfile_grant_number . "<br />";
			}
			
			if (!empty($folderfile_file_location) || !empty($folderfile_file_name)) {
				echo '<strong>Link to File:</strong> <a href="' . $folderfile_file_location . '" target="_blank">' . $folderfile_file_name . '</a><br />';
			}

?>

<br /><br />
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

</script>


  </div>
 
	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">

							<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					      <h4 class="widget_header"><i class="fa fa-arrow-circle-right"></i> Location</h4>
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
