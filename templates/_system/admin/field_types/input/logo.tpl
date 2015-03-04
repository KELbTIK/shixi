{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}
	{foreach from=$errors key=key item=error}
		{if $key === 'NOT_SUPPORTED_VIDEO_FORMAT' || $key === 'NOT_ACCEPTABLE_FILE_FORMAT'}
			<p class="error">[[Not supported video format]]</p>
		{elseif $key === 'UPLOAD_ERR_INI_SIZE'}
			<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
		{else}
			<p class="error">[[{$key}]]</p>
		{/if}
	{/foreach}

	<input type="file"
		id="autoloadFileSelect_{$id}"
		field_id="{$id}"
		field_action="upload_profile_logo"
		field_target="logo_field_content_{$id}"
		name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
		class="autouploadField {if $complexField}complexField{/if}"
		{if $value.file_name ne null}style="display:none;"{/if} />

	{if $value.file_name ne null}
		<div id="profile_logo_{$id}" style="float:left;">
			<img src="{$value.file_url|escape:'html'}" alt="" border="0" />
			<br/><br/>
			<a class="delete_profile_logo"
			   field_id="{$id}"
			   file_id="{$value.file_id}"
			   user_sid="{$user_info.sid}"
			   href="{$GLOBALS.user_site_url}/users/delete-uploaded-file/?field_id={$id}">[[Remove]]</a>
			<br/><br/>
		</div>
		<span id="extra_field_info_{$id}" style="display:none;"><small>([[max.]] {$uploadMaxFilesize} M)</small>
	{else}
		<small>([[max.]] {$uploadMaxFilesize} M)</small>
	{/if}
{else}
	<div id="logo_field_content_{$id}">
		<input type="file"
			   id="autoloadFileSelect_{$id}"
			   field_id="{$id}"
			   field_action="upload_profile_logo"
			   field_target="logo_field_content_{$id}"
			   name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
			   class="autouploadField {if $complexField}complexField{/if}"
			   {if $value.file_name ne null}style="display:none;"{/if} />

		{if $value.file_name ne null}
			<div id="profile_logo_{$id}" style="float:left;">
				<img src="{$value.file_url|escape:'html'}" alt="" border="0" />
				<br/><br/>
				<a class="delete_profile_logo"
				   field_id="{$id}"
				   file_id="{$value.file_id}"
				   user_sid="{$user_info.sid}"
				   href="{$GLOBALS.user_site_url}/users/delete-uploaded-file/?field_id={$id}">[[Remove]]</a>
				<br/><br/>
			</div>
			<span id="extra_field_info_{$id}" style="display:none;"><small>([[max.]] {$uploadMaxFilesize} M)</small></span>
		{else}
			<small>([[max.]] {$uploadMaxFilesize} M)</small>
		{/if}
		
	</div>
{/if}