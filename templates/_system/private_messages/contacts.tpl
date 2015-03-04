<div class="numberPerPage">
	<form id="listings_per_page_form" method="get" action="">
			[[Number of contacts per page]]:
		<select name="contactsPerPage" onchange="submit()">
			<option value="10" {if $contactsPerPage == 10}selected="selected"{/if}>10</option>
			<option value="20" {if $contactsPerPage == 20}selected="selected"{/if}>20</option>
			<option value="50" {if $contactsPerPage == 50}selected="selected"{/if}>50</option>
			<option value="100" {if $contactsPerPage == 100}selected="selected"{/if}>100</option>
		</select>
	</form>
	<br />
		<span class="prevBtn"><img src="{image}prev_btn.png" alt=""/>
			{if $page-1 > 0}<a href="?page={$page-1}&contactsPerPage={$contactsPerPage}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}
		</span>
		<span class="navigationItems">
			{if $page-3 > 0}<a href="?page=1&contactsPerPage={$contactsPerPage}">1</a>{/if}
			{if $page-3 > 1}...{/if}
			{if $page-2 > 0}<a href="?page={$page-2}&contactsPerPage={$contactsPerPage}">{$page-2}</a>{/if}
			{if $page-1 > 0}<a href="?page={$page-1}&contactsPerPage={$contactsPerPage}">{$page-1}</a>{/if}
			<span class="strong">{$page}</span>
			{if $page+1 <= $totalPages}<a href="?page={$page+1}&contactsPerPage={$contactsPerPage}">{$page+1}</a>{/if}
			{if $page+2 <= $totalPages}<a href="?page={$page+2}&contactsPerPage={$contactsPerPage}">{$page+2}</a>{/if}
			{if $page+3 < $totalPages}...{/if}
			{if $page+3 < $totalPages + 1}<a href="?page={$totalPages}&contactsPerPage={$contactsPerPage}">{$totalPages}</a>{/if}
		</span>
		<span class="nextBtn">{if $page+1 <= $totalPages}<a href="?page={$page+1}&contactsPerPage={$contactsPerPage}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}
			<img src="{image}next_btn.png"  alt=""/>
		</span>
</div>
<div class="clr"><br/></div>
<form action="" method="post" id="pm_form">
	<input type="hidden" id="pm_action" name="pm_action" value="" />
	<table cellspacing="0">
		<thead>
			<tr>
				<th class="tableLeft"> </th>
				<th width="1"><input type="checkbox" id="pm_all_check" /></th>
				<th width="30%">[[Name]]</th>
				<th width="40%">[[Email]]</th>
				<th width="15%">[[Phone Number]]</th>
				<th> </th>
				<th class="tableRight"> </th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$message_list item=one}
			<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
				<td> </td>
				<td><input type="checkbox" name="pm_check[]" value="{$one.sid}" class="pm_checkbox" /></td>
				<td>
					{if $one.user_group_id eq "Employer"}
						<a href="{$GLOBALS.site_url}/private-messages/contact/{$one.sid}/">{$one.ContactName}</a>
					{elseif $one.user_group_id eq "JobSeeker"}
						<a href="{$GLOBALS.site_url}/private-messages/contact/{$one.sid}/">{$one.FirstName} {$one.LastName}</a>
					{else}
						<a href="{$GLOBALS.site_url}/private-messages/contact/{$one.sid}/">{$one.username}</a>
					{/if}
				</td>
				<td>
					<a href="{$GLOBALS.site_url}/private-messages/contact/{$one.sid}/"><span class="longtext-40">{$one.email}</span></a>
				</td>
				<td>{$one.PhoneNumber}</td>
				<td><input type="button" name="send-message" value="[[Send private message]]" onclick="javascript:location.href='{$GLOBALS.site_url}/private-messages/send/?to={$one.sid}'"/></td>
				<td> </td>
			</tr>
			<tr>
				<td colspan="7" class="separateListing"> </td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<div class="clr"><br/></div>
	<input type="button" class="button" value="[[Delete]]" id="pm_controll_delete" />
	<div class="pageNavigation">
		<span class="prevBtn">
			{if $page-1 > 0}
				<a href="?page={$page-1}&contactsPerPage={$contactsPerPage}">
					<img src="{image}prev_btn.png" alt="[[Previous]]" border="0"/>
					[[Previous]]
				</a>
			{else}
				<img src="{image}prev_btn.png" alt="[[Previous]]"  border="0" /><a>[[Previous]]</a>
			{/if}
		</span>
		<span class="navigationItems">
			{if $page-3 > 0}<a href="?page=1&contactsPerPage={$contactsPerPage}">1</a>{/if}
			{if $page-3 > 1}...{/if}
			{if $page-2 > 0}<a href="?page={$page-2}&contactsPerPage={$contactsPerPage}">{$page-2}</a>{/if}
			{if $page-1 > 0}<a href="?page={$page-1}&contactsPerPage={$contactsPerPage}">{$page-1}</a>{/if}
			<span class="strong">{$page}</span>
			{if $page+1 <= $totalPages}<a href="?page={$page+1}&contactsPerPage={$contactsPerPage}">{$page+1}</a>{/if}
			{if $page+2 <= $totalPages}<a href="?page={$page+2}&contactsPerPage={$contactsPerPage}">{$page+2}</a>{/if}
			{if $page+3 < $totalPages}...{/if}
			{if $page+3 < $totalPages + 1}<a href="?page={$totalPages}&contactsPerPage={$contactsPerPage}">{$totalPages}</a>{/if}
		</span>
		<span class="nextBtn">
			{if $page+1 <= $totalPages}
				<a href="?page={$page+1}&contactsPerPage={$contactsPerPage}" >[[Next]]</a> <img src="{image}next_btn.png" alt="[[Next]]"  border="0"/>
			{else}
				<a>[[Next]]</a> <img src="{image}next_btn.png" alt="[[Next]]" border="0"/>
			{/if}
		</span>
	</div>
</form>

<script type="text/javascript" language="JavaScript">
	function submit() {
		var form = document.getElementById("listings_per_page_form");
		form.submit();
	}
</script>