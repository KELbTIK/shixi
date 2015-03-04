{if $GLOBALS.current_user.logged_in}
	
{else}
	{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"} 
	<p class="error">
	[[Please log in to access this page. If you do not have an account, please]] <a href="{$url}">[[Register.]]</a>
	</p><br/><br/>
	{module name="users" function="login"}
{/if}

