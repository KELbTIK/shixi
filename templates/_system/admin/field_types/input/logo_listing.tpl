{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}
	{foreach from=$errors key=key item=error}
		{if $key === 'UPLOAD_ERR_INI_SIZE'}
			<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
		{else}
			<p class="error">[[{$key}]]</p>
		{/if}
	{/foreach}

    <input type="file"
       id="autoloadFileSelect_{$id}"
       field_id="{$id}"
       field_action="upload_listing_logo"
       field_target="logo_field_content_{$id}"
       name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
       class="autouploadField {if $complexField}complexField{/if}"
       {if $value.file_name ne null}style="display:none;"{/if} />
	{if $value.file_name ne null}
		<div id="listing_logo_{$id}" style="float:left;">
			<img src="{$value.file_url}" alt="" border="0" />
			<br/><br/>
			<a class="delete_listing_logo"
			   field_id="{$id}"
			   file_id="{$value.file_id}"
			   listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
			   href="{$GLOBALS.user_site_url}/classifieds/delete-uploaded-file/?field_id={$id}">[[Remove]]</a>
			<br/><br/>
		</div>
		<span id="extra_field_info_{$id}" style="display:none;"><small>([[max.]] {$uploadMaxFilesize} M)</small>
	{else}
		<small>([[max.]] {$uploadMaxFilesize} M)</small>
	{/if}
{else}
    <div id="logo_field_content_{$id}">
    	{if $value.file_name ne null}
            <div id="listing_logo_{$id}">
                <img src="{$value.file_url}" alt="" border="0" />
                <br/><br/>
                <a class="delete_listing_logo"
                   field_id="{$id}"
                   file_id="{$value.file_id}"
                   listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
                   href="{$GLOBALS.user_site_url}/classifieds/delete-uploaded-file/?field_id={$id}">[[Remove]]</a>
                <br/><br/>
            </div>
    	{/if}
    	<input type="file"
            id="autoloadFileSelect_{$id}"
            field_id="{$id}"
            field_action="upload_listing_logo"
            field_target="logo_field_content_{$id}"
            name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
            class="autouploadField {if $complexField}complexField{/if}"
            {if $value.file_name ne null}style="display:none;"{/if} />
		<small>([[max.]] {$uploadMaxFilesize} M)</small>
    </div>
    <script>
        getClassifiedsLogoData('{$id}', '{$form_token}', '{if $listing_id}{$listing_id}{else}{$listing.id}{/if}');
    </script>
{/if}