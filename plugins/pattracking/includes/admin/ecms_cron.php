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

//POST Request to Content Ingestion Endpoint

$target_url = 'http://lippizzan3.rtpnc.epa.gov:8080/apiman-gateway/ecms/save/1.2?apiKey=XXXX';
//Username.
$username = 'xxxx';
 
//Password.
$password = 'xxxx';

$file_name_with_full_path ='final_2017_cgp_current_as_of_6-6-2019.pdf';

//$cfile = new CURLFile($file_name_with_full_path,mime_content_type($file_name_with_full_path),'imported_pdf');    
// Assign POST data

$fileHandler = fopen($file_name_with_full_path, 'r');
$fileData = fread($fileHandler, filesize($file_name_with_full_path));

$post = array( 
'metadata' => '{ 
"properties":{ 
"r_object_type":"erma_content",
"object_name":"final_2017_cgp_current_as_of_6-6-2019.pdf",
"a_application_type":"PATT",
"erma_content_title":"NPDES",
"erma_content_unid":"0000001a",
"erma_content_date":"2012-09-12T21:45:32",
"erma_content_schedule":"000_000_a",
"erma_content_eventdate":"2013-09-12T21:45:32",
"erma_sensitivity_id":"0",
"erma_custodian":"ayuen",
"erma_folder_path":""
}
}
','content'=> $fileData);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL,$target_url);
curl_setopt($curl, CURLOPT_USERPWD, $username.":".$password);
curl_setopt($curl, CURLOPT_INFILE, $fileHandler);
curl_setopt($curl, CURLOPT_INFILESIZE, filesize($file_name_with_full_path));
curl_setopt($curl, CURLOPT_POST,1);
curl_setopt($curl, CURLOPT_POST, count($post));
curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl, CURLOPT_VERBOSE,true);
curl_setopt($curl, CURLINFO_HEADER_OUT, true);
$result = curl_exec($curl);
$retry = 0;
$json = json_decode($result, true);
//print_r($json);

if (array_key_exists("status",$json) || array_key_exists("code",$json))
  {
while($json['status'] == 401 && $json['code'] == 'E_BAD_CREDENTIALS_ERROR' && $retry < 3){
    $result = curl_exec($curl);
    $retry++;
}
  }

if(!$result){
    die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
}

$information = curl_getinfo($curl);
print_r( $information);

curl_close($curl);
var_dump($result);

// Error Handling

// Check Response success 
if ($response = '') {
// Update Ticket Status
$wpscfunction->change_status($item->ticket_id, 67);

// Update Filelocation to ECMS URL
$table_name = 'wpqa_wpsc_epa_folderdocinfo';
$ecms_location = 'link to document in ecms';
$wpdb->update( $table_name, array( 'file_location' => $ecms_location),array('ID'=>$item->folderdocid));
}

}

?>
