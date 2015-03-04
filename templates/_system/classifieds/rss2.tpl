<?xml version="1.0"?>
<rss version="2.0">
	<channel>
		<title>[[{$TITLE}]]</title>
		<link><![CDATA[{$GLOBALS.site_url}]]></link>
		<description><![CDATA[{$DESCRIPTION}]]></description>
		<language>{$GLOBALS.current_language}-us</language>
		<pubDate>{$lastBuildDate} GMT</pubDate>
		<lastBuildDate>{$lastBuildDate} GMT</lastBuildDate>
		<docs><![CDATA[{$GLOBALS.site_url}{$GLOBALS.user_page_uri}]]></docs>
		<generator></generator>
		<managingEditor>editor@example.com</managingEditor>
		<webMaster>webmaster@example.com</webMaster>
		{foreach from=$listings item=listing name=listings_block}
			<item>
				<title><![CDATA[{$listing.Title}]]></title>
				<link><![CDATA[{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#\$\s]/":"-"|htmlspecialchars}.html]]></link>
				<description>
					<![CDATA[{$listing.Location.City}, [[$listing.Location.State]]
					{$listing.user.CompanyName}<br/>
					{$listing.JobDescription }]]>
				</description>
				<pubDate>{$listing.activation_date|date_format:'D, d M Y H:i:s'} GMT</pubDate>
				<guid><![CDATA[{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#\$\s]/":"-"|htmlspecialchars}.html]]></guid>
			</item>
		{/foreach}
	</channel>
</rss>