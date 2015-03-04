<h1>[[Resume Alerts]]</h1>

{foreach from=$errors item="message" key="error"}
	<p class="error">[[{$error}]]</p>
{/foreach}

<p><a href="{$GLOBALS.site_url}/resume-alerts/add/">[[Add new resume alert]]</a></p>

<table cellspacing="0" style="width: 70%;" id="table-alerts">
	<thead>
		<tr>
			<th class="tableLeft"> </th>
			<th>[[Resume Alert Name]]</th>
			<th colspan="4"><div class="text-center">[[Actions]]</div></th>
			<th class="tableRight"> </th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$saved_searches item=saved_search}
		<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
			<td> </td>
			<td><span class="strong">{$saved_search.name}</span></td>
			<td width="10%">
				<form style="margin:0;padding:0;" method="post" action="{$GLOBALS.site_url}/resume-alerts/edit/" id="editForm_{$saved_search.id}">
					<input type="hidden" name="action" value="edit_alert" />
					<input type="hidden" name="id_saved" value="{$saved_search.sid}" />
					<input type='hidden' name='name[equal]' value='{$saved_search.name}' />
					<input type='hidden' name='email_frequency[multi_like][]' value='{$saved_search.email_frequency}' />
					{foreach from=$saved_search.data item=criteria_fields}
						{foreach from=$criteria_fields item=criterion_field}
							{$criterion_field}
						{/foreach}
					{/foreach}
					<a href="javascript:document.getElementById('editForm_{$saved_search.id}').submit()">[[Edit]]</a>
				</form>
			</td>
			<td width="10%">{$saved_search.data.listing_type[equal]}<a href="?action=delete&amp;search_id={$saved_search.id}" onclick="return confirm('[[Are you sure?]]')">[[Delete]]</a></td>
			<td width="27%">
				<form style="margin:0;padding:0;" method="post" action="{$GLOBALS.site_url}/search-results-resumes/" id='PreviewSearchResults_{$saved_search.id}'>
					<input type="hidden" name="action" value="search" />
					{foreach from=$saved_search.data item=criteria_fields}
						{foreach from=$criteria_fields item=criterion_field}
							{$criterion_field}
						{/foreach}
					{/foreach}
					<a href="javascript:document.getElementById('PreviewSearchResults_{$saved_search.id}').submit()">[[Preview Search Results]]</a>
				</form>
			</td>
			<td width="10%">
				{if $saved_search.auto_notify}
					<a href="?action=disable_notify&amp;search_id={$saved_search.id}">[[Disable]]</a>
				{else}
					<a href="?action=enable_notify&amp;search_id={$saved_search.id}">[[Enable]]</a>
				{/if}
			</td>
			<td> </td>
		</tr>
		<tr>
			<td colspan="7" class="separateListing"> </td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="7"><div class="text-center">[[You have not saved any searches yet.]]</div></td>
		</tr>
	{/foreach}
	</tbody>
</table>
