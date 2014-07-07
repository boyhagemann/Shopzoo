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
    $logs = array();

    $pattern = "/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/";
    $files = File::glob(storage_path('**/*.log'));
    foreach($files as $file) {
        $body = File::get($file);

        preg_match_all($pattern, $body, $matches);

        $headings = $matches[0];
        rsort($headings);

        $collection = Illuminate\Support\Collection::make($headings);
        $logs = $collection->take(10);
    }

    try {
        $ready = $queue->peekReady();
    }
    catch(Pheanstalk_Exception_ServerException $e) {
        $ready = null;
    }

    $failed = FailedJob::orderBy('failed_at', 'DESC')->paginate(10);

	return View::make('import', compact('stats', 'ready', 'failed', 'logs'));
});

Route::get('info', function() {

    Queue::push('ImportTradeTrackerCampaignInfo');

    return Redirect::to('/')->withSuccess('The campaign info is now queued for update');
});

Route::get('/import/{id?}', function($id = null)
{
    // Load all feeds
    App::make('TradeTrackerImporter')->run($id);

    return Redirect::to('/')->withSuccess('The product feed is now queued for import');
});

Route::get('export', function()
{
    Artisan::call('export:tasks');

	return Redirect::to('/')->withSuccess('The tasks are now queued for export');
});

Route::get('clicks', function()
{
    Queue::push('ExportTradeTrackerClicks')
        ->withSuccess('The clicks are now queued for export');

    return Redirect::to('/')->withSuccess('All clicks are exported');
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

    return Redirect::to('/')->withSuccess('All queued jobs are deleted succesfully');
});


Route::get('quick/refresh', function() {

    // Hack to enable migrations without errors
    define('STDIN',fopen("php://stdin","r"));

    Artisan::call('migrate:reset', ['--force' => true]);
    Artisan::call('migrate');

    return Redirect::to('/') ->withSuccess('The database is now empty');
});

Route::get('quick/ready', function() {

    Task::where('exported', 1)->update(['exported' => 0]);

    return Redirect::to('/') ->withSuccess('All tasks are now ready to be exported');
});

Route::get('quick/clear-failed-job', function() {

    DB::table('failed_jobs')->delete();

    return Redirect::to('/') ->withSuccess('All failed jobs are cleared');
});

Route::get('quick/clear-log-files', function() {

    File::cleanDirectory(storage_path('logs'));

    return Redirect::to('/') ->withSuccess('All log files removed');
});