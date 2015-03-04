{breadcrumbs}<a href="{$GLOBALS.site_url}/promotions/">[[Promotions]]</a> &#187; [[Edit Promotion Code]]{/breadcrumbs}
<h1><img src="{image}/icons/paperstar32.png" border="0" alt="" class="titleicon"/>[[Edit Promotion Code]]</h1>
{include file='../classifieds/errors.tpl'}

<fieldset >
	<legend>[[Edit Promotion Code]]</legend>
	<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="event" value="save_code">
	<input type="hidden" name="sid" value="{$sid}">
	<table>
		{foreach from=$form_fields item=form_field}
		
		{if $form_field.id == 'discount'}
			<tr>
				<td valign="top" width="20%">[[$form_field.caption]]</td>
				<td valign="top" class="required">{if $form_field.is_required}*{/if}</td>
				<td>{input property=$form_field.id} {input property=type}</td>
			</tr>
		{* not show 'type' field in other place *}
		{elseif $form_field.id != 'type'}
			<tr>
				<td valign="top" width="20%">[[$form_field.caption]]</td>
				<td valign="top" class="required">{if $form_field.is_required}*{/if}</td>
				<td>{input property=$form_field.id}{if $form_field.comment}<div><small>[[{$form_field.comment}]]</small></div>{/if}</td>
			</tr>
		{/if}
		{/foreach}
		<tr>
			<td colspan="3">
                <div class="floatRight"><input type="submit" class="greenButton" value="[[Save]]" /></div>
			</td>
		</tr>
	</table>
	</form>
</fieldset>