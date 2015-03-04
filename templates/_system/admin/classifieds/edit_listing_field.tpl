{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-fields/">[[Listing Fields]]</a> &#187; [[{$listing_field_info.caption}]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" class="titleicon" />[[Edit Listing Field Info]]</h1>
{include file="field_errors.tpl"}

{if $field_type eq 'list' or $field_type eq 'multilist'}
    <p><a href="{$GLOBALS.site_url}/edit-listing-field/edit-list/?field_sid={$field_sid}" class="grayButton">[[Edit List Values]]</a></p>
{elseif $field_type eq 'complex'}
    <p><a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/?field_sid={$field_sid}" class="grayButton">[[Edit Fields]]</a></p>
{elseif $field_type eq 'location'}
    <p><a href="{$GLOBALS.site_url}/edit-listing-field/edit-location-fields/?field_sid={$field_sid}" class="grayButton">[[Edit Fields]]</a></p>
{elseif $field_type eq 'geo'}
    <p><a href="{$GLOBALS.site_url}/geographic-data/" class="grayButton">[[Edit Geographic Data]]</a></p>
{elseif $field_type eq 'tree'}
    <p style="margin: 13px 0;"><a href="{$GLOBALS.site_url}/edit-listing-field/edit-tree/?field_sid={$field_sid}" class="grayButton">[[Edit Tree Values]]</a></p>
{/if}

<fieldset>
<legend>[[Listing Field Info]]</legend>
	<form id="fieldData" method="post" action="">
		<input type="hidden" id="action" name="action" value="save_info" />
		<input type="hidden" name="sid" value="{$field_sid}" />
		<table>
			{foreach from=$form_fields key=field_name item=form_field}
				<tr id="tr_{$field_name}">
					<td valign="top" id="td_caption_{$field_name}" width="22%">
						{if $form_field.id == 'default_value'}
							<div id="defaultCaption">[[{$form_field.caption}]]</div>
						{elseif $form_field.id == 'profile_field_as_dv'}
						{else}
							[[{$form_field.caption}]]
						{/if}
					</td>
					<td class="required">{if $form_field.is_required}*{/if}</td>
					<td valign="top">
						{if $form_field.id == 'default_value' && $form_field.type != 'tree'}
							<input type="checkbox" id="profile_field" name="profile_field" {if $profileFieldAsDV}checked=checked{/if} /><label for="profile_field">[[Use user profile field as a default value]]</label>
							<div id='defaultValue' {if !$profileFieldAsDV}style='display: block;'{else}style='display: none'{/if}>{input property=$form_field.id}</div>
						{elseif $form_field.id == 'profile_field_as_dv'}
							<div id='profileFieldAsDefaultValue' {if !$profileFieldAsDV}style='display: none'{else}style='display: block'{/if}>{input property=$form_field.id}</div>
							<div class="commentSmall">[[This value will be automatically set for this field]].</div>
						{elseif $field_name eq 'display_as_select_boxes'}
							<input type="radio" name="display_as_select_boxes" value="0" {if !$listing_field_info.display_as_select_boxes}checked="checked"{/if}/>[[Tree Block]]<br/>
							<input type="radio" name="display_as_select_boxes" value="1" {if $listing_field_info.display_as_select_boxes}checked="checked"{/if}/>[[Select Boxes]]
                        {elseif $field_name eq 'choiceLimit'}
                            {input property=$form_field.id}<br />
                            <span class="commentSmall">[[Set empty or 0 for unlimited selection]]</span>
						{elseif $field_name == 'display_as' && ($field_type == 'list' || $field_type == 'multilist')}
							{input property=$form_field.id template="list_empty.tpl"}
						{else}
							{input property=$form_field.id}
						{/if}
					</td>
				</tr>
				{if $form_field.comment}<tr><td style="font-size:12px;" colspan="3">{$form_field.comment}</td></tr>{/if}
				{if $form_field.id == 'signs_num'}
					<tr>
						<td>&nbsp;</td>
                        <td>&nbsp;</td>
						<td>[[This setting will be overlapped <br />by the language setting 'Decimals' <br />in the beta version. <br />It will be fixed in the release]].</td>
					</tr>
				{/if}
			{/foreach}
			<tr>
				<td colspan="3">
					<input type="hidden" name="old_listing_field_id" value="{$listing_field_info.id}" />
                    <div class="floatRight">
                        <input type="button" id="apply" value="[[Apply]]" class="greenButton"/>
                        <input type="button" id="save" value="[[Save]]" class="greenButton"/>
                    </div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<div id="messageWindow" style="display: none;">
	<p>[[You are trying to edit the system field (id). If you change the default value of this field there would be a need to make appropriate changes in the settings, templates and PHP code. Otherwise the system will function unpredictably]]</p>
</div>
{capture name="change_anyway"}[[Change anyway]]{/capture}
{capture name="don_t_change"}[[Don't change]]{/capture}


<script>
	$("#profile_field").click(function() {
		if (!this.checked) {
			$("#defaultValue").css('display', 'block');
			$("#profileFieldAsDefaultValue").css('display', 'none');
		}
		else {
			$("#defaultValue").css('display', 'none');
			$("#profileFieldAsDefaultValue").css('display', 'block');
		}
	});
	{if $tree_levels_number}
		$("document").ready(function(){
			showHideTreeLevels($("[name='display_as_select_boxes']:checked").val());
			$("[id^='td_caption_level']").css({ "text-align":"right"});
			$("[name='display_as_select_boxes']").click(function(){
				showHideTreeLevels($(this).val());
			});
			function showHideTreeLevels(show){
				if(show==1){ $("[id^='tr_level_']").show();
				}else{ $("[id^='tr_level_']").hide(); }
			}
		});
	{/if}
	var listingFieldId = "{$listing_field_info.id}";
	$('#apply').click(function () {
			$('#action').attr('value', 'apply_info');
			saveFieldSettings();
	});
	$("#save").click(function () {
			saveFieldSettings();
	});

	function saveFieldSettings()
	{
		if (listingFieldId == $("input[name='id']").val() || (listingFieldId == 'Location')) {
			$('#fieldData').submit();
		} else {
			showMessageWindow();
		}
	}

	function showMessageWindow()
	{
		$("#messageWindow").dialog({
			width: 600,
			height: 200,
			buttons: {
				"{$smarty.capture.change_anyway|escape:"javascript"}": function () {
					$('#fieldData').submit();
				},
				"{$smarty.capture.don_t_change|escape:"javascript"}": function () {
					$("input[name='id']").val(listingFieldId);
					$('#action').attr('value', 'save_info');
					$("#messageWindow").dialog('destroy');
				}
			}
		});
	}
</script>