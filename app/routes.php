<?php

use Symfony\Component\DomCrawler\Crawler;

ini_set('max_execution_time', 600);


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













Route::get('/import/2626', function()
{
	// Afvalemmershop
	App::make('TradeTrackerImporter')->import(2626, function($data, $info) {

		dd($data);

		$percent = $info->commission->saleCommissionVariable;
		$value = $data->price * ($percent / 100);

		return array(
			'campaign_id' 	=> 2626,
			'uid' 			=> 'tt_' . $data->identifier,
			'action' 		=> 'sell',
			'provider_id' 	=> 2,
			'title' 		=> $data->name,
			'teaser' 		=> $data->description . PHP_EOL . $data->additional[10]->value,
//		'description' 	=> $data->description . PHP_EOL . $data->additional[10]->value, // Tekst scrap
			'uri' 			=> $data->productURL,
			'image' 		=> $data->imageURL,
			'value' 		=> $value,
			'currency' 		=> 'EUR',
		);

	});

	// Load all feeds
	App::make('TradeTrackerImporter')->run();

	return Redirect::to('/');
});


Route::get('/import/867', function()
{
	// Bestelkado.nl
	App::make('TradeTrackerImporter')->import(867, function($data, $info) {

		$percent = $info->commission->saleCommissionVariable;
		$value = $data->price * ($percent / 100);

		return array(
			'campaign_id' 	=> 867,
			'uid' 			=> 'tt_' . $data->identifier,
			'action' 		=> 'sell',
			'provider_id' 	=> 2,
			'title' 		=> $data->name,
			'teaser' 		=> $data->description,
			'description' 	=> $data->description,
			'uri' 			=> $data->productURL,
			'image' 		=> $data->imageURL,
			'value' 		=> $value,
			'currency' 		=> 'EUR',
		);

	});

	// Load all feeds
	App::make('TradeTrackerImporter')->run();

	return Redirect::to('/');
});


Route::get('/import/1078', function()
{
	// Algebeld.nl
	App::make('TradeTrackerImporter')->import(1078, function($data, $info) {

		$value = $info->commission->saleCommissionFixed;

		$row = array(
			'campaign_id' 	=> 1078,
			'uid' 			=> 'tt_' . $data->identifier,
			'action' 		=> 'sell',
			'provider_id' 	=> 2,
			'title' 		=> $data->name,
			'teaser' 		=> $data->description,
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

	// Load all feeds
	App::make('TradeTrackerImporter')->run();

	return Redirect::to('/');
});



Route::get('/', function()
{
	return View::make('import');
});

Route::get('export', function()
{
	$tasks = Task::where('exported', 0)->get();

	foreach(array_chunk($tasks->toArray(), 300) as $splitted) {

		$client = new GuzzleHttp\Client;
		$client->post('http://taskreward.app/api/tasks', array(
			'body' => array(
				'tasks' => $splitted,
			),
		));

	}

	DB::table('tasks')->where('exported', 0)->update(array('exported' => 1));

	return Redirect::to('/');
});

Route::get('description/2626', function()
{
	$tasks = Task::where('campaign_id', 2626)->whereNull('description')->limit(50)->get();

	foreach($tasks as $task) {

		Scraper::add('afvalemmershop-product', function(Crawler $crawler) use ($task) {

			$node = $crawler->filter('.productdetail-right .omschrijving')->first();

			$description = trim($node->html());
			$teaser = substr($description, 0, stripos(trim($node->text()), PHP_EOL));

			$task->teaser = $teaser;
			$task->description = $description;
			$task->exported = 0;
			$task->save();
		});

		$id = str_replace('tt_', '', $task->uid);
		$url = sprintf('http://www.afvalemmershop.nl/product/%s/%s', Str::slug($task->title), $id);

		Scraper::scrape('afvalemmershop-product', $url);

	}
	return Redirect::to('/');
});

