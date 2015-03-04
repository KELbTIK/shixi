{capture name="select_box_field_distance"}
	{assign var=radiusArr value=','|explode:'0,10,20,30,40,50'}
	<select class="searchGeoDistance" name="{$id}[location][radius]">
		{foreach from=$radiusArr item=radius}
			{if $radius == 0}
				<option value="">[[Within $radius {$GLOBALS.radius_search_unit}]]</option>
			{else}
				<option value="{$radius}" {if $value.location.radius == $radius}selected="selected"{/if} >[[Within $radius {$GLOBALS.radius_search_unit}]]</option>
			{/if}
		{/foreach}
	</select>
{/capture}
{assign var="select_box_field_distance" value="`$smarty.capture.select_box_field_distance`"}

<input type="text"  id="{$id}" name="{$id}[location][value]" value="{$value.location.value}" />
{if  $enable_search_by_radius}
	{if $searchWithin}
		{$select_box_field_distance}
	{else}
		<input type="hidden"  name="{$id}[location][radius]" value="10" />
	{/if}
{/if}

<script type="text/javascript">
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
		if ($("#Location").val() == defaultInputTextFieldValue) {
			$("#Location").val("");
		}
	});

	$("#quickSearch").submit(function() {
		if ($("#Location").val() == defaultInputTextFieldValue) {
			$("#Location").val("");
		}
	});

	$("#quickSearchForm").submit(function() {
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
