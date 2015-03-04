{breadcrumbs}[[HTML filters]]{/breadcrumbs}
<h1><img src="{image}/icons/exchange32.png" border="0" alt="" class="titleicon"/>[[HTML filters]]</h1>
<div style="font-size:11px; padding-bottom:20px;">
	[[On this page you can select which HTML tags will be allowed when entering or copying text to WYSIWYG editor.<br/> If you don't select any tag, all text will be saved as a plain text without any formatting.]]
</div>

<script>
	function checkAll(val) {
		var form = document.getElementById('htmlElem');
		var htmltags = form.getElementsByTagName('input');
		for(var i=0; i<htmltags.length; i++) {
			if(htmltags[i].type == 'checkbox'){
				switch (val) {
					case 'check':
						htmltags[i].checked = true;
						break;
					case 'uncheck':
						htmltags[i].checked = false;
						break;
				}
			}
		}
	}

	function checkTag(tagName, check) {
		$("input[name*='tags[" + tagName +  "]']").each(
				function () {
					this.checked = check;
				}
			);
	}
</script>

<form method="post" id="htmlElem">
	<div><a href="#" onclick='checkAll("check")'>[[Check all]]</a> / <a href="#" onclick='checkAll("uncheck")'>[[Uncheck all]]</a></div>
	<br />
	<table id="clear">
		<tr>
			<td> 
				{assign var="className" value="evenrow"}
				{foreach from=$htmltags item=htmltag name=htmltagsloop}
					{if $smarty.foreach.htmltagsloop.iteration == $rowsInColumn}
						</td><td valign="top">
					{/if}
					<div><div class="setting_ico">&nbsp;</div><b><input type='checkbox' name='tags[{$htmltag}]' id='tags[{$htmltag}]' {if $savedFilters.$htmltag}checked{/if} onClick='javascript: test="tags[{$htmltag}]";'/>{$htmltag}</b></div>
				{/foreach}
			</td>
		</tr>
		<tr>
			<td colspan="2"><div class="floatRight"><input type="submit" name="action" value="[[Save]]" class="greenButton" /></div></td>
		</tr>
	</table>
</form>

<script>
var test = false;
$(".setting_butt").click(function(){
	var butt = $(this);
	var check = false;

	if(test) {
		check = document.getElementById(test);
		if(check) check = document.getElementById(test).checked;
	}

	if((check != true && test == false) || (check == true && $(this).next(".setting_block").css("display") == "none") || (check == false && test != false && $(this).next(".setting_block").css("display") == "block")) {
	$(this).next(".setting_block").slideToggle("normal", function(){
			if ($(this).css("display") == "block") {
				butt.children(".setting_ico").html("[-]");
			} else {
				butt.children(".setting_ico").html("[+]");
			}
		});
	}
	test = false;
});
</script>