<script language="JavaScript" type="text/javascript">
{literal}
	function cancelSubscription (gatewayID, contractID, recurringID, invoiceID)
	{
		$("#messageBox").dialog( 'destroy' ).html({/literal}'{capture name="displayJobProgressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}{$smarty.capture.displayJobProgressBar|escape:'quotes'}'{literal});
		$("#messageBox").dialog({
			width: 300,
			height: 200,
			modal: true,
			title: '',
			buttons: {
				"{/literal}[[Yes]]{literal}": function() {
					var url = '{/literal}{$GLOBALS.site_url}{literal}/cancel-recurring/?gateway='+gatewayID+'&contractId='+contractID+'&subscriptionId='+recurringID;
					if (invoiceID)
						url = url+'&invoiceID='+invoiceID;
					location.href=url;
				},
				"{/literal}[[No]]{literal}": function() {
					$(this).dialog('close');
				}
			}
		}).dialog( 'open' );
		$("#messageBox").html($("#cancelSubscription").html());
		return false;
	}
{/literal}
</script>
{if $cancelRecurringContractId}
	{foreach from=$contracts_info item=contractInfo}
		{if $contractInfo.id == $cancelRecurringContractId}
			{assign var='cancelProductName' value=$contractInfo.product_info.name}
			<p class="message">[[Your recurring subscription for product]] [[{$cancelProductName}]] [[was successfully canceled. The product will not renew automatically after expiration.]]</p>
		{/if}
	{/foreach}
{/if}
{if $errors}
	{foreach from=$errors item=error key=message}
		{if $message == 'SUBSCRIPTION_IS_FAIL'}<p class="error">[[Subscription failed]]</p>{/if}
	{/foreach}
	{elseif $smarty.get.subscriptionComplete}
	<p class="message">[[Your payment was successfully completed. Recurring Subscription activation may take some time. Once subscription is activated it will appear in the table below.]]</p>
{/if}
{if $productsFailed}
	{foreach from=$productsFailed item=product}
	<p class="error">[[Subscription to product "$product" failed]]</p>
	{/foreach}
{/if}

<h1>[[Your Current Products]]</h1>
{if $statistics.listingAmount}
	{foreach from=$statistics.listingAmount item=listingAmount}
        <div class="currentProducts-name">[[{$listingAmount.name}s Left to Post]]:</div>
        <div class="currentProducts-info">{if $listingAmount.listingsLeft === 0}0{else}[[{$listingAmount.listingsLeft}]]{/if}</div>
        <div class="clr"></div>
	{/foreach}
{/if}

{if $statistics.avalaibleViews}
	{foreach from=$statistics.avalaibleViews item=avalaibleViews key = listingType}
		{if ($GLOBALS.current_user.group.id != "JobSeeker" && $GLOBALS.current_user.group.id != "Employer") || ($GLOBALS.current_user.group.id == "JobSeeker" && $listingType == 'Job') || ($GLOBALS.current_user.group.id == "Employer" && $listingType == 'Resume')}
            <div class="currentProducts-name">[[{$avalaibleViews.name}s Left to View]]:</div>
            <div class="currentProducts-info">{if $avalaibleViews.viewsLeft === 0}0{else}[[{$avalaibleViews.viewsLeft}]]{/if}</div>
            <div class="clr"></div>
		{/if}
	{/foreach}
{/if}
{if $statistics.avalaibleContactViews}
	{foreach from=$statistics.avalaibleContactViews item=avalaibleContactViews key = listingType}
		{if ($GLOBALS.current_user.group.id != "JobSeeker" && $GLOBALS.current_user.group.id != "Employer") || ($GLOBALS.current_user.group.id == "JobSeeker" && $listingType == 'Job') || ($GLOBALS.current_user.group.id == "Employer" && $listingType == 'Resume')}
            <div class="currentProducts-name">[[{$avalaibleContactViews.name} Contact details left to view]]:</div>
            <div class="currentProducts-info">{if $avalaibleContactViews.viewsLeft === 0}0{else}[[{$avalaibleContactViews.viewsLeft}]]{/if}</div>
            <div class="clr"></div>
		{/if}
	{/foreach}
{/if}

<div class="clr"><br/></div>
<table cellspacing="0">
	<thead>
		<tr>
			<th class="tableLeft"> </th>
			<th>[[Product]]</th>
			<th class="text-center">[[Price]]</th>
			<th class="text-center">[[Activation Date]]</th>
			<th class="text-center">[[Exp / Renewal Date]]</th>
			<th>[[Stats]]</th>
			<th class="text-center">[[Status]]</th>
			<th class="tableRight"> </th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$contracts_info item=contract}
			<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
				<td></td>
				<td valign="top">
					<span class="strong">[[{$contract.product_info.name}]]</span>
					<div style="font-size: 11px;">
						{if $contract.listingAmount}
							{assign var=numberOfListings value=0}
							{foreach from=$contract.listingAmount item=amountInfo key=listingType}
								{if $amountInfo.count == 'unlimited' || $numberOfListings === 'unlimited'}
									{assign var=numberOfListings value='unlimited'}
								{else}
									{assign var=numberOfListings value=$numberOfListings+$amountInfo.count}
								{/if}
							{/foreach}
							<div>[[Postings]]: {$numberOfListings}</div> 
						{/if}
						{if $contract.extra_info.pricing_type== 'period'}
							[[Period]]: [[{$contract.extra_info.expiration_period|capitalize}]]<br />
						{elseif $contract.extra_info.expiration_period}
							[[Period]]: {$contract.extra_info.expiration_period} [[days]]<br />
						{/if}
						{if $contract.avalaibleViews}
							{foreach from=$contract.avalaibleViews item=views key=listingType}
								{if $views.count}<div>[[{$views.name} Views]]: {$views.count}</div> {/if}
							{/foreach}
						{/if}
						{if $contract.avalaibleContactViews}
							{assign var=contactDetailsViews value=0}
							{foreach from=$contract.avalaibleContactViews item=contactViews key=listingType}
								{if $contactViews.count == 'unlimited' || $contactDetailsViews === 'unlimited'}
									{assign var=contactDetailsViews value='unlimited'}
								{else}
									{assign var=contactDetailsViews value=$contactDetailsViews+$contactViews.count}
								{/if}
							{/foreach}
							<div>[[Contact Detail Views]]: {$contactDetailsViews}</div> 
						{/if}
					</div>
				</td>
				{capture assign="contractPrice"}{tr type="float"}{$contract.price}{/tr}{/capture}
				<td valign="top" class="text-center">{currencyFormat amount=$contractPrice}</td>
				<td valign="top" class="text-center">{tr type="date"}{$contract.creation_date}{/tr}</td>
				<td valign="top">
                    <div class="text-center">
                        {if $contract.expired_date}{tr type="date"}{$contract.expired_date}{/tr}{else}[[unlimited]]{/if}
                        {if $contract.recurring_id}
                            <br/><a href="{$GLOBALS.site_url}/cancel-recurring/" class="remove" style="font-size: 11px !important;" onClick="cancelSubscription('{$contract.gateway_id}', '{$contract.id}', '{$contract.recurring_id}', '{$contract.invoice_id}'); return false;" />[[Cancel Subscription]]</a>
                            <div id="cancelSubscription" style="display: none">[[Are you sure you want to cancel this subscription?]]</div>
                        {/if}
                    </div>
				<td valign="top">
					{if $contract.listingAmount}
						{foreach from=$contract.listingAmount item=listingAmount}
							<div>[[{$listingAmount.name}s Left to Post]]: {if $listingAmount.listingsLeft === 0}0{else}[[{$listingAmount.listingsLeft}]]{/if}</div>
						{/foreach}
					{/if}
					{if $contract.avalaibleViews}
						{foreach from=$contract.avalaibleViews item=avalaibleViews}
							<div>[[{$avalaibleViews.name}s Left to View]]: {if $avalaibleViews.viewsLeft === 0}0{else}[[{$avalaibleViews.viewsLeft}]]{/if}</div>
						{/foreach}
					{/if}
					{if $contract.avalaibleContactViews}
						{foreach from=$contract.avalaibleContactViews item=avalaibleContactViews}
							<div>[[{$avalaibleContactViews.name} Contact details left to view]]: {if $avalaibleContactViews.viewsLeft === 0}0{else}[[{$avalaibleContactViews.viewsLeft}]]{/if}</div>
						{/foreach}
					{/if}
				</td>
				<td valign="top" class="text-center">[[{$contract.status|capitalize}]]</td>
				<td></td>
			</tr>
		{/foreach}
	</tbody>
</table>

<div class="clr"><br/></div>
<p>[[To purchase a new product please go to the Products section of the website]]:</p>
<p><a href="{$GLOBALS.site_url}/products/" class="button">[[Products]]</a></p>