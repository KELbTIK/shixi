{assign var="feedName" value=$feedInfo.feed_name}
{assign var="networkName" value="{$network|capitalize}"}

<h2>[[Feed]]: <span class="twitter-feed-name" style="color:#00abeb;">{$feedName}</span></h2>

{if isset($foundListingsToPost)}
	[[Jobs meeting criteria]]: <span class="strong">{$foundListingsToPost}</span><br/>
	{if $foundListingsToPost > 0}
		[[Posted today]]: <span class="strong">{$postedToday}</span><br/>
		[[Posting Limit]]: <span class="strong">{$postingLimit}</span> ([[per day]])<br/><br/>
		[[To post these Jobs to $networkName ($feedName) now press "Ok"]]<br/>
	{/if}
{else}
	{if !empty($errors.feed)}
		<div style="font-size: 10px;">
			{foreach from=$errors.feed item="error"}
				{foreach from=$error.params item="param" key="paramKey"}
					{assign var="param_$paramKey" value=$param}
				{/foreach}
				<p class="error">[[{$error.message}]]</p>
			{/foreach}
		</div>
	{/if}
	{if !empty($errors.common)}
		{foreach from=$errors.common item="error"}
		<p class="error">[[{$error}]]</p>
		{/foreach}
	{/if}

	<p>[[Posting Limit]]: <span class="strong">{$postingLimit}</span> ([[per day]])</p>
	<p>[[Jobs were successfully posted]]: <span class="strong">{$postedListingsNum}</span></p>
	<hr/>
{/if}
