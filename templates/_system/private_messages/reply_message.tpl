<form method="post" action="" id="pm_send_form" onsubmit="disableSubmitButton('submitSend');">
	<input type="hidden" name="reply_id" value="{$reply_id}" />
	<div id="pmDetails">
	{include file='field_errors.tpl'}
		<fieldset class="reply">
			<div class="inputName"><span class="strong">[[Message to]]:</span></div>
			<div class="inputField">
				{if $message.anonym && $message.anonym == $message.from_id}
					[[Anonymous User]]
					<input type="hidden" name="form_to" id="form_to" value="{$message.to_name}" />
				{else}
					<input type="text" name="form_to" id="form_to" value="{$message.to_name}" />
				{/if}
				<input type="hidden" name="anonym" value="{$message.anonym}" />
			</div>
		</fieldset>
		<fieldset class="reply">
			<div class="inputName"><span class="strong">[[Subject]]:</span></div>
			<div class="inputField"><input type="text" name="form_subject" id="form_subject" value="{$message.subject}" /></div>
		</fieldset>
		<span class="strong">[[Message]]:</span><br /><br />
		{WYSIWYGEditor name="form_message" class="inputText" height="300px" value=$message.message conf="Basic"}
		<br/>
		<input type="checkbox" name="form_save" value="1" {if $save }checked="checked"{/if} /> [[Save to outbox]]
		<br/><br/>
		<input type="submit" value="[[Send]]" id="submitSend" />
	</div>
</form>
<div class="clr"><br/></div>
<script type="text/javascript">
	{literal}
		$("#pm_send_form").submit(function(){
			// verification
			return true;
		});
	{/literal}
</script>