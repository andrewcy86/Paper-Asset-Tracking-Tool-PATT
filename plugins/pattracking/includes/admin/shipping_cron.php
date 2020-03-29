<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//$path = preg_replace('/wp-content.*$/','',__DIR__);
//include($path.'wp-load.php');

global $current_user, $wpscfunction, $wpdb;

$shippingArray = ["usps", "fedex", "ups", "dhl"];

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
?>
