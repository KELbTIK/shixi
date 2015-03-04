{if $listings}
	{foreach from=$listings item=listing name=listings_block}
		<div class="featuredListings">
			<a href="{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">{$listing.Title|escape:'html'}</a><br />
			<span class="green">
				{$listing.user.CompanyName|escape:'html'}
			</span>
		</div>
		{if $smarty.foreach.listings_block.iteration is div by $number_of_cols}{/if}
	{/foreach}
{else}
	<div class="text-center">[[There are no listings with requested parameters in the system.]]</div>
{/if}