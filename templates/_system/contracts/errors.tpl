{foreach from=$ERRORS item="error_message" key="error"}
{if $error eq "NOT_LOGGED_IN"}
	{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"} 
	<div class="error alert alert-danger">
	[[Please log in to access this page. If you do not have an account, please]] <a href="{$url}">[[Register.]]</a>
	<br/><br/></div>
	{module name="users" function="login"}
{elseif $error eq "ALREADY_SUBSCRIBED"}
	<<div class="error alert alert-danger">[[You have already subscribed]]</div>
{/if}
{/foreach}