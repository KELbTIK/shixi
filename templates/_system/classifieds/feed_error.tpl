<?xml version="1.0"?>
<rss version="2.0">
  <channel>
    <title>RSS Error</title>
    <link>{$GLOBALS.site_url}</link>
    <description>{foreach from=$errors item=error} {$error} {/foreach}</description>
    <language>{$GLOBALS.current_language}-us</language>
    <pubDate></pubDate>
	<lastBuildDate></lastBuildDate>
    <docs>{$GLOBALS.site_url}</docs>
    <generator></generator>
    <managingEditor></managingEditor>
    <webMaster></webMaster>
   	<item></item>
  </channel>
</rss>