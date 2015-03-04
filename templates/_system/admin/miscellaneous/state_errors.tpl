{if $action == 'edit'}
{breadcrumbs}<a href="{$GLOBALS.site_url}/states/">[[States]]</a> &#187; [[Edit State]]{/breadcrumbs}
<h1><img src="{image}/icons/usersplus32.png" border="0" alt="" class="titleicon" />[[Edit State]]</h1>
{/if}
{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{elseif $error eq 'NOT_VALID_ID_VALUE'}
			<p class="error">'[[{$field_caption}]]' [[is not valid]]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[you've specified is already used in the system]]</p>
	{elseif $error eq 'NOT_STRING_ID_VALUE'}
		<p class="error">[[Use at least one A-Z letter value in the '$field_caption' field.]]</p>
	{elseif $error eq 'WRONG_STATE_ID_SPECIFIED'}
		<p class="error">[[There is no state in the system with the specified ID]]</p>	
	{elseif $error eq 'UPLOAD_ERR_NO_FILE'}
		<p class="error">[[Please choose Excel or csv file]]</p>
	{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
		<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{foreachelse}
	{if $action eq 'save_order'}
		<p class="message">[[Order is successfully saved]]</p>
	{/if}
{/foreach}