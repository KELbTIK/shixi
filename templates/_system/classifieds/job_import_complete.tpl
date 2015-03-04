<h1>[[Bulk job import from exl/csv file]]</h1>

<div>
	{if $payment_page_url}
	{$listingsNum} jobs have been successfully imported.<br/>
	Click <a href="{$GLOBALS.site_url}/payment-page/?payment_id={$payment_id}">here</a> to pay $ {$price} for imported listings.
	{else}
	{$listingsNum} jobs have been successfully imported and posted.<br/>
	Click <a href="{$GLOBALS.site_url}/my-listings/Job/">here</a> to return to the "My Jobs" section to review jobs posted.
	{/if}
</div>