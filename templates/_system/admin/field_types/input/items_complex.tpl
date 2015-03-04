<script type="text/javascript">
function windowMessage(message){
	$("#messageBox").dialog( 'destroy' ).html(message);
	$("#messageBox").dialog({
		width: 370,
		height: 170,
		title: '[[Error]]',
		buttons: {
			OK: function(){
				$(this).dialog('close');
			}
		}

	}).dialog( 'open' );
	return false;
}
</script>
{assign var="complexField" value='items' scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
<div  class="complex">
	<table id="complexFieldsItems">
		<tr>
			<td>[[Item]]</td>
			<td>[[Qty]]</td>
			<td>[[Price]]</td>
			<td colspan="2">[[Amount]]</td>
		</tr>
	    {foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
	        {if $complexElementKey != 1}
	            <tr id='complexFieldsAdd_{$complexField}_{$complexElementKey}'>
            {else}
	            <tr id='complexFields_{$complexField}'>
	        {/if}
		    {foreach from=$form_fields item=form_field}
				{if $form_field.id == 'products'}
					<td>
						{input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey template="items_list.tpl"}
						<br /><br />
						{input property='custom_item' complexParent=$complexField complexStep=$complexElementKey}

					</td>
				{elseif $form_field.id != 'custom_item' && $form_field.id != 'custom_info'}
					<td>{input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}</td>
				{/if}
			{/foreach}

		  {if $complexElementKey == 1}
	          </tr><tr id='complexFieldsAdd_{$complexField}'>
	      {else}
		          <td><a href='' class="remove" onclick='removeComplexField_{$complexField}({$complexElementKey}); return false;' >[[Delete]]</a></td></tr>
		      {/if}
		{/foreach}
	</table>
</div>
<a href='#' class="add" onclick='addComplexField_{$complexField}(); return false;' >[[Add New Item]]</a>
<div id="errorMessage"></div>

<script type="text/javascript">
	var langSettings = {
		thousands_separator : '{$GLOBALS.current_language_data.thousands_separator}',
		decimal_separator : '{$GLOBALS.current_language_data.decimal_separator}',
		decimals : '{$GLOBALS.current_language_data.decimals}',
		currencySign: '{$GLOBALS.settings.transaction_currency}',
		showCurrencySign: 0,
		rightToLeft: {$GLOBALS.current_language_data.rightToLeft}
	};
	var i_{$complexField} = {$complexElementKey} + 1;

	$('input[name^="{$complexField}[qty]"]').each(function() {ldelim}
			$(this).data('old_value',$(this).val());
	{rdelim});

	$('input[name^="{$complexField}[price]"]').each(function() {ldelim}
		$(this).data('old_value',$(this).val());
	{rdelim});

	$('select[name^="{$complexField}[products]"]').each(function() {ldelim}
		var idx = getFieldsIndex($(this).attr('name'));
		var objQty = $('input[name="{$complexField}[qty]'+idx+'"]');
		var objPrice = $('input[name="{$complexField}[price]'+idx+'"]');
		var product_sid = $(this).val();
		if (product_sid > 0 ){ldelim}
			if ($("#price_type_"+product_sid).val() == 1) {ldelim}
				objQty.attr("readonly", "").css("background-color","#FFFFFF").css("color","#000000");
				objPrice.attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
			{rdelim} else {ldelim}
				objQty.attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
				objPrice.attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
			{rdelim}
		{rdelim} else if (product_sid == -1){ldelim}
			objQty.attr("readonly", "").css("background-color","#FFFFFF").css("color","#000000");
			objPrice.attr("readonly", "").css("background-color","#FFFFFF").css("color","#000000");
		{rdelim}else {ldelim}
			objQty.attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
			objPrice.attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
		{rdelim}
	{rdelim});

	function addComplexField_{$complexField}() {ldelim}
		var id = "complexFieldsAdd_{$complexField}_" + i_{$complexField};
		var newField = $('#complexFields_{$complexField}').clone();
		$("<tr id='" + id + "'></tr>").appendTo("#complexFieldsItems");
		$('#' + id).html(newField.html());
		$('#' + id).append('<td><a class="remove" href="" onclick="removeComplexField_{$complexField}(' + i_{$complexField} + '); return false;">[[Delete]]</a></td>');
		$('#'+ id +' .complexField').each(function() {ldelim}
				$(this).attr( 'name',  $(this).attr( 'name' ).replace('[1]', '['+i_{$complexField}+']'));
				$(this).attr( 'id',  $(this).attr( 'id' ).replace('[1]', '['+i_{$complexField}+']'));
			{rdelim}
		);
		$('#'+ id +' input[type=text]').val('');
		$('#'+ id +' select').val('');
		$('input[name="{$complexField}[qty]['+i_{$complexField}+']"]').attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
		$('input[name="{$complexField}[price]['+i_{$complexField}+']"]').val(0).data('old_value',0).attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
		$('input[name="{$complexField}[amount]['+i_{$complexField}+']"]').val(0);
		$('input[name="{$complexField}[custom_item]['+i_{$complexField}+']"]').hide();
		i_{$complexField}++;
	{rdelim}

    function removeComplexField_{$complexField}(id) {ldelim}
	    var idx = '[' + id + ']';
	    var objQty = $('input[name="{$complexField}[qty]'+idx+'"]');
	    var objPrice = $('input[name="{$complexField}[price]'+idx+'"]');
	    $('#complexFieldsAdd_{$complexField}_' + id).remove();
	    calcAmount(objPrice.val(),objQty.val(), idx);
	    calcTotal();
	{rdelim}

	function getFieldsIndex(name){ldelim}
		var re = /\d+/g;
		return '['+name.match(re)+']';
	{rdelim}

	function calcTotal(){ldelim}
		var sub_total = 0;
		var total = 0;
		var tax = 0;
		var rate = unformatNumber({$tax.tax_rate|default:0});
		var enable_taxes = $('input[name^="include_tax"]').is(":checked");
		var price_includes_tax = {$tax.price_includes_tax|default:0};
		$('input[name^="{$complexField}[amount]"]').each(function(){ldelim}
			sub_total = sub_total + unformatNumber($(this).val());
		{rdelim});
		sub_total = roundNumber(sub_total);
		if (enable_taxes) {ldelim}
			tax = calcTaxAmount(sub_total, rate, price_includes_tax);
			if (price_includes_tax){ldelim}
				total = sub_total;
			{rdelim} else {ldelim}
				total = parseFloat(sub_total) + parseFloat(tax);
			{rdelim}
			$('input[name="tax_info[tax_amount]"]').val(formatNumber(tax));
		{rdelim} else {ldelim}
			total = sub_total;
		{rdelim}
		$('input[name="sub_total"]').val(formatNumber(sub_total));
		$('input[name="total"]').val(formatNumber(roundNumber(total)));
	{rdelim}

	function calcAmount(price, qty, idx){ldelim}
		var amount = roundNumber(parseInt(qty)*unformatNumber(price));
		$('input[name="{$complexField}[amount]'+idx+'"]').val(formatNumber(amount));
	{rdelim}

	function chooseItem(product_sid, qty, idx){ldelim}
		var objAmount = $('input[name="{$complexField}[amount]'+idx+'"]');
		var objQty = $('input[name="{$complexField}[qty]'+idx+'"]');
		var objPrice = $('input[name="{$complexField}[price]'+idx+'"]');
		var objCustomItem = $('input[name="{$complexField}[custom_item]'+idx+'"]');
		var price = 0;
		if (product_sid > 0){ldelim}
			if ($("#price_type_"+product_sid).val() == 1) {ldelim}
				objQty.attr("readonly", "").css("background-color","#FFFFFF").css("color","#000000");
				objPrice.attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
				qty = 1;
			{rdelim}
			else {ldelim}
				objQty.attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
				objPrice.attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
				qty = $("#number_"+product_sid).val();
			{rdelim}
			objCustomItem.hide();
		{rdelim} else if (product_sid == -1){ldelim}
			objQty.attr("readonly", "").css("background-color","#FFFFFF").css("color","#000000");
			objPrice.attr("readonly", "").css("background-color","#FFFFFF").css("color","#000000");
			qty = 1;
			objPrice.val(formatNumber(roundNumber(0)));
			objPrice.data("old_value", objPrice.val());
			objCustomItem.show();
		{rdelim} else {ldelim}
			objPrice.val(formatNumber(roundNumber(0)));
			objPrice.data("old_value", objPrice.val());
			objCustomItem.hide();
		{rdelim}
		getPrice(qty,idx);
	{rdelim}

	$('.items_products').live('change',function(){ldelim}
		var idx = getFieldsIndex($(this).attr('name'));
		var objQty = $('input[name="{$complexField}[qty]'+idx+'"]');
		var objPrice = $('input[name="{$complexField}[price]'+idx+'"]');
		var qty = objQty.val();
		var product_sid = $(this).val();
		chooseItem(product_sid, qty, $(this).attr("id"));
	{rdelim}
	);

	$('input[name^="{$complexField}[price]"]').live('change', function(event){ldelim}
		var price = unformatNumber($(this).val());
		var idx = getFieldsIndex($(this).attr('name'));
		var objQty = $('input[name="{$complexField}[qty]'+idx+'"]');
		if (isNaN(price)) {ldelim}
			validateField("[['Price' is not a valid float value]]");
			$(this).val($(this).data("old_value"));
		{rdelim} else {ldelim}
			$(this).data("old_value", $(this).val());
			var qty = objQty.val();
			calcAmount($(this).val(), qty, idx);
			calcTotal();
		{rdelim}
	{rdelim});


	$('input[name^="{$complexField}[qty]"]').live('change', function(){ldelim}
		var qty = parseInt($(this).val());
		if (isNaN(qty)) {ldelim}
			validateField("[['Qty' is not a valid integer value]]");
			$(this).val($(this).data("old_value"));
		{rdelim} else {ldelim}
			var idx = getFieldsIndex($(this).attr('name'));
			getPrice(qty,idx);
		{rdelim}
	{rdelim});

	$('input[name^="{$complexField}[custom_item]"]').each(function(){ldelim}
		var idx = getFieldsIndex($(this).attr('name'));
		var product_sid = $('select[name="{$complexField}[products]'+idx+'"]').val();
		if (product_sid != -1)
			$(this).hide();
		else
			$(this).show();
	{rdelim});

	$('input[name^="include_tax"]').click(function (){ldelim}
		if ({$tax.sid|default:0}) {ldelim}
			if ($(this).is(':checked')) {ldelim}
				$('#tax_info').show();
				$(this).attr("checked", "checked");
			{rdelim} else {ldelim}
				$('#tax_info').hide();
				$(this).attr("checked", "");
			{rdelim}
			calcTotal();
		{rdelim}
	{rdelim});

	function getPrice(qty,idx)
	{ldelim}
		var product_sid = $('select[name="{$complexField}[products]'+idx+'"]').val();
		var objPrice = $('input[name="{$complexField}[price]'+idx+'"]');
		var objQty = $('input[name="{$complexField}[qty]'+idx+'"]');
		var objAmount = $('input[name="{$complexField}[amount]'+idx+'"]');
		var price = 0;
		if (product_sid > 0){ldelim}
			price = roundNumber($("#price_per_unit_"+product_sid+"_"+qty).val());
			if ($("#price_type_"+product_sid).val() == 1) {ldelim}
				if (isNaN(price)) {ldelim}
					validateField("[['Price' for this qty can not be calculated]]");
					objQty.val(objQty.data("old_value"));
					price = $("#price_per_unit_"+product_sid+"_"+objQty.val()).val();
					objPrice.val(formatNumber(price));
					calcAmount(objPrice.val(), objQty.val(), idx);
					objPrice.data("old_value", objPrice.val());
				{rdelim} else {ldelim}
					objQty.val(qty);
					objQty.data("old_value", objQty.val());
					objPrice.val(formatNumber(price));
					objPrice.data("old_value", objPrice.val());
					calcAmount(objPrice.val(), qty, idx);
				{rdelim}
			{rdelim} else {ldelim}
				objQty.val(qty);
				objQty.data("old_value", objQty.val());
				objPrice.val(formatNumber(price));
				objPrice.data("old_value", objPrice.val());
				objAmount.val(formatNumber(price));
			{rdelim}
		{rdelim} else if (product_sid == -1){ldelim}
			price = objPrice.val();
			objQty.val(qty);
			objQty.data("old_value", objQty.val());
			objPrice.val(formatNumber(price));
			objPrice.data("old_value", objPrice.val());
			calcAmount(price, qty, idx);
		{rdelim} else {ldelim}
			objPrice.val(formatNumber(price));
			objPrice.data("old_value", objPrice.val());
			calcAmount(price, qty, idx);
		{rdelim}
		calcTotal();
	{rdelim}

	function validateField(st)
	{ldelim}
		message = $("#errorMessage").html(st);
		windowMessage(message);
		return false;
	{rdelim}
</script>

{assign var="complexField" value=false scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
