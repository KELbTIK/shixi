{assign var=i value=1}
<ul class="browseListing">
	{foreach from=$browseItems key=elementId item=elementCount name=browseItems}
		{if ( ($GLOBALS.settings.enableBrowseByCounter && $elementCount > 0 || !$GLOBALS.settings.enableBrowseByCounter) && $i <= $recordsNumToDisplay )}
			{assign var=i value=$i+1}
			<li>
				<a class='brByCategoryLink'  href="{$GLOBALS.site_url}/browse-by-category/{$elementId|replace:"/":"-or-"|escape:'url'}/">[[{$elementId|escape:'html'|truncate:28:"...":true}]]
					{if $GLOBALS.settings.enableBrowseByCounter}<span class="blue">({$elementCount})</span>{/if}
				</a>
			</li>
			{if $smarty.foreach.browseItems.iteration is div by $columns}</ul><ul class="browseListing">{/if}
		{/if}
	{foreachelse}
		<li>[[There are no listings with requested parameters in the system.]]</li>
	{/foreach}
	{if $i == 1}
		 <li>[[There are no listings with requested parameters in the system.]]</li>
	{/if}
</ul>
