{if $errors}
	{foreach from=$errors key=error item=error_data}
	      <div class="error alert alert-danger">
			{if $error == 'NOT_IMPLEMENTED'}[[There is something missing in the code]]<br />{/if}
			{if $error == 'INVOICE_ID_IS_NOT_SET'}[[Callback parameters are missing required payment information.]]<br />{/if}
			{if $error == 'NONEXISTED_INVOICE_ID_SPECIFIED'}[[System is unable to identify the payment processed.]]<br />{/if}
			{if $error == 'INVOICE_IS_NOT_PENDING'}[[The invoice that you are requesting to process has already been processed before.]]<br />{/if}
			{if $error == 'INVOICE_STATUS_NOT_VERIFIED'}[[Invoice is not verified]]<br />{/if}
			{if $error == 'INVOICE_IS_NOT_UNPAID'}[[Invoice already paid]]<br />{/if}
			{if $error == 'AMOUNT_IS_NOT_MATCH'}[[You payment is not valid and the product(s) was not purchased. The amount you paid does not match the price of the product(s)]]{/if}
		  </div>
	{/foreach}
{elseif $message}
	<div class="message alert alert-info">[[Your payment was successfully completed. Please wait for product/service activation.]]</div>
{/if}
