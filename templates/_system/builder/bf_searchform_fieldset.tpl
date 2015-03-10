<div class="builder-col-{$holderType}">
	<div id="{$fieldsHolderID}" class="active-fields sortable-column">
		{if $holderTitle}<legend class="fh-legend">{$holderTitle}&nbsp;</legend>{/if}
		{*<span class="fh-status">&nbsp;</span>*}
		{foreach from=$fields_active item=theField}
			{if !($theField.type eq 'complex')}
				{include file="../builder/bf_searchform_fieldsblocks.tpl"}
			{/if}
		{/foreach}
	</div>
</div>
