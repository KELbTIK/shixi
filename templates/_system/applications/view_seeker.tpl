<h1>[[Jobs Applied]]</h1>
<form method="post" name="applicationForm" action="" id="applicationForm">
	<input type="hidden" name="orderBy" value="{$orderBy}" />
	<input type="hidden" name="order" value="{$order}" />
	<input id="action" type="hidden" name="action" value="" />
	<p><input type="submit" value="[[Delete]]"	class="button" onclick="if (confirm('[[Are you sure you want to delete selected application(s)?]]')) submitForm('delete');" /></p>
	
	<table border="0" cellpadding="0" cellspacing="0" class="tableSearchResultApplications" width="100%">
		<thead>
			<tr>
				<th class="tableLeft"> </th>
				<th class="pointedInListingInfo2"><input type="checkbox" id="all_checkboxes_control" /></th>
				<th class="pointedInListingInfo2" width="15%">
					<a href="?orderBy=date&amp;order={if $orderBy == "date" && $order == "asc"}desc{else}asc{/if}">[[Date Applied]]</a>
					{if $orderBy == 'date'}{if $order == 'asc'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
				</th>
				<th class="pointedInListingInfo2">
					<a href="?orderBy=title&amp;order={if $orderBy == "title" && $order == "asc"}desc{else}asc{/if}">&nbsp; [[Job Title]]</a>
					{if $orderBy == 'title'}{if $order == 'asc'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
				</th>
				<th class="pointedInListingInfo2">
					<a href="?orderBy=company&amp;order={if $orderBy == "company" && $order == "asc"}desc{else}asc{/if}">&nbsp; [[Company]]</a>
					{if $orderBy == 'company'}{if $order == 'asc'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
				</th>
				<th class="pointedInListingInfo2">
					<a href="?orderBy=status&amp;order={if $orderBy == "status" && $order == "asc"}desc{else}asc{/if}">&nbsp; [[Status]]</a>
					{if $orderBy == 'status'}{if $order == 'asc'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
				</th>
				<th class="tableRight"> </th>
			</tr>
		</thead>
		{foreach item=application from=$applications name=applications}
		<tr>
			<td>&nbsp;</td>
			<td rowspan="2" class="ApplicationPointedInListingInfo2" width="1"><input type="checkbox" name="applications[{$application.id}]" value="1" id="checkbox_{$smarty.foreach.applications.iteration}" /></td>
			<td class="ApplicationPointedInListingInfo" width="10%">[[$application.date]]</td>
			<td class="ApplicationPointedInListingInfo">{if $application.job != NULL}<a href="{$GLOBALS.site_url}/display-job/{$application.job.sid}/">{$application.job.Title}</a>{else}[[Not Available Anymore]]{/if}</td>
			<td class="ApplicationPointedInListingInfo" width="20%">{$application.company.CompanyName}&nbsp; <br/>
				{if $acl->isAllowed('use_private_messages')}
					<a href="{$GLOBALS.site_url}/private-messages/send/?to={$application.company.sid}" onclick="popUpWindow('{$GLOBALS.site_url}/private-messages/aj-send/?to={$application.company.username}', 700, '[[Send private message]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;"  class="pm_send_link">[[Send private message]]</a>
				{elseif $acl->getPermissionParams('use_private_messages') == "message"}
					<a href="{$GLOBALS.site_url}/private-messages/send/?to={$application.company.sid}" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 400, '[[Send private message]]'); return false;"  class="pm_send_link">[[Send private message]]</a>
				{/if}
			</td>
			<td class="ApplicationPointedInListingInfo" width="10%">[[{$application.status}]]</td>
			<td>&nbsp;</td>
		</tr>
		<tr class="table-application-border-bottom">
			<td colspan="2">&nbsp;</td>
			<td colspan="4" class="ApplicationPointedInListingInfo"><span class="strong">[[Cover Letter]]</span>:<br/>{$application.comments|escape:'html'}<br/><br/></td>
			<td>&nbsp;</td>
		</tr>
		{foreachelse}
		<tr>
			<td colspan="7" class="ApplicationPointedInListingInfo" style="border-left: 1px solid #B2B2B2"><br/><span class="text-center">[[You have no Applications now]]</span><br/></td>
		</tr>
		{/foreach}
	</table>
</form>
<br/>

<script type="text/javascript">
var total = {$smarty.foreach.applications.total};
{literal}

function set_checkbox(param) {
	for (i = 1; i <= total; i++) {
		if (checkbox = document.getElementById('checkbox_' + i))
			checkbox.checked = param;
	}
}

$("#all_checkboxes_control").click(function() {
	if ( this.checked == false)
		set_checkbox(false);
	else
		set_checkbox(true);
});

function submitForm(action) {
	document.getElementById('action').value = action;
	var form = document.applicationForm;
	form.submit();
}

</script>
{/literal}