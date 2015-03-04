{foreach from=$checkPaymentErrors key=error item=value}
	{if $error == 'NOT_OWNER'}
		<p class="error">[[You're not the owner of this payment]]</p>
	{elseif $error == 'NOT_LOGGED_IN'}
		<p class="error">[[Please log in to place a listing. If you do not have an account, please]] <a href="{$GLOBALS.site_url}">[[Register]]</a></p>
		<br/><br/>
		{module name="users" function="login"}
	{elseif $error == 'WRONG_INVOICE_PARAMETERS'}
		<p class="error">[[Invoice contains wrong parameters. Check all items listed in it.]]</p>
	{elseif $error == 'PROMOTION_TOO_MANY_USES'}
		<p class="error">[[Promotion code that was applied to this invoice is expired. Invoice cannot be paid for. Please generate a new invoice by purchasing product(s) again.]]</p>
	{/if}
{foreachelse}
	<br />
	{if count($gateways) == 1}
		[[Please wait. You will be redirected to the payment page in a moment]]
	{else}
		[[Dear customer!]]<br /><br />
		{capture assign = "formatingProductPrice"}{tr type="float"}{$invoice_info.total}{/tr}{/capture}
		{capture assign="productPrice"}{currencyFormat amount=$formatingProductPrice}{/capture}
		[[Please make a payment in the amount of $productPrice for product(s)]]:
		{foreach name="product_names_loop" item="productName" from=$productsNames}
			<span class="strong">[[{$productName|paymentTranslate}]]</span>{if !$smarty.foreach.product_names_loop.last}, {/if}
		{/foreach}

		<p>[[Please choose from the following payment methods:]]</p>
	{/if}
	{foreach from=$gateways item="gateway" key="gatewayID" name="gateways"}
		<form action="{$gateway.url}" method="post" id="form_{$gatewayID}" onsubmit="disableSubmitButton('submit_{$gatewayID}');">
			{$gateway.hidden_fields}
			{capture name="trGatewayCaption"}[[{$gateway.caption}]]{/capture}
			{if $smarty.foreach.gateways.last && $smarty.foreach.gateways.first}
				<script type="text/javascript">
					$(document).ready(function(){
						$("#form_" + "{$gatewayID}").submit();
					});
				</script>
			{else}
				<br/><input type='submit' value="{$smarty.capture.trGatewayCaption|escape:'html'}" class="paymentButton" id="submit_{$gatewayID}"/>
			{/if}
		</form>
	{/foreach}
{/foreach}
