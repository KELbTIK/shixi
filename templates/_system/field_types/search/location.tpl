{foreach from=$form_fields item=form_field}
	<fieldset id="{$parentID}_{$form_field.id}" {if $form_field.id == 'State'} style="display: none;" {/if}>
        <div class="inputName">
        	{if $form_field.id eq "ZipCode"}
				[[Search Within]]
			{else}
				{tr}{$form_field.caption}{/tr|escape:'html'}
			{/if}
		</div>
        <div class="inputField">{if $form_field.type == 'location'}{search property=$form_field.id searchWithin=true template="location.like.tpl"}{elseif $form_field.id eq "City"}{search property=$form_field.id parent=$parentID template="string.like.tpl"}{else}{search property=$form_field.id parent=$parentID}{/if}</div>
	</fieldset>
{/foreach}
<script language='JavaScript' type='text/javascript'>
{if $form_field.id == 'State'} 
	function get{$parentID}States(countrySID) {ldelim}
		{foreach from=$form_fields item=form_field}
			{if $form_field.id == 'State'}
				$.get("{$GLOBALS.site_url}/get-states/", {ldelim} country_sid: countrySID, "state_sid[multi_like][0]": "{$value.multi_like.0|escape:'javascript'}", parentID: "{$parentID}", display_as: "{$form_field.display_as}", type: "search" {rdelim}, 
					  function(data){ldelim}
							$("#{$parentID}_State .inputField").html(data);
					 {rdelim});
			{/if}
		{/foreach}
	{rdelim}
{/if}
$("document").ready(function(){ldelim}
	{if $form_field.id == 'State'} 
		var country = $("#"+'{$parentID}'+"_Country");
		if (country.html()) {ldelim}
			$("#"+'{$parentID}'+"_State").css('display', 'block');
		{rdelim}
		else {ldelim}
			{if $defaultCountry}
				$("#"+'{$parentID}'+"_State").css('display', 'block');
				get{$parentID}States("{$defaultCountry|escape:'javascript'}");
			{/if}
		{rdelim}
	{elseif $form_field.id == 'Country'}
		{if $value.multi_like.0}
			get{$parentID}States("{$value.multi_like.0|escape:'javascript'}");
		{elseif !isset($value.multi_like.0) && $defaultCountry}
			$("#"+"{$parentID}"+"_Country [value='{$defaultCountry|escape}']").attr("selected", "selected");
			get{$parentID}States("{$defaultCountry|escape:'javascript'}");
		{/if}
	{/if}
{rdelim});
</script>
{assign var="parentID" value=false} 