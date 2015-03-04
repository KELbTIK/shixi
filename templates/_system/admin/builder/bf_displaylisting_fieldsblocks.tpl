{if $theField.type eq "complex"}
	<div class="portlet htmlBlock" id="{$theField.sid}" btype="htmlBlock">
		<div class="b-actions"></div>
		<div class="portlet-header">[[{$theField.caption}]]</div>
		<div class="portlet-content">
			<fieldset>
				<h3>[[{$theField.caption}]]:</h3>
				<div class="inputField" id="edit_{$theField.sid}" style="display:none;">
					<div id="complexFields_info_{$theField.sid}" class="complexBlock_info"></div>
					<textarea name="{$theField.sid}" class="htmlBlock" cols="80" rows="20">{include file="bf_displaylisting_complex_field_defaults.tpl"}</textarea>
				</div>
				<div class="fieldValue" id="view_{$theField.sid}"></div>
				<div class="clr"></div>
				<div class="button" id="button_{$theField.sid}">[[Edit]]</div>
				<script type="text/javascript">
					$("document").ready(function(){
						builderFieldBlockView("{$theField.sid}");
					});
				</script>
				<div id="complexFields_info_{$theField.sid}_details" class="hidden">
					<strong>******</strong>
					<h1>[[You can use the following values]]:</h1>
					{foreach from=$theField.fields item="complexChildField"}
						{$complexChildField.id} <br/>
					{/foreach}
				</div>
			</fieldset>
		</div>
	</div>
{elseif $theField.type eq "location"}
	<div class="portlet htmlBlock" id="{$theField.sid}" btype="htmlBlock">
		<div class="b-actions"></div>
		<div class="portlet-header">[[{$theField.caption}]]</div>
		<div class="portlet-content">
			<fieldset>
				<h3>[[{$theField.caption}]]:</h3>
				<div class="clr"></div>
				<div class="inputField" id="edit_{$theField.sid}" style="display:none;">
					<div id="locationFields_info_{$theField.sid}" class="locationBlock_info"></div>
					<textarea name="{$theField.sid}" class="htmlBlock" style="z-index: 1000000000000">{if !empty($theField.html)}{$theField.html}{else}{literal}{City}, {State.Code}{/literal}{/if}
					</textarea>
				</div>
				<div class="clr"></div>
				<div class="fieldValue" id="view_{$theField.sid}"></div>
				<div class="clr"></div>
				<div class="button" id="button_{$theField.sid}">[[Edit]]</div>
				<script type="text/javascript">
					$("document").ready(function(){
						builderFieldBlockView("{$theField.sid}");
					});
				</script>
				<div id="locationFields_info_{$theField.sid}_details" class="hidden">
					[[A variable should be put in braces]]: &#123;<strong>[[variable]]</strong>&#125;
					<h1>[[You can use the following values]]:</h1>
					{foreach from=$theField.fields item="complexChildField"}
						{if $complexChildField.id == 'State' || $complexChildField.id == 'Country'}
							{$complexChildField.id}.Name <br/>
							{$complexChildField.id}.Code <br/>
						{else}
							{$complexChildField.id} <br/>
						{/if}
					{/foreach}
				</div>
			</fieldset>
		</div>
	</div>
{elseif $listingTypeID eq "Resume" && $theField.b_field_sid eq "desiredSalary" || $theField.id eq "desiredSalary"}
	<div class="portlet htmlBlock" id="desiredSalary" btype="htmlBlock">
		<div class="b-actions"></div>
		<div class="portlet-header">[[Desired Salary and Salary Type]]</div>
		<div class="portlet-content">
			<fieldset>
				<h3>[[Desired Salary and Salary Type]]:</h3>
				<div class="clr"></div>
				<div class="inputField" id="edit_desiredSalary" style="display:none;">
					<div class="htmlBlock_info"></div>
					<textarea name="desiredSalary" class="htmlBlock">{if !empty($theField.html)}{$theField.html}{else}{literal}{DesiredSalary} {DesiredSalaryType}{/literal}{/if}
					</textarea>
				</div>
				<div class="clr"></div>
				<div class="fieldValue" id="view_desiredSalary"></div>
				<div class="clr"></div>
				<div class="button" id="button_desiredSalary">[[Edit]]</div>
				<script type="text/javascript">
					$("document").ready(function(){
						builderFieldBlockView("desiredSalary");
					});
				</script>
			</fieldset>
		</div>
	</div>
{elseif $listingTypeID eq "Job" && $theField.b_field_sid eq "customSalary" || $theField.id eq "customSalary"}
	<div class="portlet htmlBlock" id="customSalary" btype="htmlBlock">
		<div class="b-actions"></div>
		<div class="portlet-header">[[Salary and Salary Type]]</div>
		<div class="portlet-content">
			<fieldset>
				<h3>[[Salary and Salary Type]]:</h3>
				<div class="clr"></div>
				<div class="inputField" id="edit_customSalary" style="display:none;">
					<div class="htmlBlock_info"></div>
					<textarea name="customSalary" class="htmlBlock">{if !empty($theField.html)}{$theField.html}{else}{literal}{Salary} {SalaryType}{/literal}{/if}
					</textarea>
				</div>
				<div class="clr"></div>
				<div class="fieldValue" id="view_customSalary"></div>
				<div class="clr"></div>
				<div class="button" id="button_customSalary">[[Edit]]</div>
				<script type="text/javascript">
					$(document).ready(function(){
						builderFieldBlockView("customSalary");
					});
				</script>
			</fieldset>
		</div>
	</div>
{elseif $theField.html || $theField.b_field_sid|substr:0:9 eq "htmlBlock"}
	<div class="portlet htmlBlock" id="{$theField.b_field_sid}" btype="htmlBlock">
		<div class="b-actions"></div>
		<div class="portlet-header">[[Html Block]]</div>
		<div class="portlet-content">
			<fieldset>
				<div class="inputName"><h3>[[Html Block]]:</h3></div>
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
{elseif $theField.b_field_sid eq "id" || $theField.id eq "id"}
	<div class="portlet" id="id">
		<div class="b-actions"></div>
		<div class="portlet-header">[[{$listingTypeID} ID]]</div>
		<div class="portlet-content">
			<fieldset>
				<h3>[[{$listingTypeID} ID]]:</h3>
				<div class="inputField">&lt;[[{$theField.b_field_sid} value]]&gt;</div>
			</fieldset>
		</div>
	</div>
{elseif $theField.b_field_sid eq "views" || $theField.id eq "views"}
	<div class="portlet" id="views">
		<div class="b-actions"></div>
		<div class="portlet-header">[[{$listingTypeID} Views]]</div>
		<div class="portlet-content">
			<fieldset>
				<h3>[[{$listingTypeID} Views]]:</h3>
				<div class="inputField">&lt;[[{$theField.b_field_sid} value]]&gt;</div>
			</fieldset>
		</div>
	</div>
{elseif $theField.b_field_sid eq "posted" || $theField.id eq "posted"}
	<div class="portlet" id="posted">
		<div class="b-actions"></div>
		<div class="portlet-header">[[Posted]]</div>
		<div class="portlet-content">
			<fieldset>
				<h3>[[Posted]]:</h3>
				<div class="inputField">&lt;[[{$theField.b_field_sid} value]]&gt;</div>
			</fieldset>
		</div>
	</div>
{else}
	<div class="portlet" id="{$theField.sid}">
		<div class="b-actions"></div>
		<div class="portlet-header">[[{$theField.caption}]]</div>
		<div class="portlet-content">
			<fieldset>
				<h3>[[{$theField.caption}]]:</h3>
				<div class="inputField">&lt;[[{$theField.id} value]]&gt;</div>
			</fieldset>
		</div>
	</div>
{/if}
