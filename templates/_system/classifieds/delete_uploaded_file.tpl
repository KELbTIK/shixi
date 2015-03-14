{foreach from=$errors item=message key=error}
	<div class="error alert alert-danger">
		{if $error eq 'PARAMETERS_MISSED'}
			[[The key parameters are not specified]]
		{elseif $error eq 'WRONG_PARAMETERS_SPECIFIED'}
			[[Wrong parameters specified]]
		{elseif $error eq 'NOT_OWNER'}
			[[You are not owner of this listing]]
		{/if}
	</div>
{foreachelse}
	<div class="message alert alert-info">[[File deleted successfully]]</div>
	<a href="{$GLOBALS.site_url}/edit-{$listingTypeSID|lower}/?listing_id={$listing_id}">[[Back to edit listing]]</a>
{/foreach}