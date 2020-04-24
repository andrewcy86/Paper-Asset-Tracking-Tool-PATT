  <!-- JM Developer

TODO:  Add input mask for validating if the miltiline textarea 
        so that it only Box ID's can be added to the area.
        
        -->
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


<script>

        /* JM - 4/24/2020 - Clear buttons for the input and textarea*/
        function clearValue(id) {
       
            var elem = "";
            
            /* Get the scan input input box and clear the contents */
            if(id === "resetscanbtn"){
                elem = "scan-input"
                document.getElementById(id).value = "";
                resetValues();
            }
            
            /* Get the boxid textarea  and clear the contents */
            if(id === "resetboxidbtn"){
                
                elem = "boxid-textarea";
                document.getElementById(id).value = "";
            }
        }

        /* JM - 4/24/2020 - Submit button actions */
        $('#scanform button[name=submitbtn]').click(function(e){
              e.preventDefault();
              $('.confirmation').show();
              
              //setTimeout(function(){
              //  $('#scanform').submit();
              //}, 5000);
        });
        
        
        /* This code controls the menu look and feel */
         jQuery(document).ready(function() {
        	 jQuery('#toplevel_page_wpsc-tickets').removeClass('wp-not-current-submenu'); 
        	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-has-current-submenu'); 
        	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-menu-open'); 
        	 jQuery('#toplevel_page_wpsc-tickets a:first').removeClass('wp-not-current-submenu');
        	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-has-current-submenu'); 
        	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-menu-open');
        	 jQuery('.wp-first-item').addClass('current'); 
        	 jQuery('#menu-dashboard').removeClass('current');
        	 jQuery('#menu-dashboard a:first').removeClass('current');
        } );

                           /*
                This code will determine when a code has been either entered manually or
                entered using a scanner.
                It assumes that a code has finished being entered when one of the following
                events occurs:
                    • The enter key (keycode 13) is input
                    • The input has a minumum length of text and loses focus
                    • Input stops after being entered very fast (assumed to be a scanner)
            */
            
            var inputStart, inputStop, firstKey, lastKey, timing, userFinishedEntering;
            var minChars = 3;
            
            // handle a key value being entered by either keyboard or scanner
            jQuery("#scanInput").keypress(function (e) {
                // restart the timer
                if (timing) {
                    clearTimeout(timing);
                }
                
                // handle the key event
                if (e.which == 13) {
                    // Enter key was entered
                    
                    // don't submit the form
                    e.preventDefault();
                    
                    // has the user finished entering manually?
                    if (jQuery("#scanInput").val().length >= minChars){
                        userFinishedEntering = true; // incase the user pressed the enter key
                        inputComplete();
                    }
                }
                else {
                    // some other key value was entered
                    
                    // could be the last character
                    inputStop = performance.now();
                    lastKey = e.which;
                    
                    // don't assume it's finished just yet
                    userFinishedEntering = false;
                    
                    // is this the first character?
                    if (!inputStart) {
                        firstKey = e.which;
                        inputStart = inputStop;
                        
                        // watch for a loss of focus
                        jQuery("body").on("blur", "#scanInput", inputBlur);
                    }
                    
                    // start the timer again
                    timing = setTimeout(inputTimeoutHandler, 500);
                }
            });
            
            // Assume that a loss of focus means the value has finished being entered
            function inputBlur(){
                clearTimeout(timing);
                if (jQuery("#scanInput").val().length >= minChars){
                    userFinishedEntering = true;
                    inputComplete();
                }
            }
            
            // JM - 4/24/2020 - Commented out to use the clearValue() method instead
            // reset the page
            // jQuery("#resetscanbtn").click(function (e) {
            //    e.preventDefault();
            //    resetValues();
            //});
            
            // JM - 4/24/2020 - Commented out all clear value variable resets to remove 
            // the elements from the page.
            function resetValues() {
                // clear the variables
                //inputStart = null;
                //inputStop = null;
                //firstKey = null;
                //lastKey = null;
                // clear the results
                inputComplete();
                
                // JM - 4/24/2020 - Clearing the inputbox
                //$('input[name="scanInput"]').val('');
                
            }
              
            // Assume that it is from the scanner if it was entered really fast
            function isScannerInput() {
                return (((inputStop - inputStart) / jQuery("#scanInput").val().length) < 15);
            }
            
            // Determine if the user is just typing slowly
            function isUserFinishedEntering(){
                return !isScannerInput() && userFinishedEntering;
            }
            
            function inputTimeoutHandler(){
                // stop listening for a timer event
                clearTimeout(timing);
                // if the value is being entered manually and hasn't finished being entered
                if (!isUserFinishedEntering() || jQuery("#scanInput").val().length < 3) {
                    // keep waiting for input
                    return;
                }
                else{
                    reportValues();
                }
            }
            
            // here we decide what to do now that we know a value has been completely entered
            function inputComplete(){
                // stop listening for the input to lose focus
                jQuery("body").off("blur", "#scanInput", inputBlur);
                // report the results
                reportValues();
            }
            
            // JM - 4/24/2020 - Commented out all clear value variable resets to remove 
            // the elements from the page.
            function reportValues() {
                // update the metrics
                //jQuery("#startTime").text(inputStart === null ? "" : inputStart);
                //jQuery("#firstKey").text(firstKey === null ? "" : firstKey);
                //jQuery("#endTime").text(inputStop === null ? "" : inputStop);
                //jQuery("#lastKey").text(lastKey === null ? "" : lastKey);
                //jQuery("#totalTime").text(inputStart === null ? "" : (inputStop - inputStart) + " milliseconds");
                if (!inputStart) {
                    // clear the results
                    //jQuery("#resultsList").html("");
                    jQuery("#scanInput").focus().select();
                } else {
                    // prepend another result item
                    //var inputMethod = isScannerInput() ? "Scanner" : "Keyboard";
                    //jQuery("#resultsList").prepend("<div class='resultItem " + inputMethod + "'>" +
                    //    "<span>Value: " + jQuery("#scanInput").val() + "<br/>" +
                    //    "<span>ms/char: " + ((inputStop - inputStart) / jQuery("#scanInput").val().length) + "</span></br>" +
                    //    "<span>InputMethod: <strong>" + inputMethod + "</strong></span></br>" +
                    //    "</span></div></br>");
                    jQuery("#scanInput").focus().select();
                    inputStart = null;
                }
            }
            
            jQuery("#scanInput").focus();           
</script>


<?php
// Code to add ID lookup
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly

}

global $current_user, $wpscfunction, $wpdb;

$GLOBALS['id'] = $_GET['id'];

$id = $GLOBALS['id'];
$dash_count = substr_count($id, '-');

// JM - 4/23/2020 - Added custom style function for bootstrap 
include_once WPPATT_ABSPATH . 'includes/class-wppatt-functions.php';
$load_styles = new wppatt_Functions();
$load_styles->addStyles(); 

$general_appearance = get_option('wpsc_appearance_general_settings');

$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');

$edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';


// JM Developer - echo Link to CSS  
echo '<link rel="stylesheet" type="text/css" href="' . WPPATT_PLUGIN_URL . 'includes/admin/css/scan-table.css"/>';

?>


<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>

    <div class="bootstrap-iso">
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
            <form id="scanform" action="#" method="post"> 
                <div class="row" id="scan_input_row">
                    
                    <div class="column left" id="container-scaninfo-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >
                
                        <div class="justified">
                            <div class="dvboxid-layer">
                                <div>
                                    <h2>
                                        <label for="boxid-scan">
                                            Box ID(s)
                                        </label>
                                    </h2>
                                    <br/>
                                    <textarea id="boxid-textarea" name='boxid-textarea[]' pattern="(\d\d\d\d\d\d\d-\d{1,})" title="The Box ID must consist of 7 numbers representing the Request ID and any number of digits after the hyphen (-)." id="boxid-scan" rows="15" cols="15">

                                    </textarea>  
                                </div>
                                <div class="validationbtns">
                                    <input name="resetboxidbtn" type="button" value="Reset" onclick="clearValue()" />
                                    <input name="next-to-scan-btn" type="button" value="Next">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column middle" id="container-scaninfo-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >
                        
                        <div class="justified">
                            <div clas="dvscanner-layer">
                                <div>
                                    <h2>
                                        <label for="location-scan">
                                            Scan Location
                                        </label>
                                    </h2>
                                    <br/>
                                    <br/>
                                    <input id="scan-input" name="scan-input"/>
                                    <!-- <button id="reset">Reset</button> -->
                                </div>
                                <br/>
                                <div>
                                    <input name="back-to-textarea-btn" type="button" value="Back"> 
                                    <input id="resetscanbtn" name="resetscanbtn" type="button" value="Reset" onclick="clearValue()" />
                                    <input name="next-to-submit-btn" type="button" value="Next"> 
<!--                                      <h2>Event Information</h2> -->
<!--                                         Start: <span id="startTime"></span>  -->
<!--                                     <br/>First Key: <span id="firstKey"></span>  -->
<!--                                     <br/>Last Ley: <span id="lastKey"></span>  -->
<!--                                     <br/>End: <span id="endTime"></span>  -->
<!--                                     <br/>Elapsed: <span id="totalTime"></span> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column right" id="submit-scan-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >
                
                        <div class="justified">
                            <div class="dvsubmission_layer">
                                <div style="text-align:center">
                                    
                                    <h2>
                                        <!-- <label for="submit-scan">-->
                                            Submission Status
                                        <!-- </label>-->
                                    </h2>
                                    <br/>
                                    <input name="submitbtn" type="submit" value="Submit"> 
                                    <div class="confirmation">
                                        <p>The box(es) location information has been received.  Thanks.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

<!-- JM Developer End -->





