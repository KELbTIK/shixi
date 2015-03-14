{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}
	{foreach from=$errors key=key item=error}
		{if $key == 'NOT_SUPPORTED_VIDEO_FORMAT'}
			<div class="error alert alert-danger">[[Not supported video format]]</div>
		{elseif $key == 'NOT_ACCEPTABLE_FILE_FORMAT'}
			<div class="error alert alert-danger">[[Not acceptable file type]]</div>
		{elseif $key == 'UPLOAD_ERR_INI_SIZE'}
			<div class="error alert alert-danger">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</div>
		{else}
			<div class="error alert alert-danger">{$key}</div>
		{/if}
	{/foreach}

	<div id="file_{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}">
		{if $complexField && $filesInfo[$complexStep].file_name ne null}
			<div class="complex-view-file-caption">
				{$filesInfo.$complexStep.file_name} ({$filesize|string_format:"%.2f"} {$size_token})
				| <a class="delete_file_complex"
					 form_token="{$form_token}"
					 listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
					 field_id="{$complexField}:{$id}:{$complexStep}"
					 file_id="{$filesInfo.$complexStep.file_id}"
					 href="{$GLOBALS.site_url}/classifieds/delete-complex-file/?listing_id={$listing.id}&amp;field_id={$complexField}:{$id}:{$complexStep}&amp;file_id={$filesInfo.$complexStep.file_id}&amp;form_token={$form_token}">[[Remove]]</a>
				{* Нужно написать чистку файлов при их удалении из комплексного поля *}
				<br/><br/>
			</div>
			<input type="hidden"
				   id="hidden_{$complexField}[{$id}][{$complexStep}]"
				   name="{$complexField}[{$id}][{$complexStep}]"
				   value="{$filesInfo.$complexStep.file_id}"
				   class="complexField"/>
		{/if}
		{if !$complexField && $value.file_name ne null}
			{$value.file_name|escape:'html'} ({$filesize|string_format:"%.2f"} {$size_token})
			| <a class="delete_file"
				 form_token="{$form_token}"
				 listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
				 field_id="{$id}"
				 file_id="{$value.file_id}"
				 href="{$GLOBALS.site_url}/classifieds/delete-uploaded-file/?listing_id={$listing.id}&amp;field_id={$id}&amp;form_token={$form_token}">[[Remove]]</a>
			<br/><br/>
		{/if}
	</div>

	<input type="file"
		   field_id="{if $complexField}{$complexField}:{$id}:{$complexStep}{else}{$id}{/if}"
		   field_action="upload_file{if $complexField}_complex{/if}"
		   field_target="file_field_content_{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
		   name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
		   class="autouploadField {if $complexField}complexField{/if}"
           id="input_file_{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
           {if ($complexField && $filesInfo.$complexStep.file_name ne null) || (!$complexField && $value.file_name ne null)}style="display:none;"{/if} />


{else}


	<div id="file_field_content_{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}">

		<div class="errors"></div>

		<div id="file_{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}">

			{if $complexField && $filesInfo.$complexStep.file_name ne null}
				<div class="complex-view-file-caption">
					{if $filesInfo.$complexStep.saved_file_name}
						<a href="?listing_id={$listing.id}&amp;filename={$filesInfo.$complexStep.saved_file_name|escape:'url'}&amp;complex_field={$complexField}:{$id}:{$complexStep}_{$listing.id}">{$filesInfo.$complexStep.file_name|escape:'html'}</a>
					{else}
						<a href="{$filesInfo.$complexStep.file_url|escape:'url'}">{$filesInfo.$complexStep.file_name|escape:'html'}</a>
					{/if}
					| <a class="delete_file_complex"
						 form_token="{$form_token}"
						 listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
						 field_id="{$complexField}:{$id}:{$complexStep}"
						 file_id="{$filesInfo.$complexStep.file_id}"
						 href="{$GLOBALS.site_url}/classifieds/delete-complex-file/?listing_id={$listing.id}&amp;field_id={$complexField}:{$id}:{$complexStep}&amp;file_id={$filesInfo.$complexStep.file_id}&amp;form_token={$form_token}">[[Remove]]</a>
					{* Нужно написать чистку файлов при их удалении из комплексного поля *}
					<br/><br/>
				</div>
				<input type="hidden"
					   id="hidden_{$complexField}[{$id}][{$complexStep}]"
					   name="{$complexField}[{$id}][{$complexStep}]"
					   value="{$filesInfo.$complexStep.file_id}"
					   class="complexField"/>
			{/if}
			{if !$complexField && $value.file_name ne null}
				{if $value.saved_file_name}
					<a href="?listing_id={$listing.id}&amp;filename={$value.saved_file_name|escape:'url'}&amp;field_id={$id}">{$value.file_name|escape:'html'}</a>
				{else}
					<a href="{$value.file_url}">{$value.file_name|escape:'html'}</a>
				{/if}
				| <a class="delete_file"
					 form_token="{$form_token}"
					 listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
					 field_id="{$id}"
					 file_id="{$value.file_id}"
					 href="{$GLOBALS.site_url}/classifieds/delete-uploaded-file/?listing_id={$listing.id}&amp;field_id={$id}&amp;form_token={$form_token}">[[Remove]]</a>
				<br/><br/>
			{/if}

		</div>

		<input type="file"
			   field_id="{if $complexField}{$complexField}:{$id}:{$complexStep}{else}{$id}{/if}"
			   field_action="upload_file{if $complexField}_complex{/if}"
			   field_target="file_field_content_{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
			   name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
			   class="autouploadField {if $complexField}complexField{/if}"
               id="input_file_{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
			   {if ($complexField && $filesInfo.$complexStep.file_name ne null) || (!$complexField && $value.file_name ne null)}style="display:none;"{/if} />

        {if ($complexField && $filesInfo.$complexStep.file_name eq null)}
           <input type="hidden" id="hidden_{$complexField}[{$id}][{$complexStep}]" name="{$complexField}[{$id}][{$complexStep}]" value="" class="complexField"/>
        {/if}

	</div>


	<script type="text/javascript">
		{literal}

		// check temporary uploaded data of field
		$(function() {
			{/literal}
			{if $complexField}
				getComplexFileFieldData('{$complexField}[{$id}][{$complexStep}]', '{if $listing_id}{$listing_id}{else}{$listing.id}{/if}', '{if $listing.type.id}{$listing.type.id}{/if}', '{$form_token}');
			{else}
				getFileFieldData('{$id}', '{if $listing_id}{$listing_id}{else}{$listing.id}{/if}', '{if $listing.type.id}{$listing.type.id}{/if}', '{$form_token}');
			{/if}
			{literal}
		});

		{/literal}
	</script>

{/if}

