{breadcrumbs}<a href="{$GLOBALS.site_url}/states/?country_sid={$country_sid}">[[States/Regions]]</a> &#187; [[Edit State/Region]]{/breadcrumbs}
<h1><img src="{image}/icons/usersplus32.png" border="0" alt="" class="titleicon" />[[Edit State/Region]]</h1>

{include file="state_errors.tpl"}

<fieldset>
	<legend>[[Edit State/Region]]</legend>
	<form method="post">
	<input type="hidden" name="action_add" value="add" />
	<input type="hidden" name="state_id" value="{$state_id}" />
		<table>
		{foreach from=$form_fields item=form_field}
			<tr>
				<td valign=top>[[{$form_field.caption}]]</td>
				<td valign=top class="required">{if $form_field.is_required}*{/if}</td>
				<td> {input property=$form_field.id}</td>
			</tr>
			{/foreach}
			
			<tr>
				<td colspan="3">
                    <div class="floatRight"><input type="submit" value="[[Save]]" class="grayButton" /></div>
                </td>
			</tr>
		</table>
	</form>
</fieldset>
<br />