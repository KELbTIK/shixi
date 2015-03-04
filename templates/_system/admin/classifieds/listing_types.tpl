{breadcrumbs}[[Listing Types]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpapercheck32.png" border="0" class="titleicon"/>[[Listing Types]]</h1>
<p><a href="{$GLOBALS.site_url}/add-listing-type/" class="grayButton">[[Add a New Listing Type]]</a></p>

<table>
	<thead>
		<tr>
			<th>[[ID]]</th>
			<th>[[Name]]</th>
			<th>[[Number of listings]]</th>
			<th colspan="3" class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$listing_types item=listing_type}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>{$listing_type.id}</td>
			<td>[[{$listing_type.caption}]]</td>
			<td>{$listing_type.listing_number}</td>
			<td align="center"><a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$listing_type.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
			{if $listing_type.listing_number > 0}
                <td>&nbsp;</td>
			{else}
				<td><a href="{$GLOBALS.site_url}/delete-listing-type/?sid={$listing_type.sid}" onclick='return confirm("[[Are you sure you want to delete this listing type?]]")' title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
			{/if}
			<td><a href="{$GLOBALS.site_url}/posting-pages/{$listing_type.id|lower}" title="[[Posting Pages]]" class="editbutton">[[Posting Pages]]</a></td>
		</tr>
		{/foreach}
	</tbody>
</table>