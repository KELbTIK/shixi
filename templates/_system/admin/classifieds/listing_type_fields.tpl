{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Fields]]</a> &#187; [[{$listing_type_info.name}]]{/breadcrumbs}
<div class="clr"><br/></div>
<h1><img src="{image}/icons/linedpapercheck32.png" border="0" alt="" class="titleicon" /> [[Listing Fields]]</h1>
<p><a href="{$GLOBALS.site_url}/add-listing-type-field/?listing_type_sid={$listing_type_sid}" class="grayButton">[[Add a New Listing Field]]</a></p>
<table>
	<thead>
		<tr>
			<th>[[ID]]</th>
			<th>[[Caption]]</th>
			<th>[[Type]]</th>
			<th>[[Required]]</th>
			<th colspan="2" class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$listing_field_sids item=listing_field_sid name=items_block}
			{display property='id' object_sid=$listing_field_sid assign=fieldID}
			{if !in_array($fieldID, array('anonymous', 'access_type', 'screening_questionnaire', 'expiration_date'))}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>{display property='id' object_sid=$listing_field_sid}</td>
				<td>[[{display property='caption' object_sid=$listing_field_sid}]]</td>
				<td>[[{display property='type' object_sid=$listing_field_sid}]]</td>
				<td>{display property='is_required' object_sid=$listing_field_sid}</td>
				<td><a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$listing_field_sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
				<td><a href="{$GLOBALS.site_url}/delete-listing-type-field/?sid={$listing_field_sid}" onclick='return confirm("[[Are you sure you want to delete this field?]]")' title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
			</tr>
			{/if}
		{/foreach}
	</tbody>
</table>