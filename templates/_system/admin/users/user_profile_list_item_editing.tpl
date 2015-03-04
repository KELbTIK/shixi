{breadcrumbs}<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-group/?sid={$user_group_sid}">[[Edit User Group]] </a> &#187; <a href="{$GLOBALS.site_url}/edit-user-profile/?user_group_sid={$user_group_sid}">[[Edit User Profile Fields]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-profile-field/?sid={$user_profile_field_sid}&user_group_sid={$user_group_sid}">[[{$user_profile_field_info.caption}]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-profile-field/edit-list/?field_sid={$user_profile_field_sid}">[[Edit List]]</a> &#187; [[{$list_item_value}]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon" /> [[Edit List Item]]</h1>
{include file='field_errors.tpl'}

<fieldset>
	<legend>&nbsp;[[Edit List Item]]</legend>
	<table>
		<form method="post">
		<input type="hidden" name="action" value="save">
		<input type="hidden" name="field_sid" value="{$user_profile_field_sid}">
		<input type="hidden" name="item_sid" value="{$item_sid}">
		<tr>
			<td>[[Value]] </td>
			<td class="required">*</td>
			<td><input type="text" name="list_item_value" value="{$list_item_value}"></td>
		</tr>
		<tr>
			<td colspan="3" align="right"><span class="greenButtonEnd"><input type="submit" value="[[Save]]" class="greenButton" /></span></td>
		</tr>
		</form>
	</table>
</fieldset>