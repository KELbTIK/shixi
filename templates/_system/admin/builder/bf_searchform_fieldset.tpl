<div class="builder-col-{$holderType}">
	<fieldset id="{$fieldsHolderID}" class="active-fields">
		<legend class="fh-legend">[[{$holderTitle}]]</legend>
		<div class="sortable-column">
			{foreach from=$fields_active item=theField}
				{if !($theField.type eq 'complex')}
					{include file="../builder/bf_searchform_fieldsblocks.tpl"}
				{/if}
			{/foreach}
		</div>
	</fieldset>
</div>
