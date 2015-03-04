{breadcrumbs}<a href="{$GLOBALS.site_url}/task-scheduler-settings/">[[Task Scheduler Settings]]</a> &#187; [[Log View]]{/breadcrumbs}
<h1>[[Log View]]</h1>
<p>[[Show last 30 records from logs]]</p>
<textarea style="width:100%;height:400px;font-size:0.9em;" readonly="readonly">{foreach from=$log_content item=record}{$record}{/foreach}</textarea>