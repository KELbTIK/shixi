{foreach from=$ERRORS item="error_message" key="error"}
	{if $error eq "NOT_LOGGED_IN"}
		{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"}
		<p class="error">
		[[Please log in to access this page. If you do not have an account, please]] <a href="{$url}">[[Register.]]</a>
		<br/><br/></p>
		{module name="users" function="login"}
	{elseif $error eq "ALREADY_SUBSCRIBED"}
		<p class="error">[[You have already subscribed]]</p>
	{elseif $error == 'INVALID_GATEWAY'}
        <p class="error">[[Invalid gateway ID is specified]]</p>
	{else}
		<p class="error">[[{$error_message}]]</p>
	{/if}
{/foreach}
