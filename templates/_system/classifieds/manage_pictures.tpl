{if $field_errors != ''}
{foreach from=$field_errors item=error key=field_caption}
	<div class="error alert alert-danger">
		{if $error eq 'FILE_NOT_SPECIFIED'}
			{$field_caption}' [[file not specified]]
		{elseif $error eq 'NOT_SUPPORTED_IMAGE_FORMAT'}
			'{$field_caption}' - [[Image format is not supported]]
		{elseif $error eq 'PICTURES_LIMIT_EXCEEDED'}
			'{$field_caption}' [[limit exceeded]]
		{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
			[[File size exceeds system limit]]
		{elseif $error eq 'UPLOAD_ERR_FORM_SIZE'}
			[[File size exceeds system limit]]
		{elseif $error eq 'UPLOAD_ERR_PARTIAL'}
			[[There was an error during file upload]]
		{elseif $error eq 'UPLOAD_ERR_NO_FILE'}
			'{$field_caption}' [[file not specified]]
		{/if}
	</div>
{/foreach}
{/if}
{if $errors != ''}
	{foreach from=$errors item=error_message key=error}
		<div class="error alert alert-danger">
			{if $error eq 'WRONG_PARAMETERS_SPECIFIED'}
				[[Wrong parameters are specified]]
			{elseif $error eq 'PARAMETERS_MISSED'}
				[[The key parameters are not specified]]
			{elseif $error eq 'NOT_OWNER'}
				[[You are not owner of this listing]]
			{/if}
		</div>
	{/foreach}
{else}
	{if $number_of_picture < $number_of_picture_allowed}
		<form class="form-file" id="uploadForm" method="post" action="{$GLOBALS.site_url}/manage-pictures/" enctype="multipart/form-data" onsubmit="return uploadPicture();">
			<input type="hidden" name="action" value="add" />
			<input type="hidden" id="listing_id" name="listing_sid" value="{$listing.id}" />
			<div class="col-xs-12">
				<div class="form-group has-feedback">
					<div class="inputField"><input type="file" name="picture" /></div>
				</div>
				<div class="form-group has-feedback">
					<div class="inputField">
						[[Caption]]<br/>
						<input class="form-control" type="text" name="caption" value="" />
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<input type="submit" value="[[Add Picture]]" class="btn btn-default btn-sm"/>
		</form>
		<div class="clearfix"></div>



	{else}
		<div class="information alert alert-info">[[You've reached the limit of number of pictures allowed by your product]]</div>
	{/if}
	{if $pictures}
		<div class="table-responsive">
			<table id="manage-pictures" class="table table-condensed">
				<thead>
					<tr>
						<th class="tableLeft"></th>
						<th class="text-center">[[Thumbnail]]</th>
						<th>[[Caption]]</th>
						<th class="text-center">[[Actions]]</th>
						<th class="tableRight"></th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$pictures item=picture name=pictures_block}
						<tr>
							<td></td>
							<td class="text-center thumbnail"><img src="{$picture.thumbnail_url}" alt="" border="0" /></td>
							<td class="caption">{$picture.caption|truncate:15|escape:"html"}</td>
							<td class="text-center actions">
								<a href="#" onclick="editPicture({$listing.id}, {$picture.id}, '[[Edit Picture]]'); return false;"><img src="{$GLOBALS.site_url}/templates/_system/main/images/b_edit.gif" border="0" alt="" /></a>
								&nbsp;
								<a href="#" onclick="deletePicture({$listing.id}, {$picture.id}); return false;"><img src="{$GLOBALS.site_url}/templates/_system/main/images/b_drop.gif" /></a>
							</td>
							<td></td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
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
				$("#loading-progbar").css("display", "block");
			},
			success: function(data) {
				$("#UploadPics").css("opacity", "1");
				$("#loading-progbar").css("display", "none");
				if ($.browser.msie) {
					data = data.replace(/(\w+)=([^ ">]+)/g, '$1="$2"');
				}
				$("#UploadPics").html(data);
		}
		};
		$("#uploadForm").ajaxSubmit(options);
		return false;
	}
</script>