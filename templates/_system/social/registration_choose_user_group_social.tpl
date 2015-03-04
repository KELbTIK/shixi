<h1>[[Registration]]</h1>
[[You are almost registered on our site! Please choose your user group]]:
{foreach from=$user_groups_info item=user_group_info}
	<p><a href="?user_group_id={$user_group_info.id}">[[$user_group_info.name]]</a></p>
{/foreach}