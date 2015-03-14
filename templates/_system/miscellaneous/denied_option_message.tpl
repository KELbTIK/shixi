{if $message}
	<div class="message alert alert-info"> [[{$message}]]</div>
{else}
	<div class="error alert alert-danger">[[This action is not allowed within your current product]]</div>
{/if}