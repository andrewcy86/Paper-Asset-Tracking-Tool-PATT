<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'wppatt_Admin' ) ) :
  
  final class wppatt_Admin {
    // Added function to inject label button
    public function pdflabel_btnAfterClone(){
    include WPPATT_ABSPATH . 'includes/admin/wppatt_get_pdflabel_file.php';    
    }
    
    public function get_pdf_label_field(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_pdf_label_field.php';
    die();
    }
    
    // Added function to create a shipping ticket widget
    public function shipping_widget( $post_id ) {

	global $current_user, $wpscfunction,$wpdb;
	
	$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
	$ticket_data = $wpscfunction->get_ticket($ticket_id);
	$status_id   	= $ticket_data['ticket_status'];

    $get_shipping_count = $wpdb->get_var('SELECT COUNT(*) FROM ' .$wpdb->prefix .'wpsc_epa_shipping_tracking WHERE ticket_id = ' . $ticket_id );
     
	//if ( ! $current_user->has_cap( 'wpsc_agent' ) ) {	// Only show widget for agents.
	//	return;
	//}

	//echo $status_id;
if ($status_id != 3) {
	$ticket_widget_name = __( 'Shipping', 'supportcandy' );

	$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

	echo '<div class="row" style="';
	echo 'background-color:' . $wpsc_appearance_individual_ticket_page[ 'wpsc_ticket_widgets_bg_color' ] . ' !important;';
	echo 'color:' . $wpsc_appearance_individual_ticket_page[ 'wpsc_ticket_widgets_text_color' ] . ' !important;';
	echo 'border-color:' . $wpsc_appearance_individual_ticket_page[ 'wpsc_ticket_widgets_border_color' ] . ' !important;';
	echo '">';

	echo '<h4 class="widget_header"><i class="fa fa-truck"></i> ' . $ticket_widget_name . '</h4>';
	echo '<hr class="widget_devider">';
	
  if ($get_shipping_count > 0) {
      
    echo '<ul>';


    $shipping_rows = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix .'wpsc_epa_shipping_tracking WHERE ticket_id = ' . $ticket_id );
    
    $i = 0;

    foreach( $shipping_rows as $row) {

    $tracking_num = $row->tracking_number;
    $tracking_num_display = mb_strimwidth($tracking_num, 0, 25, "...");
    $company_name = $row->company_name;

switch ($company_name) {
    case "ups":
        echo '<li><i class="fab fa-ups fa-lg"></i> <a href="https://www.ups.com/track?loc=en_US&tracknum=' . $tracking_num . '" target="_blank">'. $tracking_num_display .'</a> </li>';
        break;
    case "fedex":
        echo '<li><i class="fab fa-fedex fa-lg"></i> <a href="https://www.fedex.com/apps/fedextrack/?tracknumbers=' . $tracking_num . '" target="_blank">'. $tracking_num_display .'</a></li>';
        break;
    case "usps":
        echo '<li><i class="fab fa-usps fa-lg"></i> <a href="https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=' . $tracking_num . '" target="_blank">'. $tracking_num_display .'</a></li>';
        break;
    case "dhl":
        echo '<li><i class="fab fa-dhl fa-lg"></i> <a href="https://www.logistics.dhl/global-en/home/tracking.html?tracking-id=' . $tracking_num . '" target="_blank">'. $tracking_num_display .'</a></li>';
        break;
    default:
        echo $tracking_num;

}
    if (++$i == 10) break;
    }
    echo '</ul>';
     if ($get_shipping_count > 10) {echo '... <a href="#" onclick="wpsc_get_shipping_details(' . $ticket_id . ')">[View More]</a>';}
  } else {
    echo '<strong>No Tracking Numbers Assigned.</strong><br /><br />';
  }
    ?>
    
    <p><a href="#" onclick="wpsc_get_shipping_details(<?php echo $ticket_id?>)"><i class="fas fa-plus-square"></i> View/Edit Shipping Info</a></p>

	<script>
		function wpsc_get_shipping_details(ticket_id){
		  wpsc_modal_open('Shipping Details');
		  var data = {
		    action: 'wpsc_get_shipping_details',
		    ticket_id: ticket_id
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  });  
		}
	</script>
	
	</div>
	<?php
	}
}


    public function get_shipping_details(){    
    include WPPATT_ABSPATH . 'includes/ajax/get_shipping_details.php';
    die();
    }

    
  }
  
endif;

new wppatt_Admin();