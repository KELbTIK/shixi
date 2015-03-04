{breadcrumbs}<a href="{$GLOBALS.site_url}/stat-pages/">[[Static Content]]</a> &#187; [[Edit Static Content]]{/breadcrumbs}
<h1><img src="{image}/icons/notepencil32.png" border="0" alt="" class="titleicon"/>[[Edit Static Content]]</h1>
{module name="user_pages" function="register_page_link" pageInfo=$pageInfo caption="Content"}
{$error}
<form id="staticContent" method="post">
	<input type="hidden" name="action" value="change" />
	<input type="hidden" id="formSubmitted" name="formSubmitted" value="save_content" />
	<input type="hidden" name="page_sid" value={$page_sid} />
	<table width="100%" id="editStaticContent">
		<tr>
			<td width="15%">[[ID]]</td>
			<td><input type="text" name="page_id" value="{$page.id}" /></td>
		</tr>
		<tr>
			<td>[[Static content name]]</td>
			<td><input type="text" name="name" value="{$page.name}" /></td>
		</tr>
		<tr>
			<td>[[Language]]</td>
			<td>
				<select name="lang">
					{foreach from=$languages item=language}
						<option value="{$language.id}"{if $language.id == $page.lang} selected="selected"{/if}>{$language.caption}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">[[Static content]]:</td>
		</tr>
		<tr>
			<td colspan="2">{WYSIWYGEditor name="content" width="99%" height="700" value=$page_content conf="BasicAdmin"}</td>
		</tr>
		<tr id="clearTable">
			<td colspan="2">
				<div class="floatRight">
					<input type="button" id="apply" value="[[Apply]]" class="greenButton"/>
					<input type="button" id="save" value="[[Save]]" class="greenButton" />
				</div>
			</td>
		</tr>
	</table>
</form>
<div id="messageWindow" style="display: none;">
	<p>[[You are trying to edit the system field (id). If you change the default value of this field there would be a need to make appropriate changes in the settings, templates and PHP code. Otherwise the system will function unpredictably]]</p>
</div>
{capture name="change_anyway"}[[Change anyway]]{/capture}
{capture name="don_t_change"}[[Don't change]]{/capture}
<script>
	var pageId = "{$page.id}";
	$('#apply').click(function () {
		$('#formSubmitted').attr('value', 'apply_content');
		saveStaticContentSettings();
	});
	$("#save").click(function () {
		saveStaticContentSettings();
	});

	function saveStaticContentSettings()
	{
		if (pageId == $("input[name='page_id']").val()) {
			$('#staticContent').submit();
		} else {
			showMessageWindow();
		}
	}

	function showMessageWindow()
	{
		$("#messageWindow").dialog({
			width: 600,
			height: 200,
			buttons: {
				"{$smarty.capture.change_anyway|escape:"javascript"}": function () {
					$('#staticContent').submit();
				},
				"{$smarty.capture.don_t_change|escape:"javascript"}": function () {
					$("input[name='page_id']").val(pageId);
					$('#action').attr('value', 'save_info');
					$("#messageWindow").dialog('destroy');
				}
			}
		});
	}
</script>