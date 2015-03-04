{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a>  &#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$listing_type_sid}">[[{$listing_type_info.name}]]</a> &#187; [[Add Listing Field]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperplus32.png" border="0" alt="" class="titleicon"/>[[Add Listing Field]]</h1>
{include file="field_errors.tpl" errors=$errors}
<fieldset>
	<legend>[[Add a New Listing Field]]</legend>
	<form method="post" action="">
	<input type="hidden" name="action" value="add" />
	<input type="hidden" name="listing_type_sid" value="{$listing_type_sid}" />
		<table>
			{foreach from=$form_fields key=field_name item=form_field}
				<tr>
					<td>[[{$form_field.caption}]]</td>
					<td class="required">{if $form_field.is_required}*{/if}</td>
					<td>{input property=$form_field.id}</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="3">
                    <div class="floatRight"><input type="submit" value="[[Add]]" class="greenButton"/></div>
                </td>
			</tr>
		</table>
	</form>
</fieldset>