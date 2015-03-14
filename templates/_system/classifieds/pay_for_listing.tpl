{foreach from=$errors item=message key=error}
	<div class="error alert alert-danger">
	{if $error eq 'INVALID_LISTING_ID'}
			[[Invalid listing ID is specified]]
		{elseif $error eq 'LISTING_IS_NOT_COMPLETE'}
			[[Your listing cannot be activated unless all required fields are filled in.]]
		{elseif $error eq 'LISTING_ALREADY_ACTIVE'}
			[[Listing is already active.]]
		{elseif $error eq 'WRONG_LISTING_ID_SPECIFIED'}
			[[Listing does not exist]]
		{/if}
	</div>
{foreachelse}
	<div class="message alert alert-info"></div>  [[Your listing is successfully activated]]</div>
	<div style="overflow:hidden;">
		<a href="{$GLOBALS.site_url}/my-listings/{$listingTypeID|lower}/" >[[Back to My Listings]]</a>
	</div>
{/foreach}