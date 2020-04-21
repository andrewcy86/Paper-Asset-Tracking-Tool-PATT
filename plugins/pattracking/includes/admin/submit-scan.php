<?php

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



// TODO:  Create the following supporting MYSQL functions:
//          mysql_query(<STATEMENT>)
//          mysql_num_rows(<QUERY>)
//          Regex's for SWITCH/CASE


if(isset($_POST['submit-scan'])) {
    

    // Split the textarea entries by newline or return characters
    $scan_textarea = preg_split('/(\r?\n)+/', $_POST['boxid-scan']);

    // Split the input value entries by the comma, semicolon, or space 
    $scan_input = preg_split("/[,: ]/", $_POST['submit-scan']);
    
    // Create the MySQL connection
    $mysqli = new mysqli("localhost", "username", "password", "dbname");
    
    // Insert the scan_input values for each BoxID value in the scan_textarea
    foreach($scan_textarea as $boxid_value) {
        
        
        // Determine if the BoxID exist in the DB and prepare SQL 
        // Get the count of rows returned to set the flag for an insert or update action
        $sql = "SELECT COUNT(*) AS row_count FROM wpqa_wpsc_scan_list WHERE boxid = $boxid_value";
        $result = mysqli_query($mysqli, $sql);
        $row = mysqli_fetch_array($result);
        $count = $row['row_count'];

        // For each scan value in the scan_input array
        foreach($scan_input as $scan){
       
            // Perform a Update since the BoxID does exist
            // Create a switch statement that determines 
            // if the length/characters/special character location (-) used by 
            // the regix matches current value for
            
            // Use regex for each case
            
            // Cart Barcode
              $cartid = "";  

            // Staging barcode
              $reg_stagingarea = "";  

            // Scanning barcode
              $reg_scanning = '/\b(sa-e|sa-w)\b/';

            switch($scan)
            {
                case (preg_match($reg_cart, $scan)? true : false):
                    // Case a cart barcode
                    // Determine if an update or insert will be performed
                    if ($count > 0){
                        
                        $statement = "UPDATE wpqa_wpsc_scan_list SET (cartid) VALUES ($cartid) WHERE boxid = $boxid_value";
                    }
                    else{
                        
                        $statement = "INSERT INTO wpqa_wpsc_scan_list (cartid) VALUES ($cartid) WHERE boxid = $boxid_value";
                             
                    }
                    
                    break;
                    
                case (preg_match($reg_stagingarea, $scan)? true : false):
                    
                    // Case a staging area barcode
                    // Determine if an update or insert will be performed
                    if ($count > 0){
                        
                         $statement = "UPDATE wpqa_wpsc_scan_list SET (stagingareaid) VALUES ($stagingareaid) WHERE boxid = $boxid_value"; 
                    }
                    else{
                        
                         $statement = "INSERT INTO wpqa_wpsc_scan_list (staginareaid) VALUES ($stagingareaid) WHERE boxid = $boxid_value";
                         
                    }
                    break;
                    
                case (preg_match($reg_scanning, $scan)? true : false):
                    
                    // Case a scanner barcode
                    // Determine if an update or insert will be performed
                    if ($count > 0){
                        
                        $statement = "UPDATE wpqa_wpsc_scan_list SET (scanningid) VALUES ($scanningid) WHERE boxid = $boxid_value";
                    }
                    else{
                           
                        $statement = "INSERT INTO wpqa_wpsc_scan_list (scanningid) VALUES ($scanningid) WHERE boxid = $boxid_value";
                    }
                    break;
            }
       }       
       
       $res=mysqli_query($statement);
       
        if(!$res) {
            die('could not connect'.mysql_error());
        }
        
    echo "Submission Test";
    }
}








?>