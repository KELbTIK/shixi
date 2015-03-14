<script type="text/javascript">
	function applySubmit() {
		var options = {
				target: "#messageBox",
				url:  $("#editForm").attr("action"),
				success: function(data) {
					$("#UploadPics").load(url);
				}
			};
		$("#editForm").ajaxSubmit(options);
		$("#messageBox").dialog('destroy');
		return false;
	}
</script>

<form action="{$GLOBALS.site_url}/classifieds/edit-picture/" id="editForm" method="post" onsubmit="return applySubmit();">
<input type="hidden" name="picture_id" value="{$picture_id}" />
<input type="hidden" name="listing_id" value="{$listing_id}" />
	<div class="form-group has-feedback">
		<div class="inputName">[[Picture]]</div>
		<div class="inputField"><img src="{$picture.thumbnail_url}" alt="" /></div>
	</div >
	<div class="form-group has-feedback">
		<div class="inputName">[[Caption]]</div>
		<div class="inputField"><input class="form-control" type="text" name="picture_caption" value="{$picture.caption}" /></div>
	</div>
	<div class="form-group has-feedback">
		<div class="inputName">&nbsp;</div>
		<div class="inputField"><input type="submit" value="[[Save]]" class="btn btn-success" /></div>
	</div>
</form>