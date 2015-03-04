{foreach from=$errors item=message key=error}
	{if $error eq 'INVALID_LISTING_ID'}
		<p class="error">[[Invalid listing ID is specified]]</p>
	{elseif $error eq 'LISTING_ALREADY_PRIORITY'}
		<p class="error">[[Listing is already priority]]</p>
	{elseif $error eq 'PARAMETERS_MISSED'}
		<p class="error">[[The key parameters are not specified]]</p>
	{/if}
{foreachelse}
	<p class="message">[[Your listing succesfully upgraded to priority]]</p>
{/foreach}