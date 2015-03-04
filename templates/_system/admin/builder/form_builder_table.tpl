{foreach from=$listingTypesInfo item="listingTypeInfo"}
    {assign var="listingTypeSID" value=$listingTypeInfo.sid}
	<div class="listing-type-form">
		<table border="1">
			<colgroup span="2">
				<col width="70%"/>
				<col width="10%"/>
			</colgroup>
			<thead>
				<tr>
					<th colspan="3">[[{$listingTypeInfo.name} Listings]]</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>[[Search {$listingTypeInfo.name} Form]]</td>
					<td>
						<span class="editbutton" title="[[Edit {$listingTypeInfo.name} Search]]" id="search-{$listingTypeInfo.id}"
							  href="{$GLOBALS.site_url}/builder/search-form/{$listingTypeInfo.id}/">[[Edit]]</span>
					</td>
				</tr>
				<tr>
					<td>[[Display {$listingTypeInfo.name} Page]]</td>
					<td>
						<span class="editbutton" title="[[Edit {$listingTypeInfo.name} Display]]" id="display-{$listingTypeInfo.id}"
							  href="{$GLOBALS.site_url}/builder/display-listing/{$listingTypeInfo.id}/">[[Edit]]</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
{/foreach}


<script type="text/javascript">
	$("document").ready(function(){
		$(".editbutton").each(function(){
			var curElem = $(this);
			curElem.click(function() {
				var elemHref = curElem.attr("href");
				var elemID = curElem.attr("id").replace("-", "_");
				window.open (elemHref,elemID,"status=0,toolbar=0,scrollbars=1,resizable=1,width=1200,height=800");
			});
		});
	});
</script>