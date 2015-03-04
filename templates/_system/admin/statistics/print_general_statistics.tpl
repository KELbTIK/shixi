<div class="InContent text-center">
<h1>[[General Statistics]]</h1>
<br/>
{if !$errors && $statistics}
{assign var=listingCount value=$listingTypes|@count}
{assign var=userCount value=$userGroups|@count}
<table align="center" class="no-border"><tr><td class="no-border">
<table class="print-table">
	<tr>
		<td></td>
		{foreach from=$statistics item=statistic key=key}
			<td  nowrap="nowrap" class="strong">
			{if $key == 'total'}
				[[Total]]
			{elseif $groupBy == 'day'}
				[[{$key}]]
			{elseif $groupBy == 'month'}
				{$statistic.month}, {$statistic.year}
			{elseif $groupBy == 'year'}
				{$statistic.year}
			{else}
				{$statistic.quarter}, {$statistic.year}
			{/if}
			</td>
		{/foreach}
	</tr>
	
	{if $filter.popularity == 1}
	<tr><td class="strong text-center">[[Popularity]]</td><td colspan="{$countItems - 2}"></td></tr>
	<tr>
		<td nowrap="nowrap" >[[Number of Website Views]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.siteView.statistic}</td>
		{/foreach}
	</tr>
	{foreach from=$listingTypes item=listingType}
	<tr>
		<td nowrap="nowrap" >[[Number of {$listingType.caption} Views]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="viewListing$listingTypeID"}
			<td>{$test}{$statistic.$event.statistic}</td>
		{/foreach}
	</tr>
	{/foreach}
	{/if}
	
	{if $filter.users == 1}
	<tr><td class="strong text-center">[[Users]]</td><td colspan="{$countItems - 2}"></td></tr>
	{foreach from=$userGroups item=userGroup name=users_block}
	<tr>
		{assign var=userGroupID value=$userGroup.id}
		<td nowrap="nowrap" >[[Number of {if $userGroup.key == 'Employer'}Employers{elseif $userGroup.key == 'JobSeeker'}Job Seekers{else}"{$userGroup.caption}" Users{/if} Registered]]</td>
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="addUser$userGroupID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
	</tr>
	{/foreach}
	<tr>
		<td nowrap="nowrap" >[[Number of Sub-Employers Registered]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.addSubAccount.statistic}</td>
		{/foreach}
	</tr>
	<tr>
		<td nowrap="nowrap" >[[Number of Profiles Deleted]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.deleteUser.statistic}</td>
		{/foreach}
	</tr>
	{/if}
	
	{if $filter.listings == 1}
	<tr><td class="strong text-center">[[Listings]]</td><td colspan="{$countItems - 2}"></td></tr>
	{foreach from=$listingTypes item=listingType name=listings_block}
	<tr>
		<td nowrap="nowrap" >[[Number of {if $listingType.key == 'Job' || $listingType.key == 'Resume'}{$listingType.key}s{else}"{$listingType.caption}" Listings{/if} Posted]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="addListing$listingTypeID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
	</tr>
	{if $listingType.id == 6}
		<tr>
			<td nowrap="nowrap" >[[Number of Featured {if $listingType.key == 'Job' || $listingType.key == 'Resume'}{$listingType.key}s{else}"{$listingType.caption}" Listings{/if} Posted]]</td>
			{assign var=listingTypeID value=$listingType.id}
			{foreach from=$statistics item=statistic key=key}
				{assign var="event" value="addListingFeatured$listingTypeID"}
				<td>{$statistic.$event.statistic}</td>
			{/foreach}
		</tr>
	{/if}
	<tr>
		<td nowrap="nowrap" >[[Number of Priority {if $listingType.key == 'Job' || $listingType.key == 'Resume'}{$listingType.key}s{else}"{$listingType.caption}" Listings{/if} Posted]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="addListingPriority$listingTypeID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
	</tr>
	{/foreach}
	{foreach from=$listingTypes item=listingType}
	<tr>
		<td nowrap="nowrap" >[[Number of {if $listingType.key == 'Job' || $listingType.key == 'Resume'}{$listingType.key}s{else}"{$listingType.caption}" Listings{/if} Deleted]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="deleteListing$listingTypeID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
	</tr>
	{/foreach}
	{/if}
	
	{if $filter.applications == 1}
	<tr><td class="strong text-center">[[Applications]]</td><td colspan="{$countItems - 2}"></td></tr>
	<tr>
		<td nowrap="nowrap" >[[Number of Applications Made]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.apply.statistic}</td>
		{/foreach}
	</tr>
	<tr>
		<td nowrap="nowrap" >[[Number of Applications Approved]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.applyApproved.statistic}</td>
		{/foreach}
	</tr>
	<tr>
		<td nowrap="nowrap" >[[Number of Applications Rejected]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.applyRejected.statistic}</td>
		{/foreach}
	</tr>
	{/if}
	
	{if $filter.alerts == 1}
	<tr><td class="strong text-center">[[Alerts]]</td><td colspan="{$countItems - 2}"></td></tr>
	{foreach from=$listingTypes item=listingType name=alert_block}
	<tr>
		<td nowrap="nowrap" >[[Number of {$listingType.caption} Alerts Subscribed for]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="addAlert$listingTypeID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
	</tr>
	{/foreach}
	{foreach from=$listingTypes item=listingType}
	<tr>
		<td nowrap="nowrap" >[[Number of {$listingType.caption} Alerts Sent]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="sentAlert$listingTypeID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
	</tr>
	{/foreach}
	{/if}
	
	{if $filter.sales == 1}
		<tr>
			<td class="strong text-center">[[Sales]]</td>
			<td colspan="{$countItems - 2}"></td>
		</tr>
		<tr>
			<td>[[Total Sales]]</td>
			{foreach from=$statistics item=statistic key=key}
				<td>
					{capture assign="statisticTotalAmount"}{tr type="float"}{$statistic.totalAmount.statistic}{/tr}{/capture}
					{currencyFormat amount=$statisticTotalAmount}
				</td>
			{/foreach}
		</tr>
		{foreach from=$userGroups item=userGroup}
		<tr>
			<td nowrap="nowrap" >[[Earnings from {if $userGroup.key == 'Employer'}Employers{elseif $userGroup.key == 'JobSeeker'}Job Seekers{else}"{$userGroup.caption}" Users{/if}]]</td>
			{assign var="group_sid" value=$userGroup.id}
			{assign var="event" value="amount_$group_sid"}
			{foreach from=$statistics item=statistic key=key}
				<td>
					{capture assign="earningsFromUsers"}{tr type="float"}{$statistic.$event.statistic}{/tr}{/capture}
					{currencyFormat amount=$earningsFromUsers}
				</td>
			{/foreach}
		</tr>
		{/foreach}
		<tr>
			<td>[[Promotion Discount]]</td>
			{foreach from=$statistics item="statistic" key="key"}
				<td>
					{capture assign="promotionDiscount"}{tr type="float"}{$statistic.promotionUsed.statistic}{/tr}{/capture}
					{currencyFormat amount=$promotionDiscount}
				</td>
			{/foreach}
		</tr>
	{/if}
	
	{if $filter.plugins == 1}
		<tr>
			<td class="strong text-center">[[Plugins]]</td>
			<td colspan="{$countItems - 2}"></td>
		</tr>
		<tr>
			<td nowrap="nowrap" >[[Number of Mobile Version Views]]</td>
			{foreach from=$statistics item=statistic key=key}
				<td>{$statistic.viewMobileVersion.statistic}</td>
			{/foreach}
		</tr>
		<tr>
			<td nowrap="nowrap" >[[Number of Redirects to Partnering Sites]]</td>
			{foreach from=$statistics item=statistic key=key}
				<td>{$statistic.partneringSites.statistic}</td>
			{/foreach}
		</tr>
		<tr>
			<td class="strong text-center">[[Social Plugins]]</td>
			<td colspan="{$countItems - 2}"></td>
		</tr>
		{foreach from=$userGroups item="userGroup"}
			<tr>
				{assign var="userGroupID" value=$userGroup.id}
				<td nowrap="nowrap" >[[Number of "{$userGroup.key}" Users registered through LinkedIn]]</td>
				{foreach from=$statistics item="statistic" key="key"}
					{assign var="event" value="addUserlinkedin$userGroupID"}
					<td>{$statistic.$event.statistic}</td>
				{/foreach}
			</tr>
		{/foreach}
		{foreach from=$userGroups item="userGroup"}
			<tr>
				{assign var="userGroupID" value=$userGroup.id}
				<td nowrap="nowrap" >[[Number of "{$userGroup.caption}" Users registered through Facebook]]</td>
				{foreach from=$statistics item="statistic" key="key"}
					{assign var="event" value="addUserfacebook$userGroupID"}
					<td>{$statistic.$event.statistic}</td>
				{/foreach}
			</tr>
		{/foreach}
		{foreach from=$userGroups item="userGroup"}
			<tr>
				{assign var="userGroupID" value=$userGroup.id}
				<td nowrap="nowrap" >[[Number of "{$userGroup.caption}" Users registered through Google]]</td>
				{foreach from=$statistics item="statistic" key="key"}
					{assign var="event" value="addUsergoogle$userGroupID"}
					<td>{$statistic.$event.statistic}</td>
				{/foreach}
			</tr>
		{/foreach}
	{/if}
</table>
</td></tr>
<tr><td class="no-border">
<input type=button value="[[Print]]" onClick="this.style.display='none';window.print();"></td></tr>
</table>
{/if}
</div>