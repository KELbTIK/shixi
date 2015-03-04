{foreach from=$errors item="error_message" key="error"}
	{if $error eq "INVALID_REQUEST"}
		<p class="error">[[{$error_message}]]</p>
	{elseif $error eq "INVALID_DATA"}
		<p class="error">[[{$error_message}]]</p>
	{elseif $error eq "PARAMETERS_MISSED"}
		<p class="error">[[The key parameters are not specified]]</p>
	{elseif $error eq "MYSQL_ERROR"}
		{$error_message}
	{elseif $error eq "NOT_LOGGED_IN"}
		<p class="error">[[No logged in user found]]</p>
	{elseif $error eq "NOT_OWNER"}
		<p class="error">[[You're not owner of this listing]]</p>
	{elseif $error eq "EMPTY_EXPORT_DATA"}
		<p class="error">[[There is no data to export. Change your search criteria.]]</p>
	{elseif $error eq "EMPTY_EXPORT_PROPERTIES"}
		<p class="error">[[There are no selected properties. Select at least one property to export.]]</p>
    {elseif $error eq "CANT_CREATE_EXPORT_FILES"}
		<p class="error">[[Cannot create export files]]</p>
	{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
		<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{elseif $error eq 'UPLOAD_ERR_NO_FILE'}
		<p class="error">[[Please choose Excel or csv file]]</p>
	{elseif $error eq 'DO_NOT_MATCH_SELECTED_FILE_FORMAT'}
		<p class="error">[[The file type do not match with selected file format]]</p>
	{else}
		<p class="error">[[{$error}]] [[{$error_message}]]</p>
	{/if}	{/foreach}