<?php

use Illuminate\Queue\Jobs\Job;
use Symfony\Component\DomCrawler\Crawler;

/**
 *
 * Bestelkado.nl
 *
 * Class EnrichTradeTrackerCampaign867
 */
class EnrichTradeTrackerCampaign867
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

        $job->delete();
    }
}