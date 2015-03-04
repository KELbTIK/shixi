{breadcrumbs}[[Banned IPs]]{/breadcrumbs}
<h1><img src="{image}/icons/usersblock32.png" border="0" alt="" class="titleicon"/>[[Banned IPs]]</h1>
{if $errors}
	{foreach from=$errors key=error item=error_data}
		{if $error == "WRONG_FORMAT"}<p class="error">[[IP format is not valid]]</p>{/if}
		{if $error == "IP_ALREADY_EXIST"}<p class="error">[[IP already banned]]</p>{/if}
		{if $error == "IP_WAS_NOT_BANNED"}<p class="error">[[IP was not banned]]</p>{/if}
		{if $error == "ID_NOT_FOUND"}<p class="error">[[IP not found]]</p>{/if}
		{if $error == "IP_NOT_ENABLED"}<p class="error">[[IP was not unbanned]]</p>{/if}
		{if $error == "IP_NOT_BANNED"}<p class="error">[[IP is not banned]]</p>{/if}
	{/foreach}
{elseif $action == 'ban'}
	<p class="message">[[IP was banned]]</p>
{elseif $action == 'unban'}
	<p class="message">[[IP was unbanned]]</p>
{/if}

<fieldset>
	<legend>[[Ban IP]]</legend>
	<form method="post" name="ban_ip_form">
		<input type="hidden" name="action" value="ban" />
		<input type="hidden" name="ip_per_page" value="{$ip_per_page}" />
		<input type="text" name="banned_ip" class="inputText"  />
		<span class="greenButtonEnd"><input type="submit" name="bun" value="[[Ban]]" class="greenButton" /></span><br/>
		<small>[[* wildcard for replacing one or several digits. E.g.: 192.168.*.*]]</small>
	</form>
</fieldset>

<div class="clr"><br/></div>
<div class="actionSelected">
	<strong>[[Number of ips per page]]:</strong>
	<select id="ip_per_page" name="ip_per_page" onchange="window.location = '?ip_per_page='+this.value;" class="perPage">
		<option value="10" {if $ip_per_page == 10}selected="selected"{/if}>10</option>
		<option value="20" {if $ip_per_page == 20}selected="selected"{/if}>20</option>
		<option value="50" {if $ip_per_page == 50}selected="selected"{/if}>50</option>
		<option value="100" {if $ip_per_page == 100}selected="selected"{/if}>100</option>
	</select>	
</div>
<div class="numberPerPage">
	{foreach from=$pages item=page}
		{if $page == $currentPage}
			<strong>{$page}</strong>
		{else}
			{if $page == $totalPages && $currentPage < $totalPages-3} ... {/if}
			<a href="?page={$page}{if $sort.field ne null}&sort[field]={$sort.field}{/if}{if $sort.order ne null}&sort[order]={$sort.order}{/if}&ip_per_page={$ip_per_page}">{$page}</a>
			{if $page == 1 && $currentPage > 4} ... {/if}
		{/if}
	{/foreach}
</div>
<div class="clr"><br/></div>

<table>
	<thead>
		<tr>
			<th>
				<a href="?sort[field]=value&sort[order]={if $sort.order == 'ASC' && $sort.field == 'value'}DESC{else}ASC{/if}&ip_per_page={$ip_per_page}">[[IP]]</a>
				{if $sort.field == 'value'}{if $sorting_order == 'ASC'}<img src="{image}b_down_arrow.gif">{else}<img src="{image}b_up_arrow.gif">{/if}{/if}
				</th>
			<th class="actions" nowrap="nowrap">[[Actions]]</th>
		</tr>
	</thead>
	{foreach from=$bannedIPs item=ip}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>{$ip.value}</td>
			<td nowrap="nowrap"><span class="greenButtonEnd"><input type="button" name="button" value="[[Unban]]" class="greenButton" onclick="window.location='?id={$ip.id}&action=unban&page={$page}&ip_per_page={$ip_per_page}'" /></span></td>
		</tr>
	{/foreach}
</table>