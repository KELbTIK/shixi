{if $errors}
	{foreach from=$errors item=message key=error}
		{if $error eq 'INVOICE_IS_NOT_VERIFIED'}
			<p class="error">[[Invoice is not verified]]</p>
		{elseif $error eq 'INVALID_INVOICE_ID'}
			<p class="error">[[Invalid invoice ID is specified]]</p>
		{/if}
	{/foreach}
{else}
	{foreach from=$products item=product name=products_block}
		{if $product.error}
			<p class="error">
				[[{$product.name}]]:
				{if $product.error eq 'INVALID_LISTING_ID'}
					[[Invalid listing ID is specified]] ([[ID]]: {$product.listingSid})
				{elseif $product.error eq 'LISTING_ALREADY_FEATURED'}
					[[Listing is already featured]] ([[ID]]: {$product.listingSid})
				{elseif $product.error eq 'LISTING_ALREADY_PRIORITY'}
					[[Listing is already priority]] ([[ID]]: {$product.listingSid})
				{elseif $product.error eq 'LISTING_ALREADY_ACTIVE'}
					[[Listing is already active]] ([[ID]]: {$product.listingSid})
				{/if}
			</p>
		{/if}
	{/foreach}
	<div style="padding: 20px 0;">
		<br />
		{assign var='firstProduct' value=false}
		{foreach from=$products item=product name=products_block}
			{if !$product.error}
				{if !$firstProduct}
					{assign var='firstProduct' value=true}
					<div style="padding: 20px 0;">[[You have successfully purchased the product(s) below]]:</div>
				{/if}
				<div style="padding: 3px 0;">[[{$product.name}]]</div>
			{/if}
		{/foreach}
		{if $firstProduct}
			[[Thank you for the purchase. Now you can manage your products from My Products section of our website]]: <a href="{$GLOBALS.site_url}/my-products/">{$GLOBALS.site_url}/my-products/</a><br />
		{/if}
		{if isset($listingTypes)}
			{foreach from=$listingTypes item=listingType name='types'}
				{if $smarty.foreach.types.first && $smarty.foreach.types.last}
					{capture assign=userListingTypes}[[{$listingType.name|strtolower}s]]{/capture}
					{capture assign=userSectionListingTypes}<a href="{$GLOBALS.site_url}/my-listings/{$listingType.ID}">[[My {$listingType.name}s]]</a>{/capture}
				{else}
					{if $smarty.foreach.types.first}
						{capture assign=userListingTypes}[[{$listingType.name|strtolower}s]], {/capture}
						{capture assign=userSectionListingTypes}<a href="{$GLOBALS.site_url}/my-listings/{$listingType.ID}">[[My {$listingType.name}s]]</a>, {/capture}
					{elseif $smarty.foreach.types.last}
						{capture assign=userListingTypes}{$userListingTypes}[[{$listingType.name|strtolower}s]]{/capture}
						{capture assign=userSectionListingTypes}{$userSectionListingTypes}<a href="{$GLOBALS.site_url}/my-listings/{$listingType.ID}">[[My {$listingType.name}s]]</a>{/capture}
					{else}
						{capture assign=userListingTypes}{$userListingTypes}[[{$listingType.name|strtolower}s]], {/capture}
						{capture assign=userSectionListingTypes}{$userSectionListingTypes}<a href="{$GLOBALS.site_url}/my-listings/{$listingType.ID}">[[My {$listingType.name}s]]</a>, {/capture}
					{/if}
				{/if}
			{/foreach}
			{if count($listingTypes) == 1}
				{capture assign=section}[[section]]{/capture}
			{else}
				{capture assign=section}[[sections]]{/capture}
			{/if}
			[[You can manage posted $userListingTypes from $userSectionListingTypes $section of your account.]]
		{/if}
	</div>
{/if}