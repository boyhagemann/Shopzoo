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

Route::get('info', function() {

    Queue::push('ImportTradeTrackerCampaignInfo');
    return Redirect::to('/')
        ->withSuccess('The campaign info is now queued for update');
});

Route::get('/import/{id?}', function($id = null)
{
    // Load all feeds
    App::make('TradeTrackerImporter')->run($id);

    return Redirect::to('/')
        ->withSuccess('The product feed is now queued for import');
});

Route::get('export', function()
{
    Artisan::call('export:tasks');

	return Redirect::to('/')
        ->withSuccess('The tasks are now queued for export');
});

Route::get('clicks', function()
{
    Queue::push('ExportTradeTrackerClicks')
        ->withSuccess('The clicks are now queued for export');

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

    return Redirect::to('/')
        ->withSuccess('All queued jobs are deleted succesfully');
});