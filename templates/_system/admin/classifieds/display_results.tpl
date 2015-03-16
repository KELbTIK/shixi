<script  type="text/javascript" src="{common_js}/pagination.js"></script>
{capture name="confirmToDelete"}[[Are you sure you want to delete this {$listingsType.name|lower}?]]{/capture}
<div class="clr"><br/></div>
<form method="post" action="{$GLOBALS.site_url}/listing-actions/" name="resultsForm">
	<input type="hidden" name="action_name" id="action_name" value="">
	<input type="hidden" name="listingTypeId" value="{$listingsType.id}">
	<input type="hidden" name="rejectReason" value="">
	<input type="hidden" name="date_to_change" id="date_to_change" value="">

	<div class="box" id="displayResults">
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
			<div id="rejectDialog" style="display: none">
				<textarea name="reason" cols="30" rows="4"></textarea>
				<input type="submit" value="[[Submit Reject]]" class="greenButton" id="submitReject" />
			</div>

			<div id="modify_date_dialog" style="display: none; z-index: 10000">
				[[Modify Expiration Date for]] <input type="text" size="2" id="new_date" name="new_date" readonly = "readonly">
				<div class="clr"><br/></div>
				<input type="submit" id="modify_send_button" name="modify_send_button" value="[[Modify]]" class="greenButton" />
			</div>
		</div>

		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%">
					<thead>
						{include file="../pagination/sort.tpl"}
					</thead>
					<tbody>
						{foreach from=$listings item=listing name=listings_block}
							<tr class="{cycle values = 'evenrow,oddrow'}">
								<td><input type="checkbox" name="listings[{$listing.id}]" value="1" id="checkbox_{$smarty.foreach.listings_block.iteration}" /></td>
								<td><a href="{$GLOBALS.site_url}/display-listing/?listing_id={$listing.id}">{$listing.id}</a></td>
								<td><a href="{$GLOBALS.site_url}/display-listing/?listing_id={$listing.id}">{$listing.Title|escape:'html'}</a></td>
								<td>[[{$listing.product.name}]]</td>
								<td><span title="{$listing.activation_date}">{$listing.activation_date|regex_replace:"/\s.*/":""}</span></td>
								<td><span title="{$listing.expiration_date}">{$listing.expiration_date|regex_replace:"/\s.*/":""}</span></td>
								<td>
									<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$listing.user.sid}">{$listing.user.username|escape:'html'}
										{if in_array($listing.type.id, ['Job', 'Resume'])}
											({if $listing.type.id == 'Job'}{$listing.user.CompanyName|escape:'html'}{else}{$listing.user.FirstName|escape:'html'} {$listing.user.LastName|escape:'html'}{/if})
										{/if}
									</a>
								</td>
								<td>{$listing.views}</td>
								<td>{if $listing.active == 1}[[Active]]{else}[[Not Active]]{/if}</td>
								{if $showApprovalStatusField != false }
									<td {if $listing.reject_reason != ''}title="Reason: {$listing.reject_reason}"{/if}>
										{$listing.status|escape}
									</td>
								{/if}
								<td nowrap="nowrap">
									{if $listing.active}
										<a href="{$GLOBALS.site_url}/listing-actions/?action_name=deactivate&amp;listings[{$listing.id}]=1&amp;listingTypeId={$listing.type.id}" class="deletebutton">[[Deactivate]]</a>
									{else}
										<a href="{$GLOBALS.site_url}/listing-actions/?action_name=activate&amp;listings[{$listing.id}]=1&amp;listingTypeId={$listing.type.id}" class="editbutton greenbtn" style="text-align: center;">[[Activate]]</a>
									{/if}
								</td>
								<td nowrap="nowrap" style="border-left: 0px;"><a href="{$GLOBALS.site_url}/edit-listing/?listing_id={$listing.id}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
								<td nowrap="nowrap" style="border-left: 0px;"><a href="{$GLOBALS.site_url}/listing-actions/?action_name=delete&amp;listings[{$listing.id}]=1&amp;listingTypeId={$listing.type.id}" onclick="return confirm('{$smarty.capture.confirmToDelete|escape}')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
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
{capture name="trTitleRejectReason"}[[Enter Reject Reason]]:{/capture}
{capture name="trTitleModifyExpirationDate"}[[Modify Expiration Date]]{/capture}
{capture name="trLoading"}[[Please wait ...]]{/capture}
<script type="text/javascript">
	$.ui.dialog.prototype.options.bgiframe = true;
	var progBar = "<img src='{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif' />";

	function isPopUp(button, textChooseAction, textChooseItem, textToDelete) {
		if (isActionEmpty(button, textChooseAction, textChooseItem)) {
			var action = $("#selectedAction_" + button).val();
			switch (action) {
				case "reject":
					$("#rejectDialog").dialog("destroy");
					$("#rejectDialog").attr({ title: "{$smarty.capture.trTitleRejectReason|escape:"javascript"}" });
					$("#rejectDialog").dialog();
					return false;
					break;
				case "modify_date_button":
					$("#modify_date_dialog").dialog('destroy');
					$("#modify_date_dialog").attr({ title: "{$smarty.capture.trTitleModifyExpirationDate|escape:"javascript"}" });
					$("#modify_date_dialog").dialog();
					break;
				case "delete":
					if (confirm(textToDelete)) {
						submitForm(action);
					}
				default:
					submitForm(action);
					break;
			}
		}
	}

	$("#submitReject").click(function(){
		val = $("textarea[name='reason']").val();
		$("input[name='rejectReason']").val( val );
		$("input[name='action_name']").val('reject');
		$("form[name='resultsForm']").submit();
	});

	$("#modify_send_button").click(function(){
		val = $("#new_date").val();
		$("#date_to_change").val( val );
		$("input[name='action_name']").val("datemodify");
		$("#modify_date_dialog").dialog('destroy').html("{$smarty.capture.trLoading|escape:"javascript"}" + progBar).dialog({ width: 250 });
		$("form[name='resultsForm']").submit();
	});

	var dFormat = '{$GLOBALS.current_language_data.date_format}';

	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");
	$(function () {
		$("#new_date").datepicker({
			dateFormat:dFormat,
			showOn:'both',
			yearRange:'-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});
	});
</script>