{foreach from=$ERRORS item="error_message" key="error"}
	{if $error eq "INVALID_REQUEST"}
		<p class="error">{$error_message}</p>
	{elseif $error eq "INVALID_DATA"}
		<p class="error">{$error_message}</p>
	{elseif $error eq "PARAMETERS_MISSED"}
		[[The key parameters are not specified]]
	{elseif $error eq "MYSQL_ERROR"}
		{$error_message}
	{elseif $error eq "NOT_LOGGED_IN"}
		{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"} 
		<p class="error">[[Please log in to place a listing. If you do not have an account, please]] <a href="{$url}">[[Register]]</a></p>
		<br/><br/>
		{module name="users" function="login"}
	{elseif $error == 'DEFAULT_VALUE_NOT_SET'}
		<p class="error">Default value for {$error_message} is not set</p>
	{elseif $error eq 'NOT_SUBSCRIBE'}
		<p class="error">[[You don't have permissions to access this page.]]</p>
	{elseif $error eq 'COMMENT_HAS_BAD_WORDS'}
		<p class="error">[[Your comment has bad words]]</p>
	{elseif $error eq "NOT_LOGGED_IN_ALERTS"}	
		{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"} 
		<p class="error">[[Please log in to access this page. If you do not have an account, please]] <a href="{$url}">[[Register]]</a></p>		<br/><br/>
		{module name="users" function="login"}
	{/if}
{/foreach}