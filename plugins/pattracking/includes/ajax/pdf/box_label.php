<?php

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

//Check to see if URL has the correct Request ID
if (isset($_GET['id']))
{

    //Set SuperGlobal ID variable to be used in all functions below
    $GLOBALS['id'] = $_GET['id'];

    //Function to obtain asset_tag value from database
    function fetch_request_id()
    {
        global $wpdb;
        $request_id = $wpdb->get_row( "SELECT * FROM wpqa_wpsc_ticket WHERE id = " . $GLOBALS['id']);

        $asset_id = $request_id->id;

        return $asset_id;
    }

    //Function to obtain serial number (box ID) from database based on Request ID
    function fetch_box_id()
    {
        global $wpdb;
        $array = array();
        
        $box_result = $wpdb->get_results( "SELECT * FROM wpqa_wpsc_temp_boxinfo WHERE TICKET_ID = " . $GLOBALS['id']);

        foreach ( $box_result as $box )
            {
                array_push($array, $box->BOX_ID);
            }

        return $array;

    }
    
    //Function to obtain location value from database
    function fetch_location()
    {
        global $wpdb;
        $box_digitization_center = $wpdb->get_row( "SELECT * FROM wpqa_wpsc_temp_boxinfo WHERE TICKET_ID = " . $GLOBALS['id'] . " limit 1");

        $location = $box_digitization_center->LOCATION;

        return strtoupper($location);
    }
    
    //Function to obtain create month and year from database
    function fetch_create_date()
    {
        global $wpdb;
        $request_create_date = $wpdb->get_row( "SELECT date_created FROM wpqa_wpsc_ticket WHERE id = " . $GLOBALS['id']);
        
        $create_date = $request_create_date->date_created;
        $date = strtotime($create_date);
        
        return strtoupper(date('M y', $date));
    }

    //Function to obtain request key
    function fetch_request_key()
    {
        global $wpdb;
        $request_key = $wpdb->get_row( "SELECT ticket_auth_code FROM wpqa_wpsc_ticket WHERE id = " . $GLOBALS['id']);
        
        $key = $request_key->ticket_auth_code;
        
        return $key;
    }


    //Pull in the TCPDF library
    require_once ('tcpdf/tcpdf.php');

    //Set styles
       $style_barcode = array(
        'border' => 0,
        'vpadding' => 'auto',
        'hpadding' => 'auto',
        'fgcolor' => array(
            0,
            0,
            0
        ),
        'bgcolor' => false,
        'module_width' => 1,
         'module_height' => 1 
         );
        $style_line = array(
            'width' => 1,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => '0',
            'phase' => 10,
            'color' => array(
                0,
                0,
                0
            )
        );
        $style_box_dash = array(
            'width' => 1,
            'cap' => 'butt',
            'join' => 'round',
            'dash' => '2,10',
            'color' => array(
                211,
                211,
                211
            )
        );

        //Set overall values for PDF
        $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $obj_pdf->SetCreator(PDF_CREATOR);
        $obj_pdf->SetTitle("Box Labels - Paper Asset Tracking Tool");
        $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $obj_pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $obj_pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        $obj_pdf->SetDefaultMonospacedFont('helvetica');
        $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
        $obj_pdf->setPrintHeader(false);
        $obj_pdf->setPrintFooter(false);
        $obj_pdf->SetAutoPageBreak(true, 10);
        $obj_pdf->SetFont('helvetica', '', 11);
        $obj_pdf->AddPage();
        
        //Obtain array of Box ID's
        $box_array = fetch_box_id();
        

        //Set count to 0. This count determine odd or even components of the array
        $c = 0;
        
        //Begin for loop to iterate through Box ID's array
        for ($i = 0;$i < count($box_array);$i++)
        {
            //Begin if statement to determine where to add new pages
            if ($c == 2)

            {

                $obj_pdf->AddPage();

                $c = 0;
            }

            $c++;
            //Define cordinates which need to be different for odd or even components of the Box ID array
            if ($i % 2 == 0)
            {
                // Even
                //1D barcode coordinates
                $x_loc_1d = 65;
                $y_loc_1d = 60;
                //QR barcode coordinates
                $x_loc_2d = 150;
                $y_loc_2d = 10;
                //Box x of y text coordinates
                $x_loc_b = 70;
                $y_loc_b = 105;
                //Box ID Printout coordinates
                $x_loc_c = 90;
                $y_loc_c = 87;
                //Line seperator coordinates
                $x_loc_l1 = 70;
                $y_loc_l1 = 100;
                $x_loc_l2 = 190;
                $y_loc_l2 = 100;
                //Box_a RFID coordinates
                $x_loc_ba1 = 10;
                $y_loc_ba1 = 45;
                $x_loc_la2 = 25;
                $y_loc_la2 = 70;
                //Location Coordinates
                $x_loc_l = 170;
                $y_loc_l = 80;
                //Creation Date Coordinates
                $x_loc_cd = 80;
                $y_loc_cd = 10;
                //Request ID Coordinates
                $x_loc_rid = 163;
                $y_loc_rid = 53;
                //RFID Vertical Text Coordinates
                $x_loc_rfid = 19;
                $y_loc_rfid = 110;
                //Digitization center box regular border
                $x_loc_digi_box_regular = 164;
                $y_loc_digi_box_regular = 79;
                //Digitization center box dashed border
                $x_loc_digi_box_dashed = 160.5;
                $y_loc_digi_box_dashed = 76;
            }
            else
            {
                // Odd
                //1D barcode coordinates
                $x_loc_1d = 65;
                $y_loc_1d = 210;
                //QR barcode coordinates
                $x_loc_2d = 150;
                $y_loc_2d = 160;
                //Box x of y text coordinates
                $x_loc_b = 70;
                $y_loc_b = 255;
                //Box ID Printout coordinates
                $x_loc_c = 90;
                $y_loc_c = 237;
                //Line seperator coordinates
                $x_loc_l1 = 70;
                $y_loc_l1 = 250;
                $x_loc_l2 = 190;
                $y_loc_l2 = 250;
                //Box_a RFID coordinates
                $x_loc_ba1 = 10;
                $y_loc_ba1 = 195;
                $x_loc_la2 = 25;
                $y_loc_la2 = 70;
                //Location Coordinates
                $x_loc_l = 170;
                $y_loc_l = 230;                
                //Creation Date Coordinates
                $x_loc_cd = 80;
                $y_loc_cd = 160;
                //Request ID Coordinates
                $x_loc_rid = 163;
                $y_loc_rid = 203;
                //RFID Vertical Text Coordinates
                $x_loc_rfid = 19;
                $y_loc_rfid = 260;
                //Digitization center box regular border
                $x_loc_digi_box_regular = 164;
                $y_loc_digi_box_regular = 229;
                //Digitization center box dashed border
                $x_loc_digi_box_dashed = 160.5;
                $y_loc_digi_box_dashed = 226;
            }
            //Determine box count out of total
            $initial_box = $i + 1;
            $total_box = count($box_array);
            $obj_pdf->SetFont('helvetica', 'B', 28);
            $obj_pdf->Text($x_loc_b, $y_loc_b, "Box " . $initial_box . " of " . $total_box);
            $obj_pdf->Line($x_loc_l1, $y_loc_l1, $x_loc_l2, $y_loc_l2, $style_line);
            //RFID Box Location
            //$obj_pdf->Rect($x_loc_ba1, $y_loc_ba1, $x_loc_la2, $y_loc_la2, 'D', array(
                //'all' => $style_box_dash
            //));
            
            //Digitization center box regular border
            $obj_pdf->Rect($x_loc_digi_box_regular, $y_loc_digi_box_regular, 30, 10, '', '', array(0, 0, 0));
            
            //Digitization center box dashed border
            $obj_pdf->RoundedRect($x_loc_digi_box_dashed, $y_loc_digi_box_dashed, 38, 16, 2, '1111', null, $style_box_dash);
            
            //RFID Box Location
            $obj_pdf->RoundedRect($x_loc_ba1, $y_loc_ba1, $x_loc_la2, $y_loc_la2, 5, '1111', null, $style_box_dash);
            
            //RFID Vertical Text
            $obj_pdf->StartTransform();
            $obj_pdf->SetFont('helvetica', '', 17);
            $obj_pdf->Rotate(90, $x_loc_rfid, $y_loc_rfid);
            $obj_pdf->Text($x_loc_rfid,$y_loc_rfid,'Place RFID Tag Here');
            $obj_pdf->StopTransform();
            
            //1D Box ID Barcode
            $obj_pdf->SetFont('helvetica', '', 11);
            $obj_pdf->write1DBarcode($box_array[$i], 'C128', $x_loc_1d, $y_loc_1d, '', 30, 0.7, $style_barcode, 'N');
            //$obj_pdf->Cell($x_loc_c, $y_loc_c, $box_array[$i], 0, 1);
            //1D Box ID Printout
            $obj_pdf->SetFont('helvetica', 'B', 24);
            $obj_pdf->Text($x_loc_c, $y_loc_c, $box_array[$i]);
            $obj_pdf->SetFont('helvetica', '', 14);
            
            $num = fetch_request_id();
            $str_length = 7;
            $padded_request_id = substr("000000{$num}", -$str_length);
            
            $obj_pdf->Text($x_loc_rid, $y_loc_rid, $padded_request_id);
            
            $obj_pdf->SetFont('helvetica', '', 11);
            $url_id = fetch_request_id();
            $url_key = fetch_request_key();
            //QR Code of Request
            $url = 'http://' . $_SERVER['SERVER_NAME'] . '/wordpress2/support-ticket/?support_page=open_ticket&ticket_id=' . $url_id . '&auth_code=' . $url_key;
            //$obj_pdf->writeHTML($url);
            $obj_pdf->write2DBarcode($url, 'QRCODE,H', $x_loc_2d, $y_loc_2d, '', 50, $style_barcode, 'N');
            //$obj_pdf->Cell(150, 50, $url, 0, 1);
            $obj_pdf->SetFont('helvetica', 'B', 18);
            $obj_pdf->Text($x_loc_l, $y_loc_l, fetch_location());
            $obj_pdf->SetFont('helvetica', 'B', 40);
            $obj_pdf->Text($x_loc_cd, $y_loc_cd, fetch_create_date()); 
            $obj_pdf->SetFont('helvetica', '', 11);  
        }
        //Generate PDF
        $obj_pdf->Output('file.pdf', 'I');
    }
    else
    {
        //Define message for when no ID exists in URL
        echo "Pass request ID in URL";
    }
?>
