{breadcrumbs}<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-group/?sid={$fieldInfo.user_group_sid}">[[{$fieldInfo.user_group}]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-profile/?user_group_sid={$fieldInfo.user_group_sid}">[[Edit User Profile Fields]]</a> &#187; [[Add Field]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperplus32.png" border="0" alt="" class="titleicon" />[[Add User Profile Field]]</h1>
{include file='field_errors.tpl'}

<fieldset>
	<p class="message">[[You have successfully added a user profile field]].</p>
	{if $fieldInfo.user_group == 'Employer'}
		<p>[[In order to display the uploaded video on Company Profile page add the following code in the company_profile.tpl template]]:</p>
		<pre>{literal}
{if&nbsp;$userInfo.{/literal}{$fieldInfo.id}{literal}&nbsp;!=&nbsp;&quot;&quot;}
&nbsp;&nbsp;&nbsp;&nbsp;&lt;br/&gt;&lt;center&gt;{display&nbsp;property=&quot;{/literal}{$fieldInfo.id}{literal}&quot;}&lt;/center&gt;&lt;br/&gt;
{/if}
		{/literal}</pre>

		<p>[[In order to display the uploaded video on Display Job page add the following code in the display_job.tpl template]]:</p>
		<pre>{literal}
{if&nbsp;$listing.user.{/literal}{$fieldInfo.id}{literal}&nbsp;!=&nbsp;&quot;&quot;}
&nbsp;&nbsp;&nbsp;&nbsp;&lt;br&gt;&lt;center&gt;{display&nbsp;property=&quot;{/literal}{$fieldInfo.id}{literal}&quot;&nbsp;object=$listingOwner}&lt;/center&gt;&lt;br/&gt;
{/if}
		{/literal}</pre>
	{elseif $fieldInfo.user_group == 'JobSeeker'}
		<p>[[In order to display the uploaded video on Display Resume page add the following code in the display_resume.tpl template]]:</p>
		{if $fieldInfo.type == "youtube"}
			<pre>{literal}
{if&nbsp;$listing.user.{/literal}{$fieldInfo.id}{literal}&nbsp;&amp;&amp;&nbsp;(!$GLOBALS.settings.cookieLaw || $smarty.cookies.cookiePreferences != &quot;System&quot;)}
&nbsp;&nbsp;&nbsp;&nbsp;&lt;br&gt;&lt;br/&gt;&lt;center&gt;{display&nbsp;property=&quot;{/literal}{$fieldInfo.id}{literal}&quot;&nbsp;object=$listingOwner}&lt;/center&gt;&lt;br/&gt;
{/if}
			{/literal}</pre>
		{else}
			<pre>{literal}
{if&nbsp;$listing.user.{/literal}{$fieldInfo.id}{literal}&nbsp;!=&nbsp;&quot;&quot;}
&nbsp;&nbsp;&nbsp;&nbsp;&lt;br&gt;&lt;br/&gt;&lt;center&gt;{display&nbsp;property=&quot;{/literal}{$fieldInfo.id}{literal}&quot;&nbsp;object=$listingOwner}&lt;/center&gt;&lt;br/&gt;
{/if}
			{/literal}</pre>
		{/if}
	{else}
		<p>[[In order to display the uploaded video on Display Listing page add the following code in the corresponding display template of this listing type (display_job.tpl, display_resume.tpl etc.)]]:</p>
		<pre>{literal}
{if&nbsp;$listing.user.{/literal}{$fieldInfo.id}{literal}&nbsp;!=&nbsp;&quot;&quot;}
&nbsp;&nbsp;&nbsp;&nbsp;&lt;br&gt;&lt;br/&gt;&lt;center&gt;{display&nbsp;property=&quot;{/literal}{$fieldInfo.id}{literal}&quot;&nbsp;object=$listingOwner}&lt;/center&gt;&lt;br/&gt;
{/if}
		{/literal}</pre>
	{/if}
</fieldset>