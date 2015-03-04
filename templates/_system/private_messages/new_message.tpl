<div id="pmDetails">
	<form method="post" action="" id="pm_send_form" onsubmit="disableSubmitButton('submitSend');">
		{if $info != ""}<p class="message">[[{$info}]]</p>{/if}
		{include file='field_errors.tpl'}
		<fieldset class="reply">
			<div class="inputName"><span class="strong">[[Message to]]:</span></div>
			<div class="inputField">
				{if $anonym}
					[[Anonymous User]]
					<input type="hidden" name="anonym" value="{if $form_to != ""}{$form_to}{else}{$to}{/if}"/>
					<input type="hidden" name="form_to" id="form_to" value="{if $form_to != ""}{$form_to}{else}{$to}{/if}" /></td></tr>
				{elseif $display_to}
					{$display_to}
					<input type="hidden" name="form_to" id="form_to" value="{if $form_to != ""}{$form_to}{else}{$to}{/if}" />
				{else}
					<input type="text" name="form_to" id="form_to" value="{if $form_to != ""}{$form_to}{else}{$to}{/if}" />
				{/if}
			</div>
		</fieldset>
		<fieldset class="reply">
			<div class="inputName"><span class="strong">[[Subject]]:</span></div>
			<div class="inputField"><input type="text" name="form_subject" id="form_subject" value="{$form_subject}" /></div>
		</fieldset>
		<span class="strong">[[Message]]:</span><br /><br />
		{WYSIWYGEditor name="form_message" class="inputText" height="300px" value=$form_message conf="Basic"}
		<br/><input type="checkbox" name="form_save" value="1" {if $save }checked="checked"{/if} /> [[Save to outbox]]
		<br/><br/><input type="submit" value="[[Send]]" id="submitSend" />
	</form>
</div>
<div class="clr"><br/></div>