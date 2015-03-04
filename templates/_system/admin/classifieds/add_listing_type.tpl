{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a> &#187; [[Add Listing Type]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperplus32.png" border="0" class="titleicon" />[[Add Listing Type]]</h1>
{include file="field_errors.tpl"}
<fieldset>
	<legend>[[Add a New Listing Type]]</legend>
		<form method="post" action="">
		<input type="hidden" name="action" value="add" />
			<table>
				{foreach from=$form_fields key=field_name item=form_field}
					<tr>
						<td valign="top">[[{$form_field.caption}]]</td>
						<td valign="top" class="required">{if $form_field.is_required}*{/if}</td>
						<td>{input property=$form_field.id}</td>
					</tr>
				{/foreach}
				<tr>
					<td colspan="3"><div class="floatRight"><input type="submit" value="[[Add]]" class="greenButton" /></div></td>
				</tr>
			</table>
		</form>
</fieldset>