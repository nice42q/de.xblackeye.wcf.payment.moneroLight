<?php

namespace wcf\acp\action;

use Laminas\Diactoros\Response\RedirectResponse;
use wcf\action\AbstractAction;
use wcf\data\paid\subscription\transaction\log\PaidSubscriptionTransactionLog;
use wcf\data\paid\subscription\transaction\log\PaidSubscriptionTransactionLogAction;
use wcf\data\paid\subscription\user\PaidSubscriptionUser;
use wcf\data\paid\subscription\user\PaidSubscriptionUserAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;

/**
 * Cart Remove Action Object.
 *
 * @author  xBlackEye
 * @license
 * @package de.xblackeye.wcf.payment.moneroLight
 */
class PaidSubscriptionMoneroLightAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_PAID_SUBSCRIPTION'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.paidSubscription.canManageSubscription'];

    /**
     * log entry id
     * @var int
     */
    public $logID = 0;

    /**
     * log entry object
     * @var PaidSubscriptionTransactionLog
     */
    public $log;
    
    /**
     * user subscription object
     * @var PaidSubscriptionUser|null
     */
    public $userSubscription;

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();
        
        if (isset($_REQUEST['id'])) {
            $this->logID = \intval($_REQUEST['id']);
        }
        $this->log = new PaidSubscriptionTransactionLog($this->logID);
        if (!$this->log->logID || $this->log->logMessage != 'open') {
            throw new IllegalLinkException();
        }
        
        // search for existing subscription
        $this->userSubscription = PaidSubscriptionUser::getSubscriptionUser(
            $this->log->subscriptionID,
            $this->log->userID
        );
        if (!$this->userSubscription->subscriptionID) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function execute(): RedirectResponse
    {
        parent::execute();
        
        $action = new PaidSubscriptionUserAction([$this->userSubscription], 'extend');
        $action->executeAction();

        // update logMessage to payment completed
        $paidSubscriptionTransactionLogAction = new PaidSubscriptionTransactionLogAction([$this->log], 'update', [
            'data' => [
                'logMessage' => 'payment completed',
            ],
        ]);
        $paidSubscriptionTransactionLogAction->executeAction();
        
        $this->executed();
        
        return new RedirectResponse(
            LinkHandler::getInstance()->getLink('PaidSubscriptionTransactionLogList', [
                'application' => 'wcf',
                'isACP' => true,
            ])
        );
    }
}
