{title}{tr}Post {$listingTypeName}s{/tr|escape:'html'}{/title}
<h1>{if $clone_job}[[Clone Job]]{else}{tr}Post {$listingTypeName}s {/tr|escape:'html'}{/if}</h1>
{if $error eq 'LISTINGS_NUMBER_LIMIT_EXCEEDED'}
	[[You've reached the limit of number of listings allowed by your product]]
	<p><a href="{$GLOBALS.site_url}/products/">[[Please choose new product]]</a></p> 
{elseif $error eq 'DO_NOT_MATCH_POST_THIS_TYPE_LISTING'}
	{tr}You do not have permissions to post {$listingTypeName}s. Please purchase a relevant product.{/tr|escape:'html'}
{elseif $error eq 'NOT_ALLOW_TO_POST_LISTING'}
    [[Your current Product does not allow posting of listings. For posting please purchase another Product]]
{elseif $error eq 'NOT_LOGGED_IN'}
	[[Please log in to place a new posting. If you do not have an account, please]] <a href="{$GLOBALS.site_url}/registration/">[[Register.]]</a>
	<br/><br/>
	{module name="users" function="login"}
{/if}
