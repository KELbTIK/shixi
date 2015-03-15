{breadcrumbs}[[Dashboard]]{/breadcrumbs}

<div id="stats">
	<div id="statblocks">
		{foreach key=key name=outer item=invoicePeriod from=$invoicesInfo}
			{if $smarty.foreach.outer.last}
				<div class="statblock">
					{foreach key=key1 name="invoiceInfo" item=invoiceInfo from=$invoicePeriod}
						{foreach key=key2 item=Info from=$invoiceInfo}
							{if $smarty.foreach.invoiceInfo.first}
								<h2>
									<a href="{$GLOBALS.site_url}/manage-invoices/?date%5Bnot_less%5D={if $key == "Today"}{$today}{/if}{if $key == "This Week"}{$weekAgo}{/if}{if $key == "This Month"}{$monthAgo}{/if}&amp;date%5Bnot_more%5D={$today}&amp;action=search&amp;status%5Bequal%5D=Paid">
										{capture assign="earningsForPeriod"}{tr type="float"}{$Info.payment}{/tr}{/capture}
										{currencyFormat amount=$earningsForPeriod}
									</a>
								</h2>
							{/if}
						{/foreach}
					{/foreach}
					<p>[[{$key}]]</p>
				</div>
			{/if}
		{/foreach}
		<div class="statblock">
			<h2>
				<a href="{$GLOBALS.site_url}/manage-invoices/?date%5Bnot_less%5D=&amp;date%5Bnot_more%5D=&amp;action=search&amp;status%5Bequal%5D=Paid">
					{capture assign="totalEarnings"}{tr type="float"}{$totalInvoices}{/tr}{/capture}
					{currencyFormat amount=$totalEarnings}
				</a>
			</h2>
			<p>[[Total Earnings]]</p>
		</div>


		{foreach key=key name=outer item=groupInfo from=$groupsInfo}
			<div class="statblock">
				<h2>
					<a href="{$GLOBALS.site_url}/manage-users/{$key|lower}/?user_group%5Bequal%5D={$key}&amp;action=search">{$groupInfo.total.count}</a>
				</h2>
				<p>[[{$groupInfo.caption}s]]</p>
			</div>
		{/foreach}

		{foreach key=key name=outer item=listingInfo from=$listingsInfo}
			<div class="statblock">
				<h2>
					<a href="{$GLOBALS.site_url}/manage-{if $key !='Job' && $key !='Resume'}{$key|lower}-listings{else}{$key|lower}s{/if}/?action=search&amp;active%5Bequal%5D=1">{$listingInfo.total.active}</a>
				</h2>
				<p>[[{$key}s]]</p>
			</div>
		{/foreach}

		{foreach key=key name=outer item=listingInfo from=$listingsInfo}
			{if $smarty.foreach.outer.first}
				{if $totalFlagsNum.$key > 0}
					<div class="statblock">
						<h2><a href="{$GLOBALS.site_url}/flagged-listings/?listing_type_id={$key}"><strong style="color: #C00;">{$totalFlagsNum.$key}</strong></a></h2>
						<p>[[Flagged Listings]]</p>
					</div>
				{/if}
			{/if}
		{/foreach}
	</div>
</div>

<div id="dashboard">
<div class="dashboardBlocks">
	<div class="box">
		<div class="box-header"><h1 class="usersOnline">[[Users online]]</h1></div>
		<div class="innerpadding">
			<table width="100%">
				<tbody>
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td><strong>[[Total Users]]</strong></td>
					{if $onlineUsers}
						<td align="center">{$totalOnlineUsers} [[online]]</td>
					{else}
						<td align="center">0</td>
					{/if}
				</tr>
				{if $onlineUsers}
					{foreach key=key name=outer item=value from=$onlineUsers}
						<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
							<td><strong>[[{$value.caption}]]</strong></td>
							<td align="center">
								<a href="{$GLOBALS.site_url}/manage-users/{$key|lower}?user_group%5Bequal%5D={$key}&amp;online=1&amp;action=search">{$value.count} [[online]]</a>
							</td>
						</tr>
					{/foreach}
				{else}
					<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
						<td colspan="2"><strong>[[No online users]]</strong></td>
					</tr>
				{/if}
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="dashboardBlocks">
	<div class="box">
		<div class="box-header">
			<h1 class="payments">
				<a href="{$GLOBALS.site_url}/manage-invoices/?date%5Bnot_less%5D=&amp;date%5Bnot_more%5D=&amp;action=search&amp;status%5Bequal%5D=">[[Payments]]</a>
			</h1>
		</div>
		<div class="innerpadding">
			<table width="100%">
				<thead>
				<tr>
					<th align="center">[[Period]]</th>
					<th align="center">[[Paid]]</th>
					<th align="center">[[Unpaid]]</th>
				</tr>
				</thead>
				<tbody>
				{foreach key=key name=outer item=invoicePeriod from=$invoicesInfo}
					<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
						<td>[[{$key}]]</td>
						{foreach key=key1 item=invoiceInfo from=$invoicePeriod}
							{foreach key=key2 item=Info from=$invoiceInfo}
								<td align="center">
									<a href="{$GLOBALS.site_url}/manage-invoices/?date%5Bnot_less%5D={if $key == "Today"}{$today}{/if}{if $key == "This Week"}{$weekAgo}{/if}{if $key == "This Month"}{$monthAgo}{/if}&amp;date%5Bnot_more%5D={$today}&amp;action=search&amp;status%5Bequal%5D={if $key1 =="paid"}Paid{else}Unpaid{/if}">
										{capture assign="paymentAmount"}{tr type="float"}{$Info.payment}{/tr}{/capture}
										{currencyFormat amount=$paymentAmount}
									</a>
								</td>
							{/foreach}
						{/foreach}
					</tr>
				{/foreach}
				<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
					<td><strong>[[Total]]</strong></td>
					<td align="center">
						<strong>
							<a href="{$GLOBALS.site_url}/manage-invoices/?date%5Bnot_less%5D=&amp;date%5Bnot_more%5D=&amp;action=search&amp;status%5Bequal%5D=Paid">
								{capture assign="totalPaidInvoices"}{tr type="float"}{$totalInvoices}{/tr}{/capture}
								<b>{currencyFormat amount=$totalPaidInvoices}</b>
							</a>
						</strong>
					</td>
					<td align="center">
						<strong>
							<a href="{$GLOBALS.site_url}/manage-invoices/?date%5Bnot_less%5D=&amp;date%5Bnot_more%5D=&amp;action=search&amp;status%5Bequal%5D=Unpaid">
								{capture assign="totalUnpaidInvoices"}{tr type="float"}{$unpaidInvoices}{/tr}{/capture}
								<b>{currencyFormat amount=$totalUnpaidInvoices}</b>
							</a>
						</strong>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="clr"><br/></div>

<div class="dashboardBlocks">
	<div class="box">
		<div class="box-header"><h1 class="registered">[[Registered Users]]: {$usersInfo.count}</h1></div>
		<div class="innerpadding">
			{foreach key=key name=outer item=groupInfo from=$groupsInfo}
				{assign var="users_url" value="manage-users/{$key|lower}"}
				{if $groupInfo.approveInfo neq ''}
					{if $groupInfo.approveInfo.Pending neq ''}
						<a href="{$GLOBALS.site_url}/{$users_url}/?approval%5Bequal%5D=pending"><strong>[[Waiting for approval]]: {$groupInfo.approveInfo.Pending}</strong></a>
					{else}
						<strong>[[Waiting for approval]]: 0</strong>
					{/if}
				{/if}
				<table width="100%">
					<thead>
					<tr>
						<th><b>[[{$groupInfo.caption}]]</b></th>
						<th align="center">[[Active]]</th>
						<th align="center">[[Not active]]</th>
						<th align="center">[[Total]]</th>
					</tr>
					</thead>
					<tbody>
					{foreach item=group key=period from=$groupInfo.periods}
						<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
							<td>[[{$period}]]</td>
							<td align="center"><a href="{$GLOBALS.site_url}/{$users_url}/?action=search&amp;active%5Bequal%5D=1&amp;registration_date%5Bnot_less%5D={if $period == "Today"}{$today}{/if}{if $period == "This Week"}{$weekAgo}{/if}{if $period == "This Month"}{$monthAgo}{/if}">{$group.active}</a></td>
							<td align="center"><a href="{$GLOBALS.site_url}/{$users_url}/?action=search&amp;active%5Bequal%5D=0&amp;registration_date%5Bnot_less%5D={if $period == "Today"}{$today}{/if}{if $period == "This Week"}{$weekAgo}{/if}{if $period == "This Month"}{$monthAgo}{/if}">{$group.count-$group.active}</a></td>
							<td align="center"><a href="{$GLOBALS.site_url}/{$users_url}/?action=search&amp;registration_date%5Bnot_less%5D={if $period == "Today"}{$today}{/if}{if $period == "This Week"}{$weekAgo}{/if}{if $period == "This Month"}{$monthAgo}{/if}">{$group.count}</a></td>
						</tr>
					{/foreach}
					<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
						<td><strong>[[Totals]]</strong></td>
						<td align="center"><strong><a href="{$GLOBALS.site_url}/{$users_url}/?action=search&amp;active%5Bequal%5D=1">{$groupInfo.total.active}</a></strong></td>
						<td align="center"><strong><a href="{$GLOBALS.site_url}/{$users_url}/?action=search&amp;active%5Bequal%5D=0">{$groupInfo.total.count-$groupInfo.total.active}</a></strong></td>
						<td align="center"><strong><a href="{$GLOBALS.site_url}/{$users_url}/?action=search">{$groupInfo.total.count}</a></strong></td>
					</tr>
					</tbody>
				</table>
				<br/>
			{/foreach}
		</div>
	</div>
</div>

<div class="dashboardBlocks">
	<div class="box">
		{assign var="totalPostings" value="0"}
		{foreach key=key name=outer item=listingInfo from=$listingsInfo}
			{assign var="totalPostings" value="`$listingInfo.total.count+$totalPostings`"}
		{/foreach}

		<div class="box-header"><h1 class="postings">[[Postings]]: {$totalPostings}</h1></div>
		<div class="innerpadding">
			{foreach key=key name=outer item=listingInfo from=$listingsInfo}
				{assign var="totalPostings" value="`$listingInfo.total.count+$totalPostings`"}
			{/foreach}

			{foreach key=key name=outer item=listingInfo from=$listingsInfo}
				{if $key !='Job' && $key !='Resume'}
					{capture name="listingType"}{$key|lower}-listings{/capture}
				{else}
					{capture name="listingType"}{$key|lower}s{/capture}
				{/if}
				{if $listingInfo.approveInfo neq ''}
					{if $listingInfo.approveInfo.pending neq ''}
						<a href="{$GLOBALS.site_url}/manage-{$smarty.capture.listingType}/?action=search&amp;status%5Bequal%5D=pending"><strong>[[Waiting for approval]]: {$listingInfo.approveInfo.pending}</strong></a>
					{else}
						<strong>[[Waiting for approval]]: 0</strong>
					{/if}
				{/if}
				<table width="100%">
					<thead>
					<tr class="headrow">
						<th>
							[[{$key}]]<br/>
							{if $totalFlagsNum.$key > 0}<a href="{$GLOBALS.site_url}/flagged-listings/?listing_type_id={$key}"><strong>[[Flagged]]: {$totalFlagsNum.$key}</strong></a>{/if}
						</th>
						<th>[[Active]]</th>
						<th>[[Not active]]</th>
						<th>[[Total]]</th>
					</tr>
					</thead>
					<tbody>
					{foreach item=listingType key=period from=$listingInfo.periods}
						<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
							<td>[[{$period}]]</td>
							<td align="center"><a href="{$GLOBALS.site_url}/manage-{$smarty.capture.listingType}/?active[equal]=1&amp;action=search&amp;activation_date[not_less]={if $period == "Today"}{$today}{/if}{if $period == "This Week"}{$weekAgo}{/if}{if $period == "This Month"}{$monthAgo}{/if}"><b>{$listingType.active}</b></a></td>
							<td align="center"><a href="{$GLOBALS.site_url}/manage-{$smarty.capture.listingType}/?active[equal]=0&amp;action=search&amp;activation_date[not_less]={if $period == "Today"}{$today}{/if}{if $period == "This Week"}{$weekAgo}{/if}{if $period == "This Month"}{$monthAgo}{/if}"><b>{$listingType.count-$listingType.active}</b></a></td>
							<td align="center"><a href="{$GLOBALS.site_url}/manage-{$smarty.capture.listingType}/?action=search&amp;activation_date[not_less]={if $period == "Today"}{$today}{/if}{if $period == "This Week"}{$weekAgo}{/if}{if $period == "This Month"}{$monthAgo}{/if}"><b>{$listingType.count}</b></a></td>
						</tr>
					{/foreach}
					<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
						<td><strong>[[Totals]]</strong></td>
						<td align="center"><strong><a href="{$GLOBALS.site_url}/manage-{$smarty.capture.listingType}/?action=search&amp;active%5Bequal%5D=1">{$listingInfo.total.active}</a></strong></td>
						<td align="center"><strong><a href="{$GLOBALS.site_url}/manage-{$smarty.capture.listingType}/?action=search&amp;active%5Bequal%5D=0">{$listingInfo.total.count-$listingInfo.total.active}</a></strong></td>
						<td align="center"><strong><a href="{$GLOBALS.site_url}/manage-{$smarty.capture.listingType}/?action=search">{$listingInfo.total.count}</a></strong></td>
					</tr>
					</tbody>
				</table>
				<br/>
			{/foreach}
		</div>
	</div>
</div>

<div class="clr"><br/></div>

<div class="dashboardBlocks">
	<div class="box">
		<div class="box-header"><h1 class="quickLinks">[[Quick links]]</h1></div>
		<div class="innerpadding">
			<table width="100%">
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td><a href="http://wiki.shixi.com/" target="_blank">[[User Manual]]</a></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td><a href="{$GLOBALS.site_url}/upload-logo/">[[Upload your logo]]</a></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td><a href="{$GLOBALS.site_url}/edit-listing-field/edit-list/?field_sid=198">[[Edit job categories list]]</a></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td><a href="{$GLOBALS.site_url}/countries/">[[Edit countries list]]</a></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td><a href="{$GLOBALS.site_url}/edit-templates/?module_name=main&amp;template_name=main.tpl">[[Edit Home page template]]</a></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td><a href="{$GLOBALS.site_url}/edit-templates/?module_name=main&amp;template_name=index.tpl">[[Edit all pages template]]</a></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td><a href="{$GLOBALS.site_url}/edit-css/?action=edit&amp;file={$file}">[[Edit CSS file]]</a></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td><a href="{$GLOBALS.site_url}/edit-css/?action=edit&amp;file=../templates/_system/main/images/css/form.css">[[Edit Forms CSS file]]</a></td>
				</tr>
			</table>
		</div>
	</div>
</div>


<div class="dashboardBlocks">
	<div class="box">
		<div class="box-header"><h1 class="updates">[[Updates]]</h1></div>
		<div class="innerpadding">
			<table width="100%">
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td>[[Current version]]</td>
					<td>{$GLOBALS.version.major}.{$GLOBALS.version.minor} [[build]] {$GLOBALS.version.build}</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td>[[New update available]]</td>
					<td id="updateInfoBlockDashboard">[[N/A]]</td>
				</tr>

				<tr>
					<td colspan="2" id="updateButtonBlockDashboard">
						<div style="text-align: center; position: relative;" id="updateProgress">
							<img src="{$GLOBALS.user_site_url}/templates/_system/main/images/ajax_preloader_circular_16.gif" alt="[[Please wait ...]]" />
						</div>
						<small id="updateNone" style="color: green; display: none;">[[You have the latest version of SJB installed. No need to update.]]</small>
						<small id="updateDeprecated" style="color: red; display: none;">[[Your current version is outdated and doesn't have fresh updates. Please upgrade to the latest version of Shixi.com]]</small>
						<small id="updateError" style="color: red; display: none;">[[Update check failed]]</small>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<div class="clr"><br/></div>
<br />
</div>