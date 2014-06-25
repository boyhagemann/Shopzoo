<?php

use Illuminate\Queue\Jobs\Job;

/**
 * Class ProcessProductJob
 */
class ProcessProductJob
{
    /**
     * @param Job $job
     * @param array $payload
     */
    public function fire(Job $job, Array $payload)
    {
        // Get an instance of the importer
        $importer = App::make('TradeTrackerImporter');

        // Get the data from the product feed
        $data = $importer->process($payload['campaignId'], $payload['product']);

        // Save the task locally
        Task::unguard();
        $task = Task::firstOrNew(array('uid' => $data['uid']));
        $task->fill($data);
        $task->save();

        $job->delete();
    }
}