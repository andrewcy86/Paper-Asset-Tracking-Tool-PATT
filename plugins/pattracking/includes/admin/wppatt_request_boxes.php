<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
		exit;
}
$ticket_id 	 = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0 ;
$raisedby_email = $wpscfunction->get_ticket_fields($ticket_id, 'customer_email');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
$wpsc_appearance_ticket_list = get_option('wpsc_appearance_ticket_list');

$agent_permissions = $wpscfunction->get_current_agent_permissions();
$current_agent_id  = $wpscfunction->get_current_user_agent_id();

$restrict_rules = array(
	'relation' => 'AND',
	array(
		'key'            => 'customer_email',
		'value'          => $raisedby_email,
		'compare'        => '='
	),
	array(
		'key'            => 'active',
		'value'          => 1,
		'compare'        => '='
	)
);
$ticket_permission = array(
	'relation' => 'OR'
);
if ($agent_permissions['view_unassigned']) {
	$ticket_permission[] = array(
		'key'            => 'assigned_agent',
		'value'          => 0,
		'compare'        => '='
	);
}

if ($agent_permissions['view_assigned_me']) {
	$ticket_permission[] = array(
		'key'            => 'assigned_agent',
		'value'          => $current_agent_id,
		'compare'        => '='
	);
}

if ($agent_permissions['view_assigned_others']) {
	$ticket_permission[] = array(
		'key'            => 'assigned_agent',
		'value'          => array(0,$current_agent_id),
		'compare'        => 'NOT IN'
	);
}

$restrict_rules [] = $ticket_permission;
$select_str        = 'DISTINCT t.*';
$sql               = $wpscfunction->get_sql_query( $select_str, $restrict_rules);
$tickets           = $wpdb->get_results($sql);
$ticket_list       = json_decode(json_encode($tickets), true);

$ticket_list_items = get_terms([
  'taxonomy'   => 'wpsc_ticket_custom_fields',
  'hide_empty' => false,
  'orderby'    => 'meta_value_num',
  'meta_key'	 => 'wpsc_tl_agent_load_order',
  'order'    	 => 'ASC',
  'meta_query' => array(
    'relation' => 'AND',
    array(
      'key'       => 'wpsc_allow_ticket_list',
      'value'     => '1',
      'compare'   => '='
    ),
    array(
      'key'       => 'wpsc_agent_ticket_list_status',
      'value'     => '1',
      'compare'   => '='
    ),
  ),
]);
ob_start();
?>

<?php //echo $status_id ?>
<style>
.datatable_header {
background-color: rgb(66, 73, 73) !important; 
color: rgb(255, 255, 255) !important; 
width: 204px;
}
</style>
<h4>Boxes Related to Request</h4>

<?php
	$box_details = Patt_Custom_Func::fetch_box_details($ticket_id);
			$tbl = '
<div class="table-responsive" style="overflow-x:auto;">
	<table id="tbl_templates_boxes" class="table table-striped table-bordered" cellspacing="5" cellpadding="5">
<thead>
  <tr>
    	  			<th class="datatable_header">ID</th>
    	  			<th class="datatable_header">Facility</th>
    	  			<th class="datatable_header">Bay</th>
    	  			<th class="datatable_header">Shelf</th>
    	  			<th class="datatable_header">Index Level</th>
  </tr>
 </thead><tbody>
';

			foreach ($box_details as $info) {
				$boxlist_id = $info->id;
				$boxlist_location = $info->location;
				$boxlist_bay = $info->bay;
				$boxlist_shelf = $info->shelf;
				$boxlist_il = $info->index_level;

				$tbl .= '
    <tr class="wpsc_tl_row_item">
            <td><a href="/wordpress3/data?id=' . $boxlist_id . '">' . $boxlist_id . '</a></td>
            <td>' . $boxlist_location . '</td>
            <td>' . $boxlist_bay . '</td>
            <td>' . $boxlist_shelf . '</td>
            <td>' . $boxlist_il . '</td>
            </tr>
            ';
			}
			$tbl .= '</tbody></table></div>';

			echo $tbl;
?>			

<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>
<script>
 jQuery(document).ready(function() {
	 jQuery('#tbl_templates_boxes').DataTable({
		 "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]]
		});
} );

</script>
