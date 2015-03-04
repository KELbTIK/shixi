<script language="JavaScript" type="text/javascript">
	function trim(str)
	{
		while (str.substring(0, 1) == " ")
			str = str.substring(1, str.length);
		while (str.substring(str.length, 1) == " ")
			str = str.substr(0, str.length - 1);
		return str;
	}

	function get_cookie(rname)
	{
		var tmp = "" + document.cookie;
		var result = "";
		while (tmp.length) {
			splitter = tmp.indexOf(";");
			if (splitter < 0)
				splitter = tmp.length + 1;
			subject = tmp.substring(0, splitter);
			if (decodeURIComponent(trim(subject.substring(0, subject.indexOf('=')))) == rname)
				result = subject.substring(subject.indexOf('=') + 1, subject.length);
			tmp = tmp.substring(splitter + 1, tmp.length);
		}
		return result;
	}

	function set_cookie(name, value)
	{
		document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + "; path=/;";
	}

	function Show(cur_id)
	{
		set_cookie(cur_id, 'v');
		document.getElementById('v' + cur_id).style.display = 'block';
			document.getElementById('s' + cur_id).innerHTML = "<img src='{image}menu_opened.png' style='margin-top:13px;' border='0' alt=''/>&nbsp;";
		document.getElementById('ImgId' + cur_id).className = "leftMenuOpen";
	}

	function highlight_menu_title(title_id)
	{
		document.getElementById(title_id).style.color = '#fffff';
	}

	function Hide(cur_id)
	{
		set_cookie(cur_id, 'h');
		document.getElementById('v' + cur_id).style.display = 'none';
			document.getElementById('s' + cur_id).innerHTML = "<img src='{image}menu_closed.png' style='margin-top:13px' border='0' alt=''/>&nbsp;";
		document.getElementById('ImgId' + cur_id).className = "leftMenu";
	}

	function ShowHide(cur_id, obj)
	{
		if (document.getElementById('v' + cur_id).style.display != 'none') {
			Hide(cur_id);
			$(obj).removeClass('leftMenuOpen');
			$(obj).addClass('leftMenu');
		} else {
			Show(cur_id);
			$(obj).removeClass('leftMenu');
			$(obj).addClass('leftMenuOpen');
		}
	}

	function Restore(cur_id, hide_def)
	{
		if (get_cookie(cur_id) == 'h')
			Hide(cur_id);
		else if (get_cookie(cur_id) == 'v')
			Show(cur_id);
		else {
			if (hide_def)
				Hide(cur_id);
			else
				Show(cur_id);
		}
	}
</script>

{foreach from=$left_admin_menu key="section" item="section_items" name='menu_block'}
	<div onclick="ShowHide('{$section_items.id}', this)" id="ImgId{$section_items.id}" class="leftMenu">
		<span id="st{$section_items.id}" class="menuName"><span class="borders">[[{$section}]]</span></span>
		<span id="s{$section_items.id}" class="menuArrow"></span>
	</div>

	<div id="v{$section_items.id}">
		<div class="menuItems">
			{foreach from=$section_items item="item"}
				{if is_array($item)}
					<div class="{if $item.active}lmsih{else}lmsi{/if}"><a href="{$item.reference}">[[{$item.title}]]</a></div>
				{/if}
			{/foreach}
		</div>
	</div>
	{if $smarty.foreach.menu_block.iteration != $smarty.foreach.menu_block.total}{/if}
	<script type="text/javascript">Restore('{$section_items.id}',true); </script>
	{if $section_items.active}<script type="text/javascript">Show('{$section_items.id}')</script>{/if}
{/foreach}
