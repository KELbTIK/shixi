{assign var="LocationCountry" value="0"} 
{assign var="LocationValues" value=$value} 
{foreach from=$form_fields item=form_field}
	{if $form_field.id == 'Country'}
		{assign var="LocationCountry" value="1"} 
	{/if}
{/foreach}
{foreach from=$form_fields item=form_field}
	{if $form_field.id != 'State' || $LocationCountry == 1 || $defaultCountry}
	<fieldset id="{$parentID}_{$form_field.id}">
		<div class="inputName">
			{if $form_field.id eq "ZipCode"}
				[[Search Within]]
			{else}
				{tr}{$form_field.caption}{/tr|escape:'html'}
			{/if}
		</div>
		<div class="inputField">
			{if $form_field.type == 'location'}
				{search property=$form_field.id template="location.like.tpl"}
			{elseif $form_field.id eq "City"}
				{search property=$form_field.id parent=$parentID template="string.like.tpl"}
			{else}
				{search property=$form_field.id parent=$parentID}
			{/if}
		</div>
	</fieldset>
	{/if}
{/foreach}
<script language='JavaScript' type='text/javascript'>
function get{$parentID}States(countrySID) {
	{foreach from=$form_fields item=form_field}
		{if $form_field.id == 'State'}
			$.get("{$GLOBALS.site_url}/get-states/", {
					country_sid: countrySID,
					"state_sid[multi_like][0]": "{$LocationValues.State.multi_like.0|escape:'javascript'}",
					parentID: "{$parentID}",
					display_as: "{$form_field.display_as}",
					type: "search" } ,
				function(data) {
					$("#{$parentID}_State .inputField").html(data);
				}
			);
		{/if}
	{/foreach}
}

{if $LocationValues.Country.multi_like.0}
	get{$parentID}States("{$LocationValues.Country.multi_like.0|escape:'javascript'}");
{elseif $defaultCountry && !$LocationCountry}
	get{$parentID}States("{$defaultCountry|escape:'javascript'}");
{/if}
</script>
{assign var="parentID" value=false}