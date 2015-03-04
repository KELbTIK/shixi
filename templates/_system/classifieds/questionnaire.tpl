{foreach from=$form_fields item=form_field}
<tr>
	<td>[[$form_field.caption]]</td>
	<td>
		<table>
			<tr>
				<td width="10%"><div class="inputReq">{if $form_field.is_required}*{/if}</div></td>
				<td>
					{if $form_field.type == 'list'}
						{input property=$form_field.id template='radiobuttons.tpl' object=$questionsObject}
					{elseif $form_field.type == 'multilist'}
						{input property=$form_field.id template='checkboxes.tpl' object=$questionsObject}
					{else}
						{input property=$form_field.id}
					{/if}
				</td>
			</tr>
		</table>
	</td>
</tr>
{/foreach}