{if $type_sid}
	{breadcrumbs}
	  <a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a>
	  &#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$type_sid}">[[{$type_info.name}]]</a>
	  &#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$field_sid}">[[{$field_info.caption}]]</a>
	  &#187; <a href='{$GLOBALS.site_url}/edit-listing-field/edit-location-fields/?field_sid={$field_sid}'>[[Edit Location Fields]]</a>
	  &#187; [[Edit Listing Field]]
	{/breadcrumbs}
{else}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/listing-fields/">[[Common Fields]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/?sid={$field_sid}">[[{$field_info.caption}]]</a>
		&#187; <a href='{$GLOBALS.site_url}/edit-listing-field/edit-location-fields/?field_sid={$field_sid}'>[[Edit Location Fields]]</a>
		&#187; [[Edit Listing Field]]
	{/breadcrumbs}
{/if}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Listing Field]]</h1>

{if $field_type eq 'list' or $field_type eq 'multilist'}
	{if $listingFieldInfo.id == 'Country'}
   		<p><a href="{$GLOBALS.site_url}/countries/" class="grayButton">[[Manage Country List]]</a></p>
   	{else}
   		<p><a href="{$GLOBALS.site_url}/states/" class="grayButton">[[Manage State List]]</a></p>
   	{/if}
{elseif $field_type eq 'geo'}
    <p><a href="{$GLOBALS.site_url}/geographic-data/" class="grayButton">[[Edit Geographic Data]]</a></p>
{/if}

{include file="field_errors.tpl"}
<fieldset>
	<legend>[[Listing Field Info]]</legend>
	<form method="post" action="">
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="sid" value="{$sid}" />
		<input type="hidden" name="field_sid" value="{$field_sid}" />
		<table>
			{foreach from=$form_fields key=field_name item=form_field}
				<tr>
					<td>
						{if $form_field.id == 'default_value'}
							<div id="defaultCaption">[[{$form_field.caption}]]</div>
						{elseif $form_field.id == 'profile_field_as_dv'}
						{else}
							[[{$form_field.caption}]]
						{/if}
					</td>
					<td class="required">{if $form_field.is_required}*{/if}</td>
					<td>
						{if $form_field.id == 'default_value'}
						{if $listingFieldInfo.id == 'Country'}
							<input type='radio' id='profile_field' name='default_value_setting' onclick="checkDefaultValue()" value="profile_field" {if $profileFieldAsDV || !$listingFieldInfo.default_value}checked=checked{/if} {if $disableField}disabled="disabled"{/if} />[[Use user profile field as a default value]]<br />
							<input type='radio' name='default_value_setting' onclick="checkDefaultValue()" value="default_country" {if $listingFieldInfo.default_value == 'default_country'} checked = "checked" {/if} /> [[Use Default Country]]<br/>
							{if $form_field.comment}
								<select disabled="disabled" >
									<option>[[Select]] [[{$form_field.caption}]]</option>
								</select>
							{else}
								<input type="radio" name="default_value_setting" onclick="checkDefaultValue()" value="0" {if $listingFieldInfo.default_value != '' && $listingFieldInfo.default_value != 'default_country'} checked = "checked" {/if} />{assign var="disable" value=true}{input property=$form_field.id}{assign var="disable" value=false}
							{/if}
							{if $form_field.comment}<br /><span class="commentSmall">[[{$form_field.comment}]]</span>{/if}
							<script type="text/javascript">
								function checkDefaultValue() {
									var setting = $('input:radio[name=default_value_setting]:checked').val()
									if (setting == 0)
										$("#default_value").attr("disabled", false);
									else {
										$("#default_value option:first").attr("selected", "selected");
										$("#default_value").attr("disabled", "disabled");
									}
								}
								checkDefaultValue();
							</script>
						{else}
							<input type='checkbox' id='profile_field' name='profile_field' {if $profileFieldAsDV}checked=checked{/if} {if $disableField}disabled="disabled"{/if} />[[Use user profile field as a default value]]<br />
							<div id='defaultValue' {if !$profileFieldAsDV}style='display: block;'{else}style='display: none'{/if}>
							{if $form_field.comment}
								<select disabled="disabled" >
									<option>[[Select]] [[{$form_field.caption}]]</option>
								</select>
							{else}
								{input property=$form_field.id}
							{/if}
							{if $form_field.comment}<br /><span class="commentSmall">[[{$form_field.comment}]]</span>{/if}
							</div>
						{/if}
						{elseif $form_field.id == 'profile_field_as_dv'}
						{else}
							{input property=$form_field.id}
						{/if}
					</td>
				</tr>
					{if $form_field.comment && $form_field.id != 'default_value'}<tr><td style='font-size:11px;' colspan="2">[[{$form_field.comment}]]</td></tr>{/if}
					{if $form_field.id == 'signs_num'}
						<tr>
							<td></td>
							<td style="font-size:90%;padding-top:0">[[This setting will be overlapped <br />by the language setting 'Decimals' <br />in the beta version. <br />It will be fixed in the release]].</td>
						</tr>
					{/if}
			{/foreach}
			<tr>
				<td colspan="3">
					<input type="hidden" name="apply" value="no" />
                    <div class="floatRight">
                        <input type="submit" name="submit_form" id="apply" value="[[Apply]]" class="greenButton"/>
                        <input type="submit" name="submit_form" value="[[Save]]" class="greenButton" />
                    </div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<script>
	$("#profile_field").click(function() {
		if ( this.checked == false) {
			$("#defaultValue").css('display', 'block');
		} 
		else {
			$("#defaultValue").css('display', 'none');
		}
	});

    $('#apply').click(
        function(){
            $("input[name='apply']").attr('value', 'yes');
        }
    );
</script>