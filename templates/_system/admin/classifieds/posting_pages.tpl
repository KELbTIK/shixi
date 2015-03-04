{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a> &#187; [[Edit {$listingTypeInfo.name} Posting Pages]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit]] [[{$listingTypeInfo.name}]] [[Posting Pages]]</h1>
<p><a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}/new" class="grayButton">[[Add a New Posting Page]]</a></p>

<table>
	<thead>
		<tr>
			<th>#</th>
			<th>[[Page ID]]</th>
			<th>[[Page Name]]</th>
			<th>[[Number of fields]]</th>
			<th colspan="3" class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$pages item=page name=page_loop}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>{$smarty.foreach.page_loop.iteration}</td>
				<td>{$page.page_id}</td>
				<td>[[{$page.page_name}]]</td>
				<td>{$page.fields_num}</td>
				<td align="center">
                    <a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}/edit/{$page.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a>
                    {if $countPages > 1}
                        <a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}/delete/{$page.sid}" {if $page.fields_num>0}onclick='return confirm("[[Fields contained on this Page will be removed from the front-end as well, until added to one of the Posting Pages again. Delete this Posting page?]]")'{else}onclick='return confirm("[[Are you sure you want to delete this Posting Page?]]")'{/if} title="[[Delete]]" class="deletebutton">[[Delete]]</a>
                    {/if}
                </td>
				<td align="center">
					{if $smarty.foreach.page_loop.iteration < $smarty.foreach.page_loop.total}
                        <a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}?page_sid={$page.sid}&amp;action=move_down"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
					{/if}
				</td>
                <td>
					{if $smarty.foreach.page_loop.iteration > 1}
                        <a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}?page_sid={$page.sid}&amp;action=move_up"><img src="{image}b_up_arrow.gif" border="0" alt=""/></a>
					{/if}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>