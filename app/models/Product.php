<?php

class Product extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'products';

	protected $fillable = array('uid', 'title', 'description', 'uri');

}
