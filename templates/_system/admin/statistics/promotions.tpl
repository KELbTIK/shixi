{breadcrumbs}[[Promotions Usage Report]]{/breadcrumbs}
<h1><img src="{image}/icons/risegraph32.png" border="0" alt="" class="titleicon"/>[[Promotions Usage Report]]</h1>

{if $errors }
	{foreach from=$errors item=error key=name}
		{if $error == 'SELECTED_PERIOD_IS_INCORRECT'}
			<p class="error">[[Report can not be generated. Please set correct dates]].</p>
		{else}
			<p class="error">[[{$error}]]</p>
		{/if}
	{/foreach}
{/if}

<form method="post">
    <input type="hidden" name="action" value="search" />
    <fieldset style="width: 600px;">
        <legend>[[Filter]]</legend>
        <table id="filterForm" border="1">
            <tbody>
				<tr class="commonFields">
					<td colspan="2">
						[[from]]:
						{capture name="input_text_field_from"}<input type="text" name="period[from]" value="{$period.from}" id="period_notless"/>{/capture}
						{capture name="input_text_field_to"}<input type="text" name="period[to]" value="{$period.to}" id="period_notmore"/>{/capture}
						{assign var="input_text_field_from" value="`$smarty.capture.input_text_field_from`"}
						{assign var="input_text_field_to" value="`$smarty.capture.input_text_field_to`"}
						[[$input_text_field_from to $input_text_field_to]]
					</td>
				</tr>
				<tr>
					<td colspan = "2" align="right"><div class="floatRight"><input  type="submit" value="[[Generate]]" class="grayButton" /></div></td>
				</tr>
            </tbody>
        </table>
    </fieldset>
</form>

{if !$errors  && $statistics}
	<div class="clr"><br/><br/></div>
	{capture name="urlParams"}?action=search&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_order={if $sorting_order == 'ASC'}DESC{else}ASC{/if}{/capture}
	{capture name="sortingOrderImg"}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.gif" alt="Up" />{else}<img src="{image}b_down_arrow.gif" alt="Down" />{/if}{/capture}
	<table>
		<thead>
		<tr>
			<th>
				<a href="{$smarty.capture.urlParams}&amp;sorting_field=promotionCode">[[Promotion Code]]</a>
				{if $sorting_field == 'promotionCode'}
					{$smarty.capture.sortingOrderImg}
				{/if}
			</th>
			<th>
				<a href="{$smarty.capture.urlParams}&amp;sorting_field=promotionType">[[Discount Type]]</a>
				{if $sorting_field == 'promotionType'}
					{$smarty.capture.sortingOrderImg}
				{/if}
			</th>
			<th>
				<a href="{$smarty.capture.urlParams}&amp;sorting_field=usageCount">[[Usage Count]]</a>
				{if $sorting_field == 'usageCount'}
					{$smarty.capture.sortingOrderImg}
				{/if}
			</th>
			<th>
				<a href="{$smarty.capture.urlParams}&amp;sorting_field=promotionDiscount">[[Discount Value]]</a>
				{if $sorting_field == 'promotionDiscount'}
					{$smarty.capture.sortingOrderImg}
				{/if}
			</th>
			<th>
				<a href="{$smarty.capture.urlParams}&amp;sorting_field=discountAmount">[[Discount Amount]]</a>
				{if $sorting_field == 'discountAmount'}
					{$smarty.capture.sortingOrderImg}
				{/if}
			</th>
			<th>
				<a href="{$smarty.capture.urlParams}&amp;sorting_field=saleSubTotal">[[Sale Subtotal]]</a>
				{if $sorting_field == 'saleSubTotal'}
					{$smarty.capture.sortingOrderImg}
				{/if}
			</th>
			<th>
				<a href="{$smarty.capture.urlParams}&amp;sorting_field=saleTotal">[[Sale Total]]</a>
				{if $sorting_field == 'saleTotal'}
					{$smarty.capture.sortingOrderImg}
				{/if}
			</th>
		</tr>
		</thead>
		<tbody>
			{foreach from=$statistics item="statistic" key="key"}
			<tr>
				<td><strong>{$statistic.promotionCode}</strong></td>
				<td>{if $statistic.promotionType == 'percentage'}%{else}{$currency.currency_sign}{/if}</td>
				<td>{$statistic.usageCount}</td>
				<td>
					{capture assign="promotionDiscount"}{tr type="float"}{$statistic.promotionDiscount}{/tr}{/capture}
				    {if $statistic.promotionType == 'percentage'}
					    {$promotionDiscount}%
				    {else}
					    {currencyFormat amount=$promotionDiscount sign=$currency.currency_sign}
					{/if}
				</td>
				<td>
					{capture assign="discountAmount"}{tr type="float"}{$statistic.discountAmount}{/tr}{/capture}
					{currencyFormat amount=$discountAmount}
				</td>
				<td>
					{capture assign="saleSubTotal"}{tr type="float"}{$statistic.saleSubTotal}{/tr}{/capture}
					{currencyFormat amount=$saleSubTotal}
				</td>
				<td>
					{capture assign="saleTotal"}{tr type="float"}{$statistic.saleTotal}{/tr}{/capture}
					{currencyFormat amount=$saleTotal}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
{elseif !$errors  && !$statistics && $action}
	<br/><br/><p class="error" style="width: 600px;">[[Report can not be generated. There is no statistics for this period.]]</p>
{/if}

<script language="Javascript">
	$(function(){ldelim}
		var dFormat = '{$GLOBALS.current_language_data.date_format}';
		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yy");

		$("#period_notless, #period_notmore").datepicker({
			dateFormat: dFormat,
			showOn: 'both',
			yearRange: '-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});
    });
</script>

