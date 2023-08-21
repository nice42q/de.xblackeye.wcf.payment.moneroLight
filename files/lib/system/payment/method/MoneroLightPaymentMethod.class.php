<?php

namespace wcf\system\payment\method;

use wcf\data\object\type\ObjectTypeCache;
use wcf\data\paid\subscription\transaction\log\PaidSubscriptionTransactionLog;
use wcf\system\event\EventHandler;
use wcf\system\payment\type\IPaymentType;
use wcf\system\WCF;

/**
 * IPaymentMethod implementation for Paypal.
 *
 * @author  xBlackEye
 * @license
 * @package de.xblackeye.wcf.payment.moneroLight
 */
class MoneroLightPaymentMethod extends AbstractPaymentMethod
{
    /**
     * payment type object type
     * @var ObjectType
     */
    public $objectType;
    
    /**
     * bank transfer object type
     * @var ObjectType
     */
    public $moneroLightObjectType;
    
    /**
     * array for additional datas
     * @var mixed[]
     */
    public $additionalData = [];
    
    /**
     * if false, the button wont be displayed
     * @var bool
     */
    public $newPurchase = true;
    
    /**
     * @inheritDoc
     */
    public function supportsRecurringPayments()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getSupportedCurrencies()
    {
        return [
            'AUD', // Australian Dollar
            'BRL', // Brazilian Real
            'CAD', // Canadian Dollar
            'CZK', // Czech Koruna
            'DKK', // Danish Krone
            'EUR', // Euro
            'HKD', // Hong Kong Dollar
            'HUF', // Hungarian Forint
            'ILS', // Israeli New Sheqel
            'JPY', // Japanese Yen
            'MYR', // Malaysian Ringgit
            'MXN', // Mexican Peso
            'NOK', // Norwegian Krone
            'NZD', // New Zealand Dollar
            'PHP', // Philippine Peso
            'PLN', // Polish Zloty
            'GBP', // Pound Sterling
            'RUB', // Russian Ruble
            'SGD', // Singapore Dollar
            'SEK', // Swedish Krona
            'CHF', // Swiss Franc
            'TWD', // Taiwan New Dollar
            'THB', // Thai Baht
            'USD', // U.S. Dollar
        ];
    }

    /**
     * @inheritDoc
     */
    public function getPurchaseButton(
        $cost,
        $currency,
        $name,
        $token,
        $returnURL,
        $cancelReturnURL,
        $isRecurring = false,
        $subscriptionLength = 0,
        $subscriptionLengthUnit = ''
    ) {
        $tokenParts = \explode(':', $token, 3);
        $this->moneroLightObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.payment.method', 'de.xblackeye.wcf.payment.method.moneroLight');
        $this->objectType = ObjectTypeCache::getInstance()->getObjectType(\intval($tokenParts[0]));
        if ($this->objectType === null || !($this->objectType->getProcessor() instanceof IPaymentType)) {
            throw new \Exception('invalid payment type id');
        }
        
        $paidSubscriptionTransactionLog = null;
        if ($this->objectType->objectType == 'com.woltlab.wcf.payment.type.paidSubscription') {
            $paidSubscriptionTransactionLog = $this->getPaidSubscriptionTransactionLog($tokenParts[1], $tokenParts[2]);
            
            if ($paidSubscriptionTransactionLog && $paidSubscriptionTransactionLog->logMessage == '') {
                $this->newPurchase = false;
            }
        }
        
        // fire event
        EventHandler::getInstance()->fireAction($this, 'beforeDisplay');
        
        return WCF::getTPL()->fetch('paymentMoneroLight', 'wcf', [
            'cost' => $cost,
            'currency' => $currency,
            'name' => $name,
            'token' => $token,
            'returnURL' => $returnURL,
            'cancelReturnURL' => $cancelReturnURL,
            'isRecurring' => $isRecurring,
            'subscriptionLength' => $subscriptionLength,
            'subscriptionLengthUnit' => $subscriptionLengthUnit,
            'newPurchase' => $this->newPurchase,
            'additionalData' => $this->additionalData,
            'paidSubscriptionTransactionLog' => $paidSubscriptionTransactionLog,
        ], true);
    }
    
    /**
     * Returns the transaction log entry by userID, payment method and objectTypeID or `null` if no such entry exists.
     */
    public function getPaidSubscriptionTransactionLog(int $userID, int $subscriptionID): ?PaidSubscriptionTransactionLog
    {
        $sql = "SELECT      *
                FROM        wcf1_paid_subscription_transaction_log
                WHERE       userID = ?
                    AND     subscriptionID = ?
                    AND     paymentMethodObjectTypeID = ?
                ORDER BY    logID DESC";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$userID, $subscriptionID, $this->moneroLightObjectType->objectTypeID]);
        $row = $statement->fetchArray();
        if ($row !== false) {
            return new PaidSubscriptionTransactionLog(null, $row);
        }

        return null;
    }
}
