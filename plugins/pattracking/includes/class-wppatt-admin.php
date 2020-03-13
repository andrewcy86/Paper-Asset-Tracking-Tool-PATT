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

	global $current_user;

	//if ( ! $current_user->has_cap( 'wpsc_agent' ) ) {	// Only show widget for agents.
	//	return;
	//}

	$ticket_widget_name = __( 'Shipping', 'supportcandy' );

	$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

	echo '<div class="row" style="';
	echo 'background-color:' . $wpsc_appearance_individual_ticket_page[ 'wpsc_ticket_widgets_bg_color' ] . ' !important;';
	echo 'color:' . $wpsc_appearance_individual_ticket_page[ 'wpsc_ticket_widgets_text_color' ] . ' !important;';
	echo 'border-color:' . $wpsc_appearance_individual_ticket_page[ 'wpsc_ticket_widgets_border_color' ] . ' !important;';
	echo '">';

	echo '<h4 class="widget_header"><i class="fa fa-truck"></i> ' . $ticket_widget_name . '</h4>';
	echo '<hr class="widget_devider">';

    echo '<ul>
    <li><i class="fab fa-fedex fa-2x"></i> <a href="https://www.fedex.com/apps/fedextrack/?tracknumbers=773885080540" target="_blank">773885080540</a></li>
    </ul>
    <p><i class="fas fa-plus-square"></i> View/Edit Shipping Info</p>';

	echo '</div>';
}

    
  }
  
endif;

new wppatt_Admin();