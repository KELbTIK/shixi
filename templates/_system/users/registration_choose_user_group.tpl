<h1>[[Registration]]</h1>
{include file="errors.tpl"}
[[Please select the appropriate user group]]:
{foreach from=$user_groups_info item=user_group_info}
	<p><a href="?user_group_id={$user_group_info.id}">[[$user_group_info.name]]</a></p>
{/foreach}