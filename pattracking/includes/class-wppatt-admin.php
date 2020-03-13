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
  
  }
  
endif;

new wppatt_Admin();