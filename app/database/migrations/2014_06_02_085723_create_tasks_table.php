<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('campaign_id');
			$table->string('uid');
			$table->string('action');
			$table->integer('provider_id');
			$table->string('title');
			$table->text('teaser')->nullable();
			$table->text('description')->nullable();
			$table->string('uri');
			$table->string('image')->nullable();
			$table->float('value');
			$table->string('currency');

			$table->tinyInteger('exported');

			$table->index('campaign_id');
			$table->unique('uid');
			$table->index('exported');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tasks');
	}

}
