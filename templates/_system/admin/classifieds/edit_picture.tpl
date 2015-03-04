<script type="text/javascript">
	function applySubmit() {
		var options = {
			target: "#messageBox",
			url: $("#editForm").attr("action"),
			success: function(data) {
				$("#UploadPics").load(url);
			}
		};
		$("#editForm").ajaxSubmit(options);
		$("#messageBox").dialog('destroy');
		return false;
	}
</script>


	<form method="post" action="{$GLOBALS.site_url}/edit-picture/" id="editForm" onsubmit="return applySubmit();">
		<input type="hidden" name="picture_id" value="{$picture_id}"/>
		<input type="hidden" name="listing_id" value="{$listing_id}"/>
		<table>
			<tr>
				<td>[[Picture]]</td>
				<td><img src="{$picture.thumbnail_url}" alt=""/></td>
			</tr>
			<tr>
				<td>[[Caption]]</td>
				<td><input type="text" name="picture_caption" value="{$picture.caption}"/></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="floatRight"><input type="submit" value="[[Save]]" class="greenButton" /></div>
				</td>
			</tr>
		</table>
	</form>
