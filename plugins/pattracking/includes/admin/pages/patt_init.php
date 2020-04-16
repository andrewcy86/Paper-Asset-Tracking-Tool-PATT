<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$support_page = isset($_REQUEST['support_page']) ? sanitize_text_field($_REQUEST['support_page']) : '';
$support_page = $support_page ? $support_page : 'boxes_list';

switch ($support_page) {
  
  case 'boxes_list': include WPSC_ABSPATH . 'includes/admin/pages/boxes_list.php';
    break;
		
	case 'open_ticket': include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/individual_ticket.php';
    break;
		
	case 'create_ticket': include WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/create_ticket.php';
    break;
  
  default:
    _e('Invalid Action','supportcandy');
    break;
  
}