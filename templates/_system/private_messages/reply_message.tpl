
<form method="post" action="" id="pm_send_form" onsubmit="disableSubmitButton('submitSend');" class="form-horizontal">
	<input type="hidden" name="reply_id" value="{$reply_id}" />
	<div id="pmDetails">
		{include file='field_errors.tpl'}
		<div class="form-group has-feedback">
			<label class="inputName col-sm-3 control-label">Message to</label>
			<div class="inputField col-sm-8">
				{if $message.anonym && $message.anonym == $message.from_id}
					[[Anonymous User]]
					<input class="form-control" type="hidden" name="form_to" id="form_to" value="{$message.to_name}" />
				{else}
					<input class="form-control" type="text" name="form_to" id="form_to" value="{$message.to_name}" />
				{/if}
				<input type="hidden" name="anonym" value="{$message.anonym}" />
			</div>
		</div>

		<div class="form-group has-feedback">
			<label class="inputName col-sm-3 control-label">Subject</label>
			<div class="inputField col-sm-8">
				<input class="form-control" type="text" name="form_subject" id="form_subject" value="{$message.subject}" />
			</div>
		</div>
		<div class="form-group has-feedback">
			<label class="inputName col-sm-3 control-label">Message</label>
			<div class="inputField col-sm-8">
				{WYSIWYGEditor name="form_message" class="inputText" height="300px" value=$message.message conf="Basic"}

				<div class="radio">
					<label>
						<input type="checkbox" name="form_save" value="1" {if $save }checked="checked"{/if} />
						Save to outbox
					</label>
				</div>
				<br/>
				<input class="btn btn-default btn-sm"type="submit" value="[[Send]]" id="submitSend" />
			</div>
		</div>
	</div>
</form>
<div class="clearfix"></div>
<script type="text/javascript">
	{literal}
	$("#pm_send_form").submit(function(){
		// verification
		return true;
	});
	{/literal}
</script>