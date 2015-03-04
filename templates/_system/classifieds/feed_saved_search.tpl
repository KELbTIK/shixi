<?xml version="1.0"?>
<rss version="2.0">
	<channel>
		<title>RSS - Saved Search</title>
		<link><![CDATA[{$GLOBALS.site_url}]]></link>
		<description><![CDATA[{$search_name} Saved Search]]></description>
		<language>{$GLOBALS.current_language}-us</language>
		<pubDate>{$lastBuildDate} GMT</pubDate>
		<lastBuildDate>{$lastBuildDate} GMT</lastBuildDate>
		<docs><![CDATA[{$GLOBALS.site_url}/listing-feeds/?{$query_string}]]></docs>
		<generator></generator>
		<managingEditor></managingEditor>
		<webMaster></webMaster>
		{foreach from=$listings item=listing name=listings_block}
			<item>
				<title><![CDATA[{$listing.Title}]]></title>
				{if $listing_type_id == 'Resume'}
					<link><![CDATA[{$GLOBALS.site_url}/display-resume/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#\$\s]/":"-"}.html]]></link>
					<description>
						<![CDATA[{$listing.Location.City}, [[$listing.Location.State]]
						{$listing.user.FirstName} {$listing.user.LastName}<br/>
						[[Objective]]: {$listing.Objective}<br/>
						[[Work Experience]]: {$listing.WorkExperience}<br/>
						[[Education]]: {$listing.Education}<br/>
						[[Skills]]: {$listing.Skills}]]>
					</description>
					<pubDate>{$listing.activation_date|date_format:'D, d M Y H:i:s'} GMT</pubDate>
					<guid><![CDATA[{$GLOBALS.site_url}/display-resume/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#\$\s]/":"-"}.html]]></guid>
				{else}
					<link>
					<![CDATA[{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#\$\s]/":"-"}.html]]></link>
					<description>
						<![CDATA[{$listing.Location.City}, [[$listing.Location.State]]
						{$listing.user.CompanyName}<br/>
						{$listing.JobDescription }]]>
					</description>
					<pubDate>{$listing.activation_date|date_format:'D, d M Y H:i:s'} GMT</pubDate>
					<guid><![CDATA[{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#\$\s]/":"-"}.html]]></guid>
				{/if}
			</item>
		{/foreach}
	</channel>
</rss>