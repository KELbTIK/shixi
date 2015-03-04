{assign var="complexField" value=$form_field.id scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
<div id='complexFields_{$complexField}' class="complex">
    {foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
            {if $complexElementKey != 1}
            <div id='complexFieldsAdd_{$complexField}_{$complexElementKey}' class="complex">
        {/if}
        {foreach from=$form_fields item=form_field}
            {if $form_field.id == 'youtube'}
                {if $contract.video}
                    <fieldset>
                        <div class="inputName">{tr}{$form_field.caption}{/tr|escape:'html'}</div>
                        <div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
                        <div class="inputField">{input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}</div>
						{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
                    </fieldset>
                {/if}
            {elseif $listingTypeID == "Job" && $form_field.id == "anonymous"}
                {* this empty place of 'anonymous' checkbox in 'Job' listing *}
            {elseif $form_field.id == "access_type"}
                {if $listingTypeID != "Job" && $listing.type.id != "Job"}{* *}
                    <fieldset>
                        <div class="inputName">{tr}{$form_field.caption}{/tr|escape:'html'}</div>
                        <div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
                        <div class="inputField">{input property=$form_field.id template='resume_access.tpl' complexParent=$complexField complexStep=$complexElementKey}</div>
						{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
                    </fieldset>
                {/if}
            {elseif ($listingTypeID == "Job" || $listing.type.id == "Job") && $form_field.id =='ApplicationSettings'}
                <fieldset>
                    <div class="inputName">{tr}{$form_field.caption}{/tr|escape:'html'}</div>
                    <div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
                    <div class="inputField">{input property=$form_field.id template='applicationSettings.tpl' complexParent=$complexField complexStep=$complexElementKey}</div>
					{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
                </fieldset>
            {else}
                <fieldset>
                    <div class="inputName">{tr}{$form_field.caption}{/tr|escape:'html'}</div>
                    <div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
                    <div class="inputField">{input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}</div>
					{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
                </fieldset>
            {/if}
        {/foreach}
        {if $complexElementKey == 1}
            </div><div id='complexFieldsAdd_{$complexField}'>
        {else}
            <a href="#" class="remove" onclick='removeComplexField_{$complexField}({$complexElementKey}); return false;' >[[Remove]]</a></div>
        {/if}
    {/foreach}
</div>
<a href='#' class="add" onclick='addComplexField_{$complexField}(); return false;' >[[Add]]</a>

<script type="text/javascript">

	var i_{$complexField} = {$complexElementKey} + 1;

	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");

	function addComplexField_{$complexField}() {ldelim}
		var id = "complexFieldsAdd_{$complexField}_" + i_{$complexField};
        var oldField = $('#complexFields_{$complexField}');
        var conf;
        var data = [];
        var oldTextAreas = oldField.find('.inputField textarea');
        oldTextAreas.each(function(){ldelim}
            var name = $(this).attr('name');
            var editor = CKEDITOR.instances[name];
            if (editor) {ldelim}
                conf = editor.config;
                data[name] =  editor.getData();
                editor.destroy(true);
                $(this).next().remove();
            {rdelim}
        {rdelim});

		var oldMultiLists = oldField.find("select[multiple]").multiselect("destroy");

        var newField = oldField.clone();

        oldTextAreas.each(function(){ldelim}
            var name = $(this).attr('name');
            CKEDITOR.replace(name, conf);
            var editor = CKEDITOR.instances[name];
            editor.setData(data[name]);
        {rdelim});

        $("<div id='" + id + "' />").appendTo("#complexFieldsAdd_{$complexField}");
       	newField.append('<a class="remove" href="#" onclick="removeComplexField_{$complexField}(' + i_{$complexField} + '); return false;">[[Remove]]<\/a><br/>');

        var str = newField.html();
        re = /\[1]/g;
        var newStr = str.replace(re, '['+i_{$complexField}+']');
        newField.html(newStr);
        newField.appendTo('#' + id);
        newField.find('.inputField textarea').each(function(){ldelim}
            $(this).val('');
            CKEDITOR.replace($(this).attr('name'), conf);
        {rdelim});
        newField.find('input[type="file"]').each(function(){ldelim}
            if ($(this).attr('field_id')){ldelim}
                $(this).attr( 'field_id',  $(this).attr( 'field_id' ).replace(':1', ':'+i_{$complexField}));
                $(this).val('');
                var fileFieldContent 	= $(this).parents('div[id^="file_field_content_"]');
                if (fileFieldContent) {ldelim}
                    fileFieldContent.children('p[class="error"]').remove();
                    fileFieldContent.children('div[id^="file_"]').empty();
                    fileFieldContent.children('input[id^="input_file_"]').show();
                {rdelim}
            {rdelim}
        {rdelim});

		oldMultiLists.each(function() {
			var oldMultiListName = $(this).attr("name");
			var newMultiListName = oldMultiListName.replace('[1]', '['+i_{$complexField}+']');
			var multiListId = oldMultiListName.replace('{$complexField}[', '').replace('][1][]', '');
			var multiListCaption = $(this).parent().parent().find("div.inputName").html();
			var options = {
				selectedList: 3,
				selectedText: "# {tr}selected{/tr|escape:'html'}",
				noneSelectedText: "{tr}Click to select{/tr|escape:'html'}",
				checkAllText: "{tr}Select all{/tr|escape:'html'}",
				uncheckAllText: "{tr}Deselect all{/tr|escape:'html'}",
				header: true,
				height: 'auto',
				minWidth: 313
			};
			newField.find("select[name='" + newMultiListName + "']").val('').getCustomMultiList(options, multiListId, null);
			oldField.find("select[name='" + oldMultiListName + "']").getCustomMultiList(options, multiListId, null);
		});

    	$('#'+ id +' input[type=text]').val('');
		$('#'+ id +' select').val('');

		var img = $('#'+ id +' input').next('.ui-datepicker-trigger');
		var el = img.prev('.input-date');
		el.removeAttr('id').removeClass('hasDatepicker').unbind();
		el.datepicker({literal}{
                dateFormat: dFormat,
                showOn: 'button',
                changeMonth: true,
                changeYear: true,
                minDate: new Date(1940, 1 - 1, 1),
                maxDate: '+10y',
                yearRange: '-99:+99',
                buttonImage: '{/literal}{$GLOBALS.user_site_url}/system/ext/jquery/calendar.gif{literal}',
                buttonImageOnly: true
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