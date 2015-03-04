{assign var="complexField" value=$id scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
<div  class="complex">
	<table id="complexFieldsVolume">
		<tr>
			<td>[[Qty]]</td>
			<td>[[Price per unit]]</td>
			<td>[[Renewal Price]]</td>
		</tr>
    {foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
        {if $complexElementKey != 1}
            <tr id='complexFieldsAdd_{$complexField}_{$complexElementKey}' class="complex">
        {else}
        	<tr id='complexFields_{$complexField}'>
        {/if}
        
			{foreach from=$form_fields item=form_field}
				{if $form_field.id == 'listings_range_from'}
				<td>
					[[From]] {input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}&nbsp;
				{elseif $form_field.id == 'listings_range_to'}
					[[To]] {input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}
				</td>
				{else}
					<td>{input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}</td>
				{/if}
			{/foreach}
	  {if $complexElementKey == 1}
          </tr><tr id='complexFieldsAdd_{$complexField}'>
      {else}
          <td><a href='' class="remove" onclick='removeComplexField_{$complexField}({$complexElementKey}); return false;' >[[Remove]]</a></td></tr>
      {/if}
	{/foreach}
	</table>
</div>
<a href='#' class="add" onclick='addComplexField_{$complexField}(); return false;' >[[Add]]</a>

<script>

	var i_{$complexField} = {$complexElementKey} + 1;

	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");

	function addComplexField_{$complexField}() {ldelim}
		var id = "complexFieldsAdd_{$complexField}_" + i_{$complexField};
		var newField = $('#complexFields_{$complexField}').clone();
		$("<tr id='" + id + "'></tr>").appendTo("#complexFieldsVolume");
		$('#' + id).html(newField.html());
		$('#' + id).append('<td><a class="remove" href="" onclick="removeComplexField_{$complexField}(' + i_{$complexField} + '); return false;">[[Remove]]</a></td>');
		$('#'+ id +' input[type=text]').val('');
		$('#'+ id +' input[type=file]').val('');
		$('#'+ id +' select').val('');
		$('#'+ id +' textarea').val('');
		$('#'+ id +' .complexField').each(function() {ldelim}
				$(this).attr( 'name',  $(this).attr( 'name' ).replace('[1]', '['+i_{$complexField}+']'));
			{rdelim}
		);
		$('#'+ id +' .complex-view-file-caption').remove();

		var img = $('#'+ id +' input').next('.ui-datepicker-trigger');
		var el = img.prev('.input-date');
		el.removeAttr('id').removeClass('hasDatepicker').unbind();
		el.datepicker({literal}{
                dateFormat: dFormat,
                showOn: 'both',
                changeMonth: true,
                changeYear: true,
                minDate: new Date(1940, 1 - 1, 1),
                maxDate: '+10y',
                yearRange: '-99:+99',
				buttonImage: '{/literal}{image}icons/icon-calendar.png{literal}'
            });
            img.remove();
		if (typeof window.instructionFunc == 'function') {
			instructionFunc();
		}
        {/literal}
		i_{$complexField}++;

	{rdelim}

    function removeComplexField_{$complexField}(id) {ldelim}
        $('#complexFieldsAdd_{$complexField}_' + id).remove();
	{rdelim}

</script>

{assign var="complexField" value=false scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}