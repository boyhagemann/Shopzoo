<?php

use Illuminate\Queue\Jobs\Job;
use Symfony\Component\DomCrawler\Crawler;

/**
 *
 * Algebeld.nl
 *
 * Class EnrichTradeTrackerCampaign1078
 */
class EnrichTradeTrackerCampaign1078
{
    /**
     * @param Job $job
     * @param array $payload
     */
    public function fire(Job $job, Array $payload)
    {
        $task = Task::findOrFail($payload['taskId']);

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

        $job->delete();
    }
}