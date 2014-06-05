<?php

use Symfony\Component\DomCrawler\Crawler;

ini_set('max_execution_time', 300);


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

// Afvalemmershop
$importer->import(2626, function($data, $info) {

	$percent = $info->commission->saleCommissionVariable;
	$value = $data->price * ($percent / 100);

	return array(
		'uid' 			=> 'tt_' . $data->identifier,
		'action' 		=> 'sell',
		'provider_id' 	=> 2,
		'title' 		=> $data->name,
		'description' 	=> $data->description . PHP_EOL . $data->additional[10]->value,
		'uri' 			=> $data->productURL,
		'image' 		=> $data->imageURL,
		'value' 		=> $value,
		'currency' 		=> 'EUR',
	);

});

// Bestelkado.nl
$importer->import(867, function($data, $info) {

	$percent = $info->commission->saleCommissionVariable;
	$value = $data->price * ($percent / 100);

	return array(
		'uid' 			=> 'tt_' . $data->identifier,
		'action' 		=> 'sell',
		'provider_id' 	=> 2,
		'title' 		=> $data->name,
		'description' 	=> $data->description,
		'uri' 			=> $data->productURL,
		'image' 		=> $data->imageURL,
		'value' 		=> $value,
		'currency' 		=> 'EUR',
	);

});

// Algebeld.nl
$importer->import(1078, function($data, $info) {

	$value = $info->commission->saleCommissionFixed;

	$row = array(
		'uid' 			=> 'tt_' . $data->identifier,
		'action' 		=> 'sell',
		'provider_id' 	=> 2,
		'title' 		=> $data->name,
		'description' 	=> $data->description,
		'uri' 			=> $data->productURL,
		'image' 		=> $data->imageURL,
		'value' 		=> $value,
		'currency' 		=> 'EUR',
	);

	foreach($data->additional as $additional) {

		switch( (string) $additional->name) {

			case 'brand':
				$row['title'] = (string) $additional->value . ' ' . $row['title'];
				break;

		}
	}

	return $row;

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
			$data = $importer->process($campaignID, $product);

			// Save the task locally
			Task::unguard();
			$task = Task::firstOrNew(array('uid' => $data['uid']));
			$task->fill($data);
			$task->save();
		}

		$job->delete();
	});

});







//Scraper::add('google-shopping-search', function(Crawler $crawler) {
//
//	$crawler->filter('h3.r a')->each(function($node) {
//
//		$url = $node->attr('href');
//
//		if(strpos($url, '/aclk?sa=') === 0) {
//			return;
//		}
//
//		$url = 'https://www.google.nl' . $url;
//
//		Scraper::scrape('google-shopping-product', $url);
//	});
//
//});
//
//Scraper::add('google-shopping-product', function(Crawler $crawler) use (&$row) {
//
//	$crawler->filter('#product-description-full')->each(function($node) {
//		$description = trim($node->text());
//
//		$description ? $row['description'] = $description : null;
//	});
//
//});
//
//$url = sprintf('https://www.google.nl/search?q=%s&gbv=1&tbm=shop', urlencode($row['title']));
//
//Scraper::scrape('google-shopping-search', $url);




Route::get('/', function()
{
	// Load all feeds
	App::make('TradeTrackerImporter')->run();

	return Redirect::to('export');
});

Route::get('export', function()
{
	$tasks = Task::where('exported', 0)->get();

	foreach(array_chunk($tasks->toArray(), 1000) as $splitted) {

		$client = new GuzzleHttp\Client;
		$client->post('http://taskreward.app/api/tasks', array(
			'body' => array(
				'tasks' => $splitted,
			),
		));

	}

	return 'exported';
});
