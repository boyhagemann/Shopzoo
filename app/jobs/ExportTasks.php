<?php

use Illuminate\Queue\Jobs\Job;

/**
 * Class ExportTasks
 */
class ExportTasks
{
    /**
     * @param Job $job
     */
    public function fire(Job $job)
    {
        $this->execute();
        $job->delete();
    }

    public function execute()
    {
        // Get all tasks that needs to be exported
        $tasks = Task::where('exported', 0)->get();

        Log::info('Number of tasks to be exported: ' . $tasks->count());

        // Split up the data to post, this can be too large for just one post request.
        foreach(array_chunk($tasks->toArray(), 300) as $splitted) {

            $uri = Config::get('services.taskreward.uri') . '/api/tasks';
            $client = new GuzzleHttp\Client;
            $response = $client->post($uri, array(
                'body' => array(
                    'tasks' => $splitted,
                ),
            ));

            Log::info('Task export response', json_decode($response->getBody(), true));
        }

        // All tasks are exported, flag it.
        DB::table('tasks')->where('exported', 0)->update(array('exported' => 1));

    }
}