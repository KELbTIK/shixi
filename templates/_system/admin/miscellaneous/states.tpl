<script type="text/javascript" src="{$GLOBALS.site_url}/../system/ext/jquery/jquery.tablednd.js"></script>
<script language="JavaScript" type="text/javascript" src="{common_js}/pagination.js"></script>
<div id="dialog" style="display: none"></div>
{breadcrumbs}[[State/Region]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/> [[States/Regions]]</h1>
<div style="padding-top: 15px;">
	[[Country]]: 
	<select id="country" name="country_sid" onchange="window.location = '?country_sid='+this.value" >
		{foreach from=$countries item=country}
			<option value="{$country.sid}" {if $country_sid == $country.sid} selected = selected {/if} >[[{$country.country_name}]]</option>
		{/foreach}
	</select>
</div>
{include file="state_errors.tpl"}
<p>
	<a href="{$GLOBALS.site_url}/add-state/?country_sid={$country_sid}" class="grayButton">[[Add State/Region]]</a>
	<a href="{$GLOBALS.site_url}/import-states/?country_sid={$country_sid}" class="grayButton">[[Import States/Regions]]</a>
	<a href="{$GLOBALS.site_url}/states/?action=move_state&country_sid={$country_sid}" title="[[Change Order]]" class="grayButton" onclick="">[[Change Order]]</a>
</p>
<div class="box" id="displayResults" style="width:80%">
	<form method="post" action="{$GLOBALS.site_url}/states/" name="resultsForm">
		<input type="hidden" name="action" id="action_name" value="" />
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%" id="list_table">
					<thead>
						<tr>
							<th width="1%"><input type="checkbox" id="all_checkboxes_control" onclick="setAllCheckboxes();"></th>
							<th>[[State/Region Code]]</th>
							<th>[[State/Region Name]]</th>
							<th>[[Status]]</th>
							<th class="actions" width="1%">[[Actions]]</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$states item=state name=states_block}
						<tr {if !$state.active} style="background: none repeat scroll 0 0 #D8D8D8" {else} class="{cycle values = 'evenrow,oddrow'}" {/if} >
							<td nowrap="nowrap">
								<input type="checkbox" name="states[{$state.sid}]" value="1" id="checkbox_{$smarty.foreach.states_block.iteration}" />
								<input type="hidden" name="item_order[{$state.sid}]" value="1"/>
							</td>
							<td>{$state.state_code}</td>
							<td>[[{$state.state_name}]]</td>
							<td>
								{if $state.active}
									[[Active]]
								{else}
									[[Not Active]]
								{/if}
							</td>
							<td nowrap="nowrap">
								<a href="{$GLOBALS.site_url}/edit-state/?state_id={$state.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a>
								<a href="{$GLOBALS.site_url}/states/?action=delete&amp;states[{$state.sid}]=1&amp;country_sid={$country_sid}&amp;itemsPerPage={$paginationInfo.itemsPerPage}&amp;currentPage={$paginationInfo.currentPage}" onclick="return confirm('[[Are you sure you want to delete this state?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a>
								{if $state.active}
									<a href="{$GLOBALS.site_url}/states/?action=deactivate&amp;states[{$state.sid}]=1&amp;country_sid={$country_sid}&amp;itemsPerPage={$paginationInfo.itemsPerPage}&amp;currentPage={$paginationInfo.currentPage}" title="[[Deactivate]]" class="editbutton">[[Deactivate]]</a>
								{else}
									<a href="{$GLOBALS.site_url}/states/?action=activate&amp;states[{$state.sid}]=1&amp;country_sid={$country_sid}&amp;itemsPerPage={$paginationInfo.itemsPerPage}&amp;currentPage={$paginationInfo.currentPage}" title="[[Activate]]" class="editbutton">[[Activate]]</a>
								{/if}
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="box-footer">
			{include file="../pagination/pagination.tpl" layout="footer"}
		</div>
	</form>
</div>