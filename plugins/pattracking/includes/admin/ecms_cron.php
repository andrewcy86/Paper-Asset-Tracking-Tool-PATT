<?php

//if ( ! defined( 'ABSPATH' ) ) {
//	exit; // Exit if accessed directly
//}

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

global $current_user, $wpscfunction, $wpdb;

// Obtain files to be transferred

$folderfile_query = $wpdb->get_results(
"SELECT wpqa_wpsc_ticket.id as ticket_id, wpqa_wpsc_epa_folderdocinfo.id as folderdocid, wpqa_wpsc_epa_folderdocinfo.folderdocinfo_id, wpqa_wpsc_ticket.ticket_status, wpqa_wpsc_epa_folderdocinfo.file_name, wpqa_wpsc_epa_folderdocinfo.file_location
FROM wpqa_wpsc_epa_folderdocinfo
INNER JOIN wpqa_wpsc_epa_boxinfo ON  wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id
INNER JOIN wpqa_wpsc_ticket ON  wpqa_wpsc_epa_boxinfo.box_id = wpqa_wpsc_ticket.id
WHERE wpqa_wpsc_epa_folderdocinfo.file_name IS NOT NULL AND wpqa_wpsc_epa_folderdocinfo.file_location LIKE '%temp_location%' AND wpqa_wpsc_ticket.ticket_status = 66"
);

foreach ($folderfile_query as $item) {
// Preliminary Test - to be commented out
echo '<strong>Folder/Document DB ID:</strong> '. $item->folderdocid . '<br />';
echo '<strong>Ticket DB ID:</strong> '. $item->ticket_id . '<br />';
echo '<strong>Ticket Status (should always be 66 or completed):</strong> '. $item->ticket_status . '<br />';
echo '<strong>Temp Storage Location & Filename:</strong> http://086.info/wordpress3'. $item->file_location . $item->file_name  . '<br />';
echo '<strong>PATT Folder/Document ID:</strong> '. $item->folderdocinfo_id;
echo '<hr />';
/*
// POST to ECMS
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://lippizzan3.rtpnc.epa.gov:8080/apiman-gateway/ecms/save/1.2?apiKey=09c7c603-3153-42f1-9eac-6b4b971e16be",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => array('metadata' => '{ 
"properties":{ 
"r_object_type":"erma_content",
"object_name":"26553380_1_Inserted Digital Signature into a PDF Document Using PIV Card (2).pdf",
"a_application_type":"BAP",
"erma_content_title":"26553380_1_Inserted Digital Signature into a PDF Document Using PIV Card (2)",
"erma_content_unid":"BAP_02312676",
"erma_content_date":"2012-09-12T21:45:32",
"erma_content_schedule":"000_000_a",
"erma_content_eventdate":"2013-09-12T21:45:32",
"erma_sensitivity_id":"3",
"erma_custodian":"mnguyen",
"erma_folder_path":"H:\\\\ECMS\\\\ECMS Share\\\\ Enhancements\\\\ECMS REST API\\\\VDD"
}
}
','content'=> new CURLFILE('/C:/Users/mnguyen/Desktop/test pdf/26553380_1_Inserted  Digital Signature into a PDF Document Using PIV Card (2).pdf')),
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic YmFwX2FkbWluOnBhcmFmYWlsQDEyMw=="
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

// Check Response success 
if ($response = '') {
// Update Ticket Status
$wpscfunction->change_status($item->ticket_id, 67);

// Update Filelocation to ECMS URL
$table_name = 'wpqa_wpsc_epa_folderdocinfo';
$ecms_location = 'link to document in ecms';
$wpdb->update( $table_name, array( 'file_location' => $ecms_location),array('ID'=>$item->folderdocid));
}

*/
}

?>
