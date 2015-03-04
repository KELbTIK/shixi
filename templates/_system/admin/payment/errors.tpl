{if $action == 'edit'}
	{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-invoices/">[[Invoices]]</a>&nbsp;&#187; [[Edit Invoice]]{/breadcrumbs}
	<h1><img src="{image}/icons/usersplus32.png" border="0" alt="" class="titleicon" />[[Edit Invoice]]</h1>
{elseif $action == 'add'}
	{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-invoices/">[[Invoices]]</a>&nbsp;&#187;[[Create Invoice]]{/breadcrumbs}
	<h1><img src="{image}/icons/paperstar32.png" border="0" alt="" class="titleicon"/>[[Create Invoice]]</h1>
{/if}
{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[this value is already used in the system]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an float value]]</p>
	{elseif $error eq 'NOT_INT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an integer value]]</p>
	{elseif $error eq 'INVALID_PERIOD_FROM'}
		<p class="error">[[Period From is not valid]]</p>
	{elseif $error eq 'INVALID_PERIOD_TO'}
		<p class="error">[[Period To is not valid]]</p>
	{elseif $error eq 'CUSTOM_ITEM_FIELD_IS_EMPTY'}
		<p class="error">[['Custom item' is empty]]</p>
	{elseif $error eq 'PRODUCT_FIELD_IS_EMPTY'}
		<p class="error">[['Product' is empty]]</p>
	{elseif $error eq 'CUSTOMER_NOT_SELECTED'}
		<p class="error">[['Customer' is not selected]]</p>
	{elseif $error eq 'OUT_OF_RANGE'}
		<p class="error">'[[{$field_caption}]]' [[value is out of range]]</p>
	{elseif $error eq 'PRODUCT_QUANTITY_IS_NOT_SET'}
		<p class="error">[[Quantity is not set]]</p>
	{elseif $error eq 'WRONG_INVOICE_ID_SPECIFIED'}
        <p class="error">[[Wrong invoice ID is specified]]</p>
	{elseif $error eq 'TCPDF_ERROR'}
        <p class="error">[[Error generating PDF]]</p>
	{elseif $error eq 'INVALID_ID'}
		<p class="error">[[Invalid ID specified]]</p>
	{else}
		<p class="error">[[{$error}]][[{$error_message}]]</p>
	{/if}
{/foreach}
