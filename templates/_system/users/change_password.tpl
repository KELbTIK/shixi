{foreach from=$errors key=error_code item=error_message}
    {if $error_code == 'EMPTY_USERNAME'}
		<p class="error">[[Username is empty]]</p>
    {elseif $error_code == 'EMPTY_VERIFICATION_KEY'}
		<p class="error">[[Verification key is empty]]</p>
    {elseif $error_code == 'WRONG_VERIFICATION_KEY'}
		<p class="error">[[Wrong verification key is specified]]</p>
	{elseif $error_code == 'PASSWORD_NOT_CONFIRMED'}
		<p class="error">[[Password is not confirmed or empty]]</p>
	{/if}
{/foreach}

<form method="post" action="">
	<table>
		<tr>
			<td colspan="2">
				<input type="hidden" name="username" value="{$username}" />
				<input type="hidden" name="verification_key" value="{$verification_key}" />
			</td>
		</tr>
		<tr>
			<td>[[Password]]:</td><td><input type="password" name="password" class="text" /></td>
		</tr>
		<tr>
			<td>[[Confirm Password]]:</td><td><input type="password" name="confirm_password" class="text" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td><td><input type="submit" name="submit" value="[[Submit]]" class="button" /></td>
		</tr>
	</table>
</form>