{if $type_sid}
	{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$type_sid}">{$type_info.name}</a> &#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$field_sid}">{$field_info.caption}</a> &#187; [[Edit Fields]]{/breadcrumbs}
{else}
	{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-fields/">[[Common Fields]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-listing-field/?sid={$field_sid}">{$field_info.caption}</a> &#187; [[Edit Fields]]{/breadcrumbs}
{/if}
<h1>[[Edit Fields]]</h1>
<p><a href="?field_sid={$field_sid}&action=add" class="grayButton">[[Add a New Listing Field]]</a></p>
<table>
	<thead>
		<tr>
			<th>[[ID]]</th>
			<th>[[Caption]]</th>
			<th>[[Type]]</th>
			<th>[[Required]]</th>
			<th colspan="5" class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$listing_field_sids item=listing_field_sid name=items_block}
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>{display property='id' object_sid=$listing_field_sid}</td>
				<td>[[{display property='caption' object_sid=$listing_field_sid}]]</td>
				<td>{display property='type' object_sid=$listing_field_sid}</td>
				<td>{display property='is_required' object_sid=$listing_field_sid}</td>
				<td><a href="?sid={$listing_field_sid}&field_sid={$field_sid}&action=edit" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
				<td><a href="?sid={$listing_field_sid}&field_sid={$field_sid}&action=delete" onclick='return confirm("[[Are you sure you want to delete this field?]]")' title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
				<td>{if $smarty.foreach.items_block.iteration < $smarty.foreach.items_block.total}<a href="?field_sid={$listing_field_sid}&amp;action=move_down"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>{/if}</td>
				<td>{if $smarty.foreach.items_block.iteration > 1}<a href="?field_sid={$listing_field_sid}&amp;action=move_up"><img src="{image}b_up_arrow.gif" border="0" alt=""/></a>{/if}</td>
			</tr>
		{/foreach}
	</tbody>
</table>