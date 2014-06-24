<?php

use Illuminate\Queue\Jobs\Job;

class ProcessProductJob
{
    public function fire(Job $job, $payload)
    {
        // Get an instance of the importer
        $importer = App::make('TradeTrackerImporter');

        // Call the function that handles the product
        $data = $importer->process($payload['campaignId'], $payload['product']);

        // Save the task locally
        Task::unguard();
        $task = Task::firstOrNew(array('uid' => $data['uid']));
        $task->fill($data);
        $task->save();

        $job->delete();
    }
}