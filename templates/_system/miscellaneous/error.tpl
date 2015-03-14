<div id="blank">
	{if $ERROR eq 'NOT_SUBSCRIBE'}
		<div class="error alert alert-danger">[You don't have permissions to access this page.]]</div>
		{if $page_function eq "search_form" or $page_function eq "search_results" or $page_function eq "display_listing"}
			<div class="error alert alert-danger">[[You have reached number of views allowed by your product. Please <a href="{$GLOBALS.site_url}/products/">products</a> again to view this page.<br/>
			<a href="javascript: history.back()">Back to search results</a>]]</div>
	{/if}
	{elseif $ERROR eq 'NOT_LOGIN'}
		{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"} 
		<div class="error alert alert-danger">[[Please log in to access this page. If you do not have an account, please]] <a href="{$url}">[[Register.]]</a></div>
		<br/><br/>
		{module name="users" function="login"}
	{elseif $ERROR eq 'ACCESS_DENIED' || $ERROR eq 'NOT_OWNER'}
		<div class="error alert alert-danger">[[You don't have permissions to access this page.]]</div>
		<p><a href="javascript: history.back()">[[Back]]</a></p>
	{elseif $ERROR == 'NOT_OWNER_OF_APPLICATIONS'}
		<div class="error alert alert-danger">    [[You are not owner of this Application(s)]]</div>
	{elseif $ERROR eq 'WRONG_INVOICE_ID_SPECIFIED'}
		<div class="error alert alert-danger">   [[There is no such invoice in the system]]</div>
	{elseif $ERROR eq 'INVOICE_ALREADY_PAID'}
		<div class="error alert alert-danger">   [[Invoice already paid]]</div>
	{elseif $ERROR eq 'NOT_VALID_PAYMENT_ID'}
		<div class="error alert alert-danger">  [[Invalid payment ID is specified]]</div>
	{/if}
</div>