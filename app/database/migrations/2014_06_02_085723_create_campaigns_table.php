<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campaigns', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->enum('source', array('tradetracker'));
			$table->integer('campaign_id');

            $table->string('name');
            $table->string('url');

            $table->float('lead_value');
            $table->float('sales_value');
            $table->float('sales_percentage');

			$table->unique(array('source', 'campaign_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('campaigns');
	}

}
