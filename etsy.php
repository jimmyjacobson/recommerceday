<?php
include('config.php');

$keywords = $_GET["keywords"];
$listingUrl = "http://openapi.etsy.com/v2/listings/active?api_key=$etsyKey&keywords=$keywords&limit=1";

$json = @file_get_contents($listingUrl, 0, null, null);
$listing = json_decode($json);
if (count($listing->results) > 0) {
	$listingId = $listing->results[0]->listing_id;
	$imgUrl = "http://openapi.etsy.com/v2/listings/$listingId/images?api_key=$etsyKey";

	$json = @file_get_contents($imgUrl, 0, null, null);
	$image = json_decode($json);

	$response = array(
		"title" => $listing->results[0]->title,
		"url" => $listing->results[0]->url,
		"images" => $image->results[0]
		);
print_r(json_encode($response));
}
else {
	print_r(json_encode(array("title" => null)));
}

?>
