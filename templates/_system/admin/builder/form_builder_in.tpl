{capture name="trSuccessfullySaved"}[[Your changes were successfully saved]]{/capture}
{capture name="trAreYouSure"}[[Are you sure? The changes you made will not be saved. Continue?]]{/capture}
{capture name="trSaving"}[[Saving...]]{/capture}
<script type="text/javascript">
	var listingTypeID = "{$listingTypeID}";
	var siteUrl = "{$GLOBALS.site_url}";
    var parentWindowStatusBox;
    var doubleHeight = false;
	var builderType = "{if $mode eq "display"}display{else}search{/if}";
	var holderStatus = $("#statusBox");
	var windCanBeCl = false;
    {literal}
    if(window.opener){
		parentWindowStatusBox = window.opener.jQuery("#statusP");
	}

	function getNewHtmlBlock(ui) {
		var newElem = ui.item.clone(true);
		var Index = parseInt(ui.item.attr("index")) + 1;
		newElem.attr("index", Index);
		newElem.attr("id", "htmlBlock_" + Index);

		var newContent = '<fieldset>' +
				'<div class="inputName">Html Block</div>' +
				'<div class="clr"></div>' +
				'<div class="inputField" id="edit_htmlBlock_' + Index + '" style="display:none;">' +
				'<div class="htmlBlock_info"></div>' +
				'<textarea name="htmlBlock_' + Index + '"  class="htmlBlock"></textarea></div>' +
				'<div class="clr"></div>' +
				'<div class="fieldValue" id="view_htmlBlock_' + Index + '"></div>' +
				'<div class="clr"></div>' +
				'<div class="button" id="button_htmlBlock_' + Index + '">Edit</div>' +
				'<script type="text/javascript"> builderFieldBlockView("htmlBlock_' + Index + '"); <\/script>' +
				'<\/fieldset>';
		newElem.children(".portlet-content").html(newContent);
		return newElem;
	}

	$(function() {
		$(".sortable-column").sortable({
			connectWith: ".sortable-column",
			//cancel: "textarea.htmlBlock, .fh-legend",
			revert: true,
			update: function(event, ui) {
				// create new wysiwyg block html, when activating one
				// for wysiwyg
                if (ui.item.attr("id").substr(0,9)=="htmlBlock") {
					if (ui.sender && ui.sender.attr("id")=="inactive-fields") {
                        var newElem = getNewHtmlBlock(ui);
                        defineBAction(newElem, false);
						newElem.appendTo("#inactive-fields");
					}
					else if (ui.item.parent(".inactive-fields").attr("id")) {
						defineBAction(ui.item, false);
					}
					else if (ui.item.parent(".sortable-column").attr("class")) {
						defineBAction(ui.item, true);
					}
				}
				else if (ui.item.parent(".sortable-column").attr("class")) {
					defineBAction(ui.item, true);
				}
				else if (ui.item.parent(".inactive-fields").attr("id")) {
					defineBAction(ui.item, false);
				}
				resizeBuilder();
			}
		});
		$( ".sortable-inactive-column" ).sortable({
			connectWith: ".sortable-column",
			cancel: ".ui-state-disabled",
			revert: true,
			update: function(event, ui) {
				// create new wysiwyg block html, when activating one
				// for wysiwyg
				if (ui.item.attr("id").substr(0,9)=="htmlBlock") {
					if (ui.sender && ui.sender.attr("id")=="inactive-fields") {
						var newElem = getNewHtmlBlock(ui);
						defineBAction(newElem, false);
						newElem.appendTo("#inactive-fields");
					}
					else if (ui.item.parent(".inactive-fields").attr("id")) {
						defineBAction(ui.item, false);
					}
					else if (ui.item.parent(".sortable-column").attr("class")) {
						defineBAction(ui.item, true);
					}
				}
				else if (ui.item.parent(".sortable-column").attr("class")) {
					defineBAction(ui.item, true);
				}
				else if (ui.item.parent(".inactive-fields").attr("id")) {
					defineBAction(ui.item, false);
				}
				resizeBuilder();
			}
		});
		{/literal}
			{foreach from=$fields_inactive item=theField}
				{if $theField.type eq 'location'}
					var fieldSID = "{$theField.sid}";
					{literal}
					$( ".sortable-column-"+fieldSID ).sortable({
						connectWith: ".sortable-column",
						cancel: ".ui-state-disabled",
						revert: true,
						update: function(event, ui) {
							// create new wysiwyg block html, when activating one
							// for wysiwyg
							if (ui.item.attr("id").substr(0,9)=="htmlBlock") {
								if (ui.sender && ui.sender.attr("id")=="inactive-fields") {
									var newElem = getNewHtmlBlock(ui);
									defineBAction(newElem, false);
									newElem.appendTo("#inactive-fields");
								}
								else if (ui.item.parent(".inactive-fields").attr("id")) {
									defineBAction(ui.item, false);
								}
								else if (ui.item.parent(".active-fields").attr("id")) {
									defineBAction(ui.item, true);
								}
							}
							else if(ui.item.parent(".active-fields").attr("id")){
								defineBAction(ui.item, true);
							}
							else if (ui.item.parent(".inactive-fields").attr("id")) {
								defineBAction(ui.item, false);
							}
							resizeBuilder();
						}
					});
					{/literal}
				{/if}
			{/foreach}
		{literal}

		$(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
			.find(".portlet-header")
			.addClass("ui-widget-header ui-corner-all")
			.prepend("<span class='ui-icon ui-icon-plusthick'></span>")
			.end()
			.find(".portlet-content");
		$(".active-fields .portlet")
			.find(".b-actions")
			.html("<a href='javascript:;' title='remove' class='b-remove-item'></a>");

		var locationCountry = new Array();
		var locationLocation = new Array();
		var locationZipCode = new Array();
		var defaultCountry = '{/literal}{$defaultCountry}{literal}';
		$(".active-fields .portlet").each(function() {
			if ($(this).attr("parent") && $( this ).find(".inputName").attr('fieldID') == 'Country') {
				locationCountry[$(this).attr("parent")] = $(this).attr("parent");
			}
			if ($(this).attr("parent") && $(this).find(".inputName").attr('fieldID') == 'Location') {
				locationLocation[$(this).attr("parent")] = $(this).attr("parent");
			}
			if ($(this).attr("parent") && $(this).find(".inputName").attr('fieldID') == 'ZipCode') {
				locationZipCode[$(this).attr("parent")] = $(this).attr("parent");
			}
		});
		$(".locationFieldset .portlet").each(function() {
			if($(this).attr("parent") && $(this).find(".inputName").attr('fieldID') == 'State' && !defaultCountry) {
				if (!locationCountry[$(this).attr("parent")]) {
					$(this).addClass("ui-state-disabled");
				}
			}
		});
		$(".locationFieldset .portlet").each(function() {
			if($(this).attr("parent") && $(this).find(".inputName").attr('fieldID') == 'ZipCode') {
				if (locationLocation[$(this).attr("parent")]) {
					$(this).addClass("ui-state-disabled");
				}
			}
		});
		$(".locationFieldset .portlet").each(function() {
			if($(this).attr("parent") && $(this).find(".inputName").attr('fieldID') == 'Location') {
				if (locationZipCode[$(this).attr("parent")]) {
					$(this).addClass("ui-state-disabled");
				}
			}
		});
		$(".portlet-header .ui-icon").click(function() {
			$(this).toggleClass("ui-icon-minusthick").toggleClass("ui-icon-plusthick");
			$(this).parents(".portlet:first").find(".portlet-content").toggle();
			resizeBuilder();
		});
	});
	function defineBAction(item, action) {
		if (action) {
			if (item.attr("parent") && item.find(".inputName").attr('fieldID') == 'Country') {
				$(".locationFieldset .portlet").each(function() {
					if ($(this).attr("parent") == item.attr("parent") && $(this).find(".inputName").attr('fieldID') == 'State') {
						$(this).removeClass("ui-state-disabled");
					}
				});
			}
			else if (item.attr("parent") && item.find(".inputName").attr('fieldID') == 'Location') {
				$(".locationFieldset .portlet").each(function(){
					if ($(this).attr("parent") == item.attr("parent") && $(this).find(".inputName").attr('fieldID') == 'ZipCode') {
						$(this).addClass("ui-state-disabled");
					}
				});
			}
			else if (item.attr("parent") && item.find(".inputName").attr('fieldID') == 'ZipCode') {
				$(".locationFieldset .portlet").each(function() {
					if ($(this).attr("parent") == item.attr("parent") && $(this).find(".inputName").attr('fieldID') == 'Location') {
						$(this).addClass("ui-state-disabled");
					}
				});
			}
			item.children(".b-actions")
					.html("<a href='javascript:;' title='remove' class='b-remove-item'></a>")
					.find(".b-remove-item").bind("click", removeItem);
		} else {
			item.children(".b-actions").html("");
		}
	}
	function resizeBuilder(){
		var index = 15;
		if (doubleHeight) {
			index = 30;
			doubleHeight = false;
		}
		var bnHeight = $(".form-builder-cont").height();
		$("#form_builder").height(bnHeight+index);
		if (bnHeight > $(".indexDiv").height()) {
			$(".indexDiv").height(bnHeight+8)
		}
		if (bnHeight > $(".MainDiv").height()) {
			$(".MainDiv").height(bnHeight+8)
		}
		changeStatusMessage();
	}
	function removeItem(){
		var defaultCountry = '{/literal}{$defaultCountry}{literal}';
		var elemClone = $(this).parents("div.portlet").clone(true);
		$(this).parents("div.portlet").hide("slow",function(){$(this).remove()});
		if ($(this).parents("div.portlet").attr("id").substr(0,9) != "htmlBlock") {
			elemClone.find(".b-actions").html("");
			if ($(this).parents("div.portlet").attr("parent")) {
				var parent = $(this).parents("div.portlet").attr("parent");
				var inactiveState = false;
				if ($(this).parents("div.portlet").find(".inputName").attr('fieldID') == 'Country' && !defaultCountry) {
					$(".active-fields .portlet").each(function() {
						if ($(this).attr("parent") == parent && $(this).find(".inputName").attr('fieldID') == 'State') {
							$(this).find(".b-remove-item").click();
						}
					});
					$(".locationFieldset .portlet").each(function() {
						if ($(this).attr("parent") == parent && $(this).find(".inputName").attr('fieldID') == 'State') {
							$(this).addClass("ui-state-disabled");
						}
					});
				}
				else if ($(this).parents("div.portlet").find(".inputName").attr('fieldID') == 'State' && !defaultCountry) {
					inactiveState = true;
				}
				else if ($(this).parents("div.portlet").find(".inputName").attr('fieldID') == 'Location') {
					$(".locationFieldset .portlet").each(function() {
						if ($(this).attr("parent") == parent && $(this).find(".inputName").attr('fieldID') == 'ZipCode') {
							$(this).removeClass("ui-state-disabled");
						}
					});
				}
				else if ($(this).parents("div.portlet").find(".inputName").attr('fieldID') == 'ZipCode') {
					$(".locationFieldset .portlet").each(function() {
						if($(this).attr("parent") == parent && $(this).find(".inputName").attr('fieldID') == 'Location') {
							$(this).removeClass("ui-state-disabled");
						}
					});
				}
				elemClone.appendTo("#inactive-fields-"+$(this).parents("div.portlet").attr("parent"));
				if (inactiveState) {
					$(".locationFieldset .portlet").each(function() {
						if($(this).attr("parent") == parent && $(this).find(".inputName").attr('fieldID') == 'State') {
							$(this).addClass( "ui-state-disabled" );
						}
					});
				}
			} else {
                elemClone.appendTo("#inactive-fields");
            }

			elemClone[0].style.cssText = defaultBlockStyle;
			resizeBuilder();
		}
	}

	function changeStatusMessage(htmlMessage) {
		holderStatus.html(htmlMessage);
	}

	function saveChanges() {

		var BuilderDataManager = function() {
			this.builderData = {
				type: builderType,
				listingTypeID: listingTypeID,
				fieldsHolders: {}
			}
		}

		BuilderDataManager.prototype.pushFieldsHolder = function(fieldsHolderData) {
			this.getData().fieldsHolders[fieldsHolderData.id] = fieldsHolderData;
		}

		BuilderDataManager.prototype.getData = function() {
			return this.builderData;
		}

		BuilderDataManager.prototype.getBuilderLayout = function () {
			this.builderData.layout = $("#h-display-layout").val();
		}

		BuilderDataManager.prototype.save = function() {
			makeRequest(this.builderData);
		}

		BuilderDataManager.prototype.getFieldsHoldersData = function() {
			var FieldsHolderDataManager = function(id) {
				this.fieldsHolderData = {
					id: id,
					fields: [],
					htmlValues: {}
				}
			}

			FieldsHolderDataManager.prototype.getData = function() {
				return this.fieldsHolderData;
			}

			FieldsHolderDataManager.prototype.getFields = function(fieldsHolder) {
				this.fieldsHolderData.fields = fieldsHolder.sortable("toArray");
			}

			FieldsHolderDataManager.prototype.getValuesForCustomFields = function() {
				var obj = this.fieldsHolderData.htmlValues;
				$.each(this.getData().fields, function (index, value) {
					if (value) {
						var ElemID = "#" + value.toString();
						if ($(ElemID).attr('btype')) {
							var htmlValue = $("textarea[name='" + value + "']").val();
							obj[value] = htmlValue;
						}
					}
				});
			}

			$(".active-fields div.sortable-column").each(function(i){
				var fieldsHolder = $(this);
				var fieldsHolderID = fieldsHolder.parent(".active-fields").attr("id");
				var fieldsHolderDataManager = new FieldsHolderDataManager(fieldsHolderID);

				fieldsHolderDataManager.getFields(fieldsHolder);
				fieldsHolderDataManager.getValuesForCustomFields();

				builderDataManager.pushFieldsHolder(fieldsHolderDataManager.getData());
			});
		}

		changeStatusMessage("<p class='error'>{/literal}{$smarty.capture.trSaving|escape:"quotes"}{literal}</p>");
		var builderDataManager = new BuilderDataManager();
		builderDataManager.getFieldsHoldersData();
		if (builderType == "display") {
			builderDataManager.getBuilderLayout();
		}
		builderDataManager.save();
	}

	function makeRequest(data) {
		$.ajax({
			type: "POST",
			url: siteUrl + "/system/builder/save/",
			data: data,
			cache: false,
			dataType: "json",
			success: function (response) {
				showResponseMessage(response);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				response = {
					success: false,
					message: textStatus + " " + errorThrown
				}
				showResponseMessage(response);
			}
		});
	}

	function showResponseMessage(response) {
		if (response.success) {
			changeStatusMessage("<p class='message'>{/literal}{$smarty.capture.trSuccessfullySaved|escape:"javascript"}{literal}</p>");
			if (windCanBeCl) {
				closePopupWindow();
			}
		} else {
			changeStatusMessage("<p class='error'>" + response.message + "</p>");
		}
	}

	function closePopupWindow() {
		if (parentWindowStatusBox) {
			parentWindowStatusBox.html("<p class='message'>{/literal}{$smarty.capture.trSuccessfullySaved|escape:"javascript"}{literal}</p>");
			window.close();
		}
	}

	$("document").ready(function() {
		$(".tree_button").bind("click", function() {
			return false;
		});
		$(".b-remove-item").bind("click", removeItem);
		if(parentWindowStatusBox) {
			parentWindowStatusBox.html("");
		}
		$(".indexDiv").css("overflow","visible").css("position","inherit");
		$(".MainDiv").css("overflow","visible").css("margin-left","10px").css("position","inherit");
		$("#builder-save").bind("click",function() {
			windCanBeCl = true;
			saveChanges();
		});
		$("#builder-apply").bind("click",function() {
			saveChanges();
		});
		$("#builder-reset").bind("click",function() {
			if(confirm("{/literal}{$smarty.capture.trAreYouSure|escape:"quotes"}{literal}")) {
				location.reload();
			}
		});
		doubleHeight = true;
		resizeBuilder();
	});
	{/literal}
	</script>

<p class="b-message">[[To add a field - drag and drop it on the form to a place you need]]</p>
{if $mode eq "display"}
	<div class="clr"></div>
	<fieldset class="dl-fieldset">
		<legend class="dl-legend">[[Layouts]]:</legend>
		<div class="display-layout">
			<div class="layout-item layout-2cols-wide">
				<a {if $display_layout == "2cols-wide"}class="active"{/if} href="{$GLOBALS.site_url}{$url}?builder-layout=2cols-wide" title=""></a>
			</div>
			<div class="layout-item layout-1col-2rows">
				<a {if $display_layout == "1col-2rows"}class="active"{/if} href="{$GLOBALS.site_url}{$url}?builder-layout=1col-2rows" title=""></a>
			</div>
			<div class="layout-item layout-wide-2cols">
				<a {if $display_layout == "wide-2cols"}class="active"{/if} href="{$GLOBALS.site_url}{$url}?builder-layout=wide-2cols" title=""></a>
			</div>
			<div class="layout-item layout-2cols">
				<a {if $display_layout == "2cols"}class="active"{/if} href="{$GLOBALS.site_url}{$url}?builder-layout=2cols" title=""></a>
			</div>
			<div class="layout-item layout-1col">
				<a {if $display_layout == "1col" || !$display_layout || $display_layout eq 'undefined'}class="active"{/if}
						href="{$GLOBALS.site_url}{$url}?builder-layout=1col" title=""></a>
			</div>
			<div class="clr"></div>
		</div>
		<input type="hidden" name="display-layout" value="{$display_layout}" id="h-display-layout"/>
	</fieldset>
	<div class="clr"></div>
{/if}
<div class="clr"></div>
{if $mode eq "search"}
	{assign var="field_block" value="../builder/bf_searchform_fieldsblocks.tpl"}
{else}
	{assign var="field_block" value="../builder/bf_displaylisting_fieldsblocks.tpl"}
{/if}
<fieldset>
	<legend>[[Inactive Fields]]</legend>
	{if $mode eq "search"}
		<div class="sortable-inactive-column" id="inactive-fields">
		<div class=""></div>
		{foreach from=$fields_inactive item=theField}
			{if $theField.type neq 'complex' && $theField.type neq 'location'}
				{include file=$field_block theField=$theField}
			{/if}
		{/foreach}
	{else}
		<div class="sortable-inactive-column" id="inactive-fields">
		<div class=""></div>
		{foreach from=$fields_inactive item=theField}
			{include file=$field_block theField=$theField}
		{/foreach}
	{/if}

	{capture name=htmlBlockIndex}{php}echo time();{/php}{/capture}
	<div class="portlet htmlBlock" id="htmlBlock_{$smarty.capture.htmlBlockIndex}" index="{$smarty.capture.htmlBlockIndex}" btype="htmlBlock">
		<div class="b-actions"></div>
		<div class="portlet-header">[[Html Block]]</div>
		<div class="portlet-content">
			<fieldset>
				<div class="inputName">[[Html Block]]</div>
				<div class="clr"></div>
				<div class="inputField" id="edit_htmlBlock_{$smarty.capture.htmlBlockIndex}" style="display:none;">
					<div class="htmlBlock_info"></div>
					<textarea name="htmlBlock_{$smarty.capture.htmlBlockIndex}" class="htmlBlock"></textarea>
				</div>
				<div class="clr"></div>
				<div class="fieldValue" id="view_htmlBlock_{$smarty.capture.htmlBlockIndex}"></div>
				<div class="clr"></div>
				<div class="button" id="button_htmlBlock_{$smarty.capture.htmlBlockIndex}">Edit</div>
				<script type="text/javascript">{literal}
					var defaultBlockStyle = $("#{/literal}button_htmlBlock_{$smarty.capture.htmlBlockIndex}{literal}")[0].style.cssText;
					$(document).ready(function(){
						builderFieldBlockView("{/literal}htmlBlock_{$smarty.capture.htmlBlockIndex}{literal}");
					});
				{/literal}
				</script>
			</fieldset>
		</div>
	</div>
	<div class="clr"></div>
	</div>
	{if $mode eq "search"}
		{foreach from=$fields_inactive item=theField}
			{if $theField.type eq 'location'}
				<fieldset class="locationFieldset">
					<legend>{$theField.caption}</legend>
					<div id="inactive-fields-{$theField.sid}" class="sortable-column-{$theField.sid}">
						{foreach from=$theField.fields item="childField"}
							{include file=$field_block theField=$childField}
						{/foreach}
						{if !$theField.used && $theField.id == 'Location'}
							{include file=$field_block theField=$theField}
						{/if}
					</div>
				</fieldset>
			{/if}
		{/foreach}
	{/if}
</fieldset>
<div class="clr"></div>

<div id="listingFieldsKeys" style="display:none;">
	{literal}{<strong>*fieldID*</strong>}{/literal}
	<h1>[[You can use the following variables]]:</h1>
	{foreach from=$form_fields item="listingItem" key="propertyKey"}
		{$propertyKey} <br/>
	{/foreach}
</div>
