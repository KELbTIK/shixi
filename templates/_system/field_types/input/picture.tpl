{if $value.file_name ne null}
	<a href="{$GLOBALS.site_url}/users/delete-uploaded-file/?field_id={$id}">[[Delete]]</a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<img src="{$value.file_url|escape:'html'}" alt="" border="0" />
{/if}
<input type="file" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}" class="{if $complexField}complexField{/if}" />