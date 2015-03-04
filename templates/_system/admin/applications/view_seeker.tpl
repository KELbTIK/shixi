{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-users/{$user_group_info.id|lower}/?restore=1">[[Manage]] {if $user_group_info.id == 'Employer' || $user_group_info.id == 'JobSeeker'}[[{$user_group_info.name}s]]{else}'{$user_group_info.name}' [[Users]]{/if}</a> &#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_sid}">[[Edit {$user_group_info.name}]]</a> &#187; [[Manage Applications]]{/breadcrumbs}
<h1>[[Manage Applications for]] {$username}</h1>
<h3>[[Jobs Applied]]</h3>

{if $applications}
<form method="post" action="">
	<input type="hidden" name="orderBy" value="{$orderBy}" />
	<input type="hidden" name="order" value="{$order}" />
	<input type="hidden" name="username" value="{$username}" />
	<input type="hidden" name="user_sid" value="{$user_sid}" />

	<input type="submit" name="action" value="[[Delete selected]]" class="deletebutton" onclick="return confirm('[[Are you sure you want to delete the selected application(s)?]]')"/>
	<div class="clr"><br /></div>
	<table width="60%">
		<thead>
			<tr>
				<th width="1%"><input type="checkbox" id="all_checkboxes_control"></th>
				<th width="20%"><a href="?user_sid={$user_sid}&amp;username={$username}&amp;orderBy=date&amp;order={if		$orderBy == "date" 		&& $order == "asc"}desc{else}asc{/if}">[[Date applied]]</a></th>
				<th width="45%"><a href="?user_sid={$user_sid}&amp;username={$username}&amp;orderBy=title&amp;order={if		$orderBy == "title" 	&& $order == "asc"}desc{else}asc{/if}">[[Job title]]</a></th>
				<th width="20%"><a href="?user_sid={$user_sid}&amp;username={$username}&amp;orderBy=company&amp;order={if	$orderBy == "company" 	&& $order == "asc"}desc{else}asc{/if}">[[Company]]</a></th>
				<th width="20%"><a href="?user_sid={$user_sid}&amp;username={$username}&amp;orderBy=status&amp;order={if		$orderBy == "status" 	&& $order == "asc"}desc{else}asc{/if}">[[Status]]</a></th>
			</tr>
		</thead>
	</table>
	<table width="60%" id="application-table">
		{foreach item=app from=$applications name=applications}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>
					<table>
						<tbody>
							<tr>
								<td width="1%" rowspan="2" valign="top"><input type="checkbox" name="applications[{$app.id}]" value="1" id="checkbox_{$smarty.foreach.applications.iteration}"/></td>
								<td width="20%">{$app.date}</td>
								<td width="45%">{if $app.job != NULL}<a href="{$GLOBALS.site_url}/display-listing/?listing_id={$app.job.sid}">{$app.job.Title}</a>{else}[[Not Available Anymore]]{/if}</td>
								<td width="20%">{$app.company.CompanyName}</td>
								<td width="20%">{$app.status}</td>
							</tr>
							<tr>
								<td colspan="4">[[Cover Letter]]:<br/>{$app.comments}</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		{/foreach}
	</table>

</form>
{/if}

<script type="text/javascript">
	var total = {$smarty.foreach.applications.total};
	{literal}
	
	function set_checkbox(param) {
		for (i = 1; i <= total; i++) {
			if (checkbox = document.getElementById('checkbox_' + i))
				checkbox.checked = param;
		}
	}
	
	$("#all_checkboxes_control").click(function() {
		if ( this.checked == false)
			set_checkbox(false);
		else
			set_checkbox(true);
	});
</script>
{/literal}