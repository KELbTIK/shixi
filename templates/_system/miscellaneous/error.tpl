<div id="blank">
	{if $ERROR eq 'NOT_SUBSCRIBE'}
		<p class="error">[[You don't have permissions to access this page.]]</p>
		{if $page_function eq "search_form" or $page_function eq "search_results" or $page_function eq "display_listing"}
			<p class="error">[[You have reached number of views allowed by your product. Please <a href="{$GLOBALS.site_url}/products/">products</a> again to view this page.<br/>
			<a href="javascript: history.back()">Back to search results</a>]]</p>
		{/if}
	{elseif $ERROR eq 'NOT_LOGIN'}
		{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"} 
		<p class="error">[[Please log in to access this page. If you do not have an account, please]] <a href="{$url}">[[Register.]]</a></p>
		<br/><br/>
		{module name="users" function="login"}
	{elseif $ERROR eq 'ACCESS_DENIED' || $ERROR eq 'NOT_OWNER'}
		<p class="error">[[You don't have permissions to access this page.]]</p>
		<p><a href="javascript: history.back()">[[Back]]</a></p>
	{elseif $ERROR == 'NOT_OWNER_OF_APPLICATIONS'}
		<p class="error">[[You are not owner of this Application(s)]]</p>
	{elseif $ERROR eq 'WRONG_INVOICE_ID_SPECIFIED'}
		<p class="error">[[There is no such invoice in the system]]</p>
	{elseif $ERROR eq 'INVOICE_ALREADY_PAID'}
		<p class="error">[[Invoice already paid]]</p>
	{elseif $ERROR eq 'NOT_VALID_PAYMENT_ID'}
		<p class="error">[[Invalid payment ID is specified]]</p>
	{/if}
</div>