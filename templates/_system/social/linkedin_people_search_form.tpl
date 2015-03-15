<div class="form-group has-feedback">
	<label class="inputName control-label col-sm-3">[[Search LinkedIn]]:</label>
	<div class="inputField col-sm-8">
		<input type="checkbox" name="li_search" value="1" {if $linkedinSearch != 'notChecked'}checked="checked"{/if} style="float:left;"/>
		<div class="instruction">
			<div onmouseout="javascript:$(this).next('.instr_block').hide();" onmouseover="javascript:$(this).next('.instr_block').show();" class="instr_icon"></div>
			<div id="instruction_FirstName" class="instr_block" style="display: none;">
				<div class="instr_arrow"></div>
				<div class="instr_cont">
					<p>[[The system uses the following search criteria when requesting LI API]]:</p>
					<ul>
						<li>[[Keywords]],</li>
						<li>[[Industry]],</li>
						<li>[[Zip Code]]</li>
					</ul>
					<p>[[Keywords is a mandatory field for receiving people search results from LI. So the system will give a message if keywords are empty.]]</p>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>