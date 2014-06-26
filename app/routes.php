<?php



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
    $queue = new Pheanstalk_Pheanstalk('localhost');
    $stats = $queue->stats();

    try {
        $ready = $queue->peekReady();
    }
    catch(Pheanstalk_Exception_ServerException $e) {
        $ready = null;
    }

	return View::make('import', compact('stats', 'ready'));
});


Route::get('/import/{id}', function($id)
{
    // Load all feeds
    App::make('TradeTrackerImporter')->run($id);

    return Redirect::to('/');
});


Route::get('enrich', function()
{
	$tasks = Task::whereNull('description')->limit(20)->get();

	foreach($tasks as $task) {
		Event::fire('task.enrich', array($task));
	}

	if(Task::whereNull('description')->count()) {
		return Redirect::to('enrich');
	}

	return Redirect::to('/');
});

Route::get('export', function()
{
    Queue::push('ExportTasks');

	return Redirect::to('/');
});

Route::get('clicks', function()
{
    Queue::push('ExportTradeTrackerClicks');

    return Redirect::to('/');
});


Route::get('delete', function()
{
    $queue = new Pheanstalk_Pheanstalk('localhost');

    try
    {
        while($job = $queue->peekReady('default'))
        {
            $queue->delete($job);
        }
    }
    catch(\Pheanstalk_Exception_ServerException $e){}

    return Redirect::to('/');
});