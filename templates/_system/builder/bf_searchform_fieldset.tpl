<div class="builder-col-{$holderType}">
	<fieldset id="{$fieldsHolderID}" class="active-fields sortable-column">
		<legend class="fh-legend">{$holderTitle}&nbsp;</legend>
		<span class="fh-status">&nbsp;</span>
		{foreach from=$fields_active item=theField}
			{if !($theField.type eq 'complex')}
				{include file="../builder/bf_searchform_fieldsblocks.tpl"}
			{/if}
		{/foreach}
	</fieldset>
</div>
