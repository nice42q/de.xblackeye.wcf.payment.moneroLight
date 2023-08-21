<?php

namespace wcf\system\user\notification\event;

use wcf\system\WCF;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\request\LinkHandler;

/**
 * Notification event for new paid subscription.
 *
 * @author  xBlackEye
 * @license
 * @package de.xblackeye.wcf.payment.moneroLight
 */
class PaidSubscriptionBankTransferUserNotificationEvent extends AbstractUserNotificationEvent implements ITestableUserNotificationEvent
{
    use TTestablePaidSubscriptionBankTransferUserNotificationEvent;

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getLanguage()->get('wcf.payment.moneroLight.notification.title');
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return $this->getLanguage()->getDynamicVariable('wcf.payment.moneroLight.notification.message', [
            'paidSubscriptionLog' => $this->userNotificationObject,
            'author' => $this->author,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant'): array
    {
        return [
            'message-id' => 'de.xblackeye.wcf.payment.moneroLight/' . $this->getUserNotificationObject()->logID,
            'template' => 'email_notification_paid_subscription_monero_light',
            'application' => 'wcf',
            'variables' => [
                'notificationObject' => $this,
                'languageVariableMessage' => 'wcf.payment.moneroLight.notification',
                'languageVariablePrefix' => 'wcf.user.notification.paidSubscription',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('PaidSubscriptionTransactionLog', [
            'object' => $this->userNotificationObject,
            'isACP' => true,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function checkAccess(): bool
    {
        return WCF::getSession()->getPermission('admin.paidSubscription.canManageSubscription');
    }
    
    /**
     * Returns a version of this message optimized for use in emails.
     */
    public function getMailText(string $mimeType = 'text/plain'): string
    {
        switch ($mimeType) {
            case 'text/plain':
                $processor = new HtmlOutputProcessor();
                $processor->setOutputType('text/plain');
                $processor->process($this->getUserNotificationObject()->getSubscription()->getFormattedDescription(), 'com.woltlab.wcf.paidSubscription', $this->getUserNotificationObject()->getSubscription()->subscriptionID);

                return $processor->getHtml();
            case 'text/html':
                // parse and return message
                $processor = new HtmlOutputProcessor();
                $processor->setOutputType('text/simplified-html');
                $processor->process($this->getUserNotificationObject()->getSubscription()->getFormattedDescription(), 'com.woltlab.wcf.paidSubscription', $this->getUserNotificationObject()->getSubscription()->subscriptionID);

                return $processor->getHtml();
        }

        throw new \LogicException('Unreachable');
    }
}
