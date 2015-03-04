{if $field.listing_type_sid}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$type_sid}">[[$type_info.name]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$field_sid}">[[$field.caption]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-tree/?field_sid={$field_sid}">[[Edit Tree]]</a>
		&#187; [[Import Tree Data]]
	{/breadcrumbs}
{else}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/listing-fields/">[[Common Fields]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/?sid={$field_sid}">[[$field.caption]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-tree/?field_sid={$field_sid}">[[Edit Tree]]</a>
		&#187; [[Import Tree Data]]
	{/breadcrumbs}
{/if}

<h1><img src="{image}/icons/boxdownload32.png" border="0" alt="" class="titleicon" />[[Import Tree Data]]</h1>

{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{elseif $error eq 'NOT_INT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an integer value]]</p>
	{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
		<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{elseif $error eq 'DO_NOT_MATCH_SELECTED_FILE_FORMAT'}
		<p class="error">[[The file type do not match with selected file format]]</p>
	{/if}
{/foreach}

<fieldset>
	<legend>[[Import Data]]</legend>
	<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="field_sid" value="{$field.sid}">
		<table>
			<tr>
				<td>[[File]]</td>
				<td><input type="file" name="imported_tree_file"> <span class="required">*</span><small>([[max.]] {$uploadMaxFilesize} M)</small></td>
			</tr>
			<tr>
				<td>[[File format]]</td>
				<td>
					<select name="file_format">
					<option value="csv">CSV</option><option value="excel" {if $imported_file_config.file_format == excel}selected{/if}>Excel</option></select>
					<span class="required">*</span>
				</td>
			</tr>
			<tr>
				<td>[[Start Line]]</td>
				<td><input type="text" name="start_line" value="{$imported_file_config.start_line}"> <span class="required">*</span></td>
			</tr>
			<tr>
				<td>[[Encoding]]<br /><small>([[for CSV-file only]])</small></td>
				<td>
					<select name="encodingFromCharset" >
						<option value="UTF-8">[[Default]]</option>
						{foreach from=$charSets item=charSet}
							<option value="{$charSet}">{$charSet}</option>
						{/foreach}
					</select>
					<div class="commentSmall">[[Select appropriate encoding for your language  in case you have problems with import of certain symbols]]</div>
				</td>
			</tr>
			<tr>
                <td colspan="2">
                    <div class="floatRight"><input type="submit" value="[[Import]]" class="greenButton"></div>
                </td>
            </tr>
		</table>
	</form>
</fieldset>