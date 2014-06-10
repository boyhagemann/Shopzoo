<?php

use Symfony\Component\DomCrawler\Crawler;

// Afvalemmershop
Event::listen('task.enrich', function(Task $task)
{
	if($task->campaign_id != 2626) {
		return;
	}

	$id = str_replace('tt_2626_', '', $task->uid);
	$url = sprintf('http://www.afvalemmershop.nl/product/%s/%s', Str::slug($task->title), $id);


	Scraper::add('afvalemmershop-product', function(Crawler $crawler) use ($task) {

		$node = $crawler->filter('.productdetail-right .omschrijving')->first();

		$description = trim($node->html());
		$teaser = substr($description, 0, stripos(trim($node->text()), PHP_EOL));

		$task->teaser = $teaser;
		$task->description = $description;
		$task->exported = 0;
		$task->save();
	});

	Scraper::scrape('afvalemmershop-product', $url);

});

// Algebeld.nl
Event::listen('task.enrich', function(Task $task)
{
	if($task->campaign_id != 1078) {
		return;
	}

	// Get the product url
	$parsed = parse_url($task->uri);
	$query = urldecode($parsed['query']);
	parse_str($query, $vars);
	$info = parse_url($vars['r']);
	$url = $info['scheme'] . '://' . $info['host'] . $info['path'];

	// Go to the product page and get the product descrption
	Scraper::add('algebeld-product', function(Crawler $crawler) use ($task) {

		$node = $crawler->filter('#product-content-beschrijving p')->first();
		$description = trim($node->html());

		// Update the task with the scraped description
		$task->teaser = utf8_decode($description);
		$task->description = utf8_decode($description);
		$task->exported = 0;
		$task->save();
	});

	Scraper::scrape('algebeld-product', $url);

});


// Bestelkado.nl
Event::listen('task.enrich', function(Task $task)
{
	if($task->campaign_id != 867) {
		return;
	}

	// Get the product url
	$parsed = parse_url($task->uri);
	$query = urldecode($parsed['query']);
	parse_str($query, $vars);
	$info = parse_url($vars['r']);
	$url = $info['scheme'] . '://' . $info['host'] . $info['path'];

	// Go to the product page and get the product descrption
	Scraper::add('bestelkado-product', function(Crawler $crawler) use ($task) {

		$node = $crawler->filter('#desc .CPprodDet')->first();
		$description = trim($node->text());
		$teaser = Str::words($description, 60);

		// Update the task with the scraped description
		$task->teaser = $teaser;
		$task->description = $description;
		$task->exported = 0;
		$task->save();
	});

	Scraper::scrape('bestelkado-product', $url);

});
