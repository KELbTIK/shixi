{if $theField.html || $theField.b_field_sid|substr:0:9 eq "htmlBlock"}
	<div class="portlet htmlBlock" id="{$theField.b_field_sid}" btype="htmlBlock">
		<div class="b-actions"></div>
		<div class="portlet-header">[[Html Block]]</div>
		<div class="portlet-content">
			<fieldset>
				<div class="inputName">[[Html Block]]</div>
				<div class="clr"></div>
				<div class="inputField" id="edit_{$theField.b_field_sid}" style="display:none;">
					<div class="htmlBlock_info"></div>
					<textarea name="{$theField.b_field_sid}" class="htmlBlock">{$theField.html}</textarea>
				</div>
				<div class="clr"></div>
				<div class="fieldValue" id="view_{$theField.b_field_sid}"></div>
				<div class="clr"></div>
				<div class="button" id="button_{$theField.b_field_sid}">[[Edit]]</div>
				<script type="text/javascript">
					$(document).ready(function(){
						builderFieldBlockView("{$theField.b_field_sid}");
					});
				</script>
			</fieldset>
		</div>
	</div>
{elseif $theField.b_field_sid eq "keywords" || $theField.id eq "keywords"}
	<div class="portlet" id="keywords">
		<div class="b-actions"></div>
		<div class="portlet-header">[[Keywords]]</div>
		<div class="portlet-content">
			<fieldset>
				<div class="inputName">[[Keywords]]</div>
				<div class="inputField">{search property="keywords" type="bool" listingType=$listingTypeID}</div>
			</fieldset>
		</div>
	</div>
{elseif $theField.b_field_sid eq "PostedWithin" || $theField.id eq "PostedWithin"}
	<div class="portlet" id="PostedWithin">
		<div class="b-actions"></div>
		<div class="portlet-header">[[Posted Within]]</div>
		<div class="portlet-content">
			<fieldset>
				<div class="inputName">[[Posted Within]]</div>
				<div class="inputField">{search property=PostedWithin template="list.date.tpl"}</div>
			</fieldset>
		</div>
	</div>
{else}
	<div class="portlet" id="{$theField.sid}" {if $theField.parent_sid}parent="{$theField.parent_sid}"{elseif $theField.type == 'location'}parent="{$theField.sid}"{/if}>
		<div class="b-actions"></div>
		<div class="portlet-header">
			{if $theField.id eq "ZipCode"}
				[[Search Within]]
			{elseif $theField.type eq "location"}
				[[Location]]
			{else}
				[[{$theField.caption}]]
			{/if}
		</div>
		<div class="portlet-content">
			<fieldset>
				<div class="inputName" fieldID={$theField.id}>
					{if $theField.id eq "ZipCode"}
						[[Search Within]]
					{else}
						[[{$theField.caption}]]
					{/if}
				</div>
				{if $theField.type eq "location"}
					<div class="inputField">{search property=$theField.id template="location.like.tpl"}</div>
				{else}
					<div class="inputField">{search property=$theField.id}</div>
				{/if}
			</fieldset>
		</div>
	</div>
{/if}
