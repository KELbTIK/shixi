{if $templateParams.type == "bool"}
	<input type="text" value="{if $value.exact_phrase}{$value.exact_phrase}{elseif $value.all_words}{$value.all_words}{elseif $value.any_words}{$value.any_words}{elseif $value.boolean}{$value.boolean}{else}{$value.like}{/if}" class="searchText" name="{$id}[like]"  id="{$id}" /><br/>
	<div style="display: inline-block; float: left;">
		<select size="1" id="searchType-{$id}">
			<option value="all_words" {if $value.all_words}selected="selected"{/if}>[[Match all words]]</option>
			<option value="any_words" {if $value.any_words}selected="selected"{/if}>[[Match any words]]</option>
			<option value="exact_phrase" {if $value.exact_phrase}selected="selected"{/if}>[[Match exact phrase]]</option>
			<option value="boolean" {if $value.boolean}selected="selected"{/if}>[[Boolean]]</option>
		</select>
	</div>
	<div class="search-only">
		<span>{if $templateParams.listingType == "Job"}[[Search job title only]]{elseif $templateParams.listingType == "Resume"}[[Search resume title only]]{else}[[Search by title]]{/if}</span>
		<input type="checkbox" value="Title" id="titleOnly-{$id}" {if $title}checked="checked"{/if} style="margin: 3px 1px !important;" />
	</div>
	<div id="helplink"></div>
	<script type="text/javascript">
	{literal}
		//FIXME: будет вываливаться на одной форме будет несколько полей типа bool
	$.ui.dialog.prototype.options.bgiframe = true;

		function setBoolSearch(id) {
			var where = id;
			var fieldId = '#' + id;
			var stId = "#searchType-" + id;
			var toId = "#titleOnly-" + id;
			$(fieldId).attr('name', where+'['+$(stId).val()+']');
			$(stId).change(function() {
				$(fieldId).attr('name', where+'['+$(stId).val()+']');
				if ($(stId).val()=="boolean") {
					$("#helplink").html("<a href='#'  onclick='showHelp();'>"+{/literal}'[[Boolean search description]]'{literal}+"<\/a>");
				}
				else {
					$("#helplink").html("");
				}
			}).change();

			if ($(stId).val() == "boolean") {
				$("#helplink").html("<a href='#'  onclick='showHelp();'>"+{/literal}'[[Boolean search description]]'{literal}+"<\/a>");
			}
			else {
				$("#helplink").html("");
			}

			$(toId).change(function() {
				where = id;
				if ($(toId).is(':checked'))
					where = "Title";
				$(fieldId).attr('name', where+'['+$(stId).val()+']');
			}).change();
		}
		setBoolSearch('{/literal}{$id}{literal}');

		function showHelp() {
			$.get('{/literal}{$GLOBALS.site_url}{literal}/boolean-search/', function(data) {
				$("#messageBox").dialog('destroy').html(data);
				$("#messageBox").dialog({
					width: 500,
					height: 500,
					modal: true,
					title: {/literal}'[[Boolean search description]]'{literal}
				}).dialog( 'open' );
			});
			return false;
		}

	</script>
	{/literal}

{else}
	<input type="text" value="{if $id == 'keywords'}{$value.all_words}{$value.exact_phrase}{$value.any_words}{$value.boolean}{else}{$value.like}{/if}" class="searchText form-control" name="{$id}[{if $id == 'keywords'}all_words{else}like{/if}]"  id="{$id}" />
{/if}
{if $GLOBALS.settings.use_autocomplete_for_keywords != false}
	{include file='../field_types/search/autocomplete.tpl'}
{/if}




