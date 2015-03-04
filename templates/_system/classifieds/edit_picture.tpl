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
	<fieldset>
		<div class="inputName">[[Picture]]</div>
		<div class="inputField"><img src="{$picture.thumbnail_url}" alt="" /></div>
	</fieldset>
	<fieldset>
		<div class="inputName">[[Caption]]</div>
		<div class="inputField"><input type="text" name="picture_caption" value="{$picture.caption}" /></div>
	</fieldset>
	<fieldset>
		<div class="inputName">&nbsp;</div>
		<div class="inputField"><input type="submit" value="[[Save]]" class="button" /></div>
	</fieldset>
</form>