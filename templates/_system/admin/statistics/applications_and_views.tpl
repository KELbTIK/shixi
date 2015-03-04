<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>

	google.load("visualization", "1", { packages:["corechart"]});
	google.setOnLoadCallback(drawChart);

    function drawChart() {
        var dataV = new google.visualization.DataTable();
        var dataA = new google.visualization.DataTable();
		var dataViews = new Array();
		var dataApply = new Array();
		var i = 0;
		dataV.addColumn('string', 'Title');
		dataV.addColumn('number', 'Count');
		dataA.addColumn('string', 'Title');
		dataA.addColumn('number', 'Count');

        {if $statistics}
       		$("#graph").css("display", "table");
       	{else}
       		$("#graph").css("display", "none");
        {/if}
        {foreach from=$statistics item=statistic key=key}
       		var totalView = parseInt('{$statistic.totalView}');
       		dataViews[i] = ['{$statistic.generalColumn}', totalView];
       		var totalApply = parseInt('{$statistic.totalApply}');
       		dataApply[i] = ['{$statistic.generalColumn}', totalApply];
       		i++;
        {/foreach}


        dataV.addRows(dataViews);
        dataA.addRows(dataApply);

        var options = {
          title: ''
        };
		if ('{$countResult}' > 0) {
	        var chartViews = new google.visualization.PieChart(document.getElementById('viewStatistics'));
	        chartViews.draw(dataV, options);
	        var chartApply = new google.visualization.PieChart(document.getElementById('applyStatistics'));
	        chartApply.draw(dataA, options);
		}
    }
</script>
        
{breadcrumbs}[[Applications and Views]]{/breadcrumbs}
<h1><img src="{image}/icons/risegraph32.png" border="0" alt="" class="titleicon"/>[[Applications and Views Reports]]</h1>

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
				<td class="filterTitle">[[Number of Views and Applications per Period by Job]]</td>
			</tr>
			{foreach from=$userGroups item=userGroup}
			<tr id="userGroup_{$userGroup.id}"  class="userGroupFields">
				{assign var=userGroupId value=$userGroup.id}
				{assign var=filterValue value="userGroup_$userGroupId"}
				<td><input type="radio" name="filter" value="{$filterValue}" {if $filter == $filterValue} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Number of Views and Applications per Period by {$userGroup.caption}]]</td>
			</tr>
			{/foreach}
			<tr class="commonFields"  >
				<td><input type="radio" name="filter" value="JobCategory" {if $filter == 'JobCategory'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Number of Views and Applications per Period by Job’s Category]]</td>
			</tr>
			<tr class="commonFields" >
				<td><input type="radio" name="filter" value="Location_Country" {if $filter == 'Location_Country'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Number of Views and Applications per Period by Job’s Country]]</td>
			</tr>
			<tr class="commonFields" >
				<td><input type="radio" name="filter" value="Location_State" {if $filter == 'Location_State'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Number of Views and Applications per Period by Job’s State]]</td>
			</tr>
			<tr class="commonFields" >
				<td><input type="radio" name="filter" value="Location_City" {if $filter == 'Location_City'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Number of Views and Applications per Period by Job’s City]]</td>
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
				<td colspan="2"><div class="floatRight"><input  type="submit" name="search" value="[[Generate]]" class="greenButton" /></div></td>
			</tr>
		</tbody>
	</table>
</fieldset>
</form>

{if !$errors  && $statistics}
<div class="clr"><br/><br/></div>
<h3>
	[[Number of Views and Applications per Period by {if $filter == 'sid'}Job{elseif $filter == 'JobCategory'}Job’s Category{elseif $filter == 'Location_Country'}Job’s Country{elseif $filter == 'Location_State'}Job’s State{elseif $filter == 'Location_City'}Job’s City{else}{foreach from=$userGroups item=userGroup}{assign var=userGroupId value=$userGroup.id}{assign var=filterValue value="userGroup_$userGroupId"}{if $filter == $filterValue}{$userGroup.caption}{/if}{/foreach}{/if}
	]]
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
				{if $filter == 'JobCategory' || $filter == 'Location_Country' || $filter == 'Location_State' || $filter == 'Location_City'}
					<a href="?search=search&amp;filter={$filter}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field={$filter}&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == $filter}DESC{else}ASC{/if}">[[{$columnTitle}]]</a>
					{if $sorting_field == $filter}
						{if $sorting_order == 'ASC'}
							<img src="{image}b_up_arrow.gif" alt="Up" />
						{else}
							<img src="{image}b_down_arrow.gif" alt="Down" />
						{/if}
					{/if}
				{elseif $filter == 'sid'}
					<a href="?search=search&amp;filter={$filter}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=Title&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'Title'}DESC{else}ASC{/if}">[[{$columnTitle}]]</a>
					{if $sorting_field == 'Title'}
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
				<a href="?search=search&amp;filter={$filter}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=CompanyName&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'CompanyName'}DESC{else}ASC{/if}">[[Company Name]]</a>
				{if $sorting_field == 'CompanyName'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			{/if}
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=totalView&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'totalView'}DESC{else}ASC{/if}">[[Number of {if $filter != 'sid'}Job{/if} Views]]</a>
				{if $sorting_field == 'totalView'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=totalApply&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'totalApply'}DESC{else}ASC{/if}">
					{assign var='header' value='Number of Applications Made'}
					{if $filter == 'sid'}
						{assign var='header' value='Number of Applications Received'}
					{else}
						{foreach from=$userGroups item=userGroup key=userGroupSID}
							{assign var=filterValue value="userGroup_$userGroupSID"}
							{if $filter == $filterValue && $userGroup.key == 'Employer'}
								{assign var='header' value='Number of Applications Received'}
							{/if}
						{/foreach}
					{/if}
					{$header}
				</a>
				{if $sorting_field == 'totalApply'}
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
					{if $statistic.generalColumn == 'Other'}
						{$statistic.generalColumn}
					{else}
						{if $link == 'user'}
							<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$statistic.user_sid}">{$statistic.generalColumn}</a>
						{elseif $link == 'listing'}
							<a href="{$GLOBALS.site_url}/edit-listing/?listing_id={$statistic.sid}">{$statistic.generalColumn}</a>
						{else}
							{$statistic.generalColumn}
						{/if}
					{/if}
					</strong>
				</td>
				{if $filter == 'sid'}
					<td>
						{if $statistic.generalColumn == 'Other'}
						{else}
							<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$statistic.user_sid}">{if $statistic.CompanyName}{$statistic.CompanyName}{else}{$statistic.username}{/if}</a>
						{/if}
					</td>
				{/if}
				<td>{if $statistic.totalView}{$statistic.totalView}{else}0{/if}</td>
				<td>{if $statistic.totalApply}{$statistic.totalApply}{else}0{/if}</td>
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
		<span class="greenButtonEnd"><input type="submit" name="export" value="[[Export in]]" class="greenButton" /></span>
		<select name="type" class="export-select">
			<option value="csv">CSV</option>
			<option value="xls">XLS</option>
		</select>
	</form>
	<div class="clr"></div>
	<form method="post" action="{$GLOBALS.site_url}/print-applications-and-views/" target="_blank">
		{foreach from=$period key=key item=value}
			<input type="hidden" name="period[{$key}]" value="{$value}" />
		{/foreach}
		<input type="hidden" name="filter" value="{$filter}" />
		<input type="hidden" name="sorting_field" value="{$sorting_field}" />
		<input type="hidden" name="sorting_order" value="{$sorting_order}" />
		<span class="greenButtonEnd printButton"><input type="submit" name="search" value="[[Print]]" class="greenButton"  /></span>
	</form>
</div>
{elseif !$errors  && !$statistics  && $search}
	<br/><br/><p class="error" style="width: 600px;">[[Report can not be generated. There is no statistics for this period.]]</p>
{/if}
<div class="clr"><br/><br/></div>
<table width="80%" style="border:0px!important; display: none;" id="graph">
	<tr>
		<td style="border:0px!important; text-align: center; font-weight: bold;">[[Number of {if $filter != 'sid'}Job{/if} Views]]</td>
		<td style="border:0px!important; text-align: center; font-weight: bold;">{$header}</td>
	</tr>
	<tr  style="border:0px!important">
		<td width="50%"  style="border:0px!important"><div id="viewStatistics" style="width:100%; height: 300px;"></div></td>
		<td width="50%"  style="border:0px!important"><div id="applyStatistics" style="width:100%; height: 300px;"></div></td>
	</tr>
</table>


<script language="Javascript">
	$(function() {

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