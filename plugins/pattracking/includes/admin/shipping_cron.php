<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//$path = preg_replace('/wp-content.*$/','',__DIR__);
//include($path.'wp-load.php');

global $current_user, $wpscfunction, $wpdb;

$shippingArray = ["usps", "fedex", "ups", "dhl"];

// Begin going through the different shipping carriers
foreach ($shippingArray as $shippingCompany)  {

switch ($shippingCompany) {
    case "usps":

$shipping_query = $wpdb->get_results(
"SELECT *
FROM wpqa_wpsc_epa_shipping_tracking
WHERE company_name = 'usps'"
);

foreach ($shipping_query as $item) {

$trackingNumber = $item->tracking_number;

if($item->shipped == 0) {
$url = "http://production.shippingapis.com/shippingAPI.dll";
$service = "TrackV2";

$xml = rawurlencode("
<TrackRequest USERID='214USENV8049'>
    <TrackID ID=\"".$trackingNumber."\"></TrackID>
    </TrackRequest>");

$request = $url . "?API=" . $service . "&XML=" . $xml;
// send the POST values to USPS
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$request);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// parameters to post

$result = curl_exec($ch);
//var_dump($result);
curl_close($ch);

$response = new SimpleXMLElement($result);

$deliveryStatus = $response->TrackInfo->TrackSummary;
$status_array = array('ACCEPTED', 'POSSESSION', 'DELIVERY', 'DELIVERED');
$table_name = 'wpqa_wpsc_epa_shipping_tracking';

if ( preg_match('('.implode('|',$status_array).')', strtoupper($deliveryStatus))){
$wpdb->update( $table_name, array( 'shipped' => 1),array('ID'=>$item->id));
$wpdb->update( $table_name, array( 'status' => $deliveryStatus),array('ID'=>$item->id));
}
}
}
        break;
    case "fedex":
        break;
    case "ups":
        break;
    case "dhl":
        break;
}

        }
        
// Change the status of request from Initial Review Complete to Shipped
$shipped_array = array();

$get_unique_tickets = $wpdb->get_results(
	"SELECT DISTINCT ticket_id
FROM wpqa_wpsc_epa_shipping_tracking"
);

foreach ($get_unique_tickets as $item) {

$ticket_id = $item->ticket_id ;
$ticket_data = $wpscfunction->get_ticket($ticket_id);
$status_id   	= $ticket_data['ticket_status'];

$get_shipped_status = $wpdb->get_results(
	"SELECT shipped
FROM wpqa_wpsc_epa_shipping_tracking
WHERE ticket_id = " . $item->ticket_id
);

foreach ($get_shipped_status as $shipped) {
	array_push($shipped_array, $shipped->shipped);
	}
	
if (($status_id == 4) && (!in_array(0, $shipped_array))) {
$wpscfunction->change_status($item->ticket_id, 5);   
}
	}
?>
