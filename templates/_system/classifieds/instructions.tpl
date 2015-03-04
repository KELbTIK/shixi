<div class="instruction">
	<div class="instr_icon" onmouseover="javascript:$(this).next('.instr_block').show();" onmouseout="javascript:$(this).next('.instr_block').hide();"></div>
    <div class="instr_block" id="instruction_{$form_field.id}">
		<div class="instr_arrow"></div>
		<div class="instr_cont">
			[[{$form_field.instructions}]]
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
	</div>
    <div class="clr"></div>
</div>
