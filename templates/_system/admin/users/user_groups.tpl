{breadcrumbs}[[User Groups]]{/breadcrumbs}
<h1><img src="{image}users-online.png" border="0" alt="" class="titleicon" />[[User Groups]]</h1>
<p><a href="{$GLOBALS.site_url}/add-user-group/" class="grayButton">[[Add a New User Group]]</a></p>

<table>
	<thead>
		<tr>
			<th>[[ID]]</th>
			<th>[[Name]]</th>
			<th>[[User number]]</th>
			<th colspan="4" class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$user_groups item=user_group}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>{$user_group.id}</td>
				<td>[[{$user_group.caption}]]</td>
				<td>{$user_group.user_number}</td>
				<td><a href="{$GLOBALS.site_url}/edit-user-group/?sid={$user_group.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
				<td><span class="greenButtonEnd"><input type="button" onclick="location.href='{$GLOBALS.site_url}/edit-user-profile/?user_group_sid={$user_group.sid}'" class="greenButton" value="[[Edit User Profile Fields]]" /></span></td>
				<td><span class="greenButtonEnd"><input type="button" onclick="location.href='{$GLOBALS.site_url}/system/users/acl/?type=group&role={$user_group.sid}'" class="greenButton" value="[[Manage permissions]]" /></span></td>
				<td>&nbsp;
					{if $user_group.user_number > 0}
					{else}
						<a class="deletebutton" title="Delete" onclick="return confirm('[[Are you sure you want to delete this user group?]]')" href="{$GLOBALS.site_url}/delete-user-group/?sid={$user_group.sid}">[[Delete]]</a>
					{/if}
				</td>
			</tr>
		{/foreach}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td colspan="5">[[Guest]]</td>
			<td><span class="greenButtonEnd"><input type="button" onclick="location.href='{$GLOBALS.site_url}/system/users/acl/?type=guest&role=guest'" class="greenButton" value="[[Manage permissions]]"/></span></td>
			<td></td>
		</tr>
	</tbody>
</table>