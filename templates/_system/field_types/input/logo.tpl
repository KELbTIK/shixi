{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}

	{foreach from=$errors key=key item=error}
		{if $key === 'NOT_SUPPORTED_VIDEO_FORMAT' || $key === 'NOT_ACCEPTABLE_FILE_FORMAT'}
			<div class="error alert alert-danger">[[Not supported video format]]</div>
		{elseif $key === 'FILE_SIZE_EXCEEDS_SYSTEM_LIMIT'}
			<div class="error alert-danger">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</div>
		{else}
			<div class="error alert-danger">[[{$key}]]</div>
		{/if}
	{/foreach}


	{if $value.file_name ne null}
	<div id="profile_logo_{$id}">
		{$value.file_name} ({$filesize|string_format:"%.2f"} {$size_token}) |
		<a class="delete_profile_logo btn btn-warning"
		   form_token="{$form_token}"
		   field_id="{$id}"
		   file_id="{$value.file_id}"
		   href="{$GLOBALS.site_url}/users/delete-uploaded-file/?field_id={$id}&form_token={$form_token}">[[Remove]]</a>
		<br />
	</div>
	{/if}
	<input type="file"
		   id="autoloadFileSelect_{$id}"
		   field_id="{$id}"
		   field_action="upload_profile_logo"
		   field_target="logo_field_content_{$id}"
		   name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
		   class="autouploadField {if $complexField}complexField{/if}"
			{if $value.file_name ne null}style="display:none;"{/if} />

{else}

<div id="logo_field_content_{$id}">

	{if $value.file_name ne null}
	<div id="profile_logo_{$id}">
		<img class="img-responsive" src="{$value.file_url|escape:'html'}" alt="" border="0" />
		<a class="delete_profile_logo btn btn-warning"
		   form_token="{$form_token}"
		   field_id="{$id}"
		   file_id="{$value.file_id}"
		   href="{$GLOBALS.site_url}/users/delete-uploaded-file/?field_id={$id}&form_token={$form_token}">[[Remove]]</a>
		<br />
	</div>
	{/if}
	<input type="file"
		   id="autoloadFileSelect_{$id}"
		   field_id="{$id}"
		   field_action="upload_profile_logo"
		   field_target="logo_field_content_{$id}"
		   name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
		   class="autouploadField {if $complexField}complexField{/if}"
		   {if $value.file_name ne null}style="display:none;"{/if} />

</div>

{/if}