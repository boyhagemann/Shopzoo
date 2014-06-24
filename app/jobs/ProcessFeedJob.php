<?php

use Illuminate\Queue\Jobs\Job;

class ProcessFeedJob
{
    public function fire(Job $job, $payload)
    {
        // Get an instance of the importer
        $importer = App::make('TradeTrackerImporter');

        $campaignId = $payload['campaignID'];

        // Get all products with this campaign
        $products = $importer->getClient()->getFeedProducts(48216, $campaignId);

        $i = 0;

        // Handle each product
        foreach($products as $product) {

            $i++;

            if($i >= 5) {
                break;
            }

            Queue::push('ProcessProductJob', compact('campaignId', 'product'));

        }

        $job->delete();
    }
}