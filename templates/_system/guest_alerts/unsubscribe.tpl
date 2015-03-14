{if !empty($error)}
	<div class="error alert alert-danger"></div>
		{if $error == "PARAMETERS_MISSED"}
			[[The key parameters are not specified]]
		{elseif $error == "INVALID_CONFIRMATION_KEY"}
			[[Wrong confirmation key specified]]
		{else}
			[[{$error}]]
		{/if}
	</div>
{else}
	<div class="message  alert alert-danger"></div>
		[[Your email $email was successfully unsubscribed from the Email Alert]]
	</div>
{/if}
