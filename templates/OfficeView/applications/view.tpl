<h1>[[Application Tracking]]</h1>
{if $errors}
	{foreach from=$errors key=error_code item=error_message}
			{if $error_code == 'NO_SUCH_FILE'} <p class="error">[[No such file found in the system]]</p>
			{elseif $error_code == 'NO_SUCH_APPS'} <p class="error">[[No such application with this ID]]</p>
			{elseif $error_code == 'NOT_OWNER_OF_APPLICATIONS'} <p class="error">[[There are no applications for "$message.listingTitle" listing]]</p>
			{elseif $error_code == 'APPLICATIONS_NOT_FOUND'}
				{if $score == 'passed'} <p class="error">[[There are no applications with "Passed" score]]</p>
				{elseif $score == 'not_passed'} <p class="error">[[There are no applications with "Not Passed" score]]</p>
				{/if}
			{/if}
	{/foreach}
{/if}
<div class="app-tracking">
	<form method="post" name="applicationFilter" action=""  id="applicationFilter">
		<input type="hidden" name="orderBy" value="{$orderBy|escape:'html'}" />
		<input type="hidden" name="order" value="{$order}" />
		<input type="hidden" name="appsPerPage" value="{$appsPerPage}" />
	[[Select Job Posting]]
		<select name="appJobId">
			<option value="">[[All Jobs]]</option>
		{foreach from=$appJobs item=appJob}
			<option value="{$appJob.id}"{if $appJob.id == $current_filter} selected="selected"{/if}>{$appJob.title}</option>
		{/foreach}
		</select>
		{if $acl->isAllowed("use_screening_questionnaires")}
		<select name="score">
			<option value="">[[Any Score]]</option>
			<option value="passed" {if $score == 'passed'} selected="selected"{/if}>[[Passed]]</option>
			<option value="not_passed" {if $score == 'not_passed'} selected="selected"{/if}>[[Not passed]]</option>
		</select>
		{/if}
	<input type="submit" name="applicationFilterSubmit" value="[[Filter]]" class="button" />
	</form>

	<form method="post" name="applicationForm" action="">
		<input type="hidden" name="orderBy" value="{$orderBy|escape:'html'}" />
		<input type="hidden" name="order" value="{$order}" />
		<input type="hidden" name="appJobId" value="{$current_filter}" />
		<input id="action" type="hidden" name="action" value="" />
		<p><input type="submit" value="[[Approve selected]]" onclick="submitForm('approve'); return false;" /> &nbsp; <input type="submit" value="[[Reject selected]]" onclick="submitForm('reject')" /> &nbsp; <input type="submit" value="[[Delete]]" onclick="if (confirm('[[Are you sure you want to delete selected application(s)?]]')) submitForm('delete');" /></p>
</div>
<div class="navigation-application">
	<div class="numberPerPage">
		[[Number of listings per page]]:
		<select class="phrases_per_page perPage" name="phrases_per_page" onchange="window.location = '?page=1&amp;appsPerPage='+ this.value +'&amp;score={$score}&amp;appJobId={$current_filter}&amp;orderBy={$orderBy|escape:'html'}&amp;order={$order}'" >
			<option value="10" {if $appsPerPage == 10}selected="selected"{/if}>10</option>
			<option value="20" {if $appsPerPage == 20}selected="selected"{/if}>20</option>
			<option value="50" {if $appsPerPage == 50}selected="selected"{/if}>50</option>
			<option value="100" {if $appsPerPage == 100}selected="selected"{/if}>100</option>
		</select>
	</div>
	<br />

	{if count($pages) != 1}
		<div class="pageNavigation" style="float:right; margin: 10px 60px 10px 0;">
			<span class="prevBtn">
				<img src="{image}prev_btn.png" alt=""/>
				{if $currentPage - 1 > 0}
					<a href="?page={$currentPage - 1}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;appJobId={$current_filter}&amp;orderBy={$orderBy|escape:'html'}&amp;order={$order}">[[Previous]]</a>
				{else}
					<a>[[Previous]]</a>
				{/if}
			</span>
			<span class="navigationItems">
				{foreach from=$pages item=page}
					{if $page == $currentPage}
						<span class="strong">{$page}</span>
					{else}
						{if $page == $totalPages && $currentPage < $totalPages-3} ... {/if}
						<a href="?page={$page}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;appJobId={$current_filter}&amp;orderBy={$orderBy|escape:'html'}&amp;order={$order}">{$page}</a>
						{if $page == 1 && $currentPage > 4} ... {/if}
					{/if}
				{/foreach}
			</span>
			<span class="nextBtn">
				{if $currentPage + 1 < $totalPages}
					<a href="?page={$currentPage + 1}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;appJobId={$current_filter}&amp;orderBy={$orderBy|escape:'html'}&amp;order={$order}">[[Next]]</a>
				{else}
					<a>[[Next]]</a>{/if}
				<img src="{image}next_btn.png"  alt=""/>
			</span>
		</div>
	{/if}
</div>

<table border="0" cellpadding="0" cellspacing="0" class="tableSearchResultApplications" width="100%">
	<thead>
	<tr>
		<th class="tableLeft"> </th>
		<th class="pointedInListingInfo2"><input type="checkbox" id="all_checkboxes_control"></th>
		<th class="pointedInListingInfo2" width="15%">
			<a href="?page={$currentPage}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;orderBy=date&amp;order={if $orderBy == 'date' && $order == 'asc'}desc{else}asc{/if}{if $current_filter}&amp;appJobId={$current_filter}{/if}">[[Date Applied]]</a>
			{if $orderBy == 'date'}{if $order == 'asc'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
		</th>
		<th class="pointedInListingInfo2">
			<a href="?page={$currentPage}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;orderBy=applicant&amp;order={if $orderBy == 'applicant' && $order == 'asc'}desc{else}asc{/if}{if $current_filter}&amp;appJobId={$current_filter}{/if}">[[Applicant]]</a>
			{if $orderBy == 'applicant'}{if $order == 'asc'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
		</th>
		<th class="pointedInListingInfo2"><a href="#">[[Resume]]</a></th>
		<th class="pointedInListingInfo2">
			<a href="?page={$currentPage}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;orderBy=title&amp;order={if $orderBy == 'title' && $order == 'asc'}desc{else}asc{/if}{if $current_filter}&amp;appJobId={$current_filter}{/if}">[[Job]]</a>
			{if $orderBy == 'title'}{if $order == 'asc'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
		</th>
		{if $acl->isAllowed('use_screening_questionnaires')}
			<th class="pointedInListingInfo2">
				<a href="?page={$currentPage}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;orderBy=score&amp;order={if $orderBy == 'score' && $order == 'asc'}desc{else}asc{/if}{if $current_filter}&amp;appJobId={$current_filter}{/if}">[[Score]]</a>
				{if $orderBy == 'score'}{if $order == 'asc'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
			</th>
		{/if}
		<th class="pointedInListingInfo2">
			<a href="?page={$currentPage}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;orderBy=status&amp;order={if $orderBy == 'status' && $order == 'asc'}desc{else}asc{/if}{if $current_filter}&amp;appJobId={$current_filter}{/if}">[[Status]]</a>
			{if $orderBy == 'status'}{if $order == 'asc'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
		</th>
		<th class="pointedInListingInfo2"><a href="#">[[Actions]]</a></th>
		<th class="tableRight"> </th>
	</tr>
	</thead>
	{foreach item=application from=$applications name=applications}
		<tr id="trApplication_{$application.id}" {if !$application.note}class="table-application-border-bottom"{/if}>
			<td>&nbsp;</td>
			<td id = "tdCheckbox_{$application.id}" {if $application.note}rowspan="2"{/if} class="ApplicationPointedInListingInfo2" width="1">
				<input type="checkbox" name="applications[{$application.id}]" value="1" id="checkbox_{$smarty.foreach.applications.iteration}" />
			</td>
			<td class="ApplicationPointedInListingInfo" width="10%">[[$application.date]]</td>
			<td class="ApplicationPointedInListingInfo">
				<span class="name">{$application.user.FirstName} {$application.user.LastName}</span><br />
				{locationFormat location=$application.user.Location format="short"}
			</td>
			<td class="ApplicationPointedInListingInfo">
				{if $application.resume && !empty($application.resumeInfo.Title)}
					<span class="app-track-link"><a href="{$GLOBALS.site_url}/display-resume/{$application.resume}/">{$application.resumeInfo.Title|escape:'html'}</a></span> <br />
				{/if}
				{if $application.file}
					<a href="?appsID={$application.id}&amp;filename={$application.file|escape:"url"}">[[View Attached Resume]]</a><br />
				{/if}
				{if $application.comments}
					<a style="cursor: pointer;" onclick="showCoverLetter('{$application.id}')">[[View Cover Letter]]</a><br />
					<div id="coverLetter_{$application.id}" style="display: none">
						{$application.comments|escape:'html'}
					</div>
				{/if}
				{if $application.user.sid}
					{if $acl->isAllowed('use_private_messages')}
						<a href="{$GLOBALS.site_url}/private-messages/send/?to={$application.user.sid}" onclick="popUpWindow('{$GLOBALS.site_url}/private-messages/aj-send/?to={$application.user.username}', 700, '[[Contact]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;"  class="pm_send_link">[[Contact]]</a>
					{elseif $acl->getPermissionParams('use_private_messages') == 'message'}
						<a href="{$GLOBALS.site_url}/private-messages/send/?to={$application.user.sid}" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 400, '[[Contact]]'); return false;"  class="pm_send_link">[[Contact]]</a>
					{/if}
				{else}
					<a href="mailto:{$application.email}" class="pm_send_link">{$application.email}</a>
				{/if}
			</td>
			<td class="ApplicationPointedInListingInfo">
				<span class="app-track-link"><a href="{$GLOBALS.site_url}/my-job-details/{$application.job.sid}/">{$application.job.Title}</a></span>
			</td>
			{if $acl->isAllowed('use_screening_questionnaires')}
				{if $application.score > 0}
					<td class="ApplicationPointedInListingInfo"><a href="{$GLOBALS.site_url}/applications/view-questionnaire/{$application.id}">[[{$application.passing_score}]] ({$application.score})</a></td>
				{else}
					<td class="ApplicationPointedInListingInfo"> ({$application.score}) </td>
				{/if}
			{/if}
			<td class="ApplicationPointedInListingInfo" width="10%">[[{$application.status}]]</td>
			<td class="ApplicationPointedInListingInfo ApplicationActions" width="10%">
				{if $application.status == 'Rejected'}
					<a href="{$GLOBALS.site_url}/system/applications/view/?action=approve&amp;applications={$application.id}&amp;page={$currentPage}&amp;appsPerPage={$appsPerPage}&amp;appJobId={$current_filter}&amp;score={$score}">[[Approve]]</a>
				{elseif $application.status == 'Approved'}
					<a href="{$GLOBALS.site_url}/system/applications/view/?action=reject&amp;applications={$application.id}&amp;page={$currentPage}&amp;appsPerPage={$appsPerPage}&amp;appJobId={$current_filter}&amp;score={$score}">[[Reject]]</a>
				{else}
					<a href="{$GLOBALS.site_url}/system/applications/view/?action=approve&amp;applications={$application.id}&amp;page={$currentPage}&amp;appsPerPage={$appsPerPage}&amp;appJobId={$current_filter}&amp;score={$score}">[[Approve]]</a>
					<br/>
					<a href="{$GLOBALS.site_url}/system/applications/view/?action=reject&amp;applications={$application.id}&amp;page={$currentPage}&amp;appsPerPage={$appsPerPage}&amp;appJobId={$current_filter}&amp;score={$score}">[[Reject]]</a>
				{/if}
				<br />
				{if $application.note != ''}
					<a href="{$GLOBALS.site_url}/edit-notes/?apps_id={$application.id}" onclick="modifyNote( '{$application.id}', '{$GLOBALS.site_url}/edit-notes/?apps_id={$application.id}&amp;page=apps'); return false;" class="action">[[Edit Notes]]</a>&nbsp;&nbsp;
				{else}
					<a href="{$GLOBALS.site_url}/add-notes/?apps_id={$application.id}" onclick="modifyNote( '{$application.id}', '{$GLOBALS.site_url}/add-notes/?apps_id={$application.id}&amp;page=apps'); return false;" class="action">[[Add Notes]]</a>&nbsp;&nbsp;
				{/if}
			</td>
		</tr>
		{if $application.note}
			<tr class="table-application-border-bottom">
				<td></td>
				<td {if $acl->isAllowed("use_screening_questionnaires")}colspan="7"{else}colspan="6"{/if} class="ApplicationPointedInListingInfo">
					<div id="formNote_{$application.id}" class="form-notes">
						{if $application.note}
							<div class="applicationCommentsHeader"><span class="strong">[[My notes]]</span>:</div>
							<div class="applicationComments">{$application.note}</div>
						{/if}
					</div>
				</td>
			</tr>
		{else}
			<tr id="trNote_{$application.id}" style="display: none">
				<td style="padding: 0 10px;"></td>
				<td id="tdNote_{$application.id}" {if $acl->isAllowed('use_screening_questionnaires')}colspan="7"{else}colspan="6"{/if} style="padding: 0 10px;">
					<div id="formNote_{$application.id}" style="padding: 0 10px;" >
					</div>
				</td>
			</tr>
		{/if}
	{foreachelse}
		<tr>
			<td colspan="9" class="pointedInListingInfo"><br /><span class="text-center">[[You have no Applications now]]</span><br /></td>
		</tr>
	{/foreach}
</table>
<br />
<div class="navigation-application">
	<div class="numberPerPage">
		[[Number of listings per page]]:
		<select class="phrases_per_page perPage" name="phrases_per_page" onchange="window.location = '?page=1&amp;appsPerPage='+ this.value +'&amp;score={$score}&amp;appJobId={$current_filter}&amp;orderBy={$orderBy|escape:'html'}&amp;order={$order}'" >
			<option value="10" {if $appsPerPage == 10}selected="selected"{/if}>10</option>
			<option value="20" {if $appsPerPage == 20}selected="selected"{/if}>20</option>
			<option value="50" {if $appsPerPage == 50}selected="selected"{/if}>50</option>
			<option value="100" {if $appsPerPage == 100}selected="selected"{/if}>100</option>
		</select>
	</div>
	<br />

	{if count($pages) != 1}
		<div class="pageNavigation" style="float:right; margin: 10px 60px 10px 0;">
			<span class="prevBtn">
				<img src="{image}prev_btn.png" alt=""/>
				{if $currentPage - 1 > 0}
					<a href="?page={$currentPage - 1}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;appJobId={$current_filter}&amp;orderBy={$orderBy|escape:'html'}&amp;order={$order}">[[Previous]]</a>
				{else}
					<a>[[Previous]]</a>
				{/if}
			</span>
			<span class="navigationItems">
				{foreach from=$pages item=page}
					{if $page == $currentPage}
						<span class="strong">{$page}</span>
					{else}
						{if $page == $totalPages && $currentPage < $totalPages-3} ... {/if}
						<a href="?page={$page}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;appJobId={$current_filter}&amp;orderBy={$orderBy|escape:'html'}&amp;order={$order}">{$page}</a>
						{if $page == 1 && $currentPage > 4} ... {/if}
					{/if}
				{/foreach}
			</span>
			<span class="nextBtn">
				{if $currentPage + 1 < $totalPages}
					<a href="?page={$currentPage + 1}&amp;appsPerPage={$appsPerPage}&amp;score={$score}&amp;appJobId={$current_filter}&amp;orderBy={$orderBy|escape:'html'}&amp;order={$order}">[[Next]]</a>
				{else}
					<a>[[Next]]</a>{/if}
				<img src="{image}next_btn.png"  alt=""/>
			</span>
		</div>
	{/if}
</div>
</form>

<script type="text/javascript">
var total = {$smarty.foreach.applications.total};

function modifyNote(noteId, url) {
	$.get(url, function(data) {
		$("#formNote_" + noteId).html(data);
		$("#trNote_" + noteId).css("display", "table-row").addClass("table-application-border-bottom");
		$("#tdNote_" + noteId).addClass("ApplicationPointedInListingInfo");
		$("#tdCheckbox_" + noteId).attr("rowspan", "2");
	});
}

function set_checkbox(param) {
	for (i = 1; i <= total; i++) {
		if (checkbox = document.getElementById("checkbox_" + i)) {
			checkbox.checked = param;
		}
	}
}

$("#all_checkboxes_control").click(function() {
	if ( this.checked == false) {
		set_checkbox(false);
	} else {
		set_checkbox(true);
	}
});

function submitForm(action) {
	document.getElementById("action").value = action;
	var form = document.applicationForm;
	form.submit();
}

function showCoverLetter(id) {
	$("#coverLetter_" + id).dialog({
		modal: true,
		width: 400,
		height: 200,
		title: "Cover Letter",
		resizable: false,
		autoOpen: false
	}).dialog("open");
}

</script>