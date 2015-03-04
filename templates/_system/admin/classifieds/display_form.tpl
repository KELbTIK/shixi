<table>
	{foreach from=$form_fields item=form_field}
		<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
			<td>[[{$form_field.caption}]]</td>
			<td> {display property=$form_field.id}</td>
		</tr>
	{/foreach}
</table>