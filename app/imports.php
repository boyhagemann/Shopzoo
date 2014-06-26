<?php


// Afvalemmershop
App::make('TradeTrackerImporter')->import(2626, function(Array $data) {

    $campaign = Campaign::findBySourceAndId('tradetracker', 2626);

    $value = $data['price'] * ($campaign->sales_percentage / 100);

    $uri = str_replace('?c=', '?r=[token]&c=', $data['productURL']);

    return array(
        'campaign_id' 	=> 2626,
        'uid' 			=> 'tt_2626_' . $data['identifier'],
        'action' 		=> 'sell',
        'provider_id' 	=> 2,
        'title' 		=> $data['name'],
        'teaser' 		=> $data['description'] . PHP_EOL . $data['additional'][10]['value'],
//		'description' 	=> $data->description . PHP_EOL . $data->additional[10]->value, // Tekst scrape
        'uri' 			=> $uri,
        'image' 		=> $data['imageURL'],
        'value' 		=> $value,
        'currency' 		=> 'EUR',
    );

});
//
//// Bestelkado.nl
//App::make('TradeTrackerImporter')->import(867, function(Array $data) {
//
//    $campaign = Campaign::findBySourceAndId('tradetracker', 867);
//
//    $value = $data['price'] * ($campaign->sales_percentage / 100);
//
//    $uri = str_replace('?c=', '?r=[token]&c=', $data['productURL']);
//
//    return array(
//        'campaign_id' 	=> 867,
//        'uid' 			=> 'tt_867_' . $data['identifier'],
//        'action' 		=> 'sell',
//        'provider_id' 	=> 2,
//        'title' 		=> $data['name'],
//        'teaser' 		=> $data['description'],
//        'description' 	=> null,
//        'uri' 			=> $uri,
//        'image' 		=> $data['imageURL'],
//        'value' 		=> $value,
//        'currency' 		=> 'EUR',
//    );
//
//});
//
//
//// Algebeld.nl
//App::make('TradeTrackerImporter')->import(1078, function(Array $data) {
//
//    $campaign = Campaign::findBySourceAndId('tradetracker', 1078);
//    $value = $campaign->sales_value;
//
//    $uri = str_replace('?c=', '?r=[token]&c=', $data['productURL']);
//
//    $row = array(
//        'campaign_id' 	=> 1078,
//        'uid' 			=> 'tt_1078_' . $data['identifier'],
//        'action' 		=> 'sell',
//        'provider_id' 	=> 2,
//        'title' 		=> $data['name'],
//        'teaser' 		=> $data['description'],
//        'description' 	=> $data['description'],
//        'uri' 			=> $uri,
//        'image' 		=> $data['imageURL'],
//        'value' 		=> $value,
//        'currency' 		=> 'EUR',
//    );
//
//    return $row;
//
//});
