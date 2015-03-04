{breadcrumbs}[[Edit css]]{/breadcrumbs}
<h1><img src="{image}/icons/wand32.png" border="0" alt="" class="titleicon"/>[[Edit css]]</h1>

{if $ERROR eq "NOT_ALLOWED_IN_DEMO"}
	<p class="error">[[CSS file is not editable in demo]].</p>
{/if}

<table>
	<thead>
		<tr>
			<th>[[File]]</th>
			<th>[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$files item=file}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>{$file}</td>
			<td><a href="?action=edit&file={$file}" class="editbutton">[[Edit]]</a></td>
		</tr>
		{/foreach}
	</tbody>
</table>

<h3>{$cssFile}</h3>
{if $action == "edit" || $action == "save"}

	<link rel="stylesheet" href="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/codemirror.css">
	<link rel="stylesheet" href="{$GLOBALS.user_site_url}/system/ext/CodeMirror/theme/default.css">
	<link rel="stylesheet" href="{$GLOBALS.user_site_url}/system/ext/CodeMirror/mode/smartyhtmlmixed/smartyhtmlmixed.css">

	<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/util/match-highlighter.js"></script>
	<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/codemirror.js"></script>
	<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/mode/javascript/javascript.js"></script>
	<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/mode/xml/xml.js"></script>
	<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/mode/css/css.js"></script>
	<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/mode/smarty/smarty.js"></script>
	<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/mode/smartyhtmlmixed/smartyhtmlmixed.js"></script>
	<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/util/search.js"></script>
	<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/util/searchcursor.js"></script>
	<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/util/dialog.js"></script>

	<script type="text/javascript">
		//first set up some variables
		var textarea = document.getElementById('template_content');
		var uiOptions = { path : 'js/', searchMode : 'popup' }
		var codeMirrorOptions = { mode: "javascript" }

		//then create the editor
		var editor = new CodeMirrorUI(textarea,uiOptions,codeMirrorOptions);
	</script>

	<form method="post">
		<input type="hidden" name="file" value="{$cssFile}" />
		<input type="hidden" name="action" value="save" />
		<textarea style="width: 100%; height: 500px;" id="template_content" name="file_content">{$file_content}</textarea>
		<div class="clr"><br/></div>
	    <div class="floatRight"><input type="submit" value="[[Save]]" class="grayButton"/></div>
	</form>

	<script type="text/javascript">
		var editor = CodeMirror.fromTextArea(document.getElementById("template_content"), {
			lineNumbers: true,
			matchBrackets: true,
			mode: "css",
			indentUnit: 2,
			indentWithTabs: true,
			enterMode: "keep",
			tabMode: "shift",
			theme: "default"
		});
	</script>
{/if}
