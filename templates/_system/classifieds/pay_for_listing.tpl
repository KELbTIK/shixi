{foreach from=$errors item=message key=error}
	{if $error eq 'INVALID_LISTING_ID'}
		<p class="error">[[Invalid listing ID is specified]]</p>
	{elseif $error eq 'LISTING_IS_NOT_COMPLETE'}
		<p class="error">[[Your listing cannot be activated unless all required fields are filled in.]]</p>
	{elseif $error eq 'LISTING_ALREADY_ACTIVE'}
		<p class="error">[[Listing is already active.]]</p>
	{elseif $error eq 'WRONG_LISTING_ID_SPECIFIED'}
		<p class="error">[[Listing does not exist]]</p>
	{/if}
{foreachelse}
	<p class="message">[[Your listing is successfully activated]]</p>
	<div style="overflow:hidden;">
		<a href="{$GLOBALS.site_url}/my-listings/{$listingTypeID|lower}/" >[[Back to My Listings]]</a>
	</div>
{/foreach}