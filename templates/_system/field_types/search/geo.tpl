{capture name="select_box_field_distance"}
	<select class="searchGeoDistance" name="{$id}[geo][radius]">
		<option value="any">[[Any Distance]]</option>
		<option value="10" {if $value.radius == 10}selected="selected"{/if}>[[Within]] 10 [[{$GLOBALS.radius_search_unit}]]</option>
		<option value="20" {if $value.radius == 20}selected="selected"{/if}>[[Within]] 20 [[{$GLOBALS.radius_search_unit}]]</option>
		<option value="30" {if $value.radius == 30}selected="selected"{/if}>[[Within]] 30 [[{$GLOBALS.radius_search_unit}]]</option>
		<option value="40" {if $value.radius == 40}selected="selected"{/if}>[[Within]] 40 [[{$GLOBALS.radius_search_unit}]]</option>
		<option value="50" {if $value.radius == 50}selected="selected"{/if}>[[Within]] 50 [[{$GLOBALS.radius_search_unit}]]</option>
	</select>
{/capture}

{capture name="input_text_field_location"}   
	<input type="text" class="searchGeoLocation" id="{$id}" name="{$id}[geo][location]" value="{$value.location}" />
{/capture}

{assign var="select_box_field_distance" value="`$smarty.capture.select_box_field_distance`"}
{assign var="input_text_field_location" value="`$smarty.capture.input_text_field_location`"}

[[$select_box_field_distance of Zip $input_text_field_location]]

{* BEGIN AUTOCOMPLETE *}
{if $useAutocomplete}
	{include file='../field_types/search/autocomplete.tpl'}
{/if}
{* END AUTOCOMPLETE *}