<?php

namespace wcf\system\user\notification\object;

use wcf\acp\page\PaidSubscriptionTransactionLogPage;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\paid\subscription\transaction\log\PaidSubscriptionTransactionLog;
use wcf\system\WCF;
use wcf\system\request\LinkHandler;

/**
 * Represents a paidSubscriptionTransactionLog as a notification object.
 *
 * @author  xBlackEye
 * @license
 * @package de.yourecom.wcf.payment.bankTransfer
 *
 * @method  PaidSubscriptionTransactionLog    getDecoratedObject()
 * @mixin   PaidSubscriptionTransactionLog
 */
class PaidSubscriptionMoneroLightUserNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = PaidSubscriptionTransactionLog::class;

    /**
     * @inheritDoc
     */
    public function getObjectID(): int
    {
        return $this->logID;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'Test Name in wcf\system\user\notification\object\PaidSubscriptionMoneroLightUserNotificationObject';
    }

    /**
     * @inheritDoc
     */
    public function getURL(): string
    {
        return LinkHandler::getInstance()->getControllerLink(PaidSubscriptionTransactionLogPage::class, ['id' => $this->getObjectID()]);
    }

    /**
     * @inheritDoc
     */
    public function getAuthorID(): int
    {
        return WCF::getUser()->userID;
    }
}
