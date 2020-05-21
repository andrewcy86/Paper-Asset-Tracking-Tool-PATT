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
    
    // CRON for shipping
    public function wpatt_shipping_cron_schedule(){    
    include WPPATT_ABSPATH . 'includes/admin/shipping_cron.php';
    }
    
    // CRON for ecms
    public function wpatt_ecms_cron_schedule(){    
    include WPPATT_ABSPATH . 'includes/admin/ecms_cron.php';
    }
    
}  
endif;

$GLOBALS['wppattfunction'] =  new wppatt_Functions();
