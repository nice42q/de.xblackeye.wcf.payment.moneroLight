<?php

namespace wcf\system\user\notification\object\type;

use wcf\data\paid\subscription\transaction\log\PaidSubscriptionTransactionLog;
use wcf\data\paid\subscription\transaction\log\PaidSubscriptionTransactionLogList;
use wcf\system\user\notification\object\PaidSubscriptionMoneroLightUserNotificationObject;

/**
 * Represents a task as a notification object type.
 *
 * @author  xBlackEye
 * @license
 * @package de.yourecom.wcf.payment.bankTransfer
 */
class PaidSubscriptionMoneroLightUserNotificationObjectType extends AbstractUserNotificationObjectType
{
    /**
     * @inheritDoc
     */
    protected static $decoratorClassName = PaidSubscriptionMoneroLightUserNotificationObject::class;

    /**
     * @inheritDoc
     */
    protected static $objectClassName = PaidSubscriptionTransactionLog::class;

    /**
     * @inheritDoc
     */
    protected static $objectListClassName = PaidSubscriptionTransactionLogList::class;
}
