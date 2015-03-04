{breadcrumbs}<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-group/?sid={$user_group_sid}">[[{$user_group_info.name}]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-profile/?user_group_sid={$user_group_sid}">[[Edit User Profile Fields]]</a> &#187; [[Add Field]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperplus32.png" border="0" alt="" class="titleicon" />[[Add User Profile Field]]</h1>
{include file='field_errors.tpl'}

<fieldset>
	<legend>[[Add a New User Profile Field]] </legend>
	<form method="post">
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="user_group_sid" value="{$user_group_sid}" />
		<table>
			{foreach from=$form_fields item=form_field}
				<tr>
					<td>[[{$form_field.caption}]] </td>
					<td class="required">{if $form_field.is_required}*{/if}</td>
					<td>{input property=$form_field.id}</td>
				</tr>
			{/foreach}
			<tr>
                <td colspan="3">
                    <div class="floatRight"><input type="submit" value="[[Add]]" class="grayButton" /></div>
                </td>
            </tr>
		</table>
	</form>
</fieldset>