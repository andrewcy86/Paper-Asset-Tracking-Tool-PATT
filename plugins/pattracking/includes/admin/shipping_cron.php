<?php
// Code to inject label button

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$trackingNumber = '420917619301920130200546438392';

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
echo '<strong>Summary</strong>: '.$deliveryStatus;

echo '<ul>';
foreach ($response->TrackInfo->TrackDetail as $detail) {
    echo '<li>' . $detail . '</li>';
}
echo '</ul>';

?>