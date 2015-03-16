<script  type="text/javascript" src="{common_js}/pagination.js"></script>
{capture name="displayTitle"}[[Add New Location]]{/capture}
{capture name="closeButtonText"}[[Close]]{/capture}

{breadcrumbs}[[ZipCode Database]] {/breadcrumbs}
<h1><img src="{image}/icons/rss32.png" border="0" alt="" class="titleicon" />[[ZipCode Database]] </h1>
<p>
	<a href="?action=clear_data" onclick="return confirm('[[Are you sure you want to clear all geographical data?]]')" class="grayButton">[[Clear data]]</a>
	<a href="{$GLOBALS.site_url}/geographic-data/import-data/" class="grayButton">[[Import data from file]]</a>
	<a href="{$GLOBALS.site_url}/geographic-data/add/" class="grayButton"
	   onclick="popUpWindow('{$GLOBALS.site_url}/geographic-data/add/', 335, 430, '{$smarty.capture.displayTitle}', true); return false;" >[[Add New Location]]</a>
</p>
<div class="setting_button" id="mediumButton"><strong>[[Click to modify search criteria]]</strong><div class="setting_icon"><div id="accordeonClosed"></div></div></div>
<div class="setting_block" id="clearTable">
	<form method="get" name="search_form" >
		<table  width="100%">
			<tr>
				<td>[[Zip Code]]</td>
				<td><input type="text" name="search[name]" value="{$search.name|escape:'html'}"></td>
			</tr>
			<tr>
				<td>[[Longitude]]</td>
				<td><input type="text" name="search[longitude]" value="{$search.longitude|escape:'html'}"></td>
			</tr>
			<tr>
				<td>[[Latitude]]</td>
				<td><input type="text" name="search[latitude]" value="{$search.latitude|escape:'html'}"></td>
			</tr>
			<tr>
				<td>[[Country]]</td>
				<td>
					<select id="country" name="search[country_sid]" onchange="getStates(this.value);">
						<option value="">[[Any Country]]</option>
						{foreach from=$countries item=country}
							<option value="{$country.id}" {if $search.country_sid == $country.id} selected = "selected"{/if} >[[{$country.caption}]]</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>[[State]]</td>
				<td>
					<select id="state" name="search[state]" disabled="true" data-old-value="{$search.state}">
						<option value=""></option>
					</select>
				</td>
			</tr>
			<tr>
				<td>[[State Code]]</td>
				<td><input type="text" name="search[state_code]" value="{$search.state_code|escape:'html'}"></td>
			</tr>
			<tr>
				<td>[[City]]</td>
				<td><input type="text" name="search[city]" value="{$search.city|escape:'html'}"></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="floatRight">
						<input type="hidden" name="action" value="search"/>
						<input type="hidden" name="page" value="1"/>
						<input type="submit" value="[[Search]]" class="greenButton"/>
					</div>
				</td>
		</table>
	</form>
</div>
<div class="clr"><br/></div>

<div class="box" id="displayResults" style="width:80%">
	<form method="post" name="locations_form">
		<input type="hidden" name="action_name" id="action_name" value="" />
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table>
					<thead>
						{include file="../pagination/sort.tpl"}
					</thead>
					<tbody>
					{foreach from=$location_collection item=location name=location_block}
						<tr class="{cycle values='oddrow,evenrow'}">
							<td><input type="checkbox" name="locations[{$location.sid}]" value="1" id="checkbox_{$smarty.foreach.location_block.iteration}" /></td>
							<td>{$location.name|escape:'html'}</td>
							<td>{$location.longitude}</td>
							<td>{$location.latitude}</td>
							<td>{$location.city|escape:'html'}</td>
							<td>[[{$location.state}]]</td>
							<td>{$location.state_code}</td>
							<td>[[{$location.country_name}]]</td>
							<td><a href="{$GLOBALS.site_url}/geographic-data/edit-location/?sid={$location.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
							<td><a href="?action=delete&location_sid={$location.sid}" onclick="return confirm('[[Are you sure you want to delete this data?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
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
<div class="clr"><br/></div>

<script>
	$(".setting_button").click(function () {
			var butt = $(this);
			$(this).next(".setting_block").slideToggle("normal", function () {
					if ($(this).css("display") == "block") {
						butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
						butt.children("strong").text("[[Click to hide search criteria]]");
					} else {
						butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
						butt.children("strong").text("[[Click to modify search criteria]]");
					}
				}
			);
		}
	);

	function getStates(countrySID) {
		if ($("#country").val() == '') {
			$("#state").attr("disabled", "disabled");
		} else {
			$("#state").removeAttr("disabled");
		}
		$.get("{$GLOBALS.site_url}/get-states/", { country_sid: countrySID, state_sid: "", parentID: "Location", display_as: "", type: "zipCodeSearch" } ,
			function (data) {
				var stateField = $("#state");
				if (data != '') {
					stateField.html(data);
					stateField.find("option[value='" + stateField.attr('data-old-value') + "']").attr("selected", "selected");
				}
			}
		);
	}

	$(document).ready(function () {
			getStates($("#country").val());
		}
	);

	{if $search}
		var butt = $(".setting_button");
		butt.next(".setting_block").slideToggle("normal", function () {
				butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
				butt.children("strong").text("[[Click to hide search criteria]]");
			}
		);
	{/if}
</script>
