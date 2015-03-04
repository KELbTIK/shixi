{if $message_sent == false}
	{module name="static_content" function="show_static_content" pageid='Contact'}

	{foreach key="key" item="value" from=$field_errors}
		{if $key == 'EMAIL'}
			<p class="error">[[Please specify a valid email address.]]</p>
		{elseif $key == 'NAME'}
			<p class="error">[[Please provide your full name.]]</p>
		{elseif $key == 'COMMENTS'}
			<p class="error">[[Please include your comments.]]</p>
		{elseif $key == 'EMPTY_VALUE'}
			<p class="error">[[Enter Security code]]</p>
		{elseif $key == 'NOT_VALID'}
			<p class="error">[[Security code is not valid]]</p>
		{/if}
	{/foreach}

	<form method="post" action="" onsubmit="disableSubmitButton('submitContact');">
	<input type="hidden" name="action" value="send_message" />
		<table width="95%" border="0" cellspacing="10" cellpadding="1" style="font-size:13px;" class="contact-us" >
			<tr>
				<td width="60%">[[Salutation First and Last Name]]<br/>
					<input type="text" class="fix" name="name" value="{if $GLOBALS.current_user.logged_in}{$name|default:"`$GLOBALS.current_user.FirstName` `$GLOBALS.current_user.LastName`"|escape:'html'}{else}{$name|escape:'html'}{/if}" style="width:90%" /></td>
				<td width="40%">[[Email]]<br/>
					<input type="text" name="email" value="{if $GLOBALS.current_user.logged_in}{$email|default:$GLOBALS.current_user.email|escape:'html'}{else}{$email|escape:'html'}{/if}" style="width:90%" /></td>
			</tr>
			<tr>
				<td colspan="2">
					[[Comments]]:<br />
					<textarea cols="20" rows="10" style="width:96%" name="comments">{$comments|escape:'html'}</textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">{module name="miscellaneous" function="captcha_handle" currentFunction="contact_form"}</td>
			</tr>
			<tr>
				<td colspan="2">
					<input class="button" type="submit" value="[[Submit]]" id="submitContact"/>
				</td>
			</tr>
		</table>
	</form>

{else}
	<br />
	<p>[[Thank you very much for your message. We will respond to you as soon as possible.]]</p>
	<br />
{/if}