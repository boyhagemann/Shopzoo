<?php

class Campaign extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'campaigns';

    /**
     * @param string $source
     * @param int    $campaignId
     * @return Campaign
     */
    public static function findBySourceAndId($source, $campaignId)
    {
        return static::where('source', $source)->where('campaign_id', $campaignId)->firstOrFail();
    }
}
