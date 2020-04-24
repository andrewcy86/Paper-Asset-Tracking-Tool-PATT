<?php

// TODO:  Create the following supporting MYSQL functions:
//          mysql_query(<STATEMENT>)
//          mysql_num_rows(<QUERY>)

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
              $reg_cartid = "/\b(CID-\d\d-e|CID-\d\d-w)\b/";   

            // Staging barcode
              $reg_stagingarea = "/\b(sa-e|sa-w)\b/";  

            // Scanning barcode
              $reg_scanning = "/\b(SCN-\d\d-e|SCN-\d\d-w)\b/";
              
            // Combined bay and shelf barcode
            $reg_bay_and_shelf = "/\b(\d\d\d-[a-zA-Z]{3})\b/";  

            // Bay barcode
            $reg_bay = "/\b(\d\d\d)\b/";  

            // Shelf barcode
            $reg_shelf = "/(\A[a-zA-Z]{3}\z)/iA";
      
            switch($scan)
            {
                case (preg_match($reg_bay_and_shelf, $scan)? true : false):
                    // Case a bay and shelf barcode
                    // Determine if an update or insert will be performed
                    
                    // Seperate the bay and shelf into thier own variable
                    $shelfid = preg_match($reg_shelf, $input_line, $output_array);
                    $bayid = preg_match($reg_bay, $input_line, $output_array);
                    

                        if ($count > 0){
                            
                            $statement = "UPDATE wpqa_wpsc_scan_list SET (shelfid) VALUES ($shelfid) WHERE boxid = $boxid_value";
                        }
                        else{
                            
                            $statement = "INSERT INTO wpqa_wpsc_scan_list (shelfid) VALUES ($shelfid) WHERE boxid = $boxid_value";
                                 
                        }

                        if ($count > 0){
                            
                            $statement = "UPDATE wpqa_wpsc_scan_list SET (bayid) VALUES ($bayid) WHERE boxid = $boxid_value";
                        }
                        else{
                            
                            $statement = "INSERT INTO wpqa_wpsc_scan_list (bayid) VALUES ($bayid) WHERE boxid = $boxid_value";
                                 
                        }

                    break;
                case (preg_match($reg_cart, $scan)? true : false):
                    // Case a cart barcode
                    // Determine if an update or insert will be performed
                    if ($count > 0){
                        
                        $statement = "UPDATE wpqa_wpsc_scan_list SET (cartid) VALUES ($scan) WHERE boxid = $boxid_value";
                    }
                    else{
                        
                        $statement = "INSERT INTO wpqa_wpsc_scan_list (cartid) VALUES ($scan) WHERE boxid = $boxid_value";
                             
                    }
                    
                    break;
                    
                case (preg_match($reg_stagingarea, $scan)? true : false):
                    
                    // Case a staging area barcode
                    // Determine if an update or insert will be performed
                    if ($count > 0){
                        
                         $statement = "UPDATE wpqa_wpsc_scan_list SET (stagingareaid) VALUES ($scan) WHERE boxid = $boxid_value"; 
                    }
                    else{
                        
                         $statement = "INSERT INTO wpqa_wpsc_scan_list (staginareaid) VALUES ($scan) WHERE boxid = $boxid_value";
                         
                    }
                    break;
                    
                case (preg_match($reg_scanning, $scan)? true : false):
                    
                    // Case a scanner barcode
                    // Determine if an update or insert will be performed
                    if ($count > 0){
                        
                        $statement = "UPDATE wpqa_wpsc_scan_list SET (scanningid) VALUES ($scan) WHERE boxid = $boxid_value";
                    }
                    else{
                           
                        $statement = "INSERT INTO wpqa_wpsc_scan_list (scanningid) VALUES ($scan) WHERE boxid = $boxid_value";
                    }
                    break;
            }
       }       
       
       $res = mysqli_query($statement);
       
        if(!$res) {
            die('could not connect: '.mysql_error());
        }else{
            
            echo '<script language="javascript">';
            echo 'alert("Location scans for the box(es) were successfully updated."';
            echo '</script>';
        }
    }
}








?>