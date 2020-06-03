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
    //include_once WPPATT_ABSPATH . 'includes/class-wppatt-functions.php';
    //$load_styles = new wppatt_Functions();
    //$load_styles->addStyles(); 
    
    $general_appearance = get_option('wpsc_appearance_general_settings');
    
    $action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';
    
    $wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');
    
    $edit_btn_css = 'background-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_bg_color'].' !important;color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_text_color'].' !important;border-color:'.$wpsc_appearance_individual_ticket_page['wpsc_edit_btn_border_color'].'!important';
    
    
    // JM Developer - echo Link to CSS  
    echo '<link rel="stylesheet" type="text/css" href="' . WPPATT_PLUGIN_URL . 'includes/admin/css/scan-table.css"/>';
    
    // JM - 4/28/2020 - Add functions to fetch location, bay from the PATT Custom Class
   // $box_location = Patt_Custom_Func::fetch_location(1);
    
   // $box_bay = Patt_Custom_Func::fetch_bay(1);
		
    // JM - 5/1/2020 - Function to obtain location value from database <br/>";
   //     $box_location = Patt_Custom_Func::fetch_location(1);


    //    $box_program_office = Patt_Custom_Func::fetch_program_office(1);

        
    //    $box_shelf = Patt_Custom_Func::fetch_shelf(1);

        
	//	$box_bay = Patt_Custom_Func::fetch_bay(1);

?> 




  <!-- JM Developer

TODO:  Add input mask for validating if the miltiline textarea 
        so that it only Box ID's can be added to the area.
        
        -->
 <!-- JM - 4/28/2020 - Moved JQuery script block to beginning of the document -->
<script>

    // JM - 5/13/20 - Maintain values for insertion into DB tables
    var boxid_values = [];
    var scanid_values = [];
    var response_messages_list = "";
    
    // Use regex for each case
    // JM - 5/13/20 - New location code for regex
    // 2A_3B_3S_2P
    const reg_physicalLocation = /^\d{1,3}A_\d{1,3}B_\d{1,3}S_\d{1,3}P$/gi;
    
    // Aisle
    const reg_aisle = /^(\d{1,3}a\z)$/i;
    
    // Bay
    const reg_bay = /^(\d{1,3}b\z)$/i;  
    
    // Shelf
    const reg_shelfid = /^(\d{1,3}s\z)$/i;  
    
    // Position
    const reg_position = /^(\d{1,3}p\z)$/i;  
    
    // Record Center
    const reg_recordCenter = /^(e|w\z)$/i;
    
    // BoxID
    const reg_boxid = /^\d{7}-\d{1,}$/i;
                   
    // Cart Barcode
    const reg_cartid = /(\bcid-\d\d-e\b|\bcid-\d\d-w\b)|(\bcid-\d\d-east\scui\b|\bcid-\d\d-west\scui\b)|(\bcid-\d\d-east\b|\bcid-\d\d-west\b)|(\bcid-\d\d-eastcui\b|\bcid-\d\d-westcui\b)/gim;   

    // Staging barcode
    const reg_stagingarea = /^\b(sa-e|sa-w)\b$/i;  

    // Scanning barcode
    const reg_scanning = /^\b(SCN-\d\d-e|SCN-\d\d-w)\b$/i;
              
    // Combined bay and shelf barcode
    // const reg_bay_and_shelf = "/\b(\d\d\d-[a-zA-Z]{3})\b/i";  


    // Shelf barcode
    // const reg_shelf = "/(\A[a-zA-Z]{3}\z)/iA";
   
    jQuery(document).ready(function() {
        
        
          jQuery("textarea#boxid-textarea").focus();
          
            /* JM - 5/22/2020 - Clear border css properties to mimic unselect 
            jQuery(this).next().find('#submitbtn').focus();*/
            jQuery('#submit-scan-border').css('border', '');
            jQuery('#submit-scan-border').css('box-shadow', '');
          
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
        	 
            /* JM - 4/28/20 - Hide the confirmation on page load 
            jQuery('div#dvconfirmaiton').css('display', 'none');*/
            
            
            /* JM - 5/11/20 - Added functon to determine when users stop typing and executes routines afterwards
            
                 $('#element').donetyping(callback[, timeout=1000])
                 Fires callback when a user has finished typing. This is determined by the time elapsed
                 since the last keystroke and timeout parameter or the blur event--whichever comes first.
                   @callback: function to be called when even triggers
                   @timeout:  (default=1000) timeout, in ms, to to wait before triggering event if not
                              caused by blur.
                 Requires jQuery 1.7+
            */

            ;(function($){
                $.fn.extend({
                    donetyping: function(callback,timeout){
                        timeout = timeout || 1e3; // 1 second default timeout
                        var timeoutReference,
                            doneTyping = function(el){
                                if (!timeoutReference) return;
                                timeoutReference = null;
                                callback.call(el);
                            };
                        return this.each(function(i,el){
                            var $el = $(el);
                            // Chrome Fix (Use keyup over keypress to detect backspace)
                            // thank you @palerdot
                            $el.is('textarea') && $el.on('keyup keypress paste',function(e){
                                // This catches the backspace button in chrome, but also prevents
                                // the event from triggering too preemptively. Without this line,
                                // using tab/shift+tab will make the focused element fire the callback.
                                if (e.type=='keyup' && e.keyCode!=8) return;
                                
                                // Check if timeout has been set. If it has, "reset" the clock and
                                // start over again.
                                if (timeoutReference) clearTimeout(timeoutReference);
                                timeoutReference = setTimeout(function(){
                                    // if we made it here, our timeout has elapsed. Fire the
                                    // callback
                                    doneTyping(el);
                                }, timeout);
                            }).on('blur',function(){
                                // If we can, fire the event since we're leaving the field
                                doneTyping(el);
                            });
                        });
                    }
                });
            })(jQuery);
            
            jQuery('textarea#boxid-textarea').donetyping(function(){
                
                var text_vals = jQuery(this).val();
              
                /* JM - 5/11/20 - Provide an input mask for textarea so only the boxID can be added */
  
                   /* Get and evaluate if the last item input into the textarea is a valid boxid,
                   get rid of the empty elements, and handle other types of whitespace (including newlines). */
                
                jQuery.each(text_vals.split(/[\s,]+/).filter(Boolean), function(index, last_boxid_val) {
                    
                  /* alert(index + ': ' + last_boxid_val); */

                /* If the last textarea entry matches the appropriate boxid form, then add a newline */
                    if (reg_boxid.test(last_boxid_val)){
                        
                        /* Add the boxid to the boxid array */
                        boxid_values.push(last_boxid_val)
                        
                        /* If there is not already a new line */
                        jQuery('textarea#boxid-textarea').val(jQuery('textarea#boxid-textarea').val() + '\n');

                            /* Else, remove the last entry */
                    }else{
                        boxid_values.pop();
                        alert( last_boxid_val + " is a invalid BoxID.")
                    }
                });
            });

            jQuery('textarea#scan-input').donetyping(function(){
                
                var text_vals = jQuery(this).val();

                /* JM - 5/12/20 - Break the values in the Scan Input textarea into a array by newline characters.
                   Get and evaluate if the last item input into the textarea is a valid boxid */
           
                jQuery.each(text_vals.split(/[\s,]+/).filter(Boolean), function(index, last_scan_val) {
                    
                /* If the last textarea entry does not match the appropriate id form */
                    if (reg_cartid.test(last_scan_val)){

                        /* remove the last entry */
                        scanid_values.push(last_scan_val);
                        /* Add a new line to the textarea*/
                        jQuery('textarea#scan-input').val(jQuery('textarea#scan-input').val() + '\n');
                        
                    }else if(reg_stagingarea.test(last_scan_val)){
                        
                         scanid_values.push(last_scan_val);
                        /* Add a new line to the textarea*/
                        jQuery('textarea#scan-input').val(jQuery('textarea#scan-input').val() + '\n');
                        
                    }else if(reg_scanning.test(last_scan_val)){
                        
                         scanid_values.push(last_scan_val);
                        jQuery('textarea#scan-input').val(jQuery('textarea#scan-input').val() + '\n');
                        
                    }else if(reg_aisle.test(last_scan_val)){
                        
                            scanid_values.push(last_scan_val);
                            jQuery('textarea#scan-input').val(jQuery('textarea#scan-input').val() + '\n');
                            
                    }else if(reg_bay.test(last_scan_val)){
                        
                            scanid_values.push(last_scan_val);
                            jQuery('textarea#scan-input').val(jQuery('textarea#scan-input').val() + '\n');
                            
                    }else if(reg_shelfid.test(last_scan_val)){
                        
                            scanid_values.push(last_scan_val);
                            jQuery('textarea#scan-input').val(jQuery('textarea#scan-input').val() + '\n');
                            
                    }else if(reg_position.test(last_scan_val)){
                        
                            scanid_values.push(last_scan_val);
                            jQuery('textarea#scan-input').val(jQuery('textarea#scan-input').val() + '\n');

                    /* JM - 6/1/20 - Detect the location string (1A_1B_1S_1P) */
                    }else if(reg_physicalLocation.test(last_scan_val)){
                        
                        /* Break the string into each position value holder */
                        var arrphysical_location = last_scan_val.split('_');
                        
                        jQuery.each(arrphysical_location, function(key, val){
                            
                            if(reg_aisle.test(val)){
                                scanid_values.push(val);
                            }
                            if(reg_bay.test(val)){
                                scanid_values.push(val);
                            }
                            if(reg_shelfid.test(val)){
                                scanid_values.push(val);
                            }
                            if(reg_position.test(val)){
                                scanid_values.push(val);
                            }
                        });
                        
                        jQuery('textarea#scan-input').val(jQuery('textarea#scan-input').val() + '\n');
                        
                    }else if(reg_recordCenter.test(last_scan_val)){
                        
                         scanid_values.push(last_scan_val);
                        /* Add a new line to the textarea*/
                        jQuery('textarea#scan-input').val(jQuery('textarea#scan-input').val() + '\n');

                    }else{                 
                        /* Add the boxid to the boxid array */
                        scanid_values.pop(last_scan_val);
                        
                        alert(last_scan_val + " is invalid.")
                                    
                        /* jQuery('#dvconfirmation').removeClass('hidden'); */
                        jQuery('.confirmation').css('display', 'inline');  
                    }
                });
            });

            /* Hide the confirmation area and clear the value once the
                boxid-textarea obtains focus...to reset the process 
            jQuery('textarea#boxid-textarea').focus(function(){
                
                jQuery('div#dvconfirmation').css('display', 'none');
                
            });*/
        
            /* JM - 4/28/2020 - Code functions for Next/Prev navigation buttons */
            jQuery('#next-to-scan-btn').on('click', function (e) {
                e.preventDefault();
                
                /* JM - 5/22/2020 - Clear border css properties to mimic unselect */
                jQuery(this).next().find('#submitbtn').focus();
                jQuery('#submit-scan-border').css('border', '');
                jQuery('#submit-scan-border').css('box-shadow', '');
                
                /* Move focus and input to the scan-input textarea */
                var textarea = jQuery('#scan-input');
                textarea.focus();
                /*textarea.setSelectionRange(textarea.val().length,textarea.val().length,'forward');*/
            });
                
            jQuery('#back-to-textarea-btn').on('click', function (e) {
                e.preventDefault();
                
                /* JM - 5/22/2020 - Clear border css properties to mimic unselect */
                jQuery(this).next().find('#submitbtn').focus();
                jQuery('#submit-scan-border').css('border', '');
                jQuery('#submit-scan-border').css('box-shadow', '');
                
                /* Move focus and input to the boxid-textarea */
                var textarea = jQuery('#boxid-textarea');
                textarea.focus();
                /*textarea.setSelectionRange(textarea.val().length,textarea.val().length,'forward');*/
            }); 
                
            jQuery('#next-to-submit-btn').on('click', function (e) {
                e.preventDefault();
                
                jQuery(this).next().find('#submitbtn').focus();
                jQuery('#submit-scan-border').css('border', '2px solid #0741AB');
                jQuery('#submit-scan-border').css('box-shadow', '0 0 2px #00a0d2');
                
              /*  jQuery( "#content" ).delegate( "*", "focus blur", function() { */
                  var elem = jQuery('#submitbtn');
                  setTimeout(function() {
                    elem.toggleClass( "focused", elem.is( ":focus" ) );
                  }, 0 );
               /* }); */
            });
            
            jQuery('form').on('submit',  function(e) {
                    e.preventDefault();

                // JM - 5/22/2020 - Clear border css properties to mimic unselect 
                jQuery(this).next().find('#submitbtn').focus();
                jQuery('#submit-scan-border').css('border', '');
                jQuery('#submit-scan-border').css('box-shadow', '');
                
                var arr_validated_scans = [];

                   //  JM - 5/13/20 - Update the database 
                    if(boxid_values.length > 0 && scanid_values.length > 0 ){
                        
                        // Insert the scan_Locations values for each BoxID value in the scan_BoxIDs
                        jQuery.each(boxid_values, function(index, boxid_value) {
                            
                            
                    
                            // For each scan value in the scan_Locations array
                            jQuery.each(scanid_values, function(index, scan) {
                           
                                // Perform a Update since the BoxID does exist
                                // Create a switch statement that determines 
                                // if the length/characters/special character location (-) used by 
                                // the regix matches current value for
                                
                                // Use regex for each case
                                
                                switch(scan)
                                {
                                    case (reg_bay.test(scan)? true : false):
                                
                                        // Case a bay barcode
                                        
                                        arr_validated_scans.push(scan);
        
                                        break;
                                    
                                    case (reg_cartid.test(scan)? true : false):
                                        
                                
                                        // Case a cart barcode
                                        arr_validated_scans.push(scan);
                                        
                                        break;
                                    
                                    case (reg_stagingarea.test(scan)? true : false):
                                    
                                        // Case a staging area barcode
                                        
                                        arr_validated_scans.push(scan);
                                        break;
                                    
                                    case (reg_scanning.test(scan)? true : false):
                                    
                                        // Case a scanner barcode
                                        arr_validated_scans.push(scan);
                                        break;
                                       
                                    case (reg_shelfid.test(scan)? true : false):
                                    
                                        arr_validated_scans.push(scan);
                                        break;
                                    
                                    case (reg_position.test(scan)? true : false):
                                    
                                        arr_validated_scans.push(scan);
                                        break;
                                      
                                    case (reg_aisle.test(scan)? true : false):
                                    
                                        arr_validated_scans.push(scan);
                                        break;
                                      
                                    case (reg_recordCenter.test(scan)? true : false):
            
                                        arr_validated_scans.push(scan);
                                        break;
                                }
                           });      
     
        
                            try{
                            
                                    // Debug
                                    alert("Box ID values: " + boxid_values + "\n" + "Location Scans: " + arr_validated_scans );
                                    

                                    // Andrews Code
                                    jQuery.post(
                                                    '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_scan_location.php',{
                                                    postvarsboxid: boxid_value,
                                                    postvarslocation: arr_validated_scans
                                                    }, 
                                                    function (response) {
                                                          if(!alert(response)){window.location.reload();
                                                    }
                                                    window.location.replace("/wordpress3/wp-admin/admin.php?page=scanning"
                                                );
                                    });
                             
                                    var response = updateLocations(boxid_values, scanid_values);
                                    var confmessage = '<div class="col-md-8 col-md-offset-2 wpsc_thread_log" style="background-color:#D6EAF8 !important;color:#000000 !important;border-color:#C3C3C3 !important;"><strong>Updated</strong>' + response + '</small></i></div>';
                                    window.response_messages_list = window.response_messages_list + confmessage;
                                    
                                    jQuery('.confirmation').append(window.response_messages_list);
                                    
                                    /* jQuery('#dvconfirmation').removeClass('hidden'); */
                                    jQuery('.confirmation').css('display', 'inline');  
                                    
                            }catch (err){
                            
                                    var response = "Either the <strong>BoxID(s)</strong> are missing or there are no <strong>Location Scan(s)</strong> to submit.";
                                    var confmessage = '<div class="col-md-8 col-md-offset-2 wpsc_thread_log" style="background-color:#D6EAF8 !important;color:#000000 !important;border-color:#C3C3C3 !important;"><strong>Please try again. </strong>' + response + '</small></i></div>';
                                    window.response_messages_list = window.response_messages_list + confmessage;
                                    
                                    jQuery('.confirmation').append(window.response_messages_list);
                                    
                                    /* jQuery('#dvconfirmation').removeClass('hidden'); */
                                    jQuery('.confirmation').css('display', 'inline');  
                                            
                            }
                            
                        });
                    }
                    return false;
            });

            /*    This code will determine when a code has been either entered manually or
                entered using a scanner.
                It assumes that a code has finished being entered when one of the following
                events occurs:
                    • The enter key (keycode 13) is input
                    • The input has a minumum length of text and loses focus
                    • Input stops after being entered very fast 
                    
                    TO DO: JM - 5/5/2020
                    Change the scanner detection code to accept input from either textarea by doing the following:
                    ~
                    ~
                    jQuery('textarea#scan-input').keypress(function (e) {
                    ~
                    ~
                        if (jQuery(this).val().length >= minChars){
                        ~
                        ~
            
            */
            

            /* handle a key value being entered by either keyboard or scanner */
            jQuery('.input').keypress(function (e) {
                /* restart the timer */
                if (timing) {
                    clearTimeout(timing);
                }
                
                /* Enter key was entered */
                if (e.which == 13) {
                    
                    /* don't submit the form */
                    e.preventDefault();
                    
                    /* has the user finished entering manually? */
                    if (jQuery(this).val().length >= minChars){
                        userFinishedEntering = true; // incase the user pressed the enter key
                        inputComplete(this);
                    }
                    
                    /* JM - 5/6/2020 - Moved the following from the changed event.
                        Start a new line after the user presses enter on the keyboard. */
                    jQuery('textarea#boxid-textarea').val(function(_,v) {
                        return v + "\n";
                    });

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
                        jQuery('body').on('blur', this, inputBlur(jQuery(this)));
                    }
                    
                    /* start the timer again */ 
                    timing = setTimeout(inputTimeoutHandler, 500);
                }
            });
            
            /* JM - 4/24/2020 - Clear buttons for the input and textarea*/
            jQuery('#resetscanbtn').on('click', function(e) {
                e.preventDefault();
                
                /* JM - 5/22/2020 - Clear border css properties to mimic unselect */
                jQuery(this).next().find('#submitbtn').focus();
                jQuery('#submit-scan-border').css('border', '');
                jQuery('#submit-scan-border').css('box-shadow', '');
                
                /* Debug 
                alert( "Handler for resetscanbtn on('click') called." );*/
                
                /* Get the scaninput input box, clear the contents of
                just the inputbox and enable all controls in the middle div*/
                
                /* JM - 5/5/2020 - Clearing the inputbox */ 
                jQuery('textarea#scan-input').val(''); 
                resetValues(jQuery(this));

            });
                  
           jQuery('#resetboxidbtn').on('click', function(e) {
                e.preventDefault(); 
                
                /* Debug 
                alert( "Handler for resetboxidbtn on('click')  called." ) */
                /* JM - 5/22/2020 - Clear border css properties to mimic unselect */
                jQuery(this).next().find('#submitbtn').focus();
                jQuery('#submit-scan-border').css('border', '');
                jQuery('#submit-scan-border').css('box-shadow', '');
                
                /* Get the boxid textarea  and clear the contents ;*/
                jQuery('textarea#boxid-textarea').val('');

            });
                
                /* Scrutinize the textarea entries on keydown 
                jQuery('.inputs').keydown(function(e) {*/
                    
                    /* Debug 
                    alert( "Handler for .keydown() called." );*/
                    
                    /* These keyboard key values initiate navigating to the next control/textarea: */
                    /*  backspace	8
                        tab	9
                        shift	16
                        ctrl	17
                        alt	18
                        pause/break	19
                        caps lock	20
                        escape	27
                        page up	33
                        page down	34
                        end	35
                        home	36
                        left arrow	37
                        up arrow	38
                        right arrow	39
                        down arrow	40
                        insert	45
                        delete	46
                        num lock	144
                        scroll lock	145
                    
                    var keys = [8, 9, /*16, 17, 18,*/ /*19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 144, 145];*/
            
                    /* If the character is 8 or the length is 0, equates to nothing 
                    if (e.which == 8 && this.value.length == 0) {*/
                        
                        /* Then set the focus on the previous input control/textarea 
                        jQuery(this).prev('.inputs').focus();*/
                        
                    /* Else if the currently pressed keyboard value is in the array() and is greater than nothing 
                    } else if (jQuery.inArray(e.which, keys) >= 0) {*/
                        
                        /* Then return true so we can go to the next input control/textarea 
                        return true;*/
                    
                    /* 5/6/2020 - JM - Removing character length restriction */    
                    /* Else if the current value is greater than or equal to the character limitation */
                    /*} else if (this.value.length >= charLimit) { */
                        
                    /* Then mark the next control as the target */
                    /*    jQuery(this).data('isNext', 'true'); */
                        
                    /* Then set focus, move to the next control set as the target */
                    /*    jQuery(this).next('.inputs').focus(); */
                        
                    /* Then return false to indicate that we must move to the next control */
                    /*    return false; */
                        
                    /* Finally, allow only alpha and numericals to be added to the texareas */
                    /* if the shift key or any keyboard value between 48 to 90 (alpha and numerical characters)
                       is pressed      
                    } else if (e.shiftKey || e.which <= 48 || e.which >= 58) {*/
                        
                        /* Then return false to indicate that we must NOT move to the next control 
                        return false;
                    }*/
                    
                    /* Scrutinize the textareas on keyup 
                }).keyup (function () {*/
                    
                    /* Debug 
                    alert( "Handler for .keyup() called." );*/
                    
                    /* Determine if the next input control/textarea contains data or is empty 
                    var isNext = (jQuery(this).prev().length)? jQuery(this).prev('.inputs').data('isNext'): '';*/
                    
                    /* If the next control is empty 
                    if (isNext == '') {*/
                        
                        /* Then determine if the length of characters is greater than the set limit 
                        if (this.value.length >= charLimit) {*/
                            
                        /* Then set the focus on the next input control/textarea 
                            jQuery(this).next('.inputs').focus();*/
                            
                            /* Then return false to indicate that we must move to the next control 
                            return false;
                        }
                    }*/
                    
                    /* Move to the next control 
                    jQuery(this).data('isNext', '');
                    */
                /* Move to the next control 
                }).data('isNext', '');
                */
            /* JM - 5/18/2020 - Created routine that moves the focus/caret to the next input control 
            
            $('.clnext').on('keyup', '.address_field', function(e) {
              if (e.keyCode == 13) {
                e.preventDefault();
                
                $(this).next().find('input[type=text]').focus();
                }
            }); */
            
    });      
    
            var inputStart, inputStop, firstKey, lastKey, timing, userFinishedEntering;
            var minChars = 3;
            
            /* Assume that a loss of focus means the value has finished being entered */
            function inputBlur(id){
                clearTimeout(timing);
                if (id.val.length >= minChars){
                    userFinishedEntering = true;
                    inputComplete(jQuery(id));
                }
            }
            
            /* JM - 5/5/2020 - Uncommented out all clear value variable resets to return 
             the element values for reporting metrics impementation. */
            function resetValues(id) {
                
                /* - JM - 5/18/20 - Remove all values from the array */
                    /* BoxID Textarea */
                if (jQuery(id).attr("id") == "boxid-textarea"){

                    boxid_values.splice(0,boxid_values.length);
                }else{
                
                    /* Scannign Textarea */
                    scanid_values.splice(0,scanid_values.length);
                   /* locationid_values.splice(0,locationid_values.length); */
                } 
                
                /* clear the variables */
                inputStart = null;
                inputStop = null;
                firstKey = null;
                lastKey = null;
                /* clear the results */
                inputComplete(jQuery(id));
            }
              
            /* Assume that it is from the scanner if it was entered really fast */
            function isScannerInput(id) {
                return (((inputStop - inputStart) / id.val.length) < 15);
            }
            
            /* Determine if the user is just typing slowly */
            function isUserFinishedEntering(id){
                return !isScannerInput(id) && userFinishedEntering;
            }
            
            function inputTimeoutHandler(id){
                /* stop listening for a timer event */
                clearTimeout(timing);
                /* if the value is being entered manually and hasn't finished being entered */
                if (!isUserFinishedEntering(jQuery(id)) || id.val.length < 3) {
                    /* keep waiting for input */
                    return;
                }
                else{
                    reportValues(jQuery(id));
                }
            }
            
            /* here we decide what to do now that we know a value has been completely entered */
            function inputComplete(id){
                /* stop listening for the input to lose focus */
                jQuery('body').off('blur', jQuery(id) , inputBlur(jQuery(id)));
                /* report the results */
                reportValues(jQuery(id));
            }
            
            /* JM - 4/24/2020 - Commented out all clear value variable resets to remove 
             the elements from the page.*/
            function reportValues(id) {
                /* update the metrics */
                /* jQuery("#startTime").text(inputStart === null ? "" : inputStart); */
                /* jQuery("#firstKey").text(firstKey === null ? "" : firstKey); */
                /* jQuery("#endTime").text(inputStop === null ? "" : inputStop); */
                /* jQuery("#lastKey").text(lastKey === null ? "" : lastKey); */
                /* jQuery("#totalTime").text(inputStart === null ? "" : (inputStop - inputStart) + " milliseconds"); */
                if (!inputStart) {
                    /* clear the results */
                    /* jQuery("#resultsList").html(""); */
                    jQuery(id).focus().select();
                } else {
                    /* prepend another result item */
                    /* var inputMethod = isScannerInput() ? "Scanner" : "Keyboard"; */
                    /* jQuery("#resultsList").prepend("<div class='resultItem " + inputMethod + "'>" + */
                    /*    "<span>Value: " + jQuery("#scanInput").val() + "<br/>" + */
                    /*    "<span>ms/char: " + ((inputStop - inputStart) / jQuery("#scanInput").val().length) + "</span></br>" + */
                    /*    "<span>InputMethod: <strong>" + inputMethod + "</strong></span></br>" + */
                    /*    "</span></div></br>"); */
                    jQuery(id).focus().select();
                    inputStart = null;
                }
            }
            
            /* JM - 5/13/20 - Created function to extract scanned location id's (2A_3B_3S_2P_E) from 
             the individual scanID array in prep for database insertion into the location tables 
             
             function extractLocationID(arr_ScanIDs){
                 
                 var loc_aisle = "";
                 var loc_bay = "";
                 var loc_shelf = "";
                 var loc_position = "";
                 var loc_recordCenter = "";*/
                 
                 /* Split the location ID by underscore to get individual coordinates
                 jQuery.each(arr_ScanIDs.split('_'), function(index, scanned_value) { */
                     
                /*  alert(index + ': ' + scanned_value); */
                  
                  
               /* });
                 
             }*/
             
             function updateLocations(scan_BoxIDs, scan_locations){
                 
                // Create the MySQL connection
                var mysqli = new mysqli("localhost", "username", "password", "dbname");
                
                var row = "";
                var result = "";
                var count = "";
                var sql = "";
                
   
             }

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
                    
              echo '<div class="column left" id="container-boxidinfo-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >';
                
                  echo '<div class="justified" style="text-align:center">';
                      echo '<div class="dvboxid-layer">';
                          echo '<div tabindex="-1">';
                              echo '<h2>';
                                  echo '<label for="boxid-textarea">';
                                    echo 'Box ID(s)';
                                  echo '</label>';
                              echo '</h2>';
                              echo '<br/>';
                              echo '<textarea id="boxid-textarea" class="input" name="boxid-textarea" pattern="(\d\d\d\d\d\d\d-\d{1,})" title="The Box ID must consist of {<7 numbers>-<any number of digits>}." rows="15" cols="15">';

                              echo '</textarea>';  
                          echo '</div>';
                          echo '<br/>';
                         /* echo '<div class="validationbtns" style="text-align:center">'; */
                              echo '<input id="resetboxidbtn" name="resetboxidbtn" type="button" value="Reset" />';
                              echo '<input id="next-to-scan-btn" name="next-to-scan-btn" class="clnext" type="button" value="Next" />';
                         /* echo '</div>'; */
                      echo '</div>';
                  echo '</div>';
              echo '</div>';
              echo '<div class="column middle" id="container-scaninfo-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >';
                        
                  echo '<div class="justified" style="text-align:center">';
                      echo '<div clas="dvscanner-layer">';
                          echo '<div tabindex="1">';
                              echo '<h2>';
                                  echo '<label for="scan-input">';
                                      echo 'Location';
                                  echo '</label>';
                              echo '</h2>';
                              echo '<br/>';
                             /* echo '<input id="scan-input" name="scan-input"/>'; */
                              echo '<textarea id="scan-input" class="input" name="scan-input" rows="15" cols="15">';
                              echo '</textarea>';
                          echo '</div>';
                          echo '<br/>';
                          echo '<div style="text-align:center">';
                            echo '<input id="back-to-textarea-btn" name="back-to-textarea-btn" type="button" value="Back" />';
                            echo '<input id="resetscanbtn" name="resetscanbtn" type="button" value="Reset" />';
                            echo '<input id="next-to-submit-btn" name="next-to-submit-btn" class="clnext" type="button" value="Next" />';

                          echo '</div>';
                       echo '</div>';
                  echo '</div>';
              echo '</div>';
              echo '<div class="column right" id="submit-scan-border" style="background-color:#FFFFFF !important;color:#2C3E50 !important;border-color:#C3C3C3 !important;" >';
                
                  echo '<div class="justified" style="text-align:center">';
                      echo '<div class="dvsubmission_layer">';
                          echo '<div style="text-align:left">';
                              echo '<div style="text-align:center" tabindex="2">'; 
                                  echo '<h2>';
                                    echo 'Submission Status';
                                  echo '</h2>';
                                  echo '<br/>';
                                  echo '<input action="#" id="submitbtn" name="submitbtn" type="submit" value="Submit">';
                              echo '</div>'; 
                              echo '<div id="dvconfirmation" class="confirmation">';
                              /*echo '<div id="dvconfirmation" class="" >';*/
                              echo '<hr/>';
                              
                               /*echo  ' <div class="col-md-8 col-md-offset-2 wpsc_thread_log" style="background-color:#D6EAF8 !important;color:#000000 !important;border-color:#C3C3C3 !important;"> ';
		                       echo  ' <strong>Updated</strong> The box(es) location information has been received.</small></i> ';
		                       echo  ' </div> ';
                                  
                                  JM echo '<hr/>';
                                  echo '<p>The box(es) location information has been received.  Thanks.';
                                  echo '<hr/>';
                                   - 5/1/2020 - Create a loop that displays the result for each updated box id location 
                                  echo "Box ID #: " . $boxid . " has been updated. New Location: " .$aisle. "A_" .$bay . "B_" . $shelf ."S_".$position."P_".$center_value;
                                  echo '<hr/>';
                                  echo '</p>';*/
                                  
                                  
                                  
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







