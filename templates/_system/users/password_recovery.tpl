<div class="password-recovery">
	<h1>[[Password Recovery]]</h1>
	{foreach from=$errors key=error_code item=error_message}
		{if $error_code == 'WRONG_EMAIL'}
			<p class="error">[[Please specify a valid email address.]]</p>
		{/if}
	{/foreach}
	[[Please, enter your email in the field below and we'll send you a link to a page where you can change your password]]:
	<br/><br/>
	<form method="post" action="">
		<input type="text" name="email" value="{$email|escape:'html'}" class="text" />
		<input type="submit" name="submit" value="[[Submit]]" class="button" />
	</form>
	<br/>
</div>