<h1>[[Wire Transfer]]</h1>
{assign var="username" value=$user.username}
{capture assign = "amount"}{tr type="float"}{$amount}{/tr}{/capture}
{capture assign="productPrice"}{currencyFormat amount=$amount}{/capture}

{foreach from=$errors item=message key=error}
 <div class="error alert alert-danger">
	{if $error == 'INVALID_INVOICE_ID'}
		[[Invalid invoice ID is specified]]
	{elseif $error == 'NOT_OWNER'}
		[[You're not owner of this invoice]]
	{elseif $error == 'INVOICE_IS_NOT_UNPAID'}
		[[Invoice already paid]]
	{else}
		[[{$error}]]: [[{$message}]]
	{/if}
 </div>
	{foreachelse}
[[Dear $username, <br /><br />Please send us a payment in the amount of $productPrice for]] [[{$item_name|regex_replace:"/(Payment for)/":" "}]]<br />
[[Your transaction reference number is $invoice_sid. <br />Once your payment is endorsed by Admin the product(s) from your Shopping Cart would be added to your account.<br /><br />Thank you!]]
{/foreach}