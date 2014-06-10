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
	return View::make('import');
});


Route::get('enrich', function()
{
	$tasks = Task::whereNull('description')->limit(20)->get();

	foreach($tasks as $task) {
		Event::fire('task.enrich', array($task));
	}

	if(Task::whereNull('description')->count()) {
//		return Redirect::to('enrich');
	}

	return Redirect::to('/');
});

Route::get('export', function()
{
	$tasks = Task::where('exported', 0)->get();

	foreach(array_chunk($tasks->toArray(), 300) as $splitted) {

		$client = new GuzzleHttp\Client;
		$client->post('http://taskreward.app/api/tasks', array(
			'body' => array(
				'tasks' => $splitted,
			),
		));

	}

	DB::table('tasks')->where('exported', 0)->update(array('exported' => 1));

	return Redirect::to('/');
});
