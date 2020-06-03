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
    
    public function addStyles(){    
        wp_register_style('wpsc-bootstrap-css', WPSC_PLUGIN_URL.'asset/css/bootstrap-iso.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-fa-css', WPSC_PLUGIN_URL.'asset/lib/font-awesome/css/all.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-jquery-ui', WPSC_PLUGIN_URL.'asset/css/jquery-ui.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-public-css', WPSC_PLUGIN_URL . 'asset/css/public.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-admin-css', WPSC_PLUGIN_URL . 'asset/css/admin.css?version='.WPSC_VERSION );
        wp_register_style('wpsc-modal-css', WPSC_PLUGIN_URL . 'asset/css/modal.css?version='.WPSC_VERSION );

        wp_enqueue_style('wpsc-bootstrap-css');
        wp_enqueue_style('wpsc-fa-css');
        wp_enqueue_style('wpsc-jquery-ui');
        wp_enqueue_style('wpsc-public-css');
        wp_enqueue_style('wpsc-admin-css');
        wp_enqueue_style('wpsc-modal-css');
    }
    
}  
endif;

$GLOBALS['wppattfunction'] =  new wppatt_Functions();
