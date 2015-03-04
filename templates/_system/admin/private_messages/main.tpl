{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-users/{$user_group_info.id|lower}/?restore=1">
		[[Manage {if $user_group_info.id == 'Employer' || $user_group_info.id == 'JobSeeker'}{$user_group_info.name}s{else}'{$user_group_info.name}' Users{/if}]]
	</a>
	&#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_sid}">[[Edit]] {$user_group_info.name}</a>
	&#187; [[Personal Messages]]
{/breadcrumbs}
<h1><img src="{image}/icons/mail32.png" border="0" alt="" class="titleicon"/>[[Manage Personal messages for]] {$username}</h1>
<h3>[[Select folder]]</h3>

<table>
	<thead>
		<tr>
			<th>[[Folder]]</th>
			<th align="center">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Inbox]] ({$total_in})</ td>
			<td width="20%" align="center"><a href="{$GLOBALS.site_url}/private-messages/pm-inbox/?user_sid={$user_sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Outbox]] ({$total_out})</td>
			<td width="20%" align="center"><a href="{$GLOBALS.site_url}/private-messages/pm-outbox/?user_sid={$user_sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
		</tr>
	</tbody>
</table>