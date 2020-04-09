<?php 
/**
 * Plugin Name: pattracking
 * Description: add-on to the support candy plugin specifically for the EPA Paper Asset Tracking Tool
 * Version: 2.1.1
 * Requires at least: 4.4
 * Tested up to: 5.3
 * Text Domain: pattracking
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'Patt_Tracking' ) ) :
  
  final class Patt_Tracking {
  
      
    public $version    = '2.1.1';
    public $db_version = '2.0';
    
    public function __construct() {
        
        // define global constants
        $this->define_constants();
        
        // Include required files and classes
        $this->includes();

        add_action( 'init', array($this,'load_textdomain') );
        
        /*
         * Cron setup
         */
        $cron_job_schedule = get_option('wppatt_cron_job_schedule_setup');
        if($cron_job_schedule) {
          add_filter('cron_schedules',array( $this, 'wppatt_cron_schedule'));
          if (!wp_next_scheduled('wppatt_cron_job_schedules')) {
              wp_schedule_event(time(), 'wppatt5min', 'wppatt_cron_job_schedules');
          }
          
          include( wppatt_ABSPATH.'includes/class-wp-cron.php' );
          $cron=new wppattWPCron();
          add_action( 'wppatt_cron_job_schedules', array( $cron, 'wppatt_cron_job'));
        }   
        
        /**
         * Attachment restructure
         */
        $restuct_attach = get_option('wppatt_restructured_attach_completed', 0);
        if( !$restuct_attach ){
          if (! wp_next_scheduled ( 'wppatt_attachment_restructure')) {
            wp_schedule_event( time(), 'hourly', 'wppatt_attachment_restructure');
          }
        }
    }
    
    function define_constants() {
        $this->define('WPPATT_STORE_URL', 'https://supportcandy.net');
        $this->define('WPPATT_PLUGIN_FILE', __FILE__);
        $this->define('WPPATT_ABSPATH', dirname(__FILE__) . '/');
        $this->define('WPPATT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        $this->define('WPPATT_PLUGIN_BASENAME', plugin_basename(__FILE__));
        $this->define('WPPATT_VERSION', $this->version);
        $this->define('WPPATT_DB_VERSION', $this->db_version);
    }
    
    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'pattracking' );
        load_textdomain( 'pattracking', WP_LANG_DIR . '/supportcandy/supportcandy-' . $locale . '.mo' );
        load_plugin_textdomain( 'pattracking', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }
    
    public function includes() {
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-install.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-ajax.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-functions.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-actions.php' );
        include_once( WPPATT_ABSPATH . 'includes/rest_api/class-rest-child.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-abstraction.php' );
        include_once( WPPATT_ABSPATH . 'includes/class-wppatt-hooks-filters.php' );
        $frontend  = new wppatt_Functions();
        // Add PATT Query Shortcode
        add_shortcode('wppattquery', array($frontend, 'get_id_details'));
        
        if ($this->is_request('admin')) {
          include_once( WPPATT_ABSPATH . 'includes/class-wppatt-admin.php' );
          
          update_option('wpsc_tl_agent_unresolve_statuses',array(3,4,5,63,64,6,65));
          update_option('wpsc_tl_customer_unresolve_statuses',array(3,4,5,63,64,6,65));
          update_option('wpsc_close_ticket_group',array(66,67,68,69));
          
          // PDF Label Add Button
          $backend  = new wppatt_Admin();
          add_action('wpsc_after_indidual_ticket_action_btn', array($backend, 'pdflabel_btnAfterClone'));
          add_action('wp_ajax_wpsc_get_pdf_label_field', array($backend, 'get_pdf_label_field'));
          // Add Shipping Widget
          add_action( 'wpsc_after_ticket_widget', array($backend, 'shipping_widget'));
          add_action('wp_ajax_wpsc_get_shipping_details', array($backend, 'get_shipping_details'));
          // Add Shipping CRON
          add_action( 'wppatt_shipping_cron', array($backend, 'wpatt_shipping_cron_schedule'));
          // Disable Show Agent Settings Button
          add_action('wpsc_show_agent_setting_button',false);
          
          // Set Barcode Scanning Page
          add_action( 'wpsc_add_submenu_page', 'my_admin_menu');

          function my_admin_menu() {
            add_submenu_page( 'wpsc-tickets', 'Barcode Scanning', 'Barcode Scanning', 'wpsc_agent', 'scanning', 'scanning_page' );
            }

          function scanning_page(){
            include_once( WPPATT_ABSPATH . 'includes/admin/pages/scanning.php' );
            }
    
        }
        if ($this->is_request('frontend')) {
          include_once( WPPATT_ABSPATH . 'includes/class-wppatt-frontend.php' );
        }
        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
          include_once( WPPATT_ABSPATH . 'includes/EDD_SL_Plugin_Updater.php' );
        }
        
    }
    
    private function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }
    
    private function is_request($type) {
        switch ($type) {
            case 'admin' :
                return is_admin();
            case 'frontend' :
                return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
        }
    }
    
    function wppatt_cron_schedule($schedules){
        if(!isset($schedules["wppattsc5min"])){
            $schedules["wppatt5min"] = array(
                'interval' => 5*60,
                'display'  => 'Once every 5 minute',
            );
        }
        return $schedules;
    }
    
  }
  
endif;

new Patt_Tracking();
