<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
{if !empty($errors)}
	{foreach from=$errors item="error"}
		<p class="error">[[{$error}]]</p>
	{/foreach}
{/if}
<form method="post" action="{$GLOBALS.site_url}/private-messages/aj-send/" id="pm_send_form" onsubmit="disableSubmitButton('pm_send_button');">
	<fieldset>
		<div class="inputName"><span class="strong">[[Message to]]:</span></div>
		<div class="inputField">
			{if $anonym}
				[[Anonymous User]]
				<input type="hidden" name="anonym" value="{if $form_to != ""}{$form_to}{else}{$to}{/if}"/>
			{else}
				{$display_to}
			{/if}
			<input type="hidden" name="form_to" id="form_to" value="{if $form_to != ""}{$form_to}{else}{$to}{/if}"/>
		</div>
	</fieldset>
	<fieldset>
		<div class="inputName"><span class="strong">[[Subject]]:</span></div>
		<div class="inputField"><input type="text" name="form_subject" id="form_subject" value="{$form_subject}"></div>
	</fieldset>
	<fieldset>
		<div class="inputName"><span class="strong">[[Message]]:</span></div>
		<div class="inputField">{WYSIWYGEditor name="form_message" class="inputText" height="250px" value="$form_message" conf="Basic"}</div>
	</fieldset>
	<fieldset>
		<div class="inputName"><span class="strong">[[Save to outbox]]:</span></div>
		<div class="inputField"><input type="checkbox" name="form_save" id="pm_checkbox" value="1" {if $save }checked="checked"{/if} /></div>
	</fieldset>
	<fieldset>
		<div class="inputName"></div>
		<div class="inputButton">
			<input type="submit" id="pm_send_button" value="[[Send]]" class="button"  />
			<input type="hidden" name="act" value="send" />
			{if $cc}
				<input type="hidden" name="cc" value="{$cc}" />
			{/if}
		</div>
	</fieldset>
</form>

<script language="JavaScript" type="text/javascript">
	{literal}
	var reloadPage = true;
	function pm_check() {

		if ($.trim($("#form_to").val()) == '') {
			alert('{/literal}[[All fields are required]]{literal}');
			return false;
		}
		if ($.trim($("#form_subject").val()) == '') {
			alert('{/literal}[[All fields are required]]{literal}');
			return false;
		}
		if ($.trim(CKEDITOR.instances['form_message'].getData()) == '') {
			alert('{/literal}[[All fields are required]]{literal}');
			return false;
		}
		return true;
	}

	$("#pm_send_form").submit(function() {
		if (pm_check()) {
			var mess = CKEDITOR.instances['form_message'].getData();
			var che = 0;
			if ($("#pm_checkbox").attr("checked"))
				che = 1;
			$("#pm_checkbox").val(che);
			$("textarea[name='form_message']").val(mess);
			var options = {
				target: "#messageBox",
				url:  $("#pm_send_form").attr("action")
			};
			$("#pm_send_form").ajaxSubmit(options);
		}
		return false;
	});
	{/literal}
</script>