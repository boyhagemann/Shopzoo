<?php


// Afvalemmershop
Route::get('/import/2626', function()
{
	App::make('TradeTrackerImporter')->import(2626, function($data, $info) {

		$percent = $info->commission->saleCommissionVariable;
		$value = $data->price * ($percent / 100);

		return array(
			'campaign_id' 	=> 2626,
			'uid' 			=> 'tt_2626_' . $data->identifier,
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



// Bestelkado.nl
Route::get('/import/867', function()
{
	App::make('TradeTrackerImporter')->import(867, function($data, $info) {

		$percent = $info->commission->saleCommissionVariable;
		$value = $data->price * ($percent / 100);

		return array(
			'campaign_id' 	=> 867,
			'uid' 			=> 'tt_867_' . $data->identifier,
			'action' 		=> 'sell',
			'provider_id' 	=> 2,
			'title' 		=> $data->name,
			'teaser' 		=> $data->description,
			'description' 	=> null,
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



// Algebeld.nl
Route::get('/import/1078', function()
{
	App::make('TradeTrackerImporter')->import(1078, function($data, $info) {

		$value = $info->commission->saleCommissionFixed;

		$row = array(
			'campaign_id' 	=> 1078,
			'uid' 			=> 'tt_1078_' . $data->identifier,
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

		return $row;

	});

	// Load all feeds
	App::make('TradeTrackerImporter')->run();

	return Redirect::to('/');
});