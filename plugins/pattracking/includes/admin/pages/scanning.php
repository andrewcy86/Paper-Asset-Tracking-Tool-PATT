<!--  -->
<?php
    
    if ( ! defined( 'ABSPATH' ) ) {
    	exit; // Exit if accessed directly
    }
    
    global $current_user,$wpscfunction;
    
    $agent_permissions = $wpscfunction->get_current_agent_permissions();
    
    if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
          {
          echo "This is a message for managers of PATT.";
          }
          
?>

<?php
// Code to add ID lookup
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly

}

global $current_user, $wpscfunction, $wpdb;

$GLOBALS['id'] = $_GET['id'];

$id = $GLOBALS['id'];
$dash_count = substr_count($id, '-');

wp_enqueue_script('jquery');

wp_register_script('dataTables-js', 'https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js', '', '', true);
wp_register_script('dataTables-responsive-js', 'https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js', '', '', true);
wp_register_script('customScriptDatatables', WPPATT_PLUGIN_URL . 'includes/admin/js/customScriptDatatables.js', '', '', true);


// JM Developer - register scanning page js
wp_register_script('customScanning-js', WPPATT_PLUGIN_URL . 'includes/admin/js/scanning.js', '', '', true);


wp_enqueue_script('dataTables-js');
wp_enqueue_script('dataTables-responsive-js');
wp_enqueue_script('customScriptDatatables');

// JM Developer - enqueue js
wp_enqueue_script('customScanning-js');


wp_enqueue_style('wpsc-fa-css', WPSC_PLUGIN_URL.'asset/lib/font-awesome/css/all.css?version='.WPSC_VERSION );

echo '<link rel="stylesheet" type="text/css" href="' . WPSC_PLUGIN_URL . 'asset/lib/DataTables/datatables.min.css"/>';
echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css"/>';

// JM Developer - echo Link to CSS  
echo '<link rel="stylesheet" type="text/css" href="' . WPPATT_PLUGIN_URL . 'includes/admin/css/scan-table.css"/>';


?>
        <div>
            <H3>Barcode Scanning</H3>
        </div>

        <div id="wpsc_tickets_container" class="row" style="border-color:#1C5D8A !important;"><div class="row wpsc_tl_action_bar" style="background-color:#1C5D8A !important;">
            <div class="row wpsc_tl_action_bar" style="background-color:#1C5D8A !important;">
                <div class="col-sm-12">            
                    <button class="btn btn-sm pull-right" type="button" id="wpsc_sign_out" onclick="window.location.href='http://086.info/wordpress3/wp-login.php?action=logout&amp;redirect_to=http%3A%2F%2F086.info%2Fwordpress3%2Fsupport-ticket%2F&amp;_wpnonce=fe1da2483c'" style=" background-color:#FF5733 !important;color:#FFFFFF !important;">
                        <i class="fas fa-sign-out-alt">
                        </i>    
                            Log Out
                    </button>
                </div>  
            </div>
        </div>
            <form action="#" method="post"> 
                <div class="row" id="scan_input_row">
                    
                    <div class="column left" id="container-scaninfo-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >
                
                        <div class="justified">
                            <div>
                                <h2>
                                    <label for="boxid-scan">
                                        Box ID(s)
                                    </label>
                                </h2>
                                <br/>
                                    <textarea name='boxid-textarea[]' id="boxid-scan" rows="15" cols="15">
                                           0000001-1
                                           0000001-2
                                           0000001-3
                                    </textarea>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column middle" id="container-scaninfo-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >
                        
                        <div class="justified">
                            <div>
                                <div>
                                    <h2>
                                        <label for="location-scan">
                                            Scan Location
                                        </label>
                                    </h2>
                                    <br/>
                                    <br/>
                                    <input id="scanInput" />
                                    <button id="reset">Reset</button>
                                </div>
                                <br/>
                                <div>
                                     <h2>Event Information</h2>
                                        Start: <span id="startTime"></span> 
                                    <br/>First Key: <span id="firstKey"></span> 
                                    <br/>Last Ley: <span id="lastKey"></span> 
                                    <br/>End: <span id="endTime"></span> 
                                    <br/>Elapsed: <span id="totalTime"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column right" id="submit-scan-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >
                
                        <div class="justified">
                            <div>
                                <div style="text-align:center">
                                    
                                    <h2>
                                        <label for="submit-scan">
                                            Submit
                                        </label>
                                    </h2>
                                    <br/>
                                    <input type="submit">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!-- JM Developer End -->





