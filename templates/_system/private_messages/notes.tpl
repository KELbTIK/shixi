<script type="text/javascript">
{literal}
	function Submit() {
		var options = {
			target: "#formNote_{/literal}{$contactInfo.sid}{literal}",
			url:  $("#notesForm").attr("action")
		};
		$("#notesForm").ajaxSubmit(options);
		return false;
	}
	function closeForm() {
		$("#formNote").html($("textarea[name='note']").text());
	}
{/literal}
</script>

{if !$errors}
	{if $action == 'save'}
		{if $noteSaved}
			<script type="text/javascript"> window.location.reload();</script>
		{/if}
	{elseif $action == 'edit_note' || $action eq 'add_note'}
	<div style='font-weight: bold;'>[[My notes]]:</div>
	<form id='notesForm' action='{$GLOBALS.site_url}/private-messages/contact/{$contactInfo.sid}/' onsubmit="return Submit()">

			<input type="hidden" name="action" value='save'/>
			<div class="note-group">
			<textarea  class="form-control" name='note'>{$contactInfo.note}</textarea>
			</div>

			<input type="submit" value="[[Save]]" class="btn btn-success btn-sm" />
			<input type="submit" value="[[Close]]" class="btn btn-danger btn-sm" onclick="javascript: location.reload(); return false;"/>

		<br/>
	</form>
	{/if}
{else}
	{foreach from=$errors key=error_code item=error_message}
		<div class="error alert alert-danger">
			{if $error_code == 'UNDEFINED_CONTACT_ID'} [[Contact ID is not defined]]
			{else}[[{$error_message}]]
			{/if}
		</div>
		<br />
	{/foreach}
{/if}