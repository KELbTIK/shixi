var RefineSearchBlock = {
	show: function (curID) {
		CookieHelper.set(curID, 'v');
		var obj = $("#"+curID);
		obj.next(".refine_block").show();
		obj.children(".refine_icon").html("[-]");
		obj.children(".refine_icon").addClass("less");
		obj.children(".refine_icon").removeClass("more");
	},

	hide: function (curID) {
		CookieHelper.set(curID, 'h');
		var obj = $("#"+curID);
		obj.next(".refine_block").hide();
		obj.children(".refine_icon").html("[+]");
		obj.children(".refine_icon").addClass("more");
		obj.children(".refine_icon").removeClass("less");
	},

	showHide: function (curID)	{
		if ($("#"+curID).next(".refine_block").css("display") == "block") {
			this.hide(curID);
		} else {
			this.show(curID);
		}
	},

	restore: function (curID, hideDef) {
		if (CookieHelper.get(curID) == 'h') {
			this.hide(curID);
		} else if (CookieHelper.get(curID) == 'v') {
			this.show(curID);
		} else {
			if (hideDef) {
				this.hide(curID);
			} else {
				this.show(curID);
			}
		}
	}
}

var CookieHelper = {
	get: function (rname)	{
		var tmp = "" + document.cookie;
		var result = "";
		while (tmp.length) {
			splitter = tmp.indexOf(";");
			if (splitter < 0) {
				splitter = tmp.length + 1;
			}
			subject = tmp.substring(0, splitter);
			if (decodeURIComponent($.trim(subject.substring(0, subject.indexOf('=')))) == rname) {
				result = subject.substring(subject.indexOf('=') + 1, subject.length);
			}
			tmp = tmp.substring(splitter + 1, tmp.length);
		}
		return result;
	},

	set: function (name, value) {
		document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + "; path=/;";
	}
}

function refineBlockBinder(textLess, textMore) {
	$(".refine_button").click(function() {
		RefineSearchBlock.showHide($(this).attr("id"));
	});

	$(".block_values_button").click(function(){
		var butt = $(this);
		$(this).prev(".block_values").slideToggle("normal", function() {
			if ($(this).css("display") == "block") {
				butt.html(textLess);
			} else {
				butt.html(textMore);
			}
		});
	});
}
