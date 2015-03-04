{breadcrumbs}<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-group/?sid={$user_group_sid}">[[{$user_group_info.name}]]</a> &#187; [[Edit User Profile Fields]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit User Profile Fields]]</h1>

{foreach from=$errors key=error item=message}
	{if $error eq "USER_GROUP_SID_NOT_SET"}
		<p class="error">[[User group SID is not set]]</p>
	{/if}
{foreachelse}
	<p><a href="{$GLOBALS.site_url}/add-user-profile-field/?user_group_sid={$user_group_sid}" class="grayButton">[[Add User Profile Field]]</a></p>
	<table>
		<thead>
			<tr>
				<th>[[SID]]</th>
				<th>[[ID]]</th>
				<th>[[Caption]]</th>
				<th>[[Type]]</th>
				<th>[[Required]]</th>
				<th colspan="4" class="actions">[[Actions]]</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$user_profile_fields item=user_profile_field name=fields_block}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>{$user_profile_field.sid}</td>
				<td>{$user_profile_field.id}</td>
				<td>[[{$user_profile_field.caption}]]</td>
				<td>{$user_profile_field.type}</td>
				<td>{if $user_profile_field.is_required}[[Yes]]{else}[[No]]{/if}</td>
				<td><a href="{$GLOBALS.site_url}/edit-user-profile-field/?sid={$user_profile_field.sid}&amp;user_group_sid={$user_group_sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
				<td>{if $user_profile_field.id != 'Location'}<a href="{$GLOBALS.site_url}/delete-user-profile-field/?sid={$user_profile_field.sid}&amp;user_group_sid={$user_group_sid}" onclick="return confirm('[[Are you sure you want to delete this field?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a>{/if}</td>
				<td>
					{if $smarty.foreach.fields_block.iteration < $smarty.foreach.fields_block.total}
						<a href="?user_group_sid={$user_group_sid}&amp;field_sid={$user_profile_field.sid}&amp;action=move_down"><img src="{image}b_down_arrow.gif" border=0>
					{/if} 
				</td>
				<td>
					{if $smarty.foreach.fields_block.iteration > 1}
						<a href="?user_group_sid={$user_group_sid}&amp;field_sid={$user_profile_field.sid}&amp;action=move_up"><img src="{image}b_up_arrow.gif" border=0>
					{/if} 
				</td>
			</tr>
		{/foreach}
	</table>
{/foreach}