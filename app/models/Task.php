<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Task extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tasks';

	/**
	 * @param $data
	 * @return array
	 */
	public function getDataAttribute($data)
	{
		return (array) json_decode($data, true);
	}

	public function setDataAttribute(Array $data)
	{
		$this->attributes['data'] = json_encode($data);
	}

}
