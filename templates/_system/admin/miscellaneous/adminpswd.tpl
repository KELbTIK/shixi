{breadcrumbs}[[Admin Password]]{/breadcrumbs}
<h1><img src="{image}/icons/lock32.png" border="0" alt="" class="titleicon"/>[[Admin Password]]</h1>

{if $action eq "change_admin_account" && !$errors}
	<p class="message">[[Your login details were successfully changed.]]</p>
{else}
	{foreach from=$errors key=field_caption item=error}
		{if $error eq 'EMPTY_VALUE'}
			<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
		{elseif $error eq 'NOT_UNIQUE_VALUE'}
			<p class="error">'[[{$field_caption}]]' [[this value is already used in the system]]</p>
		{elseif $error eq 'INVALID_PASSWORD'}
			<p class="error">[[Administrator Current Password is Incorrect]]</p>
		{elseif $error eq 'NOT_CONFIRMED'}
			<p class="error">'[[{$field_caption}]]' [[not confirmed]]</p>
		{else}
			<p class="error">[[{$error}]]</p>
		{/if}
	{/foreach}
{/if}

<form method="post">
	<fieldset>
		<legend>[[Change Administrator's Username and Password]]</legend>
		<input type="hidden" name="action" value="change_admin_account" />
			<table>
				<tr>
					<td valign="top">[[Current Username]]</td>
					<td valign="top" class="required">&nbsp;</td>
					<td>{$adminInfo.username}</td>
				</tr>
				{foreach from=$form_fields key=item_name item=form_field}
					<tr>
						<td valign="top">[[{$form_field.caption}]]</td>
						<td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
						<td>
							{if $form_field.id == 'password'}
								{input property=$form_field.id template='password_cur.tpl'}
							{else}
								{input property=$form_field.id}
							{/if}
						</td>
					</tr>
				{/foreach}
				<tr>
					<td colspan="3">
						<div class="floatRight"><input type="submit" value="[[Save]]" class="greenButton" /></div>
					</td>
				</tr>
			</table>
	</fieldset>
</form>