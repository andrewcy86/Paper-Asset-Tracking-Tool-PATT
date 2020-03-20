<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$setting_action =  isset($_REQUEST['setting_action']) ? sanitize_text_field($_REQUEST['setting_action']) : '';

switch ($setting_action) {
  
  case 'init': include WPSC_ABSPATH . 'includes/admin/tickets/init.php';
    break;
		
	case 'get_box_list': include WPSC_ABSPATH . 'includes/admin/boxes/get_box_list.php';
    break;
		

  default:
    _e('Invalid Action','supportcandy');
    break;
    
}
