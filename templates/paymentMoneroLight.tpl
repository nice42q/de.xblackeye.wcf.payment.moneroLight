{if newPurchase}
	<button class="small jsButtonMoneroLight" type="submit">{lang}wcf.payment.moneroLight.button.purchase{/lang}</button>
{/if}

<div id="purchaseMoneroLight" style="display: none;">
	<form method="post" action="{link application='wcf' controller='MoneroLightPurchase'}{/link}">
		{if $paidSubscriptionTransactionLog && $paidSubscriptionTransactionLog->logMessage == 'open'}<p class="info">{lang}wcf.payment.moneroLight.button.purchaseAgain.info{/lang}</p>{/if}
		
		<section class="section">
			<span>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.description{/lang}</span>
			<dl>
				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.time{/lang}</dt>
				<dd id="moneroTimer"></dd>

				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.amount{/lang}</dt>
				<dd style="display: flex;margin-bottom: 0px;"><input class="long" type="text" name="xmr-amount" value="Monero Betrag" id="xmr-amount" style="border-right:0px;" readonly><input style="border-left: 0px;color: #4c4c4c !important;width: 72px;text-align: center;font-weight: bold;" type="text" value="{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.xmr{/lang}" readonly><button style="border-radius: 0px 2px 2px 0px;" class="small CopyToClipboard" id="copyToClipboardXMR" data-text="Monero Betrag">{lang}wcf.payment.moneroLight.button.purchase.copy{/lang}</button></dd>
				<dd><small>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.amount.description{/lang}</small></dd>
				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.address{/lang}</dt>
				<dd style="display: flex;"><input class="long" type="text" name="moneroAddress" value="{MONERO_ADDRESS}" readonly><button style="border-radius: 0px 2px 2px 0px;" class="small CopyToClipboard" id="copyToClipboardAddress" data-text="{MONERO_ADDRESS}">{lang}wcf.payment.moneroLight.button.purchase.copy{/lang}</button></dd>

				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.transactionID{/lang}&nbsp;<span class="formFieldRequired">*</span></dt>
				<dd><input class="long" type="text" name="moneroTxID" id="moneroTxID" value="" placeholder="c0fa90648d799fc3e41683663dfef93a1fcca7fea9bf1eae3136dd71270ce7be" required></dd>

				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.transactionKey{/lang}&nbsp;<span class="formFieldRequired">*</span></dt>
				<dd><input class="long" type="text" name="moneroTxKey" id="moneroTxKey" value="" placeholder="ff7235e8c615dac88f3548ebf71eb985af037e318488f7798b23707c7622c801" required></dd>

				<dt>{lang}wcf.payment.de.xblackeye.wcf.payment.method.moneroLight.blockHeight{/lang}&nbsp;<span class="formFieldRequired">*</span></dt>
				<dd><input class="long" type="number" name="moneroBlockHeight" id="moneroBlockHeight" value="" placeholder="2948393" min="2948393" max="3948393" required></dd>
			</dl>
		</section>
		
		<div class="formSubmit">
            {if newPurchase}
			    <input type="submit" id="moneroPurchaseSubmitBtn" value="{lang}wcf.payment.moneroLight.button.purchase.completeTransaction{/lang}" accesskey="s">
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
	document.getElementById("copyToClipboardAddress").setAttribute("disabled", true);
	document.getElementById("copyToClipboardXMR").setAttribute("disabled", true);
	document.getElementById("moneroPurchaseSubmitBtn").setAttribute("disabled", true);
  }
}, 1000);
</script>

<script>
    require(['WoltLabSuite/Core/Ui/Notification', 'WoltLabSuite/Core/Clipboard'], (UiNotification, { copyTextToClipboard }) => {
        document.querySelectorAll('.CopyToClipboard').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();

                void copyTextToClipboard(button.dataset.text).then(() => {
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
