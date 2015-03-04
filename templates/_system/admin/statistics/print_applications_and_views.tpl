<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.js"></script>
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
<div class="InContent" style="text-align: center;">
{if !$errors  && $statistics}
<h3>
	[[Number of Views and Applications per Period by {if $filter == 'sid'}Job{elseif $filter == 'JobCategory'}Job’s Category{elseif $filter == 'Location_Country'}Job’s Country{elseif $filter == 'Location_State'}Job’s State	{elseif $filter == 'Location_City'}Job’s City{else}{foreach from=$userGroups item=userGroup}
			{assign var=userGroupId value=$userGroup.id}
			{assign var=filterValue value="userGroup_$userGroupId"}
			{if $filter == $filterValue}
				{$userGroup.caption}
			{/if}
		{/foreach}
	{/if}
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
<table align="center" class="print-table">
	<thead>
		<tr>
			<th>[[{$columnTitle}]]</th>
			{if $filter == 'sid'}
				<th>[[Company Name]]</th>
			{/if}
			<th>[[Number of {if $filter != 'sid'}Job{/if} Views]]</th>
			<th>
				{assign var="header" value='Number of Applications Made'}
				{foreach from=$userGroups item=userGroup key=userGroupSID}
					{assign var="filterValue" value="userGroup_$userGroupSID"}
					{if $filter == $filterValue && $userGroup.key == 'Employer'}
						{assign var="header" value="Number of Applications Received"}
					{/if}
				{/foreach}
				{$header}
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$statistics item=statistic key=key}
			<tr>
				<td>
					<strong>[[{$statistic.generalColumn}]]</strong>
				</td>
				{if $filter == 'sid'}
					<td>
						{if $statistic.generalColumn == 'Other'}
						{else}
							{if $statistic.CompanyName}{$statistic.CompanyName}{else}{$statistic.username}{/if}
						{/if}
					</td>
				{/if}
				<td>{if $statistic.totalView}{$statistic.totalView}{else}0{/if}</td>
				<td>{if $statistic.totalApply}{$statistic.totalApply}{else}0{/if}</td>
			</tr>
		{/foreach}
	</tbody>
</table>
{/if}
<div class="clr"><br/><br/><br/></div>
<table class="pring-chart" width="60%" id="graph" align="center">
	<tr>
		<td class="text-center strong">[[Number of Jobs Viewed]]</td>
		<td class="text-center strong">[[Number of Applications Received]]</td>
	</tr>
	<tr>
		<td width="50%"><div id="viewStatistics"></div></td>
		<td width="50%"><div id="applyStatistics"></div></td>
	</tr>
	<tr>
		<td colspan="2" class="print-button"><input type="button" value="[[Print]]" onClick="this.style.display='none';window.print();" /></td>
	</tr>
</table>
</div>