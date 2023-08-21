<?php

namespace wcf\system\user\notification\event;

use wcf\data\object\type\ObjectTypeCache;
use wcf\data\paid\subscription\PaidSubscription;
use wcf\data\paid\subscription\transaction\log\PaidSubscriptionTransactionLog;
use wcf\data\paid\subscription\transaction\log\PaidSubscriptionTransactionLogAction;
use wcf\data\user\UserProfile;
use wcf\system\WCF;
use wcf\system\user\notification\object\PaidSubscriptionMoneroLightUserNotificationObject;

/**
 * Provides all necessary methods for testing paid subscription user notification events.
 *
 * @author  xBlackEye
 * @license
 * @package de.xblackeye.wcf.payment.moneroLight
 */
trait TTestablePaidSubscriptionMoneroLightUserNotificationEvent
{
    use TTestableUserNotificationEvent;

    /**
     * @inheritDoc
     */
    public static function getTestObjects(UserProfile $recipient, UserProfile $author): array
    {
        return [new PaidSubscriptionMoneroLightUserNotificationObject(self::createTestPaidSubscription($recipient))];
    }
    
    /**
     * Creates a task for testing.
     */
    public static function createTestPaidSubscription(UserProfile $author): PaidSubscriptionTransactionLog
    {
        $sql = "SELECT      *
                FROM        wcf1_paid_subscription";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();
        $row = $statement->fetchArray();
        
        // get subscription
        $subscription = new PaidSubscription(null, $row);
        
        // get bank transfer object type
        $moneroLightObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.payment.method', 'de.xblackeye.wcf.payment.method.moneroLight');
        
        return (new PaidSubscriptionTransactionLogAction([], 'create', [
            'data' => [
                'subscriptionUserID' => null,
                'userID' => $author->userID,
                'subscriptionID' => $subscription->subscriptionID,
                'paymentMethodObjectTypeID' => $moneroLightObjectType->objectTypeID,
                'logTime' => TIME_NOW,
                'transactionID' => \bin2hex(\random_bytes(32)),
                'logMessage' => 'open',
            ],
        ]))->executeAction()['returnValues'];
    }
}
