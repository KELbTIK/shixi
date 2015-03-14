{if !empty($error)}
<div class="error alert alert-danger">
		{if $error == "PARAMETERS_MISSED"}
			[[The key parameters are not specified]]
		{elseif $error == "INVALID_CONFIRMATION_KEY"}
			[[Wrong confirmation key specified]]
		{else}
			[[{$error}]]
		{/if}
	</div>
{else}
	<div class="message alert alert-info">
		[[Your email is confirmed. You will receive email alerts on this email.]]
	</div>
{/if}
