{foreach from=$ERRORS item="error_message" key="error"}
	<div class="error alert alert-danger">
		{if $error eq "INVALID_REQUEST"}
			{$error_message}
		{elseif $error eq "INVALID_DATA"}
			{$error_message}
		{elseif $error eq "PARAMETERS_MISSED"}
			[[The key parameters are not specified]]
		{elseif $error eq "MYSQL_ERROR"}
			{$error_message}
		{elseif $error eq "NOT_LOGGED_IN"}
			{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"}
			[[Please log in to place a listing. If you do not have an account, please]] <a href="{$url}">[[Register]]</a>
			<br/><br/>
			{module name="users" function="login"}
		{elseif $error == 'DEFAULT_VALUE_NOT_SET'}
			Default value for {$error_message} is not set
		{elseif $error eq 'NOT_SUBSCRIBE'}
			[[You don't have permissions to access this page.]]
		{elseif $error eq 'COMMENT_HAS_BAD_WORDS'}
			[[Your comment has bad words]]
		{elseif $error eq "NOT_LOGGED_IN_ALERTS"}
			{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"}
			[[Please log in to access this page. If you do not have an account, please]] <a href="{$url}">[[Register]]</a><br/><br/>
			{module name="users" function="login"}
		{/if}

	</div>
{/foreach}search-col-wide