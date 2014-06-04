<?php

//$importer = App::make('TradeTrackerImporter');

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
//		'image' 		=> $data->additional[9]->value, // imageURL_large
//		'value' 		=> $value,
//		'currency' 		=> 'EUR',
//	);
//
//});
