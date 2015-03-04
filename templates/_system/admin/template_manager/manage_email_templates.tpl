{breadcrumbs}
	{if $group}
		<a href="{$GLOBALS.site_url}/edit-email-templates/">[[Manage Email Templates]]</a> &#187; [[{$etGroups.$group}]]
	{else}
		[[Manage Email Templates]]
	{/if}
{/breadcrumbs}

<h1><img src="{image}/icons/contactbook32.png" border="0" alt="" class="titleicon" />{if $etGroups.$group}[[{$etGroups.$group}]]{else}[[Email Templates]]{/if}</h1>
{foreach from=$errors item=error}
	{if $error == 'NOT_ALLOWED_IN_DEMO'}
	    <p class="error">[[This action is not allowed in Demo mode]]</p>
	{/if}
{/foreach}
<a href="{$GLOBALS.site_url}/edit-templates/?module_name=miscellaneous&amp;template_name=email_theme.tpl" class="grayButton">[[Edit Email Theme]]</a>
<div class="clr"><br/></div>

<form action="" method="post">
	<fieldset>
		<legend>[[Add a New Email Template]]</legend>
		{if $message}
			<p class="message">[[{$message}]]</p>
		{/if}
		{include file='../classifieds/field_errors.tpl'}
		<div class="et-add-new-block">
			<label>[[{$form_fields.name.caption}]]&nbsp;{if $form_field.is_required}<span class="required">*</span>{/if}</label>
			{input property=$form_fields.name.id}
			<label>[[{$form_fields.group.caption}]]&nbsp;{if $form_field.is_required}<span class="required">*</span>{/if}</label>
			{input property=$form_fields.group.id}
			<input type="submit" name="et_submit" value="[[Add]]" class="grayButton"/>
		</div>
	</fieldset>
</form>

<div class="clr"><br/><br/></div>
{if $group}
	<table>
		<thead>
			<tr>
				<th>[[Template Name]]</th>
				<th class="actions">[[Actions]]</th>
			</tr>
		</thead>
		<tbody>
			{assign var="counter" value=0}
			{foreach from=$templates item="template"}
				{assign var="counter" value=$counter+1}
				<tr class="{if $counter is odd}oddrow{else}evenrow{/if}">
					<td>[[{$template.name}]]</td>
					<td align=center>
						<a href="{$GLOBALS.site_url}/edit-email-templates/{$group}/{$template.sid}/" title="[[Edit]]" class="editbutton">[[Edit]]</a>
						{if $template.user_defined}
							<a href="{$GLOBALS.site_url}/edit-email-templates/{$group}/{$template.sid}/delete/"
							   title="[[Delete Template]]" onclick="javascript:return confirm('[[Are you sure?]]');" class="deletebutton">[[Delete]]</a>
						{/if}
					</td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="3">
						[[There are no modules with templates in the system. If you don't have any, your package either comes without module templates or is damaged.]]
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<table>
		<thead>
			<tr>
				<th>[[Template Group]]</th>
				<th class="actions">[[Actions]]</th>
			</tr>
		</thead>
		<tbody>
			{assign var="counter" value=0}
			{foreach from=$etGroups item="etGroupCaption" key="etGroupIndex"}
				{assign var="counter" value=$counter+1}
				<tr class="{if $counter is odd}oddrow{else}evenrow{/if}">
					<td>[[{$etGroupCaption}]]</td>
					<td align="center"><a href="{$GLOBALS.site_url}/edit-email-templates/{$etGroupIndex}/" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="3">
						[[There are no modules with templates in the system. If you don't have any, your package either comes without module templates or is damaged.]]
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{/if}



