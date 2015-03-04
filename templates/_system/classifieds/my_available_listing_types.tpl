<ul>
	{foreach from=$listingTypes item="listingTypeInfo"}
		{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id))}
			<li><a href="{$GLOBALS.site_url}/my-listings/{$listingTypeInfo.id|escape:'url'}/">{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}{tr}My {$listingTypeInfo.name}s{/tr|escape:'html'}{else}{tr}My {$listingTypeInfo.name} Listings{/tr|escape:'html'}{/if}</a></li>
		{/if}
	{/foreach}
</ul>
