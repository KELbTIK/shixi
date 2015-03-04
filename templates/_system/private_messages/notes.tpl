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
		<textarea style='width:100%;' cols=3 name='note'>{$contactInfo.note}</textarea><br/>
		<input type="submit" value="[[Save]]" class="button" />
		<input type="submit" value="[[Close]]" class="button" onclick="javascript: location.reload(); return false;"/>
	</form>
	{/if}
{else}
	{foreach from=$errors key=error_code item=error_message}
		<p class="error"
			{if $error_code == 'UNDEFINED_CONTACT_ID'} [[Contact ID is not defined]]
			{else}[[{$error_message}]]
			{/if}
		</p>
		<br />
	{/foreach}
{/if}