{foreach from=$field_errors item=error key=field_caption}
	{if $error eq 'FILE_NOT_SPECIFIED'}
		<p class="error">'[[{$field_caption}]]' [[file not specified]]</p>
	{elseif $error eq 'NOT_SUPPORTED_IMAGE_FORMAT'}
		<p class="error">'[[{$field_caption}]]' - [[Image format is not supported]]</p>
	{elseif $error eq 'PICTURES_LIMIT_EXCEEDED'}
		<p class="error">'[[{$field_caption}]]' [[limit exceeded]]</p>
	{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
		<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{elseif $error eq 'UPLOAD_ERR_FORM_SIZE'}
		<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{elseif $error eq 'UPLOAD_ERR_PARTIAL'}
		<p class="error">[[There was an error during file upload]]</p>
	{elseif $error eq 'UPLOAD_ERR_NO_FILE'}
		<p class="error">'[[{$field_caption}]]' [[file not specified]]</p>
	{/if}
{/foreach}
{if $errors != ''}
	{foreach from=$errors item=error_message key=error}
		{if $error eq 'WRONG_PARAMETERS_SPECIFIED'}
			<p class="error">[[Wrong parameters are specified]]</p>
		{elseif $error eq 'PARAMETERS_MISSED'}
			<p class="error">[[The key parameters are not specified]]</p>
		{/if}
	{/foreach}
{else}
	{if $number_of_picture < $number_of_picture_allowed}
		<form id="uploadForm" method="post" action="{$GLOBALS.site_url}/manage-pictures/">
			<input type="hidden" name="action" value="add" />
			<input type="hidden" name="listing_sid" value="{$listing.id}" />
			<table>
				<tr>
					<td>
						<input type="file" name="picture" />
						<small>([[max.]] {$uploadMaxFilesize} M)</small>
					</td>
				</tr>
				<tr>
					<td>
						[[Caption]]<br/>
						<input type="text" name="caption" value="" />
					</td>
				</tr>
				<tr>
					<td><input type="button" value="[[Add Picture]]" class="grayButton" onclick="uploadPicture();"/></td>
				</tr>
			</table>
		</form>
	{else}
		<p class="information">[[You've reached the limit of number of pictures allowed by your product]]</p>
	{/if}

	{if $pictures}
		<table id="manage-pictures">
			<thead>
				<tr>
					<th class="tableLeft"></th>
					<th class="text-center thumbnail">[[Thumbnail]]</th>
					<th class="caption">[[Caption]]</th>
					<th class="text-center actions">[[Actions]]</th>
					<th class="tableRight"></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$pictures item=picture name=pictures_block}
					<tr>
						<td></td>
						<td class="text-center"><img src="{$picture.thumbnail_url}" alt="" border="0" /></td>
						<td>{$picture.caption|truncate:15|escape:"html"}</td>
						<td class="text-center">
							<a href="#" onclick="editPicture({$listing.id}, {$picture.id}, '[[Edit Pictures]]', 'admin'); return false;" class="edit"><img src="{$GLOBALS.user_site_url}/templates/_system/main/images/b_edit.gif" border="0" alt="[[Edit]]" /></a>
							&nbsp;
							<a href="#" onclick="deletePicture({$listing.id}, {$picture.id}, 'admin'); return false;" style="cursor:pointer;"><img src="{$GLOBALS.user_site_url}/templates/_system/main/images/b_drop.gif" border="0" alt="[[Delete]]" /></a>
						</td>
						<td></td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/if}
{/if}

<script type="text/javascript">

	var confirmPhrase = '[[Are you sure?]]';

	function uploadPicture() {
		var browser = navigator.appName.toLowerCase();
		var options = {
			target: "#UploadPics",
			url:  $("#uploadForm").attr("action") + "?listing_sid=" + {$listing.id},
			beforeSend: function(data) {
				$("#UploadPics").css("opacity", "0.3");
				$("#loading").show();
			},
			success: function(data) {
				$("#UploadPics").css("opacity", "1");
				if ($.browser.msie) {
					data = data.replace(/(\w+)=([^ ">]+)/g, '$1="$2"');
				}
				$("#UploadPics").html(data);
				$("#loading").hide();
			}
		};
		$("#uploadForm").ajaxSubmit(options);
		return false;
	}

</script>