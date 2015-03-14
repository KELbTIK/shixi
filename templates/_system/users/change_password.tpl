{foreach from=$errors key=error_code item=error_message}
    {if $error_code == 'EMPTY_USERNAME'}
		<div class="error alert alert-danger">[[Username is empty]]</div>
	{elseif $error_code == 'EMPTY_VERIFICATION_KEY'}
		<div class="error alert alert-danger">[[Verification key is empty]]</div>
    {elseif $error_code == 'WRONG_VERIFICATION_KEY'}
		<div class="error alert alert-danger">[[Wrong verification key is specified]]</div>
	{elseif $error_code == 'PASSWORD_NOT_CONFIRMED'}
		<div class="error alert alert-danger">[[Password is not confirmed or empty]]</div>
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
			<td>[[Password]]:</td><td><input type="password" name="password" class="text form-control" /></td>
		</tr>
		<tr>
			<td>[[Confirm Password]]:</td><td><input type="password" name="confirm_password" class="text form-control" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td><td><input type="submit" name="submit" value="[[Submit]]" class="btn btn-default" /></td>
		</tr>
	</table>
</form>