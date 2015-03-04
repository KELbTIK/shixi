{breadcrumbs}<a href="{$GLOBALS.site_url}/settings/">[[System Settings]]</a> &#187; <a href="{$GLOBALS.site_url}/alphabet-letters/">[[Alphabet Letters for "Search by Company" section]]</a> &#187;
	{if $action == "edit"}
		[[Edit Alphabet]]
	{elseif $action == "new"}
		[[Add New Alphabet]]
	{else}
		[[Alphabet Letters]]
	{/if}
{/breadcrumbs}

{if $action == "edit"}
	<h1>[[Edit Alphabet]]</h1>
{elseif $action == "new"}
	<h1>[[Add New Alphabet]]</h1>
{else}
	<h1>[[Alphabet Letters]]</h1>
{/if}


<script type="text/javascript">

	function FormSubmit() {
		var test = document.forms['ABInputForm'].elements['value'].value;
		var newValue = '';
		
		var allSpacesRe = /\s+/g;
		
		test = test.replace(allSpacesRe, "");
	
		for(var i=0; i< test.length; i++) {
			newValue += test[i]+' ';
		}
		document.forms['ABInputForm'].elements['value'].value = newValue;
		return true;
	}

</script>
{include file="field_errors.tpl"}

<fieldset>
	<legend>&nbsp;[[Alphabet Info]]</legend>
	<table>
		<form method="post" name='ABInputForm' onsubmit = "FormSubmit()">
			<input type="hidden" name="sid" value="{$alphabet_sid}" />
			<input type="hidden" name="action" value="save" />
			{foreach from=$form_fields key=field_name item=form_field}
				<tr>
					<td>[[{$form_field.caption}]] </td>
					<td class="required">{if $form_field.is_required}*{/if}</td>
					<td>{input property=$form_field.id}</td>
				</tr>
			{/foreach}
			<tr><td colspan="3"><div class="floatRight"><span class="greenButtonEnd"><input type="submit" value="[[Save]]" class="greenButton" /></span></div></td></tr>
		</form>
	</table>
</fieldset>