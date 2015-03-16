<script type="text/javascript" src="{$GLOBALS.site_url}/../system/ext/jquery/jquery.tablednd.js"></script>
<script  type="text/javascript" src="{common_js}/pagination.js"></script>
{capture name="trSureToDelete"}[[Are you sure you want to delete this country?]]{/capture}

{breadcrumbs}[[Countries]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/> [[Countries]]</h1>
{include file="country_errors.tpl"}
<div style="width:80%; padding: 10px 0">
	<div style="float: left;">
		<a href="{$GLOBALS.site_url}/add-country/" class="grayButton">[[Add Country]]</a>
		<a href="{$GLOBALS.site_url}/import-countries/" class="grayButton">[[Import Countries]]</a>
		<a href="{$GLOBALS.site_url}/countries/?action=move_country" title="[[Change Order]]" class="grayButton">[[Change Order]]</a>
	</div>
	<div style="text-align: right;">
		<form method="post" action="{$GLOBALS.site_url}/countries/">
		<input type="hidden" name="action" value="change_setting" />
		[[Default Country]]
		<select name="default_country" onChange="submit()">
	    	<option value="">[[Select Country]]</option>
		    {foreach from=$countriesForDefault item=country}
		    	<option value="{$country.sid}" {if $settings.default_country == $country.sid} selected="selected"{/if}>[[{$country.country_name}]]</option>
		    {/foreach}
	    </select>
	    </form>
	</div>
</div>
<div class="box" id="displayResults" style="width:80%">
	<form method="post" action="{$GLOBALS.site_url}/countries/" name="resultsForm">
		<input type="hidden" name="action" id="action_name" value="" />
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%" id="list_table">
					<thead>
						<tr>
							<th nowrap="nowrap" width="1%"><input type="checkbox" id="all_checkboxes_control" onclick="setAllCheckboxes();"></th>
							<th>[[Country Code]]</th>
							<th>[[Country Name]]</th>
							<th>[[Status]]</th>
							<th class="actions" width="1%">[[Actions]]</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$countries item=country name=countries_block}
						<tr {if !$country.active} style="background: none repeat scroll 0 0 #D8D8D8" {else} class="{cycle values = 'evenrow,oddrow'}" {/if} >
							<td nowrap="nowrap">
								<input type="checkbox" name="countries[{$country.sid}]" value="1" id="checkbox_{$smarty.foreach.countries_block.iteration}" />
								<input type="hidden" name="item_order[{$country.sid}]" value="1"/>
							</td>
							<td>{$country.country_code}</td>
							<td>[[{$country.country_name}]]</td>
							<td>
								{if $country.active}
									[[Active]]
								{else}
									[[Not Active]]
								{/if}
							</td>
							<td nowrap="nowrap">
								<a href="{$GLOBALS.site_url}/edit-country/?country_id={$country.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a>
								<a href="{$GLOBALS.site_url}/countries/?action=delete&amp;countries[{$country.sid}]=1&amp;items_per_page={$paginationInfo.itemsPerPage}&amp;page={$paginationInfo.currentPage}" onclick="return confirm('{$smarty.capture.trSureToDelete|escape}')" title="[[Delete]]" class="deletebutton">[[Delete]]</a>
								{if $country.active}
									<a href="{$GLOBALS.site_url}/countries/?action=deactivate&amp;countries[{$country.sid}]=1&amp;items_per_page={$paginationInfo.itemsPerPage}&amp;page={$paginationInfo.currentPage}" title="[[Deactivate]]" class="editbutton">[[Deactivate]]</a>
								{else}
									<a href="{$GLOBALS.site_url}/countries/?action=activate&amp;countries[{$country.sid}]=1&amp;items_per_page={$paginationInfo.itemsPerPage}&amp;page={$paginationInfo.currentPage}" title="[[Activate]]" class="editbutton">[[Activate]]</a>
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