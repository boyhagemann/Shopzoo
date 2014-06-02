<?php

class TradeTrackerImporter
{
	/**
	 * @var Closure[]
	 */
	protected $imports = array();

	/**
	 * @var Closure
	 */
	protected $feed;

	/**
	 * @var SoapClient
	 */
	protected $client;

	/**
	 * @var array
	 */
	protected $info = array();

	/**
	 * @var Closure
	 */
	protected $infoCallback;

	/**
	 * @param SoapClient $client
	 */
	public function __construct(SoapClient $client)
	{
		$this->client = $client;
	}

	/**
	 * Get the soap client.
	 *
	 * @return SoapClient
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @param $id
	 * @return mixed|null
	 */
	public function getInfo($id)
	{
		if(!$this->info) {
			call_user_func_array($this->infoCallback, array($this));
		}

		return $this->info[$id];
	}

	/**
	 * @param $id
	 * @param $info
	 */
	public function setInfo($id, $info)
	{
		$this->info[$id] = $info;
	}

	/**
	 * @param $id
	 * @param $info
	 */
	public function info(Closure $callback)
	{
		$this->infoCallback = $callback;
	}

	/**
	 * Get all products from the feed and process them individually
	 * in the callback.
	 *
	 * @param callable $callback
	 */
	public function feed(Closure $callback)
	{
		$this->feed = $callback;
	}

	/**
	 * Process the raw product data using the registered callbacks
	 * for a campaign product.
	 *
	 * @param          $id
	 * @param callable $callback
	 * @return $this
	 */
	public function import($id, Closure $callback)
	{
		$this->imports[$id] = $callback;
		return $this;
	}

	/**
	 * Calls the registered callback for this campaign to
	 * handle the raw product data.
	 *
	 */
	public function process($id, $data)
	{
		$import = $this->imports[$id];
		$info = $this->getInfo($id);
		return call_user_func_array($import, array($data, $info));
	}

	/**
	 * Get the product feeds from all the registered campaigns and process
	 * each product.
	 *
	 */
	public function run()
	{
		foreach(array_keys($this->imports) as $id) {
			call_user_func_array($this->feed, array($id));
		}
	}
}
