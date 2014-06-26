<?php

use Illuminate\Queue\Jobs\Job;

/**
 * Class ImportTradeTrackerCampaignInfo
 */
class ImportTradeTrackerCampaignInfo
{
    /**
     * @param Job $job
     */
    public function fire(Job $job)
    {
        // Get an instance of the importer
        $importer = App::make('TradeTrackerImporter');

        $campaigns = $importer->getClient()->getCampaigns(48216, array(
            'assignmentStatus' => 'accepted',
        ));

        foreach($campaigns as $data) {

            Campaign::unguard();
            $campaign = Campaign::firstOrCreate([
                'source' => 'tradetracker',
                'campaign_id' => $data->ID,
            ]);

            $campaign->name = $data->name;
            $campaign->url = $data->URL;

            $commission = $data->info->commission;
            $campaign->lead_value = $commission->leadCommission;
            $campaign->sales_value = $commission->saleCommissionFixed;
            $campaign->sales_percentage = $commission->saleCommissionVariable;
            $campaign->save();
        }

        $job->delete();
    }
}