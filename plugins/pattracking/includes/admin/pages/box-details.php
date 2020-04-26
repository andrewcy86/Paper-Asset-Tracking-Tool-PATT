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
		<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="window.location.reload();" style="<?php echo $action_default_btn_css?>"><i class="fas fa-sync-alt"></i> <?php _e('Refresh','supportcandy')?></button>
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
 
	<div class="col-sm-4 col-md-3 wpsc_sidebar individual_ticket_widget">

							<div class="row" id="wpsc_status_widget" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_ticket_widgets_border_color']?> !important;">
					      <h4 class="widget_header"><i class="fa fa-arrow-circle-right"></i> Location
										<button id="wpsc_individual_change_ticket_status" onclick="wpsc_get_change_ticket_status(<?php echo $ticket_id?>)" class="btn btn-sm wpsc_action_btn" style="<?php echo $edit_btn_css ?>"><i class="fas fa-edit"></i></button>
								</h4>
								<hr class="widget_divider">

	                            <div class="wpsp_sidebar_labels"><strong>Program Office:</strong> [Insert Program Office Here]</div>
	                            <div class="wpsp_sidebar_labels"><strong>Digitization Center:</strong> [Insert Digitization Center Here]</div>
								<div class="wpsp_sidebar_labels"><strong>Bay:</strong> [Insert Bay Here]</div>
								<div class="wpsp_sidebar_labels"><strong>Shelf:</strong> [Insert Shelf Here] </div>
								<div class="wpsp_sidebar_labels"><strong>Record Schedule :</strong> [Insert Record Schedule]</div>
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
