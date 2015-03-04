<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
{literal}
	<script type="text/javascript"><!--
		function tellFriendSubmit() {
			  var options = {
					  target: "#messageBox",
					  url:  $("#tellFriendForm").attr("action")
					};
			  $("#tellFriendForm").ajaxSubmit(options);
			return false;
		}
	--></script>
{/literal}
{if $is_data_submitted && !$errors}
   	<p class="message">[[Your letter was sent]]</p>
{else}
	{if $errors}
		<p class="error">[[Cannot send letter]]</p>
	{/if}
	{if $fatal_errors}
		{foreach from=$fatal_errors key=fatal_error_code item=error_message}
			{if $fatal_error_code == 'UNDEFINED_LISTING_ID'}<p class="error"> [[Listing ID is not defined]] </p>
			{elseif $fatal_error_code == 'LISTING_ID_IS_NOT_NUMERIC'} <p class="error">[[{$error_message}]] </p>
			{/if}
		{/foreach}
	{else}
	{foreach from=$errors key=error_code item=error_message}
		<p class="error">
			{if $error_code  eq 'EMPTY_VALUE'} [[Enter Security code]] 
			{elseif $error_code eq 'NOT_VALID'} [[Security code is not valid]]
			{elseif $error_code eq 'NOT_VALID_EMAIL_FORMAT'} [[Email format is not valid]]
			{elseif $error_code eq 'SEND_ERROR'} [[Error while sending mail]]
			{/if}
		</p>
	{/foreach}
	<form method="post" action="{$GLOBALS.site_url}/tell-friends/" id="tellFriendForm" onsubmit="disableSubmitButton('submitTellFriend'); return tellFriendSubmit();">
		<input type="hidden" name="is_data_submitted" value="1" />
		<input type="hidden" name="listing_id" value="{$listing_info.id|htmlspecialchars}" />

		<div class="text-center strong">[[Recommend]]: {$listing_info.Title}</div>
		<div class="clrBorder"><br/></div>

		<fieldset>
			<div class="inputName">[[Your name]]</div>
			<div class="inputField"><input type="text" name="name" value="{$info.name|escape:"html"}" /></div>
		</fieldset>
		<fieldset>
			<div class="inputName">[[Your friend's name]]</div>
			<div class="inputField"><input type="text" name="friend_name" value="{$info.friend_name|escape:"html"}" /></div>
		</fieldset>
		<fieldset>
			<div class="inputName">[[Your friend's e-mail address]]</div>
			<div class="inputField"><input type="text" name="friend_email" value="{$info.friend_email|escape:"html"}" /></div>
		</fieldset>
		<fieldset>
			<div class="inputName">[[Your comment (will be sent with the recommendation)]]</div>
			<div class="inputField"><textarea name="comment" rows="5">{$info.comment|escape:"html"}</textarea></div>
		</fieldset>
		<fieldset>
			{module name="miscellaneous" function="captcha_handle" currentFunction="tell_friend" displayMode='fieldset'}
		</fieldset>
		<fieldset>
			<div class="inputName">&nbsp;</div>
			<div class="inputButton"><input type="submit" value="[[Send]]" id="submitTellFriend" /></div>
		</fieldset>
	</form>
	{/if}
{/if}