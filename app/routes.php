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
	$client->authenticate(20367, '3a311fd3d68428c72d22576bb158848f95e1e0b5');

//	$response = $client->getAffiliateSites();
//	$response = $client->getCampaigns(48216, array(
//		'assignmentStatus' => 'accepted',
//	));
//	$response = $client->getFeedProducts(48216, array(
//		'campaignID' => 2626,
//	));

	return new TradeTrackerImporter($client);
});

$importer = App::make('TradeTrackerImporter');

// Afvalemmershop
$importer->import(2626, function($data) {

	return Product::create(array(
		'title' => $data->name,
		'description' => 'test',
		'uri' => 'test',
	));

});

// Handle all the campaign feeds
$importer->feed(function($campaignID) {

	Queue::push(function($job) use ($campaignID)
	{
		// Get an instance of the importer
		$importer = App::make('TradeTrackerImporter');

		// Get all products with this campaign
		$products = $importer->getClient()->getFeedProducts(48216, compact('campaignID'));

		// Handle each product
		foreach($products as $product) {

			// Call the function that handles the product
			$products[] = $importer->process($campaignID, $product);
		}

		$job->delete();
	});

});

Product::saved(function(Product $product) {

	$client = new GuzzleHttp\Client;
	$client->post('http://taskreward.app:8000/api/tasks', array(
		'body' => $product->toArray(),
	));

});




Route::get('/', function()
{
	// Load all feeds
	App::make('TradeTrackerImporter')->run();

	return 'index';
});
