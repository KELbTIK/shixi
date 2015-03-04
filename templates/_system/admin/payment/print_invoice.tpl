{title} [[Invoice]] #{$invoice_sid}{/title}
{keywords} [[Print Invoice]] {/keywords}
{description} [[Print Invoice]] {/description}
<div id="view-invoice">
	{if $errors}
		{foreach from=$errors key=error item=error_message}
			<p class="error">
				{if $error eq 'WRONG_INVOICE_ID_SPECIFIED'}
					[[There is no such invoice in the system]]
					{elseif $error eq 'NOT_OWNER'}
					[[You're not owner of this invoice]]
				{/if}
			</p>
		{/foreach}
		{else}
		{if $paymentError}
			<p class="error">[[Invoice is not verified]]</p>
		{/if}
		<div class="printPage">
			{display property="status" assign=status}
			{display property="payment_method" assign=payment_method}

			<div id="invoice-logo">
				<img src="{$GLOBALS.user_site_url}/templates/{$GLOBALS.settings.CURRENT_THEME}/main/images/logo.png" border="0" />
			</div>
			<div id="invoice-info">
				<strong>[[Invoice]]</strong><br/>
				[[Date]]:&nbsp;{display property="date"}<br/>
				[[Invoice]]&nbsp;&#35;:&nbsp;{$invoice_sid}<br/>
				[[Invoice Status]]:&nbsp;{$status}{if $payment_method}&nbsp;({display property="payment_method"}){/if}<br/>
			</div>
			<div class="clr"></div>
			{capture name="location"}{locationFormat location=$user.Location format="middle"}{/capture}
			<div id="invoice-billto">
				<strong>[[Bill To]]</strong>
				<br/>{$user.CompanyName}&nbsp;{$user.LastName}&nbsp;{$user.FirstName}
				<br/>{$user.Location.Address}
				<br/>{$smarty.capture.location|trim:",\t "}
				<br/>{tr}{$user.Location.Country}{/tr|escape:'html'}
			</div>
			<div id="invoice-sendto">
				<strong>[[Send Payment To]]</strong>
				<br/>{$GLOBALS.settings.send_payment_to}
			</div>
			<div class="clr"></div>
			{display property="items" template="items_complex.tpl"}
		</div>
		<fieldset id="invoice-buttons">
			<input type=button value="[[Print]]" onClick="getElementById('invoice-buttons').style.display='none'; window.print();" class="standart-button">
		</fieldset>
	{/if}
</div>