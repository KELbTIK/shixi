(function($) {
	$.fn.getCustomMultiList = function(options, fieldId, limit) {
		var multiList;
		var searchTerm = "";
		var lastInputTime = 0;

		if (limit) {
			multiList = this.multiselect({
				create: function() {
					$("#multilist-preloader-" + fieldId).remove();
				},
				open: function() {
					$("div.ui-multiselect-menu.ui-widget.ui-widget-content.ui-corner-all").focusout(function() {
						setTimeout(function() {
								if ($(document.activeElement).get(0).tagName == "IFRAME") {
									multiList.multiselect("close");
								}
							},
							100
						);
					});
				},
				click: function(event, ui) {
					var selectedItems = $(this).multiselect("widget").find(":checkbox:checked");
					if (ui.checked) {
						if (selectedItems.length > limit) {
							return false;
						}
					}
					window["showAvailableCounter"](fieldId, limit - selectedItems.length);
				},
				uncheckAll: function() {
					window["showAvailableCounter"](fieldId, limit);
				}
			});

			multiList.multiselect("widget").find("div.ui-multiselect-header").undelegate("a", "click.multiselect").delegate("a", "click.multiselect", function(e) {
				if ($(this).hasClass("ui-multiselect-close")) {
					multiList.multiselect("close");
				} else {
					var checkAvailableQuantity = function(limit) {
						var selectedItems = multiList.multiselect("widget").find(":checkbox:checked");
						var tags = multiList.find("option");
						if (selectedItems.length < limit) {
							multiList.multiselect("widget").find(":checkbox:not(:checked)").each(function(index) {
								if (index < (limit - selectedItems.length)) {
									this.checked = true;
									this.setAttribute("aria-selected", true);
									var val = this.value;
									tags.each(function() {
										if(this.value === val) {
											this.selected = true;
										}
									});
								}
							});
							multiList.multiselect("update");
							selectedItems = multiList.multiselect("widget").find(":checkbox:checked");
							window["showAvailableCounter"](fieldId, limit - selectedItems.length);
							return false;
						}
						window["showAvailableCounter"](fieldId, 0);
						return false;
					};
					$(this).hasClass("ui-multiselect-all") ? checkAvailableQuantity(limit) : multiList.multiselect("uncheckAll");
				}
				e.preventDefault();
			});
		} else {
			multiList = this.multiselect({
				create: function() {
					$("#multilist-preloader-" + fieldId).remove();
				},
				open: function() {
					$("div.ui-multiselect-menu.ui-widget.ui-widget-content.ui-corner-all").focusout(function() {
						setTimeout(function() {
								if ($(document.activeElement).get(0).tagName == "IFRAME") {
									multiList.multiselect("close");
								}
							},
							100
						);
					});
				}
			});
		}

		multiList.multiselect("widget").undelegate("label", "keydown.multiselect").keypress(function(e) {
			e.preventDefault();
			var rEscape = /[\-\[\]{}()*+?.,\\\^$|#\s]/g;
			var currentTime = (new Date()).getTime();
			if (currentTime - lastInputTime > 1000) {
				searchTerm = "";
			}
			
			lastInputTime = currentTime;
			
			var charCode = e.which || e.keyCode;
			if ($.inArray(charCode, [9, 13, 27, 37, 38, 39, 40]) > -1) {
				return false;
			} else {
				searchTerm = searchTerm + String.fromCharCode(charCode);
				var regex = new RegExp("^" + searchTerm.replace(rEscape, "\\$&"), "i");
				var itemToScroll = $(this).find("input").filter(function() {
					if ($(this).attr("title").match(regex)) {
						return this;
					}
				}).first();
				if (itemToScroll.length) {
					var container = $(this).find("ul").last();
					container.scrollTop(itemToScroll.parent().offset().top - container.offset().top + container.scrollTop());
					itemToScroll.parent().trigger("mouseover");
					$(this).focus();
				}
			}
		}).keydown(function(e) {
			var charCode = e.which || e.keyCode;
			if ($.inArray(charCode, [9, 13, 27, 37, 38, 39, 40]) > -1) {
				e.preventDefault();
				var currentItem = $(this).find("label.ui-state-hover");
				switch (charCode) {
					case 9: // tab
					case 27: // esc
						multiList.multiselect("close");
						break;
					case 38: // up
					case 40: // down
					case 37: // left
					case 39: // right
						var moveToLast = charCode === 38 || charCode === 37;
						var nextItem = currentItem.parent()[moveToLast ? "prevAll" : "nextAll"]("li:not(.ui-multiselect-disabled, .ui-multiselect-optgroup-label)")[ moveToLast ? "last" : "first"]();
						if (!nextItem.length) {
							$(this).find("label")[ moveToLast ? "last" : "first" ]().trigger("mouseover");
						} else {
							var container = $(this).find("ul").last();
							container.scrollTop(nextItem.offset().bottom);
							nextItem.find("label").trigger("mouseover");
						}
						break;
					case 13: // enter
						currentItem.find("input")[0].click();
						break;
				}
			}
		});
		multiList.multiselect(options);
		return multiList;
	};
})(jQuery);
