<span id="listPeriod" >
	<select class="searchList" name="{$id}" id="{$id}" onChange="changePeriodName(this.value)">
		<option value="">Select {tr}{$caption}{/tr|escape:'html'}</option>
		{foreach from=$list_values item=list_value}
			<option value='{$list_value.id}' {if $list_value.id == $value}selected="selected"{/if} >{tr}{$list_value.caption}{/tr|escape:'html'}</option>
		{/foreach}
	</select>
</span>

<script>
{literal}
	function changePeriod(paymentType)
	{
		if (paymentType == 1) {
			{/literal}
				var html = '<select class="searchList" name="{$id}" onChange="changePeriodName(this.value)"><option value="">Select {$caption|escape:"html"}</option>{foreach from=$list_values item=list_value}{if $list_value.id != "unlimited"}<option value="{$list_value.id}" {if $list_value.id == $value}selected="selected"{/if} >{$list_value.caption|escape:"html"}</option>{/if}{/foreach}</select>';
			{literal}
		}
		else {
			{/literal}
				var html = '<select class="searchList" name="{$id}" onChange="changePeriodName(this.value)"><option value="">Select {$caption|escape:"html"}</option>{foreach from=$list_values item=list_value}<option value="{$list_value.id}" {if $list_value.id == $value}selected="selected"{/if} >{$list_value.caption|escape:"html"}</option>{/foreach}</select>';
			{literal}
		}
		
		$('#listPeriod').html(html);
	}

	var periodValue = '';
	function changePeriodName(val)
	{
		if (val == 'unlimited') {
			periodValue = $('input[name=period]').val();
			$('input[name=period]').val('');
			$('input[name=period]').attr('disabled', 'disabled');
		} else {
			$('input[name=period]').attr('disabled', false);
			if ($('input[name=period]').val() == '') {
				$('input[name=period]').val(periodValue);
			}
		}
	}
{/literal}
{if $value}
	changePeriodName('{$value}');
{/if}
</script>