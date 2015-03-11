<h1>[[Registration]]</h1>
{include file="errors.tpl"}
<label>[[Please select the appropriate user group]]:</label>
<ul>
{foreach from=$user_groups_info item=user_group_info}
	<li><a href="?user_group_id={$user_group_info.id}">[[$user_group_info.name]]</a></li>
{/foreach}
</ul>