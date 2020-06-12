<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPPATT_Actions' ) ) :
  
  final class wppatt_Actions {

    // constructor
    public function __construct() {
      add_action( 'init', array( $this, 'load_actions') );
      add_action( 'plugins_loaded', array( $this, 'check_download_file'), 1 );
    }
    
     // Load actions
    function load_actions() {
      
      // Log Entry
      add_action( 'wpppatt_after_unauthorized_destruction', array($this,'unauthorized_destruction'), 10, 2 );
      
    }
    
    // Change destruction
    function unauthorized_destruction ( $ticket_id, $doc_id ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s flagged Document ID: %2$s as unauthorize destruction','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $doc_id .'</strong>');
      } else {
        $log_str = sprintf( __('Document ID %1$s flagged as unauthorize destruction','supportcandy'), '<strong>'.$doc_id.'</strong>' );
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }
    
}
  
endif;

new wppatt_Actions();
