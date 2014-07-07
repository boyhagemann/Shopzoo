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


Log::listen(function($level, $message, $context) {

// Save the php sapi and date, because the closure needs to be serialized
    $apiName = php_sapi_name();
    $date = new DateTime;

    Queue::push(function() use ($level, $message, $context, $apiName, $date) {
        DB::insert("INSERT INTO logs (php_sapi_name, level, message, context, created_at) VALUES (?, ?, ?, ?, ?)", array(
            $apiName,
            $level,
            $message,
            json_encode($context),
            $date
        ));
    });

});