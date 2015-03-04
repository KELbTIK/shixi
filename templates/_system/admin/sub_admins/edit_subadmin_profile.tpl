{breadcrumbs}[[Edit Admin Sub-Account]]{/breadcrumbs}
<h1>[[Edit Admin Sub-Account]]</h1>

{include file="../users/field_errors.tpl"}
{if $saved}
	{include file="../errors/successfully_saved.tpl"}
{/if}
<form method="post">
	<fieldset>
		<legend>[[Admin Sub Account Info]]</legend>
		<table>
			{foreach from=$form_fields item=form_field}
			<tr>
				<td width="15%" valign=top nowrap>[[{$form_field.caption}]]</td>
				<td valign=top class="required">{if $form_field.is_required}*{/if}</td>
				<td width="85%"> {input property=$form_field.id}</td>
			</tr>
			{/foreach}
			<tr>
				<td colspan="3">
					<input type="hidden" name="action" value="save" />
					<input type="hidden" name="subadmin" value="{$sid}" />
					<span class="greenButtonEnd"><input type="submit" value="[[Save]]" class="greenButton" /></span>
				</td>
			</tr>
		</table>
	</fieldset>
</form>


<h4>[[Your allowed permissions are the following]]:</h4>
<ul>
	{foreach from=$resources item=permission}
		{if $permission.value eq 'allow' && !$permission.notification}
			<li>[[{$permission.title}]]</li>
		{/if}
	{/foreach}
</ul>
{if $notifications}
	<fieldset id="notifications_settings_fieldset">
		<legend class="title strong">[[Notification Settings]]</legend>
		{include file="acl_notifications.tpl" group=$group}
	</fieldset>
{/if}
