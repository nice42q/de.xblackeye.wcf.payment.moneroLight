<?php

namespace wcf\action;

use Laminas\Diactoros\Response\RedirectResponse;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\paid\subscription\PaidSubscription;
use wcf\data\paid\subscription\transaction\log\PaidSubscriptionTransactionLog;
use wcf\data\paid\subscription\transaction\log\PaidSubscriptionTransactionLogAction;
use wcf\data\paid\subscription\user\PaidSubscriptionUser;
use wcf\data\paid\subscription\user\PaidSubscriptionUserAction;
use wcf\data\user\User;
use wcf\system\event\EventHandler;
use wcf\system\payment\type\IPaymentType;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\object\PaidSubscriptionMoneroLightUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Cart Remove Action Object.
 *
 * @author  xBlackEye
 * @license
 * @package de.xblackeye.wcf.payment.moneroLight
 */
class MoneroLightPurchaseAction extends AbstractAction
{
    /**
     * object id
     * @var int
     */
    public $objectID = 0;
    
    /**
     * cost of the purchase
     * @var double
     */
    public $cost = 0.0;
    
    /**
     * identifier for the currency of the purchase cost
     * @var string
     */
    public $currency = '';
    
    /**
     * name of the purchase
     * @var string
     */
    public $name = '';
    
    /**
     * token of the purchase
     * @var string
     */
    public $token = '';
    
    /**
     * parts of the token
     * @var string[]
     */
    public $tokenParts = [];
    
    /**
     * return url of the purchase
     * @var string
     */
    public $returnURL = '';
    
    /**
     * User object
     * @var User
     */
    public $user;
    
    /**
     * subscription object
     * @var Subscription
     */
    public $subscription;
    
    /**
     * payment type object type
     * @var ObjectType
     */
    public $objectType;

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        try {
            if (isset($_POST['cost'])) {
                $this->cost = \floatval($_POST['cost']);
            }
            if (isset($_POST['currency'])) {
                $this->currency = StringUtil::trim($_POST['currency']);
            }
            if (isset($_POST['name'])) {
                $this->name = StringUtil::trim($_POST['name']);
            }
            if (isset($_POST['token'])) {
                $this->token = StringUtil::trim($_POST['token']);
            
                $this->tokenParts = \explode(':', $this->token, 2);
                if (\count($this->tokenParts) != 2) {
                    throw new \Exception('invalid custom item');
                }
                
                // get user object
                $tokenParts = \explode(':', $this->tokenParts[1]);
                if (\count($tokenParts) != 2) {
                    throw new \Exception('invalid token');
                }
                [$userID, $this->objectID] = $tokenParts;
                $this->user = new User(\intval($userID));
                if (!$this->user->userID) {
                    throw new \Exception('invalid user');
                }
            }
            
            if (isset($_POST['returnURL'])) {
                $this->returnURL = StringUtil::trim($_POST['returnURL']);
            }
            
            $this->objectType = ObjectTypeCache::getInstance()->getObjectType(\intval($this->tokenParts[0]));
            if ($this->objectType === null || !($this->objectType->getProcessor() instanceof IPaymentType)) {
                throw new \Exception('invalid payment type id');
            }
        } catch (\Exception $e) {
            \wcf\functions\exception\logThrowable($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function execute(): RedirectResponse
    {
        parent::execute();
        
        // create new subscription
        if ($this->objectType->objectType == 'com.woltlab.wcf.payment.type.paidSubscription') {
            $this->subscription = new PaidSubscription(\intval($this->objectID));
            
            if (!$this->subscription->subscriptionID) {
                throw new \Exception('invalid subscription');
            }
            
            // check if user has a subscription
            $subscriptionUser = PaidSubscriptionUser::getSubscriptionUser($this->subscription->subscriptionID, $this->user->userID);
            if ($subscriptionUser == null) {
                $action = new PaidSubscriptionUserAction([], 'create', [
                    'user' => $this->user,
                    'subscription' => $this->subscription,
                    'data' => [
                        'isActive' => 0,
                    ],
                ]);
                $action->executeAction();
            }
        }
        
        // fire event to make isActive = 0
        EventHandler::getInstance()->fireAction($this, 'beforeProcessTransaction');
        
        $transactionID = \bin2hex(\random_bytes(32));
        $moneroLightObjectTypeID = ObjectTypeCache::getInstance()->getObjectTypeIDByName(
            'com.woltlab.wcf.payment.method',
            'de.xblackeye.wcf.payment.method.moneroLight'
        );
        
        $processor = $this->objectType->getProcessor();
        $processor->processTransaction(
            $moneroLightObjectTypeID,
            $this->tokenParts[1],
            $this->cost,
            $this->currency,
            $transactionID,
            'open',
            $_POST
        );
        
        // get paidSubscriptionTransactionLog
        $paidSubscriptionTransactionLog = PaidSubscriptionTransactionLog::getLogByTransactionID($moneroLightObjectTypeID, $transactionID);
        
        // update logMessage to open
        $paidSubscriptionTransactionLogAction = new PaidSubscriptionTransactionLogAction([$paidSubscriptionTransactionLog], 'update', [
            'data' => [
                'logMessage' => 'open',
            ],
        ]);
        $paidSubscriptionTransactionLogAction->executeAction();
        
        // send notification
        if ($this->objectType->objectType == 'com.woltlab.wcf.payment.type.paidSubscription') {
            $userIDs = [];
            $sql = "SELECT      user.userID
                    FROM        wcf1_user_group_option groupOption
                    LEFT JOIN   wcf1_user_group_option_value groupValue
                    ON          groupValue.optionID = groupOption.optionID
                    LEFT JOIN   wcf1_user_to_group user
                    ON          user.groupID = groupValue.groupID
                    WHERE       groupOption.optionName = 'admin.paidSubscription.canManageSubscription'
                        AND     groupValue.optionValue = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([1]);
            while ($row = $statement->fetchArray()) {
                $userIDs[] = $row['userID'];
            }
            
            if (!empty($userIDs)) {
                UserNotificationHandler::getInstance()->fireEvent(
                    'buy',
                    'de.xblackeye.moneroLight.paidSubscription',
                    new PaidSubscriptionMoneroLightUserNotificationObject($paidSubscriptionTransactionLog),
                    $userIDs
                );
            }
        }
        
        EventHandler::getInstance()->fireAction($this, 'afterProcessTransaction');
        
        $this->executed();
        
        return new RedirectResponse(
            LinkHandler::getInstance()->getLink($this->returnURL)
        );
    }
}
