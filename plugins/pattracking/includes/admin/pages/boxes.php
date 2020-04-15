<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/*wp_enqueue_scripts('wpsc-public', WPPATT_PLUGIN_URL.'asset/js/public.js?version='.WPSC_VERSION, array('jquery'));
wp_enqueue_scripts('wpsc-modal', WPPATT_ABSPATH.'asset/js/modal.js?version='.WPSC_VERSION, array('jquery'));*/

/*echo '<script type="text/javascript" href="' . WPPATT_PLUGIN_URL . 'asset/js/admin.js"/>';
echo '<script type="text/javascript" href="' . WPPATT_PLUGIN_URL . 'asset/js/public.js"/>';
echo '<script type="text/javascript" href="' . WPPATT_PLUGIN_URL . 'asset/js/modal.js"/>';
echo '<link rel="stylesheet" type="text/css" href="' . WPPATT_PLUGIN_URL . 'asset/css/bootstrap-iso.css"/>';
echo '<link rel="stylesheet" type="text/css" href="' . WPPATT_PLUGIN_URL . 'asset/css/public.css"/>';
echo '<link rel="stylesheet" type="text/css" href="' . WPPATT_PLUGIN_URL . 'asset/css/admin.css"/>';
echo '<link rel="stylesheet" type="text/css" href="' . WPPATT_PLUGIN_URL . 'asset/css/modal.css"/>'; */


// register jquery and style on initialization
/*add_action('init', 'register_script');
function register_script() {
    wp_register_script('public-js', WPPATT_PLUGIN_URL . 'asset/js/public.js', '', '', false);

   // wp_register_style( 'new_style', plugins_url('/css/new-style.css', __FILE__), false, '1.0.0', 'all');
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueue_style');

function enqueue_style(){
   wp_enqueue_script('public-js');

  // wp_enqueue_style( 'new_style' );
}


function add_my_css_and_my_js_files(){
        wp_enqueue_script('your-script-name', $this->urlpath  . '/your-script-filename.js', array('jquery'), '1.2.3', true);
        wp_enqueue_style( 'your-stylesheet-name', plugins_url('/css/new-style.css', __FILE__), false, '1.0.0', 'all');
    }
    add_action('wp_enqueue_scripts', "add_my_css_and_my_js_files");*/


// register jquery and style on initialization
/*add_action('init', 'register_script');
function register_script() {
    wp_register_script( 'custom_jquery', plugins_url('/js/custom-jquery.js', __FILE__), array('jquery'), '2.5.1' );

    wp_register_style( 'new_style', plugins_url('/css/new-style.css', __FILE__), false, '1.0.0', 'all');
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueue_style');

function enqueue_style(){
   wp_enqueue_script('custom_jquery');

   wp_enqueue_style( 'new_style' );
}
*/


//Register The scripts 
// wp_enqueue_script('jquery');

wp_register_script('admin-js', WPPATT_PLUGIN_URL . 'asset/js/admin.js', '', '', false);
wp_register_script('public-js', WPPATT_PLUGIN_URL . 'asset/js/pattpublic.js', '', '', false);
wp_register_script('modal-js', WPPATT_PLUGIN_URL . 'asset/js/modal.js', '', '', true);
// wp_register_script('dataTables-responsive-js', 'https://cdn.datatables.net/responsive/2.2.3/js/responsive.dataTables.min.js
// ', '', '', false);
// wp_register_script('customScriptDatatables', plugins_url('pattracking/includes/admin/js/customScriptDatatables.js', '', false));

wp_enqueue_script('admin-js', WPPATT_PLUGIN_URL . 'asset/js/admin.js', '', '', true);
wp_enqueue_script('public-js', WPPATT_PLUGIN_URL . 'asset/js/pattpublic.js', '', '', true);
wp_enqueue_script('modal-js', WPPATT_PLUGIN_URL . 'asset/js/modal.js', '', '', true);

/*https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css
https://cdn.datatables.net/responsive/2.2.3/js/responsive.dataTables.min.js*/

//Enqueue The scripts 

/*function load_custom_wp_admin_style(){
    wp_register_script('admin-js', WPPATT_PLUGIN_URL . 'asset/js/admin.js', '', '', true);
    wp_enqueue_script( 'admin-js' );
}
add_action('wp_enqueue_scripts', 'load_custom_wp_admin_style');*/

/*admin_enqueue_scripts('admin-js', WPPATT_PLUGIN_URL . 'asset/js/admin.js', '', '', true);
admin_enqueue_scripts('public-js', WPPATT_PLUGIN_URL . 'asset/js/public.js', '', '', true);
admin_enqueue_scripts('modal-js', WPPATT_PLUGIN_URL . 'asset/js/modal.js', '', '', true);*/

/*wp_enqueue_script('dataTables-responsive-js');
wp_enqueue_script('customScriptDatatables');
wp_enqueue_style('wpsc-fa-css', WPSC_PLUGIN_URL.'asset/lib/font-awesome/css/all.css?version='.WPSC_VERSION );
*/


$general_appearance = get_option('wpsc_appearance_general_settings');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
?>

<div class="bootstrap-iso">
  
  <h3>
    <?php _e('Boxes','supportcandy');?>
  </h3>
  
  <div id="wppatt_boxes_container" class="row" style="border-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;"></div>
  
  <div id="wpsc_alert_success" class="alert alert-success wpsc_alert" style="display:none;" role="alert">
    <i class="fa fa-check-circle"></i> <span class="wpsc_alert_text"></span>
  </div>
  
  <div id="wpsc_alert_error" class="alert alert-danger wpsc_alert" style="display:none;" role="alert">
    <i class="fa fa-exclamation-triangle"></i> <span class="wpsc_alert_text"></span>
  </div>
  
</div>

<!-- Pop-up snippet start -->
<div id="wpsc_popup_background" style="display:none;"></div>
<div id="wpsc_popup_container" style="display:none;">
  <div class="bootstrap-iso">
    <div class="row">
      <div id="wpsc_popup" class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
        <div id="wpsc_popup_title" class="row" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_header_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_header_text_color']?> !important;"><h3><?php _e('Modal Title','supportcandy');?></h3></div>
        <div id="wpsc_popup_body" class="row"><?php _e('I am body!','supportcandy');?></div>
        <div id="wpsc_popup_footer" class="row" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_footer_bg_color']?> !important;">
          <button type="button" class="btn wpsc_popup_close" ><?php _e('Close','supportcandy');?></button>
          <button type="button" class="btn wpsc_popup_action"><?php _e('Save Changes','supportcandy');?></button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Pop-up snippet end -->



<!-- BEGIN CAR - Added Custom PATT Action -->
<?php do_action('patt_print_js_tickets_page'); ?>
<!-- END CAR - Added Custom PATT Action -->
<?php
add_action('admin_footer', 'wpsc_page_inline_script');
global $attrs;
$attrs = isset($attr['page'])? $attr['page']:'init';
?>
<script>
  var wpsc_setting_action = '<?php echo $attrs?>';
</script>

<?php
function wpsc_page_inline_script(){
  ?>
  <script>
  <?php
     $url_attrs = array();
     foreach ($_GET as $key => $value) {
       $url_attrs[] = '"'.$key.'":"'.$value.'"';
     }
     $url_attrs = '{'.implode(',',$url_attrs).'}'
  ?>
       var attrs = <?php echo $url_attrs?>;
       jQuery(document).ready(function(){
         wpsc_init(wpsc_setting_action,attrs);
       });
  </script>
  <?php
}
?>