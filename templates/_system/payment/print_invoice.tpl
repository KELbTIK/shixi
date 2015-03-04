<div id="view-invoice">
	{if $show}
		{title} [[View Invoice]] {/title}
		{keywords} [[View Invoice]] {/keywords}
		{description} [[View Invoice]] {/description}
		<h1>[[View Invoice]]</h1>
	{else}
		{title} [[Invoice]] #{$invoice_sid}{/title}
		{keywords} [[Print Invoice]] {/keywords}
		{description} [[Print Invoice]] {/description}
	{/if}
	
	{module name='flash_messages' function='display'}
	{if $paymentError}
		{if $paymentError == 2}
			{assign var="gatewayName" value="`$gatewayInfo.caption`"}
			<p class="error">[[$gatewayName payment gateway is set up incorrectly. The payment cannot be processed.]]</p>
		{else}
			<p class="error">[[We were unable to process your payment. The system has tracked down some errors.]]</p>
		{/if}
	{/if}
	<div class="printPage">
		{display property="status" assign=status}
		{display property="payment_method" assign=payment_method}
			<div id="invoice-logo">
			<img src="{image}logo.png" border="0" />
		</div>
		<div id="invoice-info">
			<span class="strong">[[Invoice]]</span><br />
			[[Date]]:&nbsp;{display property="date"}<br />
			[[Invoice]]&nbsp;&#35;:&nbsp;{$invoice_sid}<br />
			[[Invoice Status]]:&nbsp;{$status}{if $payment_method}&nbsp;({display property="payment_method"}){/if}<br />
		</div>
		<div class="clr"></div>
			<div id="invoice-billto">
			{capture name="location"}{locationFormat location=$user.Location format="middle"}{/capture}
			<span class="strong">[[Bill To]]</span>
			<br />{$username}
			<br />{$user.Location.Address}
			<br />{$smarty.capture.location|trim:",\t "}
			<br />{tr}{$user.Location.Country}{/tr|escape:'html'}
		</div>
		<div id="invoice-sendto">
			<span class="strong">[[Send Payment To]]</span>
			<br /><pre class="sendPaymentTo">{$GLOBALS.settings.send_payment_to|escape:'html'}</pre>
		</div>
		<div class="clr"></div>
		{display property="items" template="items_complex.tpl"}
	</div>
	
	<fieldset id="invoice-buttons">
		<form action="{$GLOBALS.site_url}/payment-page/?invoice_sid={$invoice_sid}" method="post">
			{if $invoice_status != 'Paid' && $invoice_status != 'Pending'}
				<input type="hidden" name="invoice_sid" value="{$invoice_sid}" />
				<input type='submit' value="[[Pay Invoice]]" class="standart-button" />
			{/if}
			<input type="button" class="standart-button" value="[[Download PDF Version]]" onclick="location.href='{$GLOBALS.site_url}/view-invoice/?sid={$invoice_sid}&amp;action=download_pdf_version'" />
			<input type=button value="[[Print]]" onClick="getElementById('invoice-buttons').style.display='none'; window.print();" class="standart-button" />
		</form>
	</fieldset>
</div>
