{foreach from=$errors item=message key=error}
	<div class="error alert alert-danger">
	{if $error eq 'INVALID_LISTING_ID'}
			[[Invalid listing ID is specified]]
		{elseif $error eq 'LISTING_ALREADY_PRIORITY'}
			[[Listing is already priority]]
		{elseif $error eq 'PARAMETERS_MISSED'}
			[[The key parameters are not specified]]
		{/if}
	</div>
{foreachelse}
	<div class="message alert alert-info"> [[Your listing succesfully upgraded to priority]]</div>
{/foreach}