{capture name="select_box_field_distance"}
	<select class="searchGeoDistance" name="{$id}[location][radius]">
		<option value="5">[[Any Distance]]</option>
		<option value="10" {if $value.location.radius == 10}selected="selected"{/if}>[[Within]] 10 [[{$GLOBALS.radius_search_unit}]]</option>
		<option value="20" {if $value.location.radius == 20}selected="selected"{/if}>[[Within]] 20 [[{$GLOBALS.radius_search_unit}]]</option>
		<option value="30" {if $value.location.radius == 30}selected="selected"{/if}>[[Within]] 30 [[{$GLOBALS.radius_search_unit}]]</option>
		<option value="40" {if $value.location.radius == 40}selected="selected"{/if}>[[Within]] 40 [[{$GLOBALS.radius_search_unit}]]</option>
		<option value="50" {if $value.location.radius == 50}selected="selected"{/if}>[[Within]] 50 [[{$GLOBALS.radius_search_unit}]]</option>
	</select>
{/capture}
{assign var="select_box_field_distance" value="`$smarty.capture.select_box_field_distance`"}

<input type="text"  id="{$id}" name="{$id}[location][value]" value="{$value.location.value}" />
{if $searchWithin && $enable_search_by_radius}
<div style="padding-top:5px;">{$select_box_field_distance}</div>
{/if}

<style type="text/css">
.location {
	color: #989898;
}
</style>
<script>
$(function() {
	var defaultInputTextFieldValue = "{tr}City, State or Zip Code{/tr|escape:'javascript'}";
	var inputTextFieldId           = "{$id}";
	
	if ($("#" + inputTextFieldId).val() == "") {
		$("#" + inputTextFieldId).val(defaultInputTextFieldValue);
		$("#" + inputTextFieldId).addClass("location");
	}
	$("#" + inputTextFieldId).focus(function() {
		if ($("#" + inputTextFieldId).val() == defaultInputTextFieldValue) {
			$("#" + inputTextFieldId).val("");
		}
	});

	$("#" + inputTextFieldId).blur(function() {
		$("#" + inputTextFieldId).removeClass("location");
		if ($("#" + inputTextFieldId).val() == "") {
			$("#" + inputTextFieldId).val(defaultInputTextFieldValue);
			$("#" + inputTextFieldId).addClass("location");
		}
	});

	$("#search_form").submit(function() {
		if ($("#Location").val() == defaultInputTextFieldValue)
			$("#Location").val("");
	});

	$("#quickSearch").submit(function() {
		if ($("#Location").val() == defaultInputTextFieldValue) {
			$("#Location").val("");
		}
	});
});
</script>

{* BEGIN AUTOCOMPLETE *}
{if $useAutocomplete == 1}
	{assign var="parentID" value=false}
	{include file='../field_types/search/autocomplete.tpl'}
{/if}
{* END AUTOCOMPLETE *}