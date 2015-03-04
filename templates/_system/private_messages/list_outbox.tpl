<div class="numberPerPage">
	<form id="listings_per_page_form" method="get" action="">
		[[Number of messages per page]]:
		<select name="messagesPerPage" onchange="submit()">
			<option value="10" {if $messagesPerPage == 10}selected="selected"{/if}>10</option>
			<option value="20" {if $messagesPerPage == 20}selected="selected"{/if}>20</option>
			<option value="50" {if $messagesPerPage == 50}selected="selected"{/if}>50</option>
			<option value="100" {if $messagesPerPage == 100}selected="selected"{/if}>100</option>
		</select>
	</form>
	<br />
		<span class="prevBtn"><img src="{image}prev_btn.png" alt=""/>
			{if $page-1 > 0}<a href="?page={$page-1}&messagesPerPage={$messagesPerPage}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}
		</span>
		<span class="navigationItems">
			{if $page-3 > 0}<a href="?page=1&messagesPerPage={$messagesPerPage}">1</a>{/if}
			{if $page-3 > 1}...{/if}
			{if $page-2 > 0}<a href="?page={$page-2}&messagesPerPage={$messagesPerPage}">{$page-2}</a>{/if}
			{if $page-1 > 0}<a href="?page={$page-1}&messagesPerPage={$messagesPerPage}">{$page-1}</a>{/if}
			<span class="strong">{$page}</span>
			{if $page+1 <= $totalPages}<a href="?page={$page+1}&messagesPerPage={$messagesPerPage}">{$page+1}</a>{/if}
			{if $page+2 <= $totalPages}<a href="?page={$page+2}&messagesPerPage={$messagesPerPage}">{$page+2}</a>{/if}
			{if $page+3 < $totalPages}...{/if}
			{if $page+3 < $totalPages + 1}<a href="?page={$totalPages}&messagesPerPage={$messagesPerPage}">{$totalPages}</a>{/if}
		</span>
		<span class="nextBtn">{if $page+1 <= $totalPages}<a href="?page={$page+1}&messagesPerPage={$messagesPerPage}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}
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
				<th width="30%">[[To]]</th>
				<th width="50%">[[Subject]]</th>
				<th>[[Date]]</th>
				<th class="tableRight"> </th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$message_list item=one}
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td> </td>
					<td><input type="checkbox" name="pm_check[]" value="{$one.id}" class="pm_checkbox" /></td>
					<td>
						{if $one.anonym && $one.anonym != $GLOBALS.current_user.id}
						[[Anonymous User]]
						{else}
						{$one.to_first_name} {$one.to_last_name}{if $one.to_first_name == '' && $one.to_last_name == ''}{$one.to_name}{/if}
						{/if}

					</td>
					<td><a href="{$GLOBALS.site_url}/private-messages/outbox/read/?id={$one.id}">{$one.subject}</a></td>
					<td>{$one.time|date_format:$GLOBALS.current_language_data.date_format} {$one.time|date_format:"%H:%M:%S"}</td>
					<td> </td>
				</tr>
				<tr>
					<td colspan="6" class="separateListing"> </td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<div class="clr"><br/></div>
	<input type="button" value="[[Delete]]" id="pm_controll_delete" class="button" />
	<div class="pageNavigation">
		<span class="prevBtn">
			{if $page-1 > 0}
				<a href="?page={$page-1}&messagesPerPage={$messagesPerPage}">
					<img src="{image}prev_btn.png" alt="[[Previous]]" border="0"/>
					[[Previous]]
				</a>
			{else}
				<img src="{image}prev_btn.png" alt="[[Previous]]"  border="0" /><a>[[Previous]]</a>{/if}
		</span>
		<span class="navigationItems">
			{if $page-3 > 0}<a href="?page=1&messagesPerPage={$messagesPerPage}">1</a>{/if}
			{if $page-3 > 1}...{/if}
			{if $page-2 > 0}<a href="?page={$page-2}&messagesPerPage={$messagesPerPage}">{$page-2}</a>{/if}
			{if $page-1 > 0}<a href="?page={$page-1}&messagesPerPage={$messagesPerPage}">{$page-1}</a>{/if}
			<span class="strong">{$page}</span>
			{if $page+1 <= $totalPages}<a href="?page={$page+1}&messagesPerPage={$messagesPerPage}">{$page+1}</a>{/if}
			{if $page+2 <= $totalPages}<a href="?page={$page+2}&messagesPerPage={$messagesPerPage}">{$page+2}</a>{/if}
			{if $page+3 < $totalPages}...{/if}
			{if $page+3 < $totalPages + 1}<a href="?page={$totalPages}&messagesPerPage={$messagesPerPage}">{$totalPages}</a>{/if}
		</span>
		<span class="nextBtn">
			{if $page+1 <= $totalPages}
				<a href="?page={$page+1}&messagesPerPage={$messagesPerPage}" >[[Next]]</a> <img src="{image}next_btn.png" alt="[[Next]]"  border="0"/>
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