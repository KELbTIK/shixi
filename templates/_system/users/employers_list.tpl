<form name="employers_selected_list" id="employers_selected_list" action="">

<table cellspacing="0" cellpadding="3" width="550" border=0>
	<tr>
		<td width="265">[[Employers List]]</td>
		<td width="30px">&nbsp;</td>
		<td width="265">[[Selected Employers]]</td>
	</tr>
	<tr>
		<td><input type="text" id="find_name" name="find_name" value=""><input type="button" id="find_button" name="find_button" value="[[Search]]"></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td align="center">
		<select id="employers_for_select" name="employers_for_select" size=10 multiple style="width: 250px;">
			{foreach from=$employers item=emp}
				<option value="{$emp.sid}">{$emp.name}</option>
			{/foreach}
		</select>
		</td>

		<td>
			<input type="button" id="move_to_selected" value=" > ">
			<input type="button" id="remove_from_selected" value=" < ">
		</td>
		
		<td align="center">
		<select id="employers_selected" name="employers_selected" size=10 multiple style="width: 250px;">
		</select>
		</td>
	</tr>
	<tr>
		<td colspan="3"><span class="small">[[* Use CTRL key to select two or more employers]]</span></td>
	</tr>
	<tr>
		<td colspan="3"><input type="button" class="button" id="set_employers_list" value="OK"></td>
	</tr>
</table>

</form>

{literal}
<script type="text/javascript">
$.ui.dialog.prototype.options.bgiframe = true;

$(function(){
	$("#set_employers_list").click(function(){
		var hidden = '';
		$('#employers_selected option').each(function(){
            hidden += "<input type='hidden' name='list_emp_ids[]' value='"+$(this).val()+"'>";
        });
		$("#employers_selected_readonly").html( $("#employers_selected").html() );
		$("#hidden_selected_ids").html('');
		$("#hidden_selected_ids").html(hidden);
		$("#employers_list").dialog('destroy');
	});

	$("#move_to_selected").click(function(){
		$('#employers_for_select option:selected').each(function(){
            $('#employers_selected').append("<option value='" + $(this).val() + "'>"+ $(this).text() +"</option>");
            $(this).remove();
        });
	});

	$("#remove_from_selected").click(function(){
		$('#employers_selected option:selected').each(function(){
			$('#employers_for_select').append("<option value='" + $(this).val() + "'>"+ $(this).text() +"</option>");
            $(this).remove();
		});
	});

{/literal}
	employers_all = new Array();
	{foreach from=$employers item=employer}
	employers_all[{$employer.sid}] = '{$employer.name|escape}';
	{/foreach}
{literal}

	// отключаем попытки отправить форму. Перехватывать события будем сами
	$("#employers_selected_list").submit(function(){
		return false;
	});

	// кнопка "Поиск"
	$("#find_button").click(function(){
		var searchText = $("#find_name").val();
		searchEmp(searchText);
		return true;
	});

	// отлавливаем нажатие ENTER в поле поиска
	$("#find_name").keyup(function(event){
		if (event.keyCode == 13) {
			var searchText = $("#find_name").val();
			searchEmp(searchText);
			return true;
		}
		return false;
	});


	// функция поиска имени работодателя в списке
	function searchEmp(find_name) {
		search_result = new Array();
		var inner_html = '';
		
		for(keyProp in employers_all) {
			empLower = employers_all[keyProp].toLowerCase();
			find_name = find_name.toLowerCase();
			if ( empLower.indexOf(find_name) >= 0 ) {
				search_result.push( employers_all[keyProp] );
				inner_html = inner_html + "<option value='" + keyProp + "'>" + employers_all[keyProp] + "</option>\n";
			}
		}
		$("#employers_for_select").html(inner_html);
		cloneEmpRemove();
	}

});
</script>
{/literal}