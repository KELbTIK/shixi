{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-users/{$user_group_info.id|lower}/?restore=1">[[Manage {if $user_group_info.id == 'Employer' || $user_group_info.id == 'JobSeeker'}{$user_group_info.name}s{else}'{$user_group_info.name}' Users{/if}]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_sid}">[[Edit {$user_group_info.name}]]</a> &#187; <a href="{$GLOBALS.site_url}/private-messages/pm-main/?user_sid={$user_sid}">[[Personal Messages]]</a> &#187; [[Outbox]]{/breadcrumbs}
<h1><img src="{image}/icons/mail32.png" border="0" alt="" class="titleicon"/>[[Manage Personal messages for]] {$username}</h1>
<h3>[[Outbox]]</h3>

<form action="{$GLOBALS.site_url}/private-messages/pm-outbox/?user_sid={$user_sid}&amp;page={$page}" method="post" id="pm_form">
	<input type="hidden" id="pm_action" name="pm_action" value="" />
	{foreach from=$navigate item=one key=page}
		{if $one|count_characters == 0}
			{$page}
		{else}
			<a href="{$GLOBALS.site_url}/private-messages/pm-outbox/?user_sid={$user_sid}&amp;page={$one}">{$page}</a>
		{/if}
	{/foreach}
	
	<table>
		<thead>
			<tr>
				<th width="40%">[[Subject]]</th>
				<th>[[For]]</th>
				<th width="25%">[[Date]]</th>
				<th align="center" width="5%"><input id="pm_all_check" type="checkbox"/></th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$message item=one}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td><a href="{$GLOBALS.site_url}/private-messages/pm-read/?user_sid={$user_sid}&amp;mess={$one.id}&amp;from=out&amp;page={$page}">{$one.subject}</a></td>
				<td>{$one.to_name}</td>
				<td>{$one.data|date_format:$GLOBALS.current_language_data.date_format} {$one.data|date_format:"%H:%M:%S"}</td>
				<td align="center"><input class="pm_checkbox" type="checkbox" value="{$one.id}" name="pm_check[]"/></td>
			</tr>
		{/foreach}
		</tbody>
		<thead>
			<tr>
				<td colspan="4"><img border="0" src="{image}delete.png" id="pm_delete" style="cursor: pointer;" /></td>
			</tr>
		</thead>
	</table>
	
	{foreach from=$navigate item=one key=page}
		{if $one|count_characters == 0}
			{$page}
		{else}
			<a href="{$GLOBALS.site_url}/private-messages/pm-outbox/?user_sid={$user_sid}&amp;page={$one}">{$page}</a>
		{/if}
	{/foreach}
</form>

<script type="text/javascript">

	$("#pm_form").submit(function() {
		if ($("#pm_action").val() == '')
			return false;
		return true;
	});
	
	$("#pm_delete").click(function() {
		var butt = $(this);
		if ($(".pm_checkbox:checked").size() > 0) {
			if (!confirm('[[Are you sure?]]')) return false;
				$("#pm_action").val("delete");
				$("#pm_form").submit();
		} else {
			alert('[[No selection]]');
		}
	});
	
	$("#pm_all_check").change(function() {
		var total = $(this).attr("checked");
		$(".pm_checkbox").attr("checked", total);
	});
	
</script>