<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

App::singleton('TradeTrackerImporter', function() {

	$client = new SoapClient('http://ws.tradetracker.com/soap/affiliate?wsdl');
	$client->authenticate($_ENV['TRADETRACKER_USER'], $_ENV['TRADETRACKER_KEY']);

	return new TradeTrackerImporter($client);
});

$importer = App::make('TradeTrackerImporter');

// Get the info from all campaigns with the Shopzoo account on TradeTracker
$importer->info(function(TradeTrackerImporter $importer) {

	$campaigns = $importer->getClient()->getCampaigns(48216, array(
		'assignmentStatus' => 'accepted',
	));

	foreach($campaigns as $campaign) {
		$importer->setInfo($campaign->ID, $campaign->info);
	}

});
//
//// Afvalemmershop
//$importer->import(2626, function($data, $info) {
//
//	$percent = $info->commission->saleCommissionVariable;
//	$value = $data->price * ($percent / 100);
//
//	return array(
//		'uid' 			=> 'tt_' . $data->identifier,
//		'action' 		=> 'sell',
//		'provider_id' 	=> 2,
//		'title' 		=> $data->name,
//		'description' 	=> $data->description . PHP_EOL . $data->additional[10]->value,
//		'uri' 			=> $data->productURL,
//		'image' 		=> $data->imageURL,
//		'value' 		=> $value,
//		'currency' 		=> 'EUR',
//	);
//
//});
//
//// Bestelkado.nl
//$importer->import(867, function($data, $info) {
//
//	$percent = $info->commission->saleCommissionVariable;
//	$value = $data->price * ($percent / 100);
//
//	return array(
//		'uid' 			=> 'tt_' . $data->identifier,
//		'action' 		=> 'sell',
//		'provider_id' 	=> 2,
//		'title' 		=> $data->name,
//		'description' 	=> $data->description,
//		'uri' 			=> $data->productURL,
//		'image' 		=> $data->imageURL,
//		'value' 		=> $value,
//		'currency' 		=> 'EUR',
//	);
//
//});

// Algebeld.nl
$importer->import(1078, function($data, $info) {

	$value = $info->commission->saleCommissionFixed;
	$title = $data->name;

	foreach($data->additional as $additional) {

		switch( (string) $additional->name) {

			case 'brand':
				$title = $additional->value . ' ' . $title;
				break;

		}
	}

	return array(
		'uid' 			=> 'tt_' . $data->identifier,
		'action' 		=> 'sell',
		'provider_id' 	=> 2,
		'title' 		=> $title,
		'description' 	=> $data->description,
		'uri' 			=> $data->productURL,
		'image' 		=> $data->imageURL,
		'value' 		=> $value,
		'currency' 		=> 'EUR',
	);

});

// Handle all the campaign feeds
$importer->feed(function($campaignID) {

	Queue::push(function($job) use ($campaignID)
	{
		// Get an instance of the importer
		$importer = App::make('TradeTrackerImporter');

		// Get all products with this campaign
		$products = $importer->getClient()->getFeedProducts(48216, compact('campaignID'));

		$collected = array();

		// Handle each product
		foreach($products as $product) {

			// Call the function that handles the product
			$collected[] = $importer->process($campaignID, $product);
		}

		// After all product data is collected, do a batch api call

		foreach(array_chunk($collected, 1000) as $splitted) {

			$client = new GuzzleHttp\Client;
			$client->post('http://taskreward.app/api/tasks', array(
				'body' => array(
					'tasks' => $splitted,
				),
			));

		}

		$job->delete();
	});

});





Route::get('/', function()
{
	// Load all feeds
	App::make('TradeTrackerImporter')->run();

	return 'index';
});
