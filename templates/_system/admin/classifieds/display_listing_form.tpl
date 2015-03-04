<table cellpadding="5">
	{foreach from=$form_fields item=form_field}
		<tr>
			<td class="strong">[[{$form_field.caption}]]</td>
			<td>{display property=$form_field.id}</td>
		</tr>
	{/foreach}
</table>