{if $field_errors != ''}
{foreach from=$field_errors item=error key=field_caption}

		{if $error eq 'FILE_NOT_SPECIFIED'}
			<div class="error alert alert-danger">'{$field_caption}' [[file not specified]]</div>
		{elseif $error eq 'NOT_SUPPORTED_IMAGE_FORMAT'}
			<div class="error alert alert-danger">'{$field_caption}' - [[Image format is not supported]]</div>
		{elseif $error eq 'PICTURES_LIMIT_EXCEEDED'}
			<div class="error alert alert-danger">'{$field_caption}' [[limit exceeded]]</div>
		{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
			<div class="error alert alert-danger">[[File size exceeds system limit]]</div>
		{elseif $error eq 'UPLOAD_ERR_FORM_SIZE'}
			<div class="error alert alert-danger">[[File size exceeds system limit]]</div>
		{elseif $error eq 'UPLOAD_ERR_PARTIAL'}
			<div class="error alert alert-danger">[[There was an error during file upload]]</div>
		{elseif $error eq 'UPLOAD_ERR_NO_FILE'}
			<div class="error alert alert-danger">'{$field_caption}' [[file not specified]]</div>
		{/if}

{/foreach}
{/if}
{if $errors != ''}
	{foreach from=$errors item=error_message key=error}

			{if $error eq 'WRONG_PARAMETERS_SPECIFIED'}
				<div class="error alert alert-danger">[[Wrong parameters are specified]]</div>
			{elseif $error eq 'PARAMETERS_MISSED'}
				<div class="error alert alert-danger">[[The key parameters are not specified]]</div>
			{elseif $error eq 'NOT_OWNER'}
				<div class="error alert alert-danger">[[You are not owner of this listing]]</div>
			{/if}

	{/foreach}
{else}
	{if $number_of_picture < $number_of_picture_allowed}
		<form class="form-file" id="uploadForm" method="post" action="{$GLOBALS.site_url}/manage-pictures/" enctype="multipart/form-data" onsubmit="return uploadPicture();">
			<input type="hidden" name="action" value="add" />
			<input type="hidden" id="listing_id" name="listing_sid" value="{$listing.id}" />

				<div class="form-group has-feedback form-group-padding">
					<div class="inputField col-xs-12"><input type="file" name="picture" /></div>
				</div>
				<div class="form-group has-feedback form-group-padding">
					<div class="inputField col-xs-12">
						[[Caption]]<br/>
						<input class="form-control" type="text" name="caption" value="" />
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
						<th class="text-center">[[Caption]]</th>
						<th class="text-center">[[Actions]]</th>
						<th class="tableRight"></th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$pictures item=picture name=pictures_block}
						<tr>
							<td></td>
							<td class="text-center thumbnail table-add-picture"><img src="{$picture.thumbnail_url}" alt="" border="0" /></td>
							<td class="caption table-add-picture">{$picture.caption|truncate:15|escape:"html"}</td>
							<td class="text-center actions table-add-picture-actions">
								<a class="pull-left" href="#" onclick="editPicture({$listing.id}, {$picture.id}, '[[Edit Picture]]'); return false;"><img src="{$GLOBALS.site_url}/templates/_system/main/images/b_edit.gif" border="0" alt="" /></a>
								&nbsp;
								<a class="pull-right" href="#" onclick="deletePicture({$listing.id}, {$picture.id}); return false;"><img src="{$GLOBALS.site_url}/templates/_system/main/images/b_drop.gif" /></a>
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