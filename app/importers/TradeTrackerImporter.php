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

    protected function auth()
    {
        $this->getClient()->authenticate($_ENV['TRADETRACKER_USER'], $_ENV['TRADETRACKER_KEY']);
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
		return call_user_func_array($import, array($data));
	}

	/**
	 * Get the product feeds from all the registered campaigns and process
	 * each product.
	 *
	 */
	public function run($id = null)
	{
        $this->auth();

        if($id) {
            return call_user_func_array($this->feed, array($id));
        }

		foreach(array_keys($this->imports) as $id) {
			call_user_func_array($this->feed, array($id));
		}
	}
}
