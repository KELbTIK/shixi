<fieldset id="{$fieldsHolderID}" class="active-fields sortable-column">
	<legend class="fh-legend">{$fieldsHolderID}&nbsp;</legend>
	<span class="fh-status">&nbsp;</span>
	{foreach from=$fields_active item=theField}
		{include file="../builder/bf_displaylisting_fieldsblocks.tpl"}
	{/foreach}
</fieldset>
<div class="clr"></div>

