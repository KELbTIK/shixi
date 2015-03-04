<p class="error">[[You don’t have the required permissions to access this page]].</p>
<h4>[[Your allowed permissions are the following]]:</h4>
<ul>
	{foreach from=$permissions item=permission}
		{if $permission.value eq 'allow' && !$permission.notification}
			<li>[[{$permission.title}]]</li>
		{/if}
	{foreachelse}
		[[You have no permissions]].
	{/foreach}
</ul>

[[For any questions, please contact the main Administrator at]] <a href="mailto:{$admin_email}">{$admin_email}</a>
<div class="return_toprev_page">
	<a href="javascript:history.back()" title="[[Back]]">[[Return to the previous page]]</a>
</div>