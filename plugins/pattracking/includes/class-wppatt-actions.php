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
      
      // PATT Log Entry
      add_action( 'wpppatt_after_unauthorized_destruction', array($this,'unauthorized_destruction'), 10, 2 );
      add_action( 'wpppatt_after_unauthorized_destruction_unflag', array($this,'unauthorized_destruction_unflag'), 10, 2 );
      add_action( 'wpppatt_after_shelf_location', array($this,'shelf_location'), 10, 3 );   
      add_action( 'wpppatt_after_digitization_center', array($this,'digitization_center'), 10, 3 );   
      add_action( 'wpppatt_after_validate_document', array($this,'validate_document'), 10, 2 );
      add_action( 'wpppatt_after_invalidate_document', array($this,'invalidate_document'), 10, 2 );
      add_action( 'wpppatt_after_add_request_shipping_tracking', array($this,'add_request_shipping_tracking'), 10, 2 );
      add_action( 'wpppatt_after_modify_request_shipping_tracking', array($this,'modify_request_shipping_tracking'), 10, 2 );
      add_action( 'wpppatt_after_remove_request_shipping_tracking', array($this,'remove_request_shipping_tracking'), 10, 2 );
      
      add_action( 'wpppatt_after_box_metadata', array($this,'box_metadata'), 10, 3 );
      add_action( 'wpppatt_after_folder_doc_metadata', array($this,'folder_doc_metadata'), 10, 3 );
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
    
    // Reverse destruction
    function unauthorized_destruction_unflag ( $ticket_id, $doc_id ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s un-flagged Document ID: %2$s as unauthorize destruction','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $doc_id .'</strong>');
      } else {
        $log_str = sprintf( __('Document ID %1$s un-flagged as unauthorize destruction','supportcandy'), '<strong>'.$doc_id.'</strong>' );
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }
    
    // Change Shelf Location
    function shelf_location ( $ticket_id, $box_id, $shelf_id ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s changed shelf location of Box ID: %2$s to %3$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $box_id .'</strong>','<strong>'. $shelf_id .'</strong>');
      } else {
        $log_str = sprintf( __('Box ID: %1$s has changed shelf location to %1$s ','supportcandy'), '<strong>'.$box_id.'</strong>','<strong>'. $shelf_id .'</strong>' );
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }

    // Change Digitization Center
    function digitization_center ( $ticket_id, $box_id, $dc ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s changed digitization center of Box ID: %2$s to %3$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $box_id .'</strong>','<strong>'. $dc .'</strong>');
      } else {
        $log_str = sprintf( __('Box ID: %1$s has changed digitization center to %1$s ','supportcandy'), '<strong>'.$box_id.'</strong>','<strong>'. $dc .'</strong>' );
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }

    // Validate
    function validate_document ( $ticket_id, $doc_id ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s validated Document ID: %2$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $doc_id .'</strong>');
      } else {
        $log_str = sprintf( __('Document ID %1$s validated','supportcandy'), '<strong>'.$doc_id.'</strong>' );
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }

    // Invalidate
    function invalidate_document ( $ticket_id, $doc_id ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s invalidated Document ID: %2$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $doc_id .'</strong>');
      } else {
        $log_str = sprintf( __('Document ID %1$s invalidated','supportcandy'), '<strong>'.$doc_id.'</strong>' );
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }
    
    // Add Shipping Tracking on Request
    function add_request_shipping_tracking ( $ticket_id, $tracking_number ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s added tracking number %2$s to request','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $tracking_number .'</strong>');
      } else {
        $log_str = sprintf( __('Tracking number %1$s added to request','supportcandy'), '<strong>'.$tracking_number.'</strong>' );
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }

    // Modified Shipping Tracking on Request
    function modify_request_shipping_tracking ( $ticket_id, $tracking_number ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s updated tracking number %2$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $tracking_number .'</strong>');
      } else {
        $log_str = sprintf( __('Tracking number %1$s updated','supportcandy'), '<strong>'.$tracking_number.'</strong>' );
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }
    // Removed Shipping Tracking on Request
    function remove_request_shipping_tracking ( $ticket_id, $tracking_number ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s removed tracking number %2$s from request','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $tracking_number .'</strong>');
      } else {
        $log_str = sprintf( __('Tracking number %1$s removed from request','supportcandy'), '<strong>'.$tracking_number.'</strong>' );
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }
    // Box Metadata edit
    function box_metadata ( $ticket_id, $metadata, $box_id ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s edited the following metadata %2$s on Box ID: %3$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $metadata .'</strong>','<strong>'. $box_id .'</strong>');
      } else {
        $log_str = sprintf( __('The following metadata %1$s on Box ID: %2$s has been edited','supportcandy'), '<strong>'.$metadata.'</strong>','<strong>'. $box_id .'</strong>');
      }
      $args = array(
        'ticket_id'      => $ticket_id,
        'reply_body'     => $log_str,
        'thread_type'    => 'log'
      );
      $args = apply_filters( 'wpsc_thread_args', $args );
      $wpscfunction->submit_ticket_thread($args);
    }
    // Folder/Document Metadata edit
    function folder_doc_metadata ( $ticket_id, $metadata, $doc_id ){
      global $wpscfunction, $current_user;
      if($current_user->ID){
        $log_str = sprintf( __('%1$s edited the following metadata %2$s on Document ID: %3$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'. $metadata .'</strong>','<strong>'. $doc_id .'</strong>');
      } else {
        $log_str = sprintf( __('The following metadata %1$s on Document ID: %2$s has been edited','supportcandy'), '<strong>'.$metadata.'</strong>','<strong>'. $doc_id .'</strong>');
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
