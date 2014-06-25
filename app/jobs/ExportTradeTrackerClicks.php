<?php

use Illuminate\Queue\Jobs\Job;

/**
 * Class ExportTradeTrackerClicks
 */
class ExportTradeTrackerClicks
{
    /**
     * @param Job $job
     */
    public function fire(Job $job)
    {
        $client = App::make('TradeTrackerImporter')->getClient();
        $transactions = $client->getClickTransactions(48216);

        foreach($transactions as $transaction) {

            if(!$transaction->reference) {
                continue;
            }

            $batch[] = array(
                'uid' => 'tt_' . $transaction->ID,
                'token' => $transaction->reference,
                'value' => $transaction->commission,
                'currency' => $transaction->currency,
            );
        }

        $uri = Config::get('services.taskreward.uri') . '/api/rewards';
        $client = new GuzzleHttp\Client;
        $client->post($uri, array(
            'body' => compact('batch'),
        ));

        $job->delete();
    }
}