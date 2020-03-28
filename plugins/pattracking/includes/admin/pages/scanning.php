<?php
define('WPSC_PLUGIN_URL', 'asset/css/'); //define CSS path 
define('WPSC_JS_PATH', 'plugins/scanning/asset/js/'); //define JavaScript path 
?>

<!-- JM Developer - Begin Dev Man Content -->
<!DOCTYPE html> 
<html>
    <head>

        <title>
            <H3>Scanning</H3>
        </title>
        <link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL; ?>scan-table.css">
        <script type="text/javascript" src="<?php echo WPSC_JS_PATH; ?>scanning.js"></script>
             
    </head>
    
<!-- JM Developer - Link to CSS using WPSC_PLUGIN_URL --> 
<body>
    <div>
        <form>
            <input id="scanInput" />
            <button id="reset">Reset</button>
        </form>
        <br/>
        <div>
            <h2>Event Information</h2>
            Start: <span id="startTime"></span> 
            <br/>First Key: <span id="firstKey"></span> 
            <br/>Last Ley: <span id="lastKey"></span> 
            <br/>End: <span id="endTime"></span> 
            <br/>Elapsed: <span id="totalTime"></span>
        </div>
        <div>
            <h2>Results</h2>
        
            <div id="resultsList"></div>
        </div>

        <div>
            <table class="wpsc_scan_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Bay</th>
                        <th>Shelf</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="3">
                            <div class="links"><a href="#">&laquo;</a> <a class="active" href="#">1</a> <a href="#">2</a> <a href="#">3</a> <a href="#">4</a> <a href="#">&raquo;</a></div>
                        </td>
                    </tr>
                </tfoot>
                <tbody>
                    <tr>
                        <td>cell1_1</td>
                        <td>cell2_1</td>
                        <td>cell3_1</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

<!-- JM Developer End -->

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


