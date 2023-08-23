<?php

/**
 * @author  xBlackEye
 * @license
 * @package de.xblackeye.wcf.payment.moneroLight
 */
namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\user\UserEditor;
use wcf\data\user\UserProfileList;
use wcf\system\WCF;

/**
 * Cronjob for Monero Interests
 */
class MoneroLightInterestsCronjob extends AbstractCronjob
{
    /**
     * @inheritdoc
     */
    public function execute(Cronjob $cronjob)
    {
        /*parent::execute($cronjob);
		
		$sql = "UPDATE	wcf".WCF_N."_option option
			SET	option.optionValue = ".$array."
			WHERE option.optionName = option.coingecko_fiat_to_xmr";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();*/
    }
}