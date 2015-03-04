{foreach from=$errors item=message key=error}
	{if $error eq 'INVALID_LISTING_ID'}
		<p class="error">[[Invalid listing ID is specified]]</p>
	{elseif $error eq 'LISTING_ALREADY_FEATURED'}
		<p class="error">[[Listing is already featured]]</p>
	{elseif $error eq 'PARAMETERS_MISSED'}
		<p class="error">[[The key parameters are not specified]]</p>
	{/if}
{foreachelse}
	<p class="message">[[Your listing was successfully upgraded to featured]]</p>
{/foreach}