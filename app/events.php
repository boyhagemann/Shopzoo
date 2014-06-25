<?php

// Afvalemmershop
Event::listen('task.enrich', function(Task $task)
{
    // Only enrich if the description is missing
    if($task->description) {
        return;
    }

    $job = 'EnrichTradeTrackerCampaign' . $task->campaign_id;

    Queue::push($job, array(
        'taskId' => $task->id,
    ));
});

