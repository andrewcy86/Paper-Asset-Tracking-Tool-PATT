 <?php
    
    if ( ! defined( 'ABSPATH' ) ) {
    	exit; /* Exit if accessed directly */
    }
    
    // Code to add ID lookup
    global $current_user, $wpscfunction, $wpdb;
    
    $agent_permissions = $wpscfunction->get_current_agent_permissions();
    
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
    
    // JM - 4/28/2020 - Add functions to fetch location, bay from the PATT Custom Class
    $box_location = Patt_Custom_Func::fetch_location(1);
    
    $box_bay = Patt_Custom_Func::fetch_bay(1);
		
    // JM - 5/1/2020 - Function to obtain location value from database <br/>";
        $box_location = Patt_Custom_Func::fetch_location(1);


        $box_program_office = Patt_Custom_Func::fetch_program_office(1);

        
        $box_shelf = Patt_Custom_Func::fetch_shelf(1);

        
		$box_bay = Patt_Custom_Func::fetch_bay(1);

?> 




  <!-- JM Developer

TODO:  Add input mask for validating if the miltiline textarea 
        so that it only Box ID's can be added to the area.
        
        -->
 <!-- JM - 4/28/2020 - Moved JQuery script block to beginning of the document -->
<script>
   
    jQuery(document).ready(function() {
        
          jQuery("#boxid-textarea").focus();
          
            /* This code controls the menu look and feel */      
          	 jQuery('#toplevel_page_wpsc-tickets').removeClass('wp-not-current-submenu'); 
        	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-has-current-submenu'); 
        	 jQuery('#toplevel_page_wpsc-tickets').addClass('wp-menu-open'); 
        	 jQuery('#toplevel_page_wpsc-tickets a:first').removeClass('wp-not-current-submenu');
        	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-has-current-submenu'); 
        	 jQuery('#toplevel_page_wpsc-tickets a:first').addClass('wp-menu-open');
        	 jQuery('.wp-first-item').addClass('current'); 
        	 jQuery('#menu-dashboard').removeClass('current');
        	 jQuery('#menu-dashboard a:first').removeClass('current');  
        	 
            /* JM - 4/28/20 - Hide the confirmation on page load */
            jQuery('#dvconfirmaiton').css('display', 'none');
            
            /* JM - 4/30/20 - Insert return character after boxid scanned into textarea */
            jQuery('#boxid-textarea').change(function(){
                jQuery('#boxid-textarea').val(function(_,v) {
                    return v + "\n";
                });
            });
            
            /* Show the confirmation after submission */
            jQuery('#submitbtn').on('click', function() {
    
               /* jQuery('#dvconfirmation').removeClass('hidden'); */
                jQuery('#dvconfirmaiton').css('display', 'inline');
            });
            
            /* Hide the confirmation area and clear the value once the
                boxid-textarea obtains focus...to reset the process */
            jQuery('#boxid-textarea').focus(function(){
                
                jQuery('#dvconfirmation').css('display', 'none');
            });
        
        
            /* JM - 4/28/2020 - Code functions for Next/Prev navigation buttons */
            jQuery('#next-to-scan-btn').click(function (e) {
                    e.preventDefault();
                    
                    /* JM - 4/28/2020 - Disable the controls in the 
                            left boxid div to support ui workflow */
                    
                    jQuery('#boxid-textarea').prop('disabled', true);
                    this.prop('disabled', true);
                    
                    /* Move focus and input to the scan-input inputbox */
                    
            });
                
            jQuery('#back-to-textarea-btn').click(function (e) {
                    e.preventDefault();
                    
                    /* JM - 4/28/2020 - Enable the middle text area div controls
                            to support ui workflow */
                    jQuery('#boxid-textarea').prop('disabled', false);
                    this.prop('disabled', false);
                    
                    /* Move focus and input to the boxid-textarea */
                    
            });
                
            jQuery('#next-to-submit-btn').click(function (e) {
                    e.preventDefault();
                    
                    /* Enable textarea and inputbox controls */
                    jQuery('#scan-input').prop('disabled', false);
                    jQuery('#boxid-textarea').prop('disabled', false);

                    /* JM - 4/28/2020 - Submit values to update the DB

                    <?php   
                            
                            /* JM - 5/1/2020 - Get the variables from the textarea and inputbox, */
                            
                            $boxid_textarea = preg_split('/(\r?\n)+/', $_POST['boxid-textarea']);
                            $scan_input = preg_split('/(\r?\n)+/', $_POST['scan-input']);
     
                               foreach($boxid_textarea as $boxid)
                               {
                                       $shelf_position = "";
                                       $shelf = "";
                                       $position = "";
                                    
                                       $aisle = "";
                                       $bay = "";
                                       $center = "";
                                       
                                       $center_value = '';
                                       
                                       /* if ($center = 'East') {
                                                $center_value = 'E';
                                           } else if ($center = 'West'){
                                             $center_value = 'W';       
                                        } */
                                    
                                        // then update the database using the Custom PATT functions. 
                                        
                                    	$box_details = $wpdb->get_row(
                                                    "SELECT storage_location_id
                                                    FROM wpqa_wpsc_epa_boxinfo
                                                    WHERE box_id = '" . $boxid . "'"
                                    		);
                                    
                                    	$box_storage_location_id = $box_details->storage_location_id;
                                    			
                                        $table_name = 'wpqa_wpsc_epa_storage_location';
                                            
                                        $data_update = array('aisle' => $aisle ,'bay'=>$bay,'shelf'=>$shelf,'position'=>$position);
                                        $data_where = array('id' => $box_storage_location_id);
                                        $wpdb->update($table_name , $data_update, $data_where);

                               }
                    ?>
            });

    



            /*    This code will determine when a code has been either entered manually or
                entered using a scanner.
                It assumes that a code has finished being entered when one of the following
                events occurs:
                    • The enter key (keycode 13) is input
                    • The input has a minumum length of text and loses focus
                    • Input stops after being entered very fast (assumed to be a scanner)
            */
            
            var inputStart, inputStop, firstKey, lastKey, timing, userFinishedEntering;
            var minChars = 3;
            
            /* handle a key value being entered by either keyboard or scanner */
            jQuery('#scan-input').keypress(function (e) {
                /* restart the timer */
                if (timing) {
                    clearTimeout(timing);
                }
                
                /* handle the key event */
                if (e.which == 13) {
                    /* Enter key was entered */
                    
                    /* don't submit the form */
                    e.preventDefault();
                    
                    /* has the user finished entering manually? */
                    if (jQuery('#scan-input').val().length >= minChars){
                        userFinishedEntering = true; // incase the user pressed the enter key
                        inputComplete();
                    }
                }
                else {
                    /* some other key value was entered */
                    
                    // could be the last character
                    inputStop = performance.now();
                    lastKey = e.which;
                    
                    /* don't assume it's finished just yet */
                    userFinishedEntering = false;
                    
                    /* is this the first character? */
                    if (!inputStart) {
                        firstKey = e.which;
                        inputStart = inputStop;
                        
                        /* watch for a loss of focus */
                        jQuery('body').on('blur', '#scanInput', inputBlur);
                    }
                    
                    /* start the timer again */
                    timing = setTimeout(inputTimeoutHandler, 500);
                }
            });
    });        
            /* Assume that a loss of focus means the value has finished being entered */
            function inputBlur(){
                clearTimeout(timing);
                if (jQuery('#scanInput').val().length >= minChars){
                    userFinishedEntering = true;
                    inputComplete();
                }
            }
            
            /* JM - 4/24/2020 - Commented out to use the clearValue() method instead
             reset the page
             jQuery("#resetscanbtn").click(function (e) {
                e.preventDefault();
                resetValues();
            });  */
            
            /* JM - 4/24/2020 - Commented out all clear value variable resets to remove 
             the elements from the page. */
            function resetValues() {
                /* clear the variables
                inputStart = null;
                inputStop = null;
                firstKey = null;
                lastKey = null;
                clear the results */
                inputComplete();
                
                /* JM - 4/24/2020 - Clearing the inputbox */
                /* $('input[name="scanInput"]').val(''); */
                
            }
              
            /* Assume that it is from the scanner if it was entered really fast */
            function isScannerInput() {
                return (((inputStop - inputStart) / jQuery('#scan-input').val().length) < 15);
            }
            
            /* Determine if the user is just typing slowly */
            function isUserFinishedEntering(){
                return !isScannerInput() && userFinishedEntering;
            }
            
            function inputTimeoutHandler(){
                /* stop listening for a timer event */
                clearTimeout(timing);
                /* if the value is being entered manually and hasn't finished being entered */
                if (!isUserFinishedEntering() || jQuery('#scan-input').val().length < 3) {
                    /* keep waiting for input */
                    return;
                }
                else{
                    reportValues();
                }
            }
            
            /* here we decide what to do now that we know a value has been completely entered */
            function inputComplete(){
                /* stop listening for the input to lose focus */
                jQuery('body').off('blur', '#scan-input', inputBlur);
                /* report the results */
                reportValues();
            }
            
            /* JM - 4/24/2020 - Commented out all clear value variable resets to remove 
             the elements from the page.*/
            function reportValues() {
                /* update the metrics */
                /* jQuery("#startTime").text(inputStart === null ? "" : inputStart); */
                /* jQuery("#firstKey").text(firstKey === null ? "" : firstKey); */
                /* jQuery("#endTime").text(inputStop === null ? "" : inputStop); */
                /* jQuery("#lastKey").text(lastKey === null ? "" : lastKey); */
                /* jQuery("#totalTime").text(inputStart === null ? "" : (inputStop - inputStart) + " milliseconds"); */
                if (!inputStart) {
                    /* clear the results */
                    /* jQuery("#resultsList").html(""); */
                    jQuery('#scan-input').focus().select();
                } else {
                    /* prepend another result item */
                    /* var inputMethod = isScannerInput() ? "Scanner" : "Keyboard"; */
                    /* jQuery("#resultsList").prepend("<div class='resultItem " + inputMethod + "'>" + */
                    /*    "<span>Value: " + jQuery("#scanInput").val() + "<br/>" + */
                    /*    "<span>ms/char: " + ((inputStop - inputStart) / jQuery("#scanInput").val().length) + "</span></br>" + */
                    /*    "<span>InputMethod: <strong>" + inputMethod + "</strong></span></br>" + */
                    /*    "</span></div></br>"); */
                    jQuery('#scan-input').focus().select();
                    inputStart = null;
                }
            }
            
            /* JM - 4/24/2020 - Clear buttons for the input and textarea*/
            function clearValue(id) {
           
                var elem = "";
                
                /* Get the scaninput input box, clear the contents of
                just the inputbox and enable all controls in the middle div*/
                if(id === "resetscanbtn"){
    
                    document.getElementById('#scan-input').value = "";
                    resetValues();
                    
                    jQuery('#back-to-textarea-btn').prop('disabled', false);
                    jQuery('#next-to-submit-btn').prop('disabled', false);
                    jQuery('#scan-input').prop('disabled', false);
                    
                }
                
                /* Get the boxid textarea  and clear the contents */
                if(id === 'resetboxidbtn'){
                    
                    elem = "boxid-textarea";
                    document.getElementById(id).value = "";
                    
                    /* JM - 4/28/2020 - Enable all controls on the form (left, middle, and right divs) */
                    jQuery('#boxid-textarea').prop('disabled', false);
                    
                    
                }
            }
    
            /* JM - 4/24/2020 - Submit button actions 
            jQuery('#scanform button[name=submitbtn]').click(function(e){
                  e.preventDefault();
                  jQuery('.confirmation').show();
                  
                  //setTimeout(function(){
                  //  $('#scanform').submit();
                  //}, 5000);
            });*/
            
</script>

       
        



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
        
      <?php
           if (($agent_permissions['label'] == 'Administrator') || ($agent_permissions['label'] == 'Agent'))
          {
          echo "This is a message for managers of PATT.";


        
     echo ' <form id="scanform" action="#" method="post"> ';
          echo '<div class="row" id="scan_input_row">';
                    
              echo '<div class="column left" id="container-scaninfo-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >';
                
                  echo '<div class="justified" style="text-align:center">';
                      echo '<div class="dvboxid-layer">';
                          echo '<div>';
                              echo '<h2>';
                                  echo '<label for="boxid-textarea">';
                                    echo 'Box ID(s)';
                                  echo '</label>';
                              echo '</h2>';
                              echo '<br/>';
                              echo '<textarea id="boxid-textarea" name="boxid-textarea" pattern="(\d\d\d\d\d\d\d-\d{1,})" title="The Box ID must consist of {<7 numbers>-<any number of digits>}." rows="15" cols="15">';

                              echo '</textarea>';  
                          echo '</div>';
                          echo '<br/>';
                          echo '<div class="validationbtns" style="text-align:center">';
                              echo '<input name="resetboxidbtn" type="button" value="Reset" onclick="clearValue()" />';
                              echo '<input name="next-to-scan-btn" type="button" value="Next">';
                          echo '</div>';
                      echo '</div>';
                  echo '</div>';
              echo '</div>';
              echo '<div class="column middle" id="container-scaninfo-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >';
                        
                  echo '<div class="justified" style="text-align:center">';
                      echo '<div clas="dvscanner-layer">';
                          echo '<div>';
                              echo '<h2>';
                                  echo '<label for="scan-input">';
                                      echo 'Location';
                                  echo '</label>';
                              echo '</h2>';
                              echo '<br/>';
                             /* echo '<input id="scan-input" name="scan-input"/>'; */
                              echo '<textarea id="scan-input" name="scan-input" rows="15" cols="15">';
                              echo '</textarea>';
                          echo '</div>';
                          echo '<br/>';
                          echo '<div style="text-align:center">';
                            echo '<input name="back-to-textarea-btn" type="button" value="Back">';
                            echo '<input id="resetscanbtn" name="resetscanbtn" type="button" value="Reset" onclick="clearValue()" />';
                            echo '<input name="next-to-submit-btn" type="button" value="Next">';

                          echo '</div>';
                       echo '</div>';
                  echo '</div>';
              echo '</div>';
              echo '<div class="column right" id="submit-scan-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >';
                
                  echo '<div class="justified" style="text-align:center">';
                      echo '<div class="dvsubmission_layer">';
                          echo '<div style="text-align:left">';
                              echo '<div style="text-align:center">'; 
                                  echo '<h2>';
                                    echo 'Submission Status';
                                  echo '</h2>';
                                  echo '<br/>';
                                  echo '<input name="submitbtn" type="submit" value="Submit">';
                              echo '</div>'; 
                              /*echo '<div id="dvconfirmation" class="confirmation">';*/
                              echo '<div id="dvconfirmation" class="" >';
                                  echo '<hr/>';
                                  echo '<p>The box(es) location information has been received.  Thanks.';
                                  echo '<hr/>';
                                  /* JM - 5/1/2020 - Create a loop that displays the result for each updated box id location */
                                  echo "Box ID #: " . $boxid . " has been updated. New Location: " .$aisle. "A_" .$bay . "B_" . $shelf ."S_".$position."P_".$center_value;
                                  echo '<hr/>';
                                  echo '</p>';
                              echo '</div>';
                          echo '</div>';
                      echo '</div>';
                  echo '</div>';
              echo '</div>';
          echo '</div>';
      echo '</div>';
  echo '</form>';
  }?>
</div>







