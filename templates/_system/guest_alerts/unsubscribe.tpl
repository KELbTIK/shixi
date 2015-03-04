{if !empty($error)}
	<p class="error">
		{if $error == "PARAMETERS_MISSED"}
			[[The key parameters are not specified]]
		{elseif $error == "INVALID_CONFIRMATION_KEY"}
			[[Wrong confirmation key specified]]
		{else}
			[[{$error}]]
		{/if}
	</p>
{else}
	<p class="message">
		[[Your email $email was successfully unsubscribed from the Email Alert]]
	</p>
{/if}
