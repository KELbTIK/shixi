{foreach from=$errors item=message key=error}
	{if $error eq 'PARAMETERS_MISSED'}
		<p class="error">[[The key parameters are not specified]]</p>
	{elseif $error eq 'WRONG_PARAMETERS_SPECIFIED'}
		<p class="error">[[Wrong parameters specified]]</p>
	{elseif $error eq 'NOT_OWNER'}
		<p class="error">[[You are not owner of this listing]]</p>
	{/if}
{foreachelse}
	<p class="message">[[File deleted successfully]]</p>
	<a href="{$GLOBALS.site_url}/edit-{$listingTypeSID|lower}/?listing_id={$listing_id}">[[Back to edit listing]]</a>
{/foreach}