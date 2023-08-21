{if $log->getPaymentMethodName() == 'de.xblackeye.wcf.payment.method.moneroLight' && $log->logMessage == 'open'}
	<li><a href="{link controller='PaidSubscriptionMoneroLight' object=$log}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.payment.moneroLight.order.activate{/lang}</span></a></li>
{/if}