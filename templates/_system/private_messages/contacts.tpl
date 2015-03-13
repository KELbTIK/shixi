<div class="numberPerPage sorting-filters">
	<form id="listings_per_page_form" method="get" class="form-inline pull-left" action="">
		<label>[[Number of messages per page]]:</label>
		<select class="form-control" name="contactsPerPage" onchange="submit()">
			<option value="10" {if $contactsPerPage == 10}selected="selected"{/if}>10</option>
			<option value="20" {if $contactsPerPage == 20}selected="selected"{/if}>20</option>
			<option value="50" {if $contactsPerPage == 50}selected="selected"{/if}>50</option>
			<option value="100" {if $contactsPerPage == 100}selected="selected"{/if}>100</option>
		</select>
	</form>
	<ul class="pagination pull-right">
		<li class="prevBtn">
			{if $page-1 > 0}<a href="?page={$page-1}&messagesPerPage={$messagesPerPage}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}
		</li>
		<li class="navigationItems">
			{if $page-3 > 0}<a href="?page=1&messagesPerPage={$messagesPerPage}">1</a>{/if}
			{if $page-3 > 1}...{/if}
			{if $page-2 > 0}<a href="?page={$page-2}&messagesPerPage={$messagesPerPage}">{$page-2}</a>{/if}
			{if $page-1 > 0}<a href="?page={$page-1}&messagesPerPage={$messagesPerPage}">{$page-1}</a>{/if}
			<a href="#">{$page}</a>
			{if $page+1 <= $totalPages}<a href="?page={$page+1}&messagesPerPage={$messagesPerPage}">{$page+1}</a>{/if}
			{if $page+2 <= $totalPages}<a href="?page={$page+2}&messagesPerPage={$messagesPerPage}">{$page+2}</a>{/if}
			{if $page+3 < $totalPages}...{/if}
			{if $page+3 < $totalPages + 1}<a href="?page={$totalPages}&messagesPerPage={$messagesPerPage}">{$totalPages}</a>{/if}
		</li>
		<li class="nextBtn">{if $page+1 <= $totalPages}<a href="?page={$page+1}&messagesPerPage={$messagesPerPage}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}
		</li>
	</ul>
	<div class="clearfix"></div>
</div>
<form action="" method="post" id="pm_form">
	<input type="hidden" id="pm_action" name="pm_action" value="" />
	<div class="table-responsive">
		<table class="table table-condensed">
			<thead>
			<tr>
				<th class="tableLeft"> </th>
				<th><input type="checkbox" id="pm_all_check" /></th>
				<th>[[Name]]</th>
				<th>[[Email]]</th>
				<th>[[Phone Number]]</th>
				<th> </th>
				<th class="tableRight"> </th>
			</tr>
			</thead>
			<tbody>
			{foreach from=$message_list item=one}
				<tr class="{cycle values = 'evenrow,oddrow' advance=true} table-contacts">
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
					<td><input class="btn btn-success btn-sm" type="button" name="send-message" value="[[Send private message]]" onclick="javascript:location.href='{$GLOBALS.site_url}/private-messages/send/?to={$one.sid}'"/></td>
					<td>
						{if $one.status == 0}<i class="fa fa-envelope lightblue"></i>
						{elseif $one.status == 1}<i class="fa fa-envelope"></i>
						{elseif $one.status == 2}<i class="fa fa-envelope lightgray"></i>
						{/if}
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	<ul class="pagination">
		<li class="prevBtn">
			{if $page-1 > 0}
				<a href="?page={$page-1}&messagesPerPage={$messagesPerPage}">
					[[Previous]]
				</a>
			{else}
				<a>[[Previous]]</a>
			{/if}
		</li>
		<li class="navigationItems">
			{if $page-3 > 0}<a href="?page=1&messagesPerPage={$messagesPerPage}">1</a>{/if}
			{if $page-3 > 1}...{/if}
			{if $page-2 > 0}<a href="?page={$page-2}&messagesPerPage={$messagesPerPage}">{$page-2}</a>{/if}
			{if $page-1 > 0}<a href="?page={$page-1}&messagesPerPage={$messagesPerPage}">{$page-1}</a>{/if}
			<a href="#">{$page}</a>
			{if $page+1 <= $totalPages}<a href="?page={$page+1}&messagesPerPage={$messagesPerPage}">{$page+1}</a>{/if}
			{if $page+2 <= $totalPages}<a href="?page={$page+2}&messagesPerPage={$messagesPerPage}">{$page+2}</a>{/if}
			{if $page+3 < $totalPages}...{/if}
			{if $page+3 < $totalPages + 1}<a href="?page={$totalPages}&messagesPerPage={$messagesPerPage}">{$totalPages}</a>{/if}
		</li>
		<li class="nextBtn">
			{if $page+1 <= $totalPages}
				<a href="?page={$page+1}&messagesPerPage={$messagesPerPage}" >[[Next]]</a>
			{else}
				<a>[[Next]]</a>
			{/if}
		</li>
	</ul>
	<div class="clearfix"></div>
	<input type="button" class="btn btn-default btn-sm" value="[[Delete]]" id="pm_controll_delete" />
</form>
<script type="text/javascript" language="JavaScript">
	function submit() {
		var form = document.getElementById("listings_per_page_form");
		form.submit();
	}
</script>