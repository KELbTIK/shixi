<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>
	google.load("visualization", "1", { packages:["corechart"] });
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = new google.visualization.DataTable();
		var dataArr = new Array();
		var i = 0;
		data.addColumn('string', 'Title');
		data.addColumn('number', 'Percent');
		{foreach from=$statistics item=statistic key=key}
			{if !$statistic.totalSum && $statistic.generalColumn != 'Other'}
			var total = parseInt('{$statistic.total}');
			var title =
				{if $filter != 'sid' && $filter != 'Country' && $filter != 'State' && $filter != 'City'}
					"{$statistic.generalColumn}";
				{elseif $statistic.featured}
					"[[Upgrade to featured]] ({$statistic.generalColumn})";
				{elseif $statistic.priority}
					"[[Upgrade to priority]] ({$statistic.generalColumn})";
				{elseif $statistic.reactivate}
					"[[Reactivation]] ({$statistic.generalColumn})";
				{else}
					"{$statistic.generalColumn}";
				{/if}
			dataArr[i] = [title, total];
			{/if}
			i++;
		{/foreach}

		data.addRows(dataArr);
		var options = {
			title: ''
		};
		if ('{$countResult}' > 0) {
			var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
			chart.draw(data, options);
		}
	}
</script>

{breadcrumbs}[[Sales]]{/breadcrumbs}
<h1><img src="{image}/icons/risegraph32.png" border="0" alt="" class="titleicon"/>[[Sales Reports]]</h1>

{if $errors }
	{foreach from=$errors item=error key=name}
		{if $error == EMPTY_PARAMETER}
			<p class="error">[[Report can not be generated. Select the report parameter]]</p>
		{elseif $error == SELECTED_PERIOD_IS_INCORRECT}
			<p class="error">[[Report can not be generated. Please set correct dates.]]</p>
		{else}
			<p class="error">[[{$error}]]</p>
		{/if}
	{/foreach}
{/if}


<form method="post">
<input type="hidden" name="action" value="search" />
<fieldset style="width: 600px;">
	<legend>[[Filter]]</legend>
	<table id="filterForm">
		<tbody>
			<tr class="commonFields"  >
				<td><input type="radio" name="filter" value="sid" {if $filter == 'sid'} checked="checked" {/if} /></td>
				<td class="filterTitle" style="width: 93%">[[Sales per Period by Product]]</td>
			</tr>
			{foreach from=$userGroups item=userGroup}
			<tr id="userGroup_{$userGroup.id}"  class="userGroupFields">
				{assign var=userGroupId value=$userGroup.id}
				{assign var=filterValue value="userGroup_$userGroupId"}
				<td><input type="radio" name="filter" value="{$filterValue}" {if $filter == $filterValue} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Sales per Period by {$userGroup.caption}]]</td>
			</tr>
			{/foreach}
			<tr class="commonFields" >
				<td><input type="radio" name="filter" value="Location_Country" {if $filter == 'Location_Country'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Sales per Period by Country]]</td>
			</tr>
			<tr class="commonFields" >
				<td><input type="radio" name="filter" value="Location_State" {if $filter == 'Location_State'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Sales per Period by State]]</td>
			</tr>
			<tr class="commonFields" >
				<td><input type="radio" name="filter" value="Location_City" {if $filter == 'Location_City'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Sales per Period by City]]</td>
			</tr>
			<tr class="commonFields"  >
				<td colspan="2">
					[[From]]:
					{capture name="input_text_field_from"}<input type="text" name="period[from]" value="{$period.from}" id="period_notless"/>{/capture}
					{capture name="input_text_field_to"}<input type="text" name="period[to]" value="{$period.to}" id="period_notmore"/>{/capture}
					{assign var="input_text_field_from" value="`$smarty.capture.input_text_field_from`"}
					{assign var="input_text_field_to" value="`$smarty.capture.input_text_field_to`"}
					[[$input_text_field_from to $input_text_field_to]]
				</td>
			</tr>
			<tr>
				<td colspan = "2"><div class="floatRight"><input  type="submit" name="search" value="[[Generate]]" class="greenButton" /></div></td>
			</tr>
		</tbody>
	</table>
</fieldset>
</form>

{if !$errors  && $statistics}
<div class="clr"><br/><br/></div>
<h3>
	[[Sales per Period by {if $filter == 'sid'}Product{elseif $filter == 'Country'}Country{elseif $filter == 'State'}State{elseif $filter == 'City'}City{else}{foreach from=$userGroups item=userGroup}{assign var=userGroupId value=$userGroup.id}{assign var=filterValue value="userGroup_$userGroupId"}{if $filter == $filterValue}{$userGroup.caption}{/if}{/foreach}{/if}]]
</h3>
<div>
	{if $periodView.from}
		[[from]]  {tr type="date"}{$periodView.from}{/tr}
	{/if}
	{if $periodView.to}
		[[to]]  {tr type="date"}{$periodView.to}{/tr}
	{/if}
</div>
<table>
	<thead>
		<tr>
			<th>
				{if $filter == 'Location_Country' || $filter == 'Location_State' || $filter == 'Location_City'}
					<a href="?search=search&amp;filter={$filter}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field={$filter}&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == $filter}DESC{else}ASC{/if}">[[{$columnTitle}]]</a>
					{if $sorting_field == $filter}
						{if $sorting_order == 'ASC'}
							<img src="{image}b_up_arrow.gif" alt="Up" />
						{else}
							<img src="{image}b_down_arrow.gif" alt="Down" />
						{/if}
					{/if}
				{elseif $filter == 'sid'}
					<a href="?search=search&amp;filter={$filter}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=name&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'name'}DESC{else}ASC{/if}">[[{$columnTitle}]]</a>
					{if $sorting_field == 'name'}
						{if $sorting_order == 'ASC'}
							<img src="{image}b_up_arrow.gif" alt="Up" />
						{else}
							<img src="{image}b_down_arrow.gif" alt="Down" />
						{/if}
					{/if}
				{else}
					<a href="?search=search&amp;filter={$filter}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=username&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'username'}DESC{else}ASC{/if}">[[{$columnTitle}]]</a>
					{if $sorting_field == 'username'}
						{if $sorting_order == 'ASC'}
							<img src="{image}b_up_arrow.gif" alt="Up" />
						{else}
							<img src="{image}b_down_arrow.gif" alt="Down" />
						{/if}
					{/if}
				{/if}
			</th>
			{if $filter == 'sid'}
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=product_type&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'product_type'}DESC{else}ASC{/if}">[[Product Type]]</a>
				{if $sorting_field == 'product_type'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			{/if}
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=units_sold&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'units_sold'}DESC{else}ASC{/if}">[[Units Sold]]</a>
				{if $sorting_field == 'units_sold'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;listingTypeSID={$listingTypeSID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=total&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'total'}DESC{else}ASC{/if}">[[Income]]</a>
				{if $sorting_field == 'total'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;listingTypeSID={$listingTypeSID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=percent&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'percent'}DESC{else}ASC{/if}">%</a>
				{if $sorting_field == 'percent'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$statistics item=statistic key=key}
			<tr>
				<td>
					<strong>
					{if $statistic.totalSum || $statistic.generalColumn == 'Other'}
						[[{$statistic.generalColumn}]]
					{else}
						{if ($filter == 'sid' || $filter == 'Country' || $filter == 'State' || $filter == 'City') && ($statistic.featured || $statistic.priority || $statistic.reactivate)}
							{if $statistic.featured}
								[[Upgrade to featured]]
							{elseif $statistic.priority}
								[[Upgrade to priority]]
							{elseif $statistic.reactivate}
								[[Reactivation]]
							{/if}
							
							{if $link == 'user'}
								<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$statistic.user_sid}">([[{$statistic.generalColumn}]])</a>
							{elseif $link == 'product'}
								<a href="{$GLOBALS.site_url}/edit-product/?sid={$statistic.sid}">([[{$statistic.generalColumn}]])</a>
							{else}
								({$statistic.generalColumn})
							{/if}
						{else}
							{if $link == 'user'}
								<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$statistic.user_sid}">[[{$statistic.generalColumn}]]</a>
							{elseif $link == 'product'}
								<a href="{$GLOBALS.site_url}/edit-product/?sid={$statistic.sid}">[[{$statistic.generalColumn}]]</a>
							{else}
								[[{$statistic.generalColumn}]]
							{/if}
						{/if}
					{/if}
					</strong>
				</td>
				{if $filter == 'sid'}
					<td>{$statistic.product_type}</td>
				{/if}
				<td>{$statistic.units_sold}</td>
				<td>
					{capture assign="statisticTotal"}{tr type="float"}{$statistic.total}{/tr}{/capture}
					{currencyFormat amount=$statisticTotal}
				</td>
				<td>{if !$statistic.totalSum}{$statistic.percent}%{/if}</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<div id="stat-footer">
	<form method="post">
		<input type="hidden" name="action" value="export" />
		{foreach from=$period key=key item=value}
			<input type="hidden" name="period[{$key}]" value="{$value}" />
		{/foreach}
		<input type="hidden" name="filter" value="{$filter}" />
		<input type="hidden" name="sorting_field" value="{$sorting_field}" />
		<input type="hidden" name="sorting_order" value="{$sorting_order}" />
		<span class="greenButtonEnd"><input type="submit" name="export" value="[[Export]]" class="greenButton" /></span>
		<select name="type" class="export-select">
			<option value="csv">CSV</option>
			<option value="xls">XLS</option>
		</select>
	</form>
	<div class="clr"></div>
	<form method="post" action="{$GLOBALS.site_url}/print-sales/" target="_blank">
		{foreach from=$period key=key item=value}
			<input type="hidden" name="period[{$key}]" value="{$value}" />
		{/foreach}
		<input type="hidden" name="filter" value="{$filter}" />
		<input type="hidden" name="sorting_field" value="{$sorting_field}" />
		<input type="hidden" name="sorting_order" value="{$sorting_order}" />
		<span class="greenButtonEnd printButton"><input type="submit" name="search" value="[[Print]]" class="greenButton"  /></span>
	</form>
</div>
{elseif !$errors  && !$statistics && $search}
	<br/><br/><p class="error" style="width: 600px;">[[Report can not be generated. There is no statistics for this period.]]</p>
{/if}
<div class="clr"><br/><br/></div>
<div id="chart_div" style="width: 900px; height: 500px;"></div>


<script >
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