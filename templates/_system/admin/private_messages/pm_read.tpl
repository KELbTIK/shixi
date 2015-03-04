{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-users/{$user_group_info.id|lower}/?restore=1">[[Manage {if $user_group_info.id == 'Employer' || $user_group_info.id == 'JobSeeker'}{$user_group_info.name}s{else}'{$user_group_info.name}' Users{/if}]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_sid}">[[Edit {$user_group_info.name}]]</a>
	&#187; <a href="{$GLOBALS.site_url}/private-messages/pm-main/?user_sid={$user_sid}">[[Personal Messages]]</a>
{if $returt_to == "in"}
	&#187; <a href="{$GLOBALS.site_url}/private-messages/pm-inbox/?user_sid={$user_sid}">[[Inbox]]</a>
{else}
	&#187; <a href="{$GLOBALS.site_url}/private-messages/pm-outbox/?user_sid={$user_sid}">[[Outbox]]</a>
{/if}
	&#187; [[Message detail]]
{/breadcrumbs}

<h1><img src="{image}/icons/mail32.png" border="0" alt="" class="titleicon"/>[[Manage Personal messages for]] {$username}</h1>

<h3>[[Message detail]]</h3>

<table> 
{if $message.outbox == 0}
	<tr>
		<td>[[Message from]]:</td>
		<td>{$message.from_name}</td>
	</tr>
{else}
	<tr>
		<td>[[Message to]]:</td>
		<td>{$message.to_name}</td>
	</tr>
{/if}
	<tr>
		<td>[[Date]]:</td>
		<td>{$message.data|date_format:$GLOBALS.current_language_data.date_format} {$message.data|date_format:"%H:%M:%S"}</td>
	</tr>
	<tr>
		<td>[[Subject]]:</td>
		<td>{$message.subject}</td>
	</tr>
	<tr><td colspan="2">{$message.message}</td></tr>
	<tr><td colspan="2"><a href="{$GLOBALS.site_url}/private-messages/pm-read/?user_sid={$user_sid}&amp;mess={$message.id}&amp;from={$returt_to}&amp;action=delete&amp;page={$page}"><img border="0" src="{image}delete.png" id="pm_delete" /></a></td></tr>
</table>

<script type="text/javascript">
	$("#pm_delete").click(function(){
		if (confirm('[[Are you sure?]]'))
			return true;
		return false;
	});
</script>