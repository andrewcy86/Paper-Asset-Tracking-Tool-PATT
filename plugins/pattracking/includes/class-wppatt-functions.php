<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'wppatt_Functions' ) ) :
  
  final class wppatt_Functions {
      
    // Shortcode Query Component
    public function get_id_details(){    
    include WPPATT_ABSPATH . 'includes/admin/get_id_details.php';
    }
    
}  
endif;

$GLOBALS['wppattfunction'] =  new wppatt_Functions();
