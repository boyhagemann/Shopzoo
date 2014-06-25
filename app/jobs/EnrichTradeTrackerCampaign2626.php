<?php

use Illuminate\Queue\Jobs\Job;
use Symfony\Component\DomCrawler\Crawler;

/**
 *
 * Afvalemmershop
 *
 * Class EnrichTradeTrackerCampaign2626
 */
class EnrichTradeTrackerCampaign2626
{
    /**
     * @param Job $job
     * @param array $payload
     */
    public function fire(Job $job, Array $payload)
    {
        $task = Task::findOrFail($payload['taskId']);

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
        $job->delete();
    }
}