{breadcrumbs}[[Flagged Listings]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperminus32.png" border="0" alt="" class="titleicon" />[[Flagged Listings]]</h1>
<script  type="text/javascript" src="{common_js}/pagination.js"></script>

	<form name="filter">
		<fieldset id="filter_fieldset">
			<legend>[[Filter By]]</legend>
			<table>
				<thead>
					<tr>
						<th>[[Listing Type]]</th>
						<th>[[Title]]</th>
						<th>[[Listing Owner]]</th>
						<th>[[Flag]]</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<select name="listing_type">
								<option value="">[[All]]</option>
								{foreach from=$listing_types item=type key=key}
									<option value="{$type.sid}" {if $type.sid == $listing_type_sid}selected="selected"{/if}>[[{$type.id}]]</option>
								{/foreach}
							</select>
						</td>
						<td><input type="text" name="filter_title"  value="{$filters.title}"/></td>
						<td><input type="text" name="filter_user" value="{$filters.username}"/></td>
						<td>
							<select name="filter_flag">
								<option value="">[[All]]</option>
									{foreach from=$all_flags item=flag}
										<option value="{$flag.sid}" {if $flag.sid == $filters.flag} selected="selected"{/if}>[[{$flag.value}]]</option>
									{/foreach}
							</select>
						</td>
						<td>
							<input name="action" value="filter" type="hidden">
							<input name="page" value="1" type="hidden">
							<span class="greenButtonEnd"><input value="[[Filter]]" type="submit" class="greenButton" /></span>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</form>
<div class="clr"><br/></div>

<form method="post" name="flagged_listings_form">
	<input type="hidden" id="action_name" name="action_name" value="" />
	<div class="box" id="displayResults">
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%">
					<thead>
						{include file="../pagination/sort.tpl"}
					</thead>
					<tbody>
					{foreach from=$listings item=elem name=flagged_block}
						<tr class="{cycle values="oddrow,evenrow"}">
							<td><input type="checkbox" name="flagged[{$elem.sid}]" value="1" id="checkbox_{$smarty.foreach.flagged_block.iteration}" /></td>
							<td>{$elem.sid}</td>
							<td>
								{if $elem.listing_info.Title}
									<a href="{$GLOBALS.site_url}/edit-listing/?listing_id={$elem.listing_sid}" class="editListing">{$elem.listing_info.Title}</a>
								{else}
									{$elem.listing_sid} ([[deleted]])
								{/if}
							</td>
							<td>{if $elem.listing_info.active == 0}[[Not Active]]{else}[[Active]]{/if}</td>
							<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$elem.user_info.sid}" class="editUser">{$elem.user_info.username}</a></td>
							<td>{if empty($elem.flagged_user)}[[anonymous]]{else}<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$elem.flagged_user.sid}" class="editUser">{$elem.flagged_user.username}</a>{/if}</td>
							<td>{$elem.date|truncate:"10":''}</td>
							<td>[[{$elem.flag_reason}]]</td>
							<td>{$elem.comment|escape:'html'}</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="box-footer">
			{include file="../pagination/pagination.tpl" layout="footer"}
		</div>
	</div>
</form>
{capture name="sureToDeleteListing"}[[Are you sure you want to delete selected listings?]]{/capture}
<script type="text/javascript">
	function isPopUp(button, textChooseAction, textChooseItem, textToDelete) {
		if (isActionEmpty(button, textChooseAction, textChooseItem)) {
			var action = $("#selectedAction_" + button).val();
			switch (action) {
				case "remove":
					if (confirm(textToDelete)) {
						submitForm(action);
					}
					break;
				case "delete":
					if (confirm("{$smarty.capture.sureToDeleteListing|escape}")) {
						submitForm(action);
					}
					break;
				default:
					submitForm(action);
					break;
			}
		}
	}
</script>