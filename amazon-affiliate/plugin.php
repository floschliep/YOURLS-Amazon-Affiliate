<?php
/*
Plugin Name: Amazon Affiliate
Plugin URI: https://github.com/floschliep/YOURLS-Amazon-Affiliate
Description: Add your Amazon Affiliate-Tag to all Amazon URLs before redirection
Version: 1.2
Author: Florian Schliep
Author URI: https://floschliep.com
*/

yourls_add_action('pre_redirect', 'flo_amazonAffiliate');

function flo_amazonAffiliate($args) {
	// insert your personal settings here
	$tagIN = 'YOUR_TAG_HERE';
	$tagIT = 'YOUR_TAG_HERE';
	$tagUS = 'YOUR_TAG_HERE';
	$tagDE = 'YOUR_TAG_HERE';
	$tagUK = 'YOUR_TAG_HERE';
	$tagFR = 'YOUR_TAG_HERE';
	$tagES = 'YOUR_TAG_HERE';
	$tagJP = 'YOUR_TAG_HERE';
	$tagAU = 'YOUR_TAG_HERE';
	$campaign = 'YOUR_CAMPAIGN_HERE';
	
	// get url from arguments; create dictionary with all regex patterns and their respective affiliate tag as key/value pairs
	$url = $args[0];
	$patternTagPairs = array(
		'/^http(s)?:\\/\\/(www\\.)?amazon.in+/ui' => $tagIN,
		'/^http(s)?:\\/\\/(www\\.)?amazon.it+/ui' => $tagIT,
		'/^http(s)?:\\/\\/(www\\.)?amazon.com.au+/ui' => $tagAU,
		'/^http(s)?:\\/\\/(www\\.)?amazon.com+/ui' => $tagUS,
		'/^http(s)?:\\/\\/(www\\.)?amazon.de+/ui' => $tagDE,
		'/^http(s)?:\\/\\/(www\\.)?amazon.co.uk+/ui' => $tagUK,
		'/^http(s)?:\\/\\/(www\\.)?amazon.fr+/ui' => $tagFR,
		'/^http(s)?:\\/\\/(www\\.)?amazon.es+/ui' => $tagES,
		'/^http(s)?:\\/\\/(www\\.)?amazon.co.jp+/ui' => $tagJP
	);
	
	// check if URL is a supported Amazon URL
	foreach ($patternTagPairs as $pattern => $tag) {
		if (preg_match($pattern, $url) == true) {
			// matched URL, now modify URL
			$url = cleanUpURL($url);
			$url = addTagToURL($url, $tag);
			$url = addCampaignToURL($url, $campaign);

			// redirect
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: $url");
			
			// now die so the normal flow of event is interrupted
			die();
		}
	}
}

function cleanUpURL($url) {
	// check if last char is an "/" (in case it is, remove it)
	if (substr($url, -1) == "/") {
		$url = substr($url, 0, -1);
	}
	
	// remove existing affiliate tag if needed
	$existingTag;
	if (preg_match('/tag=.+&?/ui', $url, $matches) == true) {
		$existingTag = $matches[0]; 
	}
	if ($existingTag) {
		$url = str_replace($existingTag, "", $url);
	}
	
	// remove existing campaign if needed
	$existingCampagin;
	if (preg_match('/camp=.+&?/ui', $url, $matches) == true) {
		$existingCampagin = $matches[0]; 
	}
	if ($existingCampagin) {
		$url = str_replace($existingCampagin, "", $url);
	}
	
	return $url;
}

function addTagToURL($url, $tag) {
	// add our tag to the URL
	if (strpos($url, '?') !== false) { 
		// there's already a query string in our URL, so add our tag with "&"
		// add tag depending on if we need to add a "&" or not
		if (substr($url, -1) == "&") {
			$url = $url.'tag='.$tag;
		} else {
			$url = $url.'&tag='.$tag;
		}
	} else { // start a new query string
		$url = $url.'?tag='.$tag;
	}

	return $url;
}

function addCampaignToURL($url, $campaign) {
	if (empty($campaign)) {
		return $url;
	}
	$url = $url.'&camp='.$campaign;
	
	return $url;
}

?>
