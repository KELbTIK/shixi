<div id="pmDetails">
	<form method="post" action="" id="pm_send_form" onsubmit="disableSubmitButton('submitSend');" class="form-horizontal">
		{if $info != ""} <div class="message alert alert-info">[[{$info}]]</div>{/if}

		{include file='field_errors.tpl'}
		<div class="form-group has-feedback">
			<label class="inputName col-sm-3 control-label">Message to</label>
			<div class="inputField col-sm-8 padding_correct" >
				{if $anonym}
					[[Anonymous User]]
					<input class="form-control" type="hidden" name="anonym" value="{if $form_to != ""}{$form_to}{else}{$to}{/if}"/>
					<input class="form-control" type="hidden" name="form_to" id="form_to" value="{if $form_to != ""}{$form_to}{else}{$to}{/if}" />
				{elseif $display_to}
					{$display_to}
					<input class="form-control" type="hidden" name="form_to" id="form_to" value="{if $form_to != ""}{$form_to}{else}{$to}{/if}" />
				{else}
					<input class="form-control" type="text" name="form_to" id="form_to" value="{if $form_to != ""}{$form_to}{else}{$to}{/if}" />
				{/if}
			</div>
		</div>

		<div class="form-group has-feedback">
			<label class="inputName col-sm-3 control-label">Subject</label>
			<div class="inputField col-sm-8">
				<input class="form-control" type="text" name="form_subject" id="form_subject" value="{$form_subject}" />
			</div>
		</div>
		<div class="form-group has-feedback">
			<label class="inputName col-sm-3 control-label">Message</label>
			<div class="inputField col-sm-8">
				{WYSIWYGEditor name="form_message" class="inputText" height="300px" value=$form_message conf="Basic"}

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

	</form>
</div>
<div class="clearfix"></div>