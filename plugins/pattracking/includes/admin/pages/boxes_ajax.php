<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$setting_action =  isset($_REQUEST['setting_action']) ? sanitize_text_field($_REQUEST['setting_action']) : '';

switch ($setting_action) {
  
  case 'init': include WPPATT_PLUGIN_URL . 'includes/admin/pages/patt_init.php';
    break;
		
	case 'sign_in': include WPSC_PLUGIN_URL . 'includes/admin/pages/sign_in/sign_in.php';
    break;
		
	case 'ticket_list': include WPSC_PLUGIN_URL . 'includes/admin/pages/ticket_list/ticket_list.php';
    break;

  default:
    _e('Invalid Action','supportcandy');
    break;
    
}
