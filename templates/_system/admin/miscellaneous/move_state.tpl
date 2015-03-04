<script type="text/javascript" src="{$GLOBALS.site_url}/../system/ext/jquery/jquery.tablednd.js"></script>
{breadcrumbs}<a href="{$GLOBALS.site_url}/states/">[[State/Region]]</a> &#187; [[Move States]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/> [[Move States]]</h1>
{include file="state_errors.tpl"}
<div class="box" id="displayResults" style="width:80%">
	<form method="post" action="{$GLOBALS.site_url}/states/?action=move_state&country_sid={$country_sid}" name="resultsForm">
		<input type="hidden" name="action" id="action_name" value="" />
		<input type="hidden" name="country_sid" value="{$country_sid}" />
		<div class="box-header">
			<div class="actionDescription">
				[[Drag and drop the items to the needed place]]:
			</div>
			<div class="actionButtons">
				<a href="#" class="grayButton" onclick="submitForm('save_order');">[[Save Order]]</a>
				<a href="{$GLOBALS.site_url}/states/" class="grayButton">[[Cancel]]</a>
			</div>
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%" id="list_table">
					<thead>
					<tr>
						<th style="text-align: left">[[State/Region Code]]</th>
						<th style="text-align: left">[[State/Region Name]]</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$states item=state name=states_block}
						<tr {if !$state.active} style="background: none repeat scroll 0 0 #D8D8D8" {else} class="{cycle values = 'evenrow,oddrow'}" {/if} >
							<td class="dragHandle">
								{$state.state_code}
								<input type="hidden" name="item_order[{$state.sid}]" value="1"/>
							</td>
							<td class="dragHandle">[[{$state.state_name}]]</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="box-footer">
			<div class="actionButtons">
				<a href="#" class="grayButton" onclick="submitForm('save_order');">[[Save Order]]</a>
				<a href="{$GLOBALS.site_url}/states/" class="grayButton">[[Cancel]]</a>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
// Drag'n'Drop table
$("#list_table").tableDnD({
	onDragClass: 'myDragClass',
	dragHandle: 'dragHandle'
});

function submitForm(action) {
	$("#action_name").val(action);
	var form = $("form[name='resultsForm']");
	form.submit();
}
</script>
