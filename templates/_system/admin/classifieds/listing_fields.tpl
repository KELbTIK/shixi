{breadcrumbs}[[Common Fields]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpapercheck32.png" class="titleicon"/> [[Common Fields]]</h1>
<p><a href="{$GLOBALS.site_url}/add-listing-field/" class="grayButton">[[Add a New Listing Field]]</a></p>
<table>
	<thead>
		<th>[[ID]]</th>
		<th>[[Caption]]</th>
		<th>[[Type]]</th>
		<th>[[Required]]</th>
		<th colspan="2" class="actions">[[Actions]]</th>
	</thead>
	<tbody>
		{foreach from=$listing_field_sids item=listing_field_sid name=items_block}
		<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
			{display property='id' object_sid=$listing_field_sid assign=fieldID}
			<td>{$fieldID}</td>
			<td>[[{display property='caption' object_sid=$listing_field_sid}]]</td>
			<td>[[{display property='type' object_sid=$listing_field_sid}]]</td>
			<td>{display property='is_required' object_sid=$listing_field_sid}</td>
			<td><a href="{$GLOBALS.site_url}/edit-listing-field/?sid={$listing_field_sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
			<td>{if $fieldID != 'Location'}<a href="{$GLOBALS.site_url}/delete-listing-field/?sid={$listing_field_sid}" onclick='return confirm("[[Are you sure you want to delete this field?]]")' title="[[Delete]]" class="deletebutton">[[Delete]]</a>{/if}</td>
		</tr>
		{/foreach}	
	</tbody>
</table>