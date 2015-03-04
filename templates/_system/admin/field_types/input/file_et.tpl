{if $value.file_name ne null}
	{if $value.saved_file_name}
		<a href="?filename={$value.saved_file_name|escape:'url'}">{$value.file_name|escape:'html'}</a>
	{else}
		<a href="{$value.file_url|escape:'url'}">{$value.file_name|escape:'html'}</a>
	{/if}
	<a href="{$GLOBALS.site_url}/edit-email-templates/delete-uploaded-file/{$tplInfo.sid}/?field_id={$id|escape:'url'}"
	   onclick="javascript:return confirm('[[Are you sure?]]');">[[Delete]]</a>
	<br/><br/>
{/if}
<input type="file" name="{$id}" /> <small>([[max.]] {$uploadMaxFilesize} M)</small>