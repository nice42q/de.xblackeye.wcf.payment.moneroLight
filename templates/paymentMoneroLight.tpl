{if newPurchase}
	<button class="small jsButtonMoneroLight" type="submit">{lang}wcf.payment.moneroLight.button.purchase{/lang}</button>
{/if}

<div id="purchaseMoneroLight" style="display: none;">
	<form method="post" action="{link application='wcf' controller='MoneroLightPurchase'}{/link}">
		{if $paidSubscriptionTransactionLog && $paidSubscriptionTransactionLog->logMessage == 'open'}<p class="info">{lang}wcf.payment.moneroLight.button.purchaseAgain.info{/lang}</p>{/if}
		
		<section class="section">
			<h2 class="sectionTitle"><img src="/images/payments/monero.png" style="max-height: 20px;margin-right: 10px;">{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.box.title{/lang}</h2>
			<span>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.description{/lang}</span>
			<dl>
				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.time{/lang}</dt>
				<dd id="moneroTimer"></dd>

				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.amount{/lang}</dt>
				<dd><span id="xmr-amount">Monero Betrag</span>&nbsp;{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.xmr{/lang}&nbsp;&nbsp;&nbsp;<button class="small CopyToClipboard" id="copyToClipboard" data-url="Monero Betrag">{lang}wcf.payment.moneroLight.button.purchase.copy{/lang}</button><br/><small>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.amount.description{/lang}</small></dd></dd>

				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.address{/lang}</dt>
				<dd><input class="long" type="text" name="moneroAddress" value="{MONERO_ADDRESS}" disabled></dd>

				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.transactionID{/lang}</dt>
				<dd><input class="long" type="text" name="moneroTxID" id="moneroTxID" value="" placeholder="c0fa90648d799fc3e41683663dfef93a1fcca7fea9bf1eae3136dd71270ce7be"></dd>

				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.transactionKey{/lang}</dt>
				<dd><input class="long" type="text" name="moneroTxKey" id="moneroTxKey" value="" placeholder="ff7235e8c615dac88f3548ebf71eb985af037e318488f7798b23707c7622c801"></dd>

				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.blockHeight{/lang}</dt>
				<dd><input class="long" type="number" name="moneroBlockHeight" id="moneroBlockHeight" value="" placeholder="2948393" min="2948393" max="3948393"></dd>
			</dl>
		</section>
		
		<div class="formSubmit">
            {if newPurchase}
			    <input type="submit" id="moneroPurchaseSubmitBtn" value="{lang}wcf.payment.moneroLight.button.purchase{/lang}" accesskey="s">
			{/if}
			<input type="hidden" name="cost" value="{$cost}">
			<input type="hidden" name="currency" value="{$currency}">
			<input type="hidden" name="name" value="{$name}">
			<input type="hidden" name="returnURL" value="{$returnURL}">
			<input type="hidden" name="cancelReturnURL" value="{$cancelReturnURL}">
			<input type="hidden" name="isRecurring" value="{$isRecurring}">
			<input type="hidden" name="subscriptionLength" value="{$subscriptionLength}">
			<input type="hidden" name="subscriptionLengthUnit" value="{$subscriptionLengthUnit}">
			
			{csrfToken}
		</div>
	</form>
</div>

<script>
// Set the date we're counting down to
var countDownDate = new Date(Date.now() + (1 * 60 * 1000)).getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();

  // Find the distance between now and the count down date
  var distance = countDownDate - now;

  // Time calculations for days, hours, minutes and seconds
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Display the result in the element with id="moneroTimer"
  document.getElementById("moneroTimer").innerHTML = minutes + "m " + seconds + "s ";

  // If the count down is finished, write some text
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("moneroTimer").innerHTML = "{jslang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.transactionOvertime{/jslang}";
	document.getElementById("copyToClipboard").setAttribute("disabled", true);
	document.getElementById("moneroPurchaseSubmitBtn").setAttribute("disabled", true);
  }
}, 1000);
</script>

<script>
    require(['WoltLabSuite/Core/Ui/Notification', 'WoltLabSuite/Core/Clipboard'], (UiNotification, { copyTextToClipboard }) => {
        document.querySelectorAll('.CopyToClipboard').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();

                void copyTextToClipboard(button.dataset.url).then(() => {
                    UiNotification.show('{jslang}wcf.global.success{/jslang}');
                });
            });
        });
    });
</script>

<script data-relocate="true">
	require(['Ui/Dialog'], (UiDialog) => {
		document.querySelectorAll('.jsButtonMoneroLight').forEach((button) => {
			button.addEventListener('click', function () {
				UiDialog.openStatic('purchaseMoneroLight', null, {
					closable: true,
					title: '{jslang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight{/jslang}'
				});
			});
		});
	});
</script>