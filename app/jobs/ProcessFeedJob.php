<?php

use Illuminate\Queue\Jobs\Job;

/**
 * Class ProcessFeedJob
 */
class ProcessFeedJob
{
    /**
     * @param Job $job
     * @param array $payload
     */
    public function fire(Job $job, Array $payload)
    {
        // Get an instance of the importer
        $importer = App::make('TradeTrackerImporter');
        $importer->auth();

        // This is the target campaign. From this campaign we need the
        // products to import them in a different job.
        $campaignId = $payload['campaignID'];

        // Get all products with this campaign
        $products = $importer->getClient()->getFeedProducts(48216, $payload);

        // Handle each product
        foreach($products as $product) {

            // For each product, process it in a different job
            Queue::push('ProcessProductJob', compact('campaignId', 'product'));
        }

        // See how much memory is used.
        Log::info(sprintf('Memory used: %s', memory_get_usage(true)));

        $job->delete();
    }
}