{assign var="LocationValues" value=$value}
{foreach from=$form_fields item=form_field}
	<div class="form-group has-feedback" {if $form_field.hidden}style="display:none;"{/if} id="{$parentID}_{$form_field.id}">
		{assign var="fixInstructionsForComplexField" value=true}
		<label class="inputName col-sm-3 control-label">
            {tr}{$form_field.caption}{/tr|escape:'html'}
            {if $form_field.is_required}<span class="text-danger small">*</span>{/if}
        </label>
		<div class="inputField col-sm-8">{input property=$form_field.id parent=$parentID}</div>
		{if $form_field.instructions && $fixInstructionsForComplexField}{assign var="instructionsExist" value="1"}{include file="../classifieds/instructions.tpl" form_field=$form_field}{/if}
		{if in_array($form_field.type, array('tree', 'multilist'))}
			<div id="count-available-{$form_field.id}" class="mt-count-available"></div>
		{/if}
	</div>
{/foreach}
<script language='JavaScript' type='text/javascript'>
function get{$parentID}States(countrySID) {ldelim}
	{foreach from=$form_fields item=form_field}
		{if $form_field.id == 'State'}
			$.get("{$GLOBALS.site_url}/get-states/", {ldelim} caption: "{$form_field.caption|escape:'javascript'}", country_sid: countrySID, state_sid: "{$LocationValues.State|escape:'javascript'}", parentID: "{$parentID}", display_as: "{$form_field.display_as}" {rdelim},
				  function(data){ldelim}
						$("#{$parentID}_State .inputField").html(data);
				 {rdelim});
		{/if}
	{/foreach}
{rdelim}

get{$parentID}States("{$LocationValues.Country|escape:'javascript'}");
</script>
{assign var="parentID" value=false scope=global} 