<h1>[[All Polls]]</h1>
<div style="float: left;">
	<span class="strong">[[Number of polls per page]]:</span>
	<select id="polls_per_page" name="polls_per_page" onchange="window.location = '?polls_per_page='+this.value;">
		<option value="10" {if $polls_per_page == 10}selected="selected"{/if}>10</option>
		<option value="20" {if $polls_per_page == 20}selected="selected"{/if}>20</option>
		<option value="50" {if $polls_per_page == 50}selected="selected"{/if}>50</option>
		<option value="100" {if $polls_per_page == 100}selected="selected"{/if}>100</option>
	</select>
</div>
<div style="text-align:right">
{foreach from=$pages item=page}
	{if $page == $currentPage}
		<b>{$page}</b>
	{else}
		{if $page == $totalPages && $currentPage < $totalPages-3} ... {/if}
		<a href="?page={$page}&amp;polls_per_page={$polls_per_page}">{$page}</a>
		{if $page == 1 && $currentPage > 4} ... {/if}
	{/if}
{/foreach}
</div>
{foreach from=$allPolls item=result key=question}
	<h3>[[{$question}]]</h3>
	{foreach from=$result.values item=poll}
		<div style="height:125px; width:40px; float: left;  margin-right:3px;">
			<div style="height:120px; width:40px;">
				<div style="height:{$poll.height}px;"><table style="height:100%; width:100%"><tr><td style="vertical-align: bottom; text-align:center; font-weight: bold;">{$poll.vote}%</td></tr></table></div>
				<div style="height: 5px; height:{$poll.vote}px; background-color: #{$poll.color};"></div>
			</div>
			<div style="height: 5px;  background-color: #{$poll.color};"></div>
		</div>
	{/foreach}
	<div class='clear'></div>
	<div style="width:{$result.width}px; text-align:center;font-weight: bold;">{$result.count_vote} {if $result.count_vote == 1}[[vote]]{else}[[votes]]{/if}</div>
	<br/><br/>
	{foreach from=$result.values item=poll}
		<div style="width:20px; height:15px; background-color: #{$poll.color}; float:left; margin-bottom:5px;">&nbsp;</div>
		<div style="font-weight: bold;">&nbsp;&nbsp;-&nbsp;&nbsp;{$poll.value}</div>
		<div class='clear'></div>
	{/foreach}
	<br/>
	<div style="border-bottom: 1px dotted #00A69C"></div>
	<br/><br/>
{/foreach}