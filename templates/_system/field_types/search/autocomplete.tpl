<script type="text/javascript">
	$(document).ready(function() {
		function liFormat (row, i, num) {
			return htmlentities(row[0]);
		}
		
		function selectItem(li) {
			var sValue;
			
			if (li == null) {
				sValue = 'Nothing Selected!';
			}
			
			if (!!li.extra) {
				sValue = li.extra[2];
			} else {
				sValue = li.selectValue;
			}
			
			alert("Selected ID: " + sValue);
		}
		
		var elementId     = "{if $parentID}{$parentID}_{$id}{else}{$id|replace:':':'\\\\:'}{/if}";
		var siteUrl       = "{$GLOBALS.site_url}/autocomplete/";
		var field         = "{if $parentID}{$parentID}_{/if}{$id}/";
		var fieldType     = "{if $type}{$type}{elseif $theField.type}{$theField.type}{else}text{/if}/";
		var tablePrefix   = "{if $isClassifieds == 1}listings{else}users{/if}/";
		var viewType      = "{$parameters.viewType}/";
		var listingTypeID = "{if $listingTypeID}{$listingTypeID|escape:'javascript'}{else}Job{/if}/";
		
		$("input#" + elementId).autocomplete(siteUrl + field + fieldType + tablePrefix + viewType + listingTypeID, {
			delay:10,
			minChars:{if $GLOBALS.settings.min_autocomplete_symbols_quantity}{$GLOBALS.settings.min_autocomplete_symbols_quantity}{else}1{/if},
			matchSubset:1,
			autoFill:false,
			width: 300,
			matchContains:1,
			cacheLength:1,
			selectFirst:true,
			formatItem:liFormat,
			maxItemsToShow:5,
			onItemSelect:selectItem
		})
	}); 
</script>
