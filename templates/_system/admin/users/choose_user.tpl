<script type="text/javascript">
	$.ui.dialog.prototype.options.bgiframe = true;
	var progbar = "<img src='{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif' />";
	var parentReload = false;
	function setParam(param, value, param2, value2)  {
		var formID = 'users_form';
		$("#" + formID + " #" + param).val(value);
		if (param == 'sortingField') {
			$("#" + formID + " #page").val(1);
		}
		if (param2) {
			$("#" + formID + " #" + param2).val(value2);
		}
		formSubmit("{$GLOBALS.site_url}/manage-users/{$userGroupInfo.id|lower}/", formID);
		return false; 
	}

	function chooseUser(username) {
		$('#username', window.parent.document).val(username);
		$("#messageBox").dialog("close");
		return false;
	}

	function chooseCustomer(user_sid) {
		$("#messageBox").dialog("close");
		location.href = "{$GLOBALS.site_url}/add-invoice/?user_sid=" + user_sid;
		return false;
	}

	function showInputPage(layout) {
		$("#pageField" + layout).css("display", "inline");
		$("#pageInput" + layout).focus();
	}

	function hideInputPage(layout) {
		setTimeout(function () {
			$("#pageField" + layout).css("display", "none");
		}, 1000);
	}

	function checkEnteredPage(keyCode ,layout, totalPages) {
		var page = $("#pageInput" + layout).val();
		if (parseInt(page) <= parseInt(totalPages) && page > 0 && !isNaN(page) && page.indexOf('.') == -1 && (page % 1 === 0)) {
			$("#pageInput" + layout).css("border", "1px solid #CACACA");
			$("#enterButton" + layout).attr("disabled", "");
			if (keyCode == 13) {
				return setParam('page', parseInt(page), false, false);
			}
		} else {
			$("#pageInput" + layout).css("border", "1px solid #ff0000");
			$("#enterButton" + layout).attr("disabled", "disabled");
			$("form").submit(function() {
				return false;
			})
		}
	}
</script>

<div class="clr"><br/></div>
<form method="post" name="users_form" id="users_form" >
	<input type="hidden" name="search_template" id="search_template" value="choose_user_search.tpl" />
	<input type="hidden" name="template" id="template" value="choose_user.tpl" />
	<input type="hidden" name="page" id="page" value="{$page}" />
	<input type="hidden" name="sortingField" id="sortingField" value="{$paginationInfo.sortingField}" />
	<input type="hidden" name="sortingOrder" id="sortingOrder" value="{$paginationInfo.sortingOrder}" />
	<input type="hidden" name="itemsPerPage" id="itemsPerPage" value="{$paginationInfo.itemsPerPage}" />
	<input type="hidden" name="online" id="online" value="{$paginationInfo.uniqueUrlParams.online}" />
	<input type="hidden" name="restore" value="1" />
	<div class="box" id="displayResults" style="margin: 0 12px 60px 0;">
		<div class="box-header">
			<div class="items-count"><strong>{$paginationInfo.itemsCount}</strong> [[{$paginationInfo.item|escape}]]</div>

			{if count($paginationInfo.pages) != 1}
				<div class="pagination">
					{if $paginationInfo.currentPage == 1}
						<a class="none"><img src="{image}arrow-left-no-active.png" /></a>
					{else}
						&nbsp;<a class="arrow-left" href="#" onclick="return setParam('page', '{$paginationInfo.currentPage - 1}', false, false)" ><img src="{image}arrow-left.png" /></a>
					{/if}
					{foreach from=$paginationInfo.pages item=page}
						{if $page == $paginationInfo.currentPage}
							<span id="currentButton" class="current" onclick="showInputPage('Header');">{$page}&nbsp;<img src="{image}arrow-down.png" /></span>

							<div id="pageFieldHeader" class="page-field" style="display: none;">
								<div class="title-current">[[go to page]]:</div>
								<div class="content-current" style="color: #FFF">
									<div class="page-input">
										<input id="pageInputHeader" type="text" autocomplete="off" onkeypress="$(this).keyup(function(event) { checkEnteredPage(event.keyCode, 'Header', '{$paginationInfo.totalPages}')});" onblur="hideInputPage('Header');" />
									</div>
									<div class="page-enter-button">
										<input id="enterButtonHeader" type="button" disabled="disabled" onclick="return setParam('page', parseInt($('#pageInputHeader').val()), false, false)" />
									</div>
								</div>
							</div>
						{else}
							{if $page == $paginationInfo.totalPages && $paginationInfo.currentPage < $paginationInfo.totalPages-3} ... {/if}
							<a href="#" onclick="return setParam('page', '{$page}', false, false)">{$page}</a>
							{if $page == 1 && $paginationInfo.currentPage > 4} ... {/if}
						{/if}
					{/foreach}

					{if $paginationInfo.currentPage == $paginationInfo.totalPages}
						<a class="none"><img src="{image}arrow-right-no-active.png" /></a>&nbsp;
					{else}
						<a class="arrow-right" href="#" onclick="return setParam('page', '{$paginationInfo.currentPage + 1}', false, false)" ><img src="{image}arrow-right.png" /></a>&nbsp;
					{/if}
				</div>
			{/if}

			<div class="numberPerPage">
				[[per page]]:
				<select id="itemsPerPage" name="itemsPerPage" onchange="setParam('itemsPerPage', this.value, 'page', '1')" class="perPage">
					{foreach from=$paginationInfo.numberOfElementsPageSelect item=numberOfElement}
							<option value="{$numberOfElement}" {if $paginationInfo.itemsPerPage == $numberOfElement}selected{/if}>{$numberOfElement}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%">
					<thead>
						<tr>
							{foreach  from=$paginationInfo.fields key=fieldKey item=fieldInfo}
								{if $fieldInfo.isVisible == true}
									<th>
										{if $fieldInfo.isSort == false}
											[[{$fieldInfo.name}]]
										{else}
											<a href="#" onclick="return setParam('sortingField', '{$fieldKey}', 'sortingOrder', '{if $paginationInfo.sortingOrder == 'ASC' && $paginationInfo.sortingField == $fieldKey}DESC{else}ASC{/if}')">[[{$fieldInfo.name}]]</a>
											{if $paginationInfo.sortingField == $fieldKey}
												{if $paginationInfo.sortingOrder == 'DESC'}
													<img src="{image}b_down_arrow.gif" />
												{else}
													<img src="{image}b_up_arrow.gif" />
												{/if}
											{/if}
										{/if}
									</th>
								{/if}
							{/foreach}
						</tr>
					</thead>
					{foreach from=$found_users item=found_user name=users_block}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td><b>{$found_user.sid}</b></td>
							<td><a href="{$GLOBALS.site_url}/choose-user/?user_sid={$found_user.sid}" onClick="if (location.href.indexOf('manage-invoices') > 0){ldelim}return chooseCustomer('{$found_user.sid}'); {rdelim} else {ldelim}return chooseUser('{$found_user.username}');{rdelim}" title="[[Edit]]"><b>{$found_user.username}{if $found_user.parent_sid}&nbsp;<small>([[sub-user]])</small>{/if}</b></a></td>
							{if $userGroupInfo.id == 'Employer'}
								<td>{$found_user.CompanyName}</td>
							{elseif $userGroupInfo.id == 'JobSeeker'}
		 						<td>{$found_user.FirstName}</td>
		 						<td>{$found_user.LastName}</td>
							{/if}
							<!-- for ie -->	<td style="word-break: break-all;"><!-- for firefox--><div style="word-wrap: break-word; width: 130px;">{$found_user.email}</div></td>
							<td>{if $found_user.products > 0}{$found_user.products}{else}N/A{/if}</td>
							<td>{$found_user.registration_date}</td>
							<td>
								{if $found_user.active == "1"}
									[[Active]]
								{else}
									[[Not Active]]
								{/if}
							</td>
							{if $ApproveByAdminChecked}
								<td>[[{$found_user.approval}]]</td>
							{/if}
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
		<div class="box-footer">
			<div class="items-count"><strong>{$paginationInfo.itemsCount}</strong> [[{$paginationInfo.item|escape}]]</div>

			{if count($paginationInfo.pages) != 1}
				<div class="pagination">
					{if $paginationInfo.currentPage == 1}
						<a class="none"><img src="{image}arrow-left-no-active.png" /></a>
					{else}
						&nbsp;<a class="arrow-left" href="#" onclick="return setParam('page', '{$paginationInfo.currentPage - 1}', false, false)" ><img src="{image}arrow-left.png" /></a>
					{/if}
					{foreach from=$paginationInfo.pages item=page}
						{if $page == $paginationInfo.currentPage}
							<span id="currentButton" class="current" onclick="showInputPage('Footer');">{$page}&nbsp;<img src="{image}arrow-down.png" /></span>

							<div id="pageFieldFooter" class="page-field" style="display: none;">
								<div class="title-current">[[go to page]]:</div>
								<div class="content-current" style="color: #FFF">
									<div class="page-input">
										<input id="pageInputFooter" type="text" autocomplete="off" onkeypress="$(this).keyup(function(event) { checkEnteredPage(event.keyCode, 'Footer', '{$paginationInfo.totalPages}')});" onblur="hideInputPage('Footer');" />
									</div>
									<div class="page-enter-button">
										<input id="enterButtonFooter" type="button" disabled="disabled" onclick="return setParam('page', parseInt($('#pageInputFooter').val()), false, false)" />
									</div>
								</div>
							</div>
						{else}
							{if $page == $paginationInfo.totalPages && $paginationInfo.currentPage < $paginationInfo.totalPages-3} ... {/if}
							<a href="#" onclick="return setParam('page', '{$page}', false, false)">{$page}</a>
							{if $page == 1 && $paginationInfo.currentPage > 4} ... {/if}
						{/if}
					{/foreach}

					{if $paginationInfo.currentPage == $paginationInfo.totalPages}
						<a class="none"><img src="{image}arrow-right-no-active.png" /></a>&nbsp;
					{else}
						<a class="arrow-right" href="#" onclick="return setParam('page', '{$paginationInfo.currentPage + 1}', false, false)" ><img src="{image}arrow-right.png" /></a>&nbsp;
					{/if}
				</div>
			{/if}

			<div class="numberPerPage">
				[[per page]]:
				<select id="itemsPerPage" name="itemsPerPage" onchange="setParam('itemsPerPage', this.value, 'page', '1')" class="perPage">
					{foreach from=$paginationInfo.numberOfElementsPageSelect item=numberOfElement}
						<option value="{$numberOfElement}" {if $paginationInfo.itemsPerPage == $numberOfElement}selected{/if}>{$numberOfElement}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</div>
</form>
