<fieldset id="{$fieldsHolderID}" class="active-fields">
	<legend class="fh-legend">{$fieldsHolderID}</legend>
	<div class="sortable-column">
		{foreach from=$fields_active item=theField}
			{include file="../builder/bf_displaylisting_fieldsblocks.tpl"}
		{/foreach}
	</div>
</fieldset>
<div class="clr"></div>

