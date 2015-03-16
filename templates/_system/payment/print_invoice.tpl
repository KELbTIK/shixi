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
			<div class="error alert alert-danger">[[$gatewayName payment gateway is set up incorrectly. The payment cannot be processed.]]</div>
		{else}
			<div class="error alert alert-danger">[[We were unable to process your payment. The system has tracked down some errors.]]</div>
		{/if}
	{/if}
	<div class="printPage">
		{display property="status" assign=status}
		{display property="payment_method" assign=payment_method}
			<div id="invoice-logo">
			<img src="{image}logo.png" border="0" />
		</div>
		<div id="invoice-info">
			<h2><span class="strong">[[Invoice]]</span><br /></h2>
			<h3>[[Date]]:</h3>{display property="date"}<br />
			<h3>[[Invoice]]&nbsp;&#35;:&nbsp;</h3>{$invoice_sid}<br />
			<h3>[[Invoice Status]]:&nbsp;</h3>{$status}{if $payment_method}&nbsp;({display property="payment_method"}){/if}<br />
		</div>
		<div class="clearfix"></div>
			<div id="invoice-billto">
			{capture name="location"}{locationFormat location=$user.Location format="middle"}{/capture}
			<h3><span class="strong">[[Bill To]]</span></h3>
			<br />{$username}
			<br />{$user.Location.Address}
			<br />{$smarty.capture.location|trim:",\t "}
			<br />{tr}{$user.Location.Country}{/tr|escape:'html'}
		</div>
		<div id="invoice-sendto">
			<h3><span class="strong">[[Send Payment To]]</span></h3>
			<br /><pre class="sendPaymentTo">{$GLOBALS.settings.send_payment_to|escape:'html'}</pre>
		</div>
		<div class="clearfix"></div>
		{display property="items" template="items_complex.tpl"}
	</div>
	
	<div id="invoice-buttons" class="form-group has-feedback">
		<form action="{$GLOBALS.site_url}/payment-page/?invoice_sid={$invoice_sid}" method="post">
			{if $invoice_status != 'Paid' && $invoice_status != 'Pending'}
				<input type="hidden" name="invoice_sid" value="{$invoice_sid}" />
				<input type='submit' value="[[Pay Invoice]]" class="btn btn-default" />
			{/if}
			<input type="button" class="btn btn-primary" value="[[Download PDF Version]]" onclick="location.href='{$GLOBALS.site_url}/view-invoice/?sid={$invoice_sid}&amp;action=download_pdf_version'" />
			<input type=button value="[[Print]]" onClick="getElementById('invoice-buttons').style.display='none'; window.print();" class="btn btn-dark" />
		</form>
	</div>
</div>
