<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$GLOBALS['id'] = $_GET['id'];
$GLOBALS['pid'] = $_GET['pid'];

//include_once WPPATT_ABSPATH . 'includes/class-wppatt-functions.php';
//$load_styles = new wppatt_Functions();
//$load_styles->addStyles();

$general_appearance = get_option('wpsc_appearance_general_settings');

$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

?>


<div class="bootstrap-iso">
  
  <h3>Box Details</h3>
  
 <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;">

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  
  <div class="col-sm-12">
    	<button type="button" id="wpsc_individual_ticket_list_btn" onclick="location.href='admin.php?page=wpsc-tickets';" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'requestdetails') {
?>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="location.href='admin.php?page=wpsc-tickets&id=<?php echo Patt_Custom_Func::convert_box_request_id($GLOBALS['id']); ?>';" style="<?php echo $action_default_btn_css?>"><i class="fas fa-chevron-circle-left"></"></i> Back to Request</button>
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'boxsearch') {
?>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="location.href='admin.php?page=boxes';" style="<?php echo $action_default_btn_css?>"><i class="fas fa-chevron-circle-left"></"></i> Back to Box Dashboard</button>
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'docsearch') {
?>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="location.href='admin.php?page=folderfile';" style="<?php echo $action_default_btn_css?>"><i class="fas fa-chevron-circle-left"></"></i> Back to Folder/File Dashboard</button>
<?php
}
?>
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="window.location.reload();" style="<?php echo $action_default_btn_css?>"><i class="fas fa-retweet"></i> <?php _e('Reset Filters','supportcandy')?></button></button>
		
		
  </div>
	
</div>

<div class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">

<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}$/", $GLOBALS['id'])) {
?>

  <div class="col-sm-8 col-md-9 wpsc_it_body">
    <div class="row wpsc_it_subject_widget">
      <h3>
	 	 <?php if(apply_filters('wpsc_show_hide_ticket_subject',true)){?>
        	[Box ID # <?php
            echo $GLOBALS['id']; ?>]
		  <?php } ?>		
      </h3>

    </div>
<style>
.datatable_header {
background-color: rgb(66, 73, 73) !important; 
color: rgb(255, 255, 255) !important; 
width: 204px;
}
</style>

<?php
$box_id = Patt_Custom_Func::convert_box_id($GLOBALS['id']);

$box_content = Patt_Custom_Func::fetch_box_content($box_id);
			$tbl = '
<div class="table-responsive" style="overflow-x:auto;">
	<table id="tbl_templates_boxes" class="table table-striped table-bordered" cellspacing="5" cellpadding="5">
<thead>
  <tr>
    	  			<th class="datatable_header">ID</th>
    	  			<th class="datatable_header">Title</th>
    	  			<th class="datatable_header">Date</th>
    	  			<th class="datatable_header">Contact</th>
  </tr>
 </thead><tbody>
';

			foreach ($box_content as $info) {
				$boxcontent_id = $info->id;
				$boxcontent_title = $info->title;
				$boxcontent_title_truncated = (strlen($boxcontent_title) > 20) ? substr($boxcontent_title, 0, 20) . '...' : $boxcontent_title;
				$boxcontent_date = $info->date;
				$boxcontent_site = $info->site;
				$boxcontent_contact = $info->contact;
				$boxcontent_sf = $info->source_format;
				

if ($GLOBALS['pid'] == 'requestdetails') {
				$tbl .= '
            <tr>
            <td><a href="admin.php?pid=requestdetails&page=filedetails&id=' . $boxcontent_id . '">' . $boxcontent_id . '</a></td>
            ';
}

if ($GLOBALS['pid'] == 'boxsearch') {
				$tbl .= '
            <tr>
            <td><a href="admin.php?pid=boxsearch&page=filedetails&id=' . $boxcontent_id . '">' . $boxcontent_id . '</a></td>
            ';
}
if ($GLOBALS['pid'] == 'docsearch') {
				$tbl .= '
            <tr>
            <td><a href="admin.php?pid=docsearch&page=filedetails&id=' . $boxcontent_id . '">' . $boxcontent_id . '</a></td>
            ';
}
            
                $tbl .='
            <td>' . $boxcontent_title_truncated . '</td>
            <td>' . $boxcontent_date . '</td>
            <td>' . $boxcontent_contact . '</td>
            </tr>
            ';
			}
			$tbl .= '</tbody></table></div>';

			echo $tbl;
?>			
<br /><br />
<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>
<script>
 jQuery(document).ready(function() {
	 jQuery('#tbl_templates_boxes').DataTable({
		 "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]]
		});

	 jQuery('#toplevel_page_wpsc-tickets').removeClass('wp-not-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-has-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-menu-open'); 
	 jQuery('#toplevel_page_wpsc-tickets a:first').removeClass('wp-not-current-submenu');
	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-has-current-submenu'); 
	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-menu-open');
	 jQuery('#menu-dashboard').removeClass('current');
	 jQuery('#menu-dashboard a:first').removeClass('current');
	 
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'requestdetails') {
?>
	 jQuery('.wp-first-item').addClass('current'); 
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'boxsearch') {
?>
	 jQuery('.wp-submenu li:nth-child(3)').addClass('current');
<?php
}
?>
<?php
if (preg_match("/^[0-9]{7}-[0-9]{1,3}$/", $GLOBALS['id']) && $GLOBALS['pid'] == 'docearch') {
?>
	 jQuery('.wp-submenu li:nth-child(4)').addClass('current');
<?php
}
?>
} );

</script>


  </div>
 
 <?php
    //get widget fields
    //$location_details = $wpdb->get_row("SELECT acronym, location, shelf, bay, record_schedule_number FROM wpqa_wpsc_epa_program_office, wpqa_wpsc_epa_boxinfo, wpqa_epa_record_schedule WHERE wpqa_wpsc_epa_program_office.id = wpqa_wpsc_epa_boxinfo.program_office_id AND wpqa_epa_record_schedule.id = wpqa_wpsc_epa_boxinfo.record_schedule_id AND wpqa_wpsc_epa_boxinfo.box_id = '" . $GLOBALS['id'] . "'");
    $location_details = $wpdb->get_row("SELECT wpqa_wpsc_ticket.request_id as request_id, wpqa_wpsc_epa_program_office.office_acronym as acronym, wpqa_terms.name as digitization_center, locations, wpqa_wpsc_epa_storage_location.shelf as shelf, wpqa_wpsc_epa_storage_location.bay as bay, wpqa_wpsc_epa_storage_location.aisle as aisle, wpqa_wpsc_epa_storage_location.position as position, record_schedule_number 
FROM wpqa_wpsc_epa_program_office, wpqa_wpsc_epa_boxinfo, wpqa_epa_record_schedule, wpqa_wpsc_epa_location_status, wpqa_wpsc_epa_storage_location, wpqa_terms, wpqa_wpsc_ticket
WHERE wpqa_wpsc_ticket.id = wpqa_wpsc_epa_boxinfo.ticket_id AND wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center AND wpqa_wpsc_epa_program_office.office_code = wpqa_wpsc_epa_boxinfo.program_office_id AND wpqa_epa_record_schedule.id = wpqa_wpsc_epa_boxinfo.record_schedule_id AND wpqa_wpsc_epa_location_status.id = wpqa_wpsc_epa_boxinfo.location_status_id AND wpqa_wpsc_epa_storage_location.id = wpqa_wpsc_epa_boxinfo.storage_location_id
AND wpqa_wpsc_epa_boxinfo.box_id = '" . $GLOBALS['id'] . "'");

    $location_request_id = $location_details->request_id;
    $location_program_office = $location_details->acronym;
    $location_digitization_center = $location_details->digitization_center;
    $location_general = $location_details->locations;
    $location_aisle = $location_details->aisle;
    $location_bay = $location_details->bay;
    $location_shelf = $location_details->shelf;
    $location_position = $location_details->position;
    $location_record_schedule = $location_details->record_schedule_number;
    
 ?>
 
	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">
		<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
      <h4 class="widget_header"><i class="fa fa-arrow-circle-right"></i> Location
			<!--only admins/agents have the ability to edit box details-->
			<?php
			    $agent_permissions = $wpscfunction->get_current_agent_permissions();
                $agent_permissions['label'];
                if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
                {
                  echo '<button id="wpsc_individual_change_ticket_status" onclick="wpsc_get_box_editor('.$box_id.');" class="btn btn-sm wpsc_action_btn" style="background-color:#FFFFFF !important;color:#000000 !important;border-color:#C3C3C3!important"><i class="fas fa-edit"></i></button>';
                } 
			?>
			
			</h4>
			<hr class="widget_divider">
			<!--error handling implemented, will not display a field if it is empty/null-->
			<?php 
			if(!empty($location_request_id)) {
                echo "<div class='wpsp_sidebar_labels'><strong>Request ID: </strong> <a href='admin.php?page=wpsc-tickets&id=" . $location_request_id . "'>" . $location_request_id . "</a></div>";
            }
            
			if(!empty($location_program_office)) {
                echo '<div class="wpsp_sidebar_labels"><strong>Program Office: </strong>' . $location_program_office . '</div>';
            }
            if(!empty($location_digitization_center)) {
                echo '<div class="wpsp_sidebar_labels"><strong>Digitization Center: </strong>' . $location_digitization_center . '</div>';
                
                if(!empty($location_general)) {
			        echo '<div class="wpsp_sidebar_labels"><strong>Location: </strong>' . $location_general . '</div>';
			       
			       //checks to make sure location of box is 'On Shelf' and that aisle/bay/shelf/position != 0
			       if($location_general == 'On Shelf' && (!($location_aisle <= 0 || $location_bay <= 0 || $location_shelf <= 0 || $location_position <= 0))) {
    			        echo '<div class="wpsp_sidebar_labels"><strong>Aisle: </strong>' . $location_aisle . '</div>';
    			        echo '<div class="wpsp_sidebar_labels"><strong>Bay: </strong>' . $location_bay . '</div>';
    			        echo '<div class="wpsp_sidebar_labels"><strong>Shelf: </strong>' . $location_shelf . '</div>';
    			        echo '<div class="wpsp_sidebar_labels"><strong>Position: </strong>' . $location_position . '</div>';
			        } 
			    }
            }
			?>
			
			<div class="wpsp_sidebar_labels"><strong>Record Schedule: </strong> <?php echo $location_record_schedule ?></div>
	</div>
	</div>
	
<?php
} else {

echo '<span style="padding-left: 10px">Please pass a valid Box ID</span>';

}
?>
</div>
</div>
</div>

<!-- Pop-up snippet start -->
<div id="wpsc_popup_background" style="display:none;"></div>
<div id="wpsc_popup_container" style="display:none;">
  <div class="bootstrap-iso">
    <div class="row">
      <div id="wpsc_popup" class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
        <div id="wpsc_popup_title" class="row"><h3>Modal Title</h3></div>
        <div id="wpsc_popup_body" class="row">I am body!</div>
        <div id="wpsc_popup_footer" class="row">
          <button type="button" class="btn wpsc_popup_close"><?php _e('Close','supportcandy');?></button>
          <button type="button" class="btn wpsc_popup_action"><?php _e('Save Changes','supportcandy');?></button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Pop-up snippet end -->

<script>
		function wpsc_get_box_editor(box_id){

		  wpsc_modal_open('Edit Box Details');
		  var data = {
		    action: 'wpsc_get_box_editor',
		    box_id: box_id
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  });  
		}
</script>