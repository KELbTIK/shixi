{foreach from=$errors item=message key=error}
	<div class="error alert alert-danger">
		{if $error eq 'INVALID_LISTING_ID'}
			[[Invalid listing ID is specified]]
		{elseif $error eq 'LISTING_ALREADY_FEATURED'}
			[[Listing is already featured]]
		{elseif $error eq 'PARAMETERS_MISSED'}
			[[The key parameters are not specified]]
		{/if}
	</div>
{foreachelse}
<div class="message alert alert-success">[[Your listing was successfully upgraded to featured]]</div>
{/foreach}