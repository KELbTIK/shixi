{capture name="collapseMessage"}[[collapse]]{/capture}
{capture name="sortMessage"}[[Click on a title to sort table]]{/capture}
{capture name="backtrace"}[[Debug backtrace]]{/capture}
<script type="text/javascript">
{literal}
var sortMessage = "{/literal}{$smarty.capture.sortMessage|escape:"javacsript"}{literal}";
var collapseMessage = "{/literal}{$smarty.capture.collapseMessage|escape:"javacsript"}{literal}";
var backtraceTitle = "{/literal}{$smarty.capture.backtrace|escape:"javacsript"}{literal}";
$(document).ready(function () {
	$("#profilerContainer").show();
	$("body").css("margin-bottom", 60);
	$("#functionsTab").bind('click', function () {
		changeTab('functions');
	});
	$("#queriesTab").bind('click', function () {
		changeTab('queries');
	});
	$("#collapseTab").bind('click', function () {
		hideAllTabs();
		$("#collapseTab").hide();
		$("body").css("margin-bottom", 60);
	});
	$("#blockResize").bind('mousedown', function () {
		changeProfilerSize();
	});
	$(".sort").bind('click', sort);
	$(".sort").attr("title", sortMessage);
	$(".collapseTab").attr('title', collapseMessage);
});

function changeTab(tab)
{
	hideAllTabs();
	$("#collapseTab").show();
	$("#profiler").addClass(tab);
	$("#logsBlock").show();
	var profilerHead = $("#logsBlock").height();
	$("body").css("margin-bottom", profilerHead + 60);
	$("#blockResize").show();
}

function hideAllTabs()
{
	$("#profiler").removeClass("functions");
	$("#profiler").removeClass("queries");
	$("#blockResize").hide();
	$("#logsBlock").hide();
}
/****************************************************************************/

// sort table. Get from http://htmlcssjs.ru/JavaScript/?22
function sort(event)
{
	var el = window.event ? window.event.srcElement : event.currentTarget;
	while (el.tagName.toLowerCase() != "td") {
		el = el.parentNode;
	}
	var sortArray = new Array();
	var name = el.lastChild.nodeValue;
	var dad = el.parentNode;
	var table = dad.parentNode.parentNode;
	var up = table.up;
	var node, arrow, currentColumn;
	var i;
	for (i = 0; (node = dad.getElementsByTagName("td").item(i)); i++) {
		if (node.lastChild.nodeValue == name) {
			currentColumn = i;
			if (node.className == "currentColumn") {
				table.up = Number(!up);
			} else {
				node.className = "currentColumn";
				table.up = 0;
			}
		} else {
			if (node.className == "currentColumn") {
				node.className = "";
			}
		}
	}
	var tbody = table.getElementsByTagName("tbody").item(0);
	for (i = 0; (node = tbody.getElementsByTagName("tr").item(i)); i++) {
		sortArray[i] = new Array();
		sortArray[i][0] = getConcatenatedTextContent(node.getElementsByTagName("td").item(currentColumn));
		sortArray[i][1] = getConcatenatedTextContent(node.getElementsByTagName("td").item(1));
		sortArray[i][2] = getConcatenatedTextContent(node.getElementsByTagName("td").item(0));
		sortArray[i][3] = node;
	}
	sortArray.sort(_sort);
	if (table.up) {
		sortArray.reverse();
	}
	for (i = 0; i < sortArray.length; i++) {
		tbody.appendChild(sortArray[i][3]);
	}
}

function getConcatenatedTextContent(node)
{
	var _result = "";
	if (node == null) {
		return _result;
	}
	var childrens = node.childNodes;
	var i = 0;
	while (i < childrens.length) {
		var child = childrens.item(i);
		switch (child.nodeType) {
			case 1: // ELEMENT_NODE
			case 5: // ENTITY_REFERENCE_NODE
				_result += getConcatenatedTextContent(child);
				break;
			case 3: // TEXT_NODE
			case 2: // ATTRIBUTE_NODE
			case 4: // CDATA_SECTION_NODE
				_result += child.nodeValue;
				break;
			case 6: // ENTITY_NODE
			case 7: // PROCESSING_INSTRUCTION_NODE
			case 8: // COMMENT_NODE
			case 9: // DOCUMENT_NODE
			case 10: // DOCUMENT_TYPE_NODE
			case 11: // DOCUMENT_FRAGMENT_NODE
			case 12: // NOTATION_NODE
				// skip
				break;
		}
		i++;
	}
	return _result;
}

function _sort(a, b)
{
	var firstCriterion = a[0];
	var secondCriterion = b[0];
	var firstModifyCriterion = (firstCriterion + '').replace(/,/, '.');
	var secondModifyCriterion = (secondCriterion + '').replace(/,/, '.');
	if (parseFloat(firstModifyCriterion) && parseFloat(secondModifyCriterion)) {
		return sortNumbers(parseFloat(firstModifyCriterion), parseFloat(secondModifyCriterion));
	} else {
		return sortInsensitive(firstCriterion, secondCriterion);
	}
}

function sortNumbers(a, b)
{
	return a - b;
}

function sortInsensitive(a, b)
{
	var firstToLowerCase = a.toLowerCase();
	var secondToLowerCase = b.toLowerCase();
	if (firstToLowerCase < secondToLowerCase) {
		return -1;
	}
	if (firstToLowerCase > secondToLowerCase) {
		return 1;
	}
	return 0;
}
/***********************************************************************/

function showBackTrace(id)
{
	$(".backtrace").dialog('destroy');
	var blockName = "#" + id;
	$(blockName).attr({title:backtraceTitle});
	$(blockName).dialog({width:500, height:600, modal: true});
}

function changeProfilerSize()
{
	$(document).bind('mousemove', function(event) {
		var y = event.clientY;
		var height = $(window).height();
		$("#logsBlock").height(height - y - 60);
		$("body").css("margin-bottom", height - y);
	});
	$(document).bind('mouseup', function () {
		$(document).unbind("mousemove");
	});
}
</script>
{/literal}

<div id="profilerContainer" class="profiler" style="display:none">
	<div id="blockResize" class="blockResize"></div>
	<div id="profiler" class="profilerHead">
		<table id="metrics" class="metrics" cellspacing="0">
			<tr>
				<td id="infometr">
					<h4>[[RAM]]: {$memory}</h4>
					<h4>[[Time]]: {$time}</h4>
				</td>
				<td id="functionsTab" class="blue">
					<var>{$functionCount}</var>
					<h4>[[Functions]]</h4>
				</td>
				<td id="queriesTab" class="purple">
					<var>{$queryCount} [[Queries]]</var>
					<h4>[[Database]]</h4>
				</td>
				<td id="collapseTab" class="collapseTab">
					<var>_</var>
				</td>
			</tr>
		</table>
		<div id="logsBlock" class="logsBlock">
			<div id="functionsLog" class="functionsTab">
				{if $functionCount == 0}<h3>[[This panel has no log items]].</h3>
					{else}
					<table class='main' align='center'>
						<thead>
						<tr>
							<td class="sort">[[Number]]</td>
							<td class="sort">[[Module]]</td>
							<td class="sort">[[Function]]</td>
							<td class="sort">[[Time]]</td>
						</tr>
						</thead>
						<tbody>
							{foreach from=$functionInfo item=functionNumber key=number}
							<tr>
								<td><b>{$number}.</b></td>
								<td><b>{$functionNumber.module_name}</b></td>
								<td><b>{$functionNumber.function_name}</b></td>
								<td><b>{$functionNumber.time}</b></td>
							</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>

			<div id="queriesLog" class="queriesTab">
				{if $queryCount == 0}
					<h3>[[This panel has no log items]]</h3>
				{else}
					<table class='main' cellspacing='0'>
						<thead>
						<tr>
							<td class="sort">[[Number]]</td>
							<td class="sort">[[SQL Query]]</td>
							<td class="sort">[[Time]]</td>
							<td class="sort">[[Module]]</td>
							<td class="sort">[[Function]]</td>
							<td style="display:none">&nbsp;</td>
						</tr>
						</thead>
						<tbody>
							{foreach from=$queryInfo item=queriesLogData key=number}
								<tr onclick="showBackTrace('block{$number}')">
									<td><b>{$number}.</b></td>
									<td><b>{$queriesLogData.sql|escape:"html"}</b></td>
									<td><b>{$queriesLogData.time}</b></td>
									<td><b>{$queriesLogData.module_name}</b></td>
									<td><b>{$queriesLogData.function_name}</b></td>
									<td style="display: none;">
										<div id="block{$number}" style="display: none;" class="backtrace">
											{foreach from=$queriesLogData.debug item=content key=id}
												<p>
													#{$id} {$content.file}({$content.line})
													{if $content.class}
														{$content.class}{$content.type}{$content.function}()
													{/if}
												</p>
											{/foreach}
										</div>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
	</div>
</div>

