<div id="pmDetails">
	<fieldset>
		{if $message.outbox == 0}
			<div class="inputName"><span class="strong">[[Message from]]:</span></div>
			<div class="inputField">
				{if $message.anonym && $message.anonym == $message.from_id}
					[[Anonymous User]]
				{elseif $message.from_first_name}
					{$message.from_first_name} {$message.from_last_name}
				{else}
					{$message.from_name}
				{/if}
			</div>
		{else}
			<div class="inputName">[[Message to]]:</div>
			<div class="inputField">{$message.to_first_name} {$message.to_last_name}</div>
		{/if}
	</fieldset>
	<fieldset>
		<div class="inputName"><span class="strong">[[Date]]:</span></div>
		<div class="inputField">{$message.data|date_format:$GLOBALS.current_language_data.date_format} {$message.data|date_format:"%H:%M:%S"}</div>
	</fieldset>
	<fieldset>
		<div class="inputName"><span class="strong">[[Subject]]:</span></div>
		<div class="inputField">{$message.subject}</div>
	</fieldset>
	{$message.message}
</div>
<div class="clr"><br/></div>
<input type="button" id="pm_delete" value="[[Delete]]" />
<input type="hidden" value="{$GLOBALS.site_url}/private-messages/inbox/read/?id={$message.id}&amp;action=delete" id="pm_delete_link" />
{if $message.outbox == 0}
	<input type="button" id="pm_reply" value="[[Reply]]" />
	<input type="hidden" value="{$GLOBALS.site_url}/private-messages/reply/?id={$message.id}" id="pm_reply_link" />
{/if}
<div class="clr"><br/></div>