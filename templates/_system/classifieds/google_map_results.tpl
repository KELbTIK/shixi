<link rel="stylesheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery-ui.css" />
{if $smarty.get.lightbox}
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.bgiframe.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
{capture name="displayProgressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}
<script type="text/javascript">
	$.ui.dialog.prototype.options.bgiframe = true;
	function submitForm(id) {
		lpp = document.getElementById("listings_per_page" + id);
		location.href = "?searchId={$searchId|escape:'url'}&action=search&page=1&listings_per_page=" + lpp.value;
	}

	function popUpWindow(url, widthWin, title, parentReload, userLoggedIn) {
		reloadPage = false;
		newPageReload = false;
		$("#loading").show();
		$("#messageBox").dialog( 'destroy' ).html("{$smarty.capture.displayProgressBar|escape:'javascript'}");
		$("#messageBox").dialog({
			autoOpen: false,
			width: widthWin,
			height: 'auto',
			modal: true,
			title: title,
			close: function(event, ui) {
				if((parentReload == true) && !userLoggedIn || newPageReload == true) {
					if(reloadPage == true)
						parent.document.location.reload();
				}
			}
		}).hide();
		$.get(url, function(data) {
			$("#messageBox").html(data).dialog("open").show();
			$("#loading").hide();
		});
		return false;
	}

	function SaveAd(noteId, url) {
		$.get(url, function(data) {
			$("#" + noteId).html(data);
		});
	}
</script>
<div id="loading"></div>
<div id="messageBox"></div>
{/if}

<!-- GOOGLE MAP SEARCH RESULTS -->
<div id="map"></div>
<!-- END OF GOOGLE MAP SEARCH RESULTS -->

<!-- GOOGLE MAP SECTION -->
<script src="https://maps.google.com/maps/api/js?v=3.3&sensor=false" type="text/javascript"></script>
<script type="text/javascript"><!--
//<![CDATA[

function xmlParse(str) {
	if (typeof ActiveXObject != 'undefined' && typeof GetObject != 'undefined') {
		var doc = new ActiveXObject('Microsoft.XMLDOM');
		doc.loadXML(str);
		return doc;
	}
	if (typeof DOMParser != 'undefined') {
		return (new DOMParser()).parseFromString(str, 'text/xml');
	}
	return createElement('div', null);
}

	var markersArray = [];
	var infoWindows = [];

function load(markersXmlData, divId) {
	var myLatLng = new google.maps.LatLng(47.814495, -122.411861);
	var myOptions = {
		zoom: 11,
		center: myLatLng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById(divId), myOptions);
	if (markersXmlData != null && markersXmlData != '<markers></markers>') {
		var lat_max, lat_min, lng_max, lng_min;
		var xml = xmlParse(markersXmlData);
		var markers = xml.documentElement.getElementsByTagName("marker");
		// set map center to first marker
		map.setCenter(new google.maps.LatLng( markers[0].getAttribute("lat"), markers[0].getAttribute("lng")));
		for (var i = 0; i < markers.length; i++) {
			var lat = parseFloat(markers[i].getAttribute("lat"));
			var lng = parseFloat(markers[i].getAttribute("lng"));
			// skip marker if no coordinates for it
			if (isNaN(lat) == false && isNaN(lng) == false) {
				if (lat > lat_max || lat_max == undefined) {
					lat_max = lat;
				}
				if (lat < lat_min || lat_min == undefined) {
					lat_min = lat;
				}
				if (lng > lng_max || lng_max == undefined) {
					lng_max = lng;
				}
				if (lng < lng_min || lng_min == undefined) {
					lng_min = lng;
				}
				var text    = markers[i].firstChild.nodeValue;
				var type    = markers[i].getAttribute("type");
				var point   = new google.maps.LatLng(lat, lng);
				markersArray.push(createMarker(map, point, text));
			}
		}
		if (markers.length > 1) {
			var lat_max_abs = Math.abs(lat_max);
			var lat_min_abs = Math.abs(lat_min);
			var lng_max_abs = Math.abs(lng_max);
			var lng_min_abs = Math.abs(lng_min);
			var lat_diff = Math.abs(lat_max_abs-lat_min_abs);
			var lng_diff = Math.abs(lng_max_abs-lng_min_abs);
			map.panTo(new google.maps.LatLng(lat_min + (lat_diff/2), lng_min + (lng_diff/2)));
			var bounds = new google.maps.LatLngBounds(new google.maps.LatLng(lat_min, lng_min), new google.maps.LatLng(lat_max, lng_max));
			map.fitBounds(bounds);
		}
	}
}

function createMarker(map, posLatLng, text) {
	var contentString = '<div style="padding: 5px 20px;">' + text + '</div>';
	var infoWindow = new google.maps.InfoWindow({
		content: contentString
	});
	infoWindows.push(infoWindow);
	var marker = new google.maps.Marker({
		position: posLatLng,
		map: map,
		title: '',
		icon:  '',
		flat: true,
		markerText: contentString,
		optimized: false
	});
	google.maps.event.addListener(marker, 'click', function() {
		for (var i = 0; i < infoWindows.length; i++) {
			infoWindows[i].close();
		}
		infoWindow.open(map, marker);
	});
	google.maps.event.addListener(infoWindow, 'closeclick', function() {
		var my_content = infoWindow.getContent();
		var id = my_content.replace(/.+<span id="notes_(\d+)".+>/, '$1');
		var note = $('#notes_' + id).html();
		infoWindow.setContent(my_content.replace(/(<span.+)<a.+<\/a>.+(<\/span>)/g, '$1' + note + '$2'));
	});
	return marker;
}

// FOR GOOGLE MAP CODE
var mapListings = '<markers>' +
{foreach from=$listings item=listing name=listings}
	{if $listing.latitude && $listing.longitude}
		{if $listing.type.id != 'Resume'}
			'<marker lat="{$listing.latitude}" lng="{$listing.longitude}" type="{if $listing.api}{$listing.api}{elseif $listing.priority}priority{else}{$listing.type.id}{/if}" ><![CDATA[' +
			"<h3><a href=\"{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"|escape:"url"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}\" target=\"_blank\"><strong>{$listing.Title|escape:'html'}<\/strong><\/a><\/h3>" +
			"<strong>[[Company]]:<\/strong> " +
				'<a href="{$GLOBALS.site_url}/company/{$listing.user.id}/{$listing.user.CompanyName|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/">{$listing.user.CompanyName|escape:"html"}<\/a>' +
			'<br />' +
			'<strong>[[Location]]:<\/strong> {locationFormat location=$listing.Location format="short"}<br />' +
			'<hr color="#DDDDDD" />' +
			'<span id="notes_{$listing.id}">' +
			{if $listing.saved_listing &&  $acl->isAllowed('save_job')}
				{if $listing.saved_listing.note && $listing.saved_listing.note != ''}
					'<a href="{$GLOBALS.site_url}/edit-notes/?listing_id={$listing.id}" onclick="popUpWindow(\'{$GLOBALS.site_url}/edit-notes/?listing_sid={$listing.id}&amp;view=map\', 500, \'[[Edit notes]]\'); return false;"  class="action">[[Edit notes]]<\/a>&nbsp;&nbsp;' +
					{else}
					'<a href="{$GLOBALS.site_url}/add-notes/?listing_id={$listing.id}" onclick="popUpWindow(\'{$GLOBALS.site_url}/add-notes/?listing_sid={$listing.id}&amp;view=map\', 500, \'[[Add notes]]\'); return false;"  class="action">[[Add notes]]<\/a>&nbsp;&nbsp;' +
				{/if}
				{else}
				{if $acl->isAllowed("save_job")}
					'<a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}" onclick="{if $GLOBALS.current_user.logged_in}SaveAd(\'notes_{$listing.id}\', \'{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&listing_type=job&amp;view=map\'){else}popUpWindow(\'{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&listing_type=job\', 300, \'Save this Job\', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}){/if}; return false;" class="action">[[Save ad]]<\/a>&nbsp;&nbsp;' +
					{elseif $acl->getPermissionParams("save_job") == "message"}
					'<a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}" onclick="popUpWindow(\'{$GLOBALS.site_url}/access-denied/?permission=save_job\', 300, \'[[Save ad]]\'); return false;" class="action">[[Save ad]]<\/a>&nbsp;&nbsp;' +
				{/if}
			{/if}
			'<\/span>' +
			"<a href=\"{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"|escape:"url"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}\" target=\"_blank\">[[View job details]]<\/a>" +
			{elseif $listing.type.id == 'Resume'}
			'<marker lat="{$listing.latitude}" lng="{$listing.longitude}" type="{$listing.type.id}" ><![CDATA[' +
			'<strong>[[Name]]:<\/strong> {if $listing.anonymous == 1}[[Anonymous User]]{else}{$listing.user.FirstName|escape:'html'} {$listing.user.LastName|escape:'html'}{/if}' +
			"<h3><a href=\"{$GLOBALS.site_url}/display-resume/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"|escape:"url"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}\" target=\"_blank\"><strong>{$listing.Title|escape:'html'}<\/strong><\/a><\/h3>" +
			'<strong>[[City]]:<\/strong> {$listing.Location.City}<br />' +
			'<span id="notes_{$listing.id}">' +
			{if $listing.saved_listing && $acl->isAllowed('save_resume')}
				{if $listing.saved_listing.note && $listing.saved_listing.note != ''}
					'<a href="{$GLOBALS.site_url}/edit-notes/?listing_id={$listing.id}" onclick="popUpWindow(\'{$GLOBALS.site_url}/edit-notes/?listing_sid={$listing.id}&amp;view=map\', 500, \'[[Edit notes]]\'); return false;"  class="action">[[Edit notes]]<\/a>&nbsp;&nbsp;' +
					{else}
					'<a href="{$GLOBALS.site_url}/add-notes/?listing_id={$listing.id}" onclick="popUpWindow(\'{$GLOBALS.site_url}/add-notes/?listing_sid={$listing.id}&amp;view=map\', 500, \'[[Add notes]]\'); return false;"  class="action">[[Add notes]]<\/a>&nbsp;&nbsp;' +
				{/if}
				{else}
				{if $acl->isAllowed('save_resume')}
					'<a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}" onclick="{if $GLOBALS.current_user.logged_in}SaveAd(\'notes_{$listing.id}\', \'{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&listing_type=resume&amp;view=map\'){else}popUpWindow(\'{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&listing_type=resume\', 300, \'[[Save this Resume]]\', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}){/if}; return false;"  class="action">[[Save ad]]<\/a>&nbsp;&nbsp;' +
					{elseif $acl->getPermissionParams("save_resume") == "message"}
					'<a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}" onclick="popUpWindow(\'{$GLOBALS.site_url}/access-denied/?permission=save_resume\', 300, \'[[Save ad]]\'); return false;"  class="action">[[Save ad]]<\/a>&nbsp;&nbsp;' +
				{/if}
			{/if}
			'<\/span>' +
			"<a href=\"{$GLOBALS.site_url}/display-resume/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"|escape:"url"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}\" target=\"_blank\">[[View resume details]]<\/a>" +
		{/if}
		']]><\/marker>' +
		'\n' +
	{/if}
{/foreach}
'<\/markers>';

load(mapListings, "map");

//]]>-->
</script>
<!-- END OF GOOGLE MAP SECTION -->