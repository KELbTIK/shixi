{foreach from=$errors item="error" key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[this value is already used in the system]]</p>
	{elseif $error eq 'NOT_CONFIRMED'}
		<p class="error">'[[{$field_caption}]]' [[not confirmed]]</p>
	{elseif $error eq 'DATA_LENGTH_IS_EXCEEDED'}
		<p class="error">'[[{$field_caption}]]' [[length is exceeded]]</p>
	{elseif $error eq 'NOT_INT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an integer value]]</p>
	{elseif $error eq 'OUT_OF_RANGE'}
		<p class="error">'[[{$field_caption}]]' [[value is out of range]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an float value]]</p>
	{elseif $error eq 'FILE_NOT_UPLOADED'}
		<p class="error">'[[{$field_caption}]]' [[is not uploaded]]</p>
	{elseif $error eq 'WRONG_FORMAT'}
		<p class="error">'[[Please choose Excel or csv file]]</p>
	{elseif $error eq 'DO_NOT_MATCH_SELECTED_FILE_FORMAT'}
		<p class="error">[[The file type do not match with selected file format]]</p>
	{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
		<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{elseif $error eq 'UPLOAD_ERR_NO_FILE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{elseif $error eq 'CHARSET_INCORRECT'}
		<p class="error">[[The file encoding is incorrect. Please select appropriate encoding and try again.]]</p>
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{foreachelse}
		<table width="100%">
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<th>[[Name]]</th>
				<th>[[Longitude]]</th>
				<th>[[Latitude]]</th>
				<th>[[City]]</th>
				<th>[[State]]</th>
				<th>[[State Code]]</th>
				<th>[[Country]]</th>
			</tr>
			{foreach from=$importedGeographicData item="geograhicData"}
				<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
					<td>{$geograhicData.name}</td>
					<td>{$geograhicData.longitude}</td>
					<td>{$geograhicData.latitude}</td>
					<td>[[{$geograhicData.city}]]</td>
					<td>[[{$geograhicData.state}]]</td>
					<td>{$geograhicData.stateCode}</td>
					<td>[[{$geograhicData.country}]]</td>
				</tr>
			{/foreach}
		</table>
{/foreach}
