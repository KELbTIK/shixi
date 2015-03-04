{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-users/{$user_group_info.id|lower}/?restore=1">[[Manage]] {if $user_group_info.id == 'Employer' || $user_group_info.id == 'JobSeeker'}[[{$user_group_info.name}s]]{else}'{$user_group_info.name}' [[Users]]{/if}</a> &#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_sid}">[[Edit {$user_group_info.name}]]</a> &#187; [[Manage Applications]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/> [[Manage Applications for]] {$username}</h1>

<form method="post" name="applicationForm" action="">
	<input type="hidden" name="orderBy" value="{$orderBy}" />
	<input type="hidden" name="order" value="{$order}" />
	<input id="action" type="hidden" name="action" value="" />
	<input type="hidden" name="username" value="{$username}" />
	<input type="hidden" name="user_sid" value="{$user_sid}" />
	
	<input type="submit" value="[[Approve selected]]"	class="grayButton" onclick="submitForm('approve'); return false;"/>
	<input type="submit" value="[[Reject selected]]" class="grayButton" onclick="submitForm('reject')"/>
    <input type="submit" value="[[Delete selected]]" class="deletebutton" onclick="if (confirm('[[Are you sure you want to delete the selected application(s)?]]')) submitForm('delete');"/>
	
	<div class="clr"><br/></div>

	<table width="60%">
		<thead>
			<tr>
				<th width="1%"><input type="checkbox" id="all_checkboxes_control"></th>
				<th width="20%"><a href="?user_sid={$user_sid}&amp;username={$username}&amp;orderBy=date&amp;order={if		$orderBy == "date" 		&& $order == "asc"}desc{else}asc{/if}">[[Date applied]]</a></th>
				<th width="35%"><a href="?user_sid={$user_sid}&amp;username={$username}&amp;orderBy=title&amp;order={if		$orderBy == "title" 	&& $order == "asc"}desc{else}asc{/if}">[[Job title]]</a></th>
				<th width="25%"><a href="?user_sid={$user_sid}&amp;username={$username}&amp;orderBy=applicant&amp;order={if	$orderBy == "applicant" && $order == "asc"}desc{else}asc{/if}">Applicant’s Name</a></th>
				<th width="20%"><a href="?user_sid={$user_sid}&amp;username={$username}&amp;orderBy=status&amp;order={if		$orderBy == "status" 	&& $order == "asc"}desc{else}asc{/if}">Status</a></th>
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
								<td rowspan="2" width="1%" valign="top"><input type="checkbox" name="applications[{$app.id}]" value="1" id="checkbox_{$smarty.foreach.applications.iteration}"/></td>
								<td width="20%">{$app.date}</td>
								<td width="35%"><a href="{$GLOBALS.site_url}/display-listing/?listing_id={$app.job.sid}">{$app.job.Title}</a></td>
								<td width="25%">{$app.user.FirstName} {$app.user.LastName}</td>
								<td width="20%">[[{$app.status}]]</td>
							</tr>
							<tr>
								<td colspan="4">
									<div class="applicationCommentsHeader">[[Cover Letter ]]:</div>
									<div class="applicationComments">
										{$app.comments|escape:'html'}
										{if $app.resume}
											<br />- <a href="{$GLOBALS.site_url}/display-listing/?listing_id={$app.resume}">[[Attached Resume]]</a>
										{/if}
										{if $app.file}
											<br />- <a href="?appsID={$app.id}&amp;filename={$app.file|escape:"url"}">[[View Attached File]]</a>
										{/if}
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		{/foreach}
	</table>
</form>

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
	set_checkbox(this.checked);
});

function submitForm(action) {
	document.getElementById('action').value = action;
	var form = document.applicationForm;
	form.submit();
}

</script>
{/literal}