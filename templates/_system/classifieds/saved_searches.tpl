<h1>[[Edit Saved Search]]</h1>
<table cellspacing="0" class="saved-search-tbl">
	<thead>
		<tr>
			<th class="tableLeft"> </th>
			<th width="10%">RSS</th>
			<th width="40%">[[Saved Searches Name]]</th>
			<th colspan="3"><div class="text-center">[[Actions]]</div></th>
			<th class="tableRight"> </th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$saved_searches item=saved_search}
		<tr>
			<td> </td>
			<td><a href="{$GLOBALS.site_url}/listing-feeds/?searchSID={$saved_search.sid}"><img alt="" src="{image}rss_icon.png" border="0" /></a></td>
			<td><span class="strong">{$saved_search.name|escape:"html"}</span></td>
			<td><a href="?action=delete&amp;search_id={$saved_search.id}" onclick="return confirm('Are you sure to delete \'{$saved_search.name|escape:"html"}\' search?');">[[Delete]]</a></td>
			<td>
				<form style="margin:0;padding:0;" method="post" action="{$GLOBALS.site_url}/saved-searches/edit/" id="editForm_{$saved_search.id}">
					<input type="hidden" name="action" value="edit_search" />
					<input type="hidden" name="id_saved" value="{$saved_search.sid}" />
					<input type='hidden' name='name[equal]' value='{$saved_search.name}' />
					<input type='hidden' name='listing_type_id' value='{$listing_type_id}' />
					<input type='hidden' name='formTemplateNem' value='{if $listing_type_id == 'Job'}search_form.tpl{else}search_form_resumes.tpl{/if}' />
					{foreach from=$saved_search.data item=criteria_fields}
						{foreach from=$criteria_fields item=criterion_field}
							{$criterion_field}
						{/foreach}
					{/foreach}
					<a href="javascript:document.getElementById('editForm_{$saved_search.id}').submit()">[[Edit]]</a>
				</form>
			</td>
			<td>
				<form style="margin:0;padding:0;" method="post" action="{$GLOBALS.site_url}/{if $saved_search.listing_type == 'Resume'}search-results-resumes/{else}search-results-jobs/{/if}">
					<input type="hidden" name="action" value="search" />
					{foreach from=$saved_search.data item=criteria_fields}
						{foreach from=$criteria_fields item=criterion_field}
							{$criterion_field}
						{/foreach}
					{/foreach}
					<input type="submit" value="[[Go Search]]" class="button" style="float: right;" />
				</form>
			</td>
			<td> </td>
		</tr>
		<tr>
			<td colspan="7" class="separateListing">&nbsp;</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="7"><div class="text-center">[[You have not saved any searches yet.]]</div></td>
		</tr>
	{/foreach}
	</tbody>
</table>