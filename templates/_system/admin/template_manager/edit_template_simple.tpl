<script language="JavaScript" type="text/javascript">
<!--//
 
var ModulesFunctions = new Array();
{foreach from=$LIST_FUNCTIONS key=KEY_MOD item=VALUE_ARRAY_FUNC}  				    	
	ModulesFunctions["{$KEY_MOD}"]=[[-1,'Choose function:']{foreach from=$VALUE_ARRAY_FUNC key=KEY_FUNC item=VALUE_FUNC},[{$KEY_FUNC},'{$VALUE_FUNC}']{/foreach}];
{/foreach}

var Params = new Array();
{foreach from=$LIST_PARAMS key=KEY_MOD item=VALUE_ARRAY_FUNC}
  {foreach from=$VALUE_ARRAY_FUNC key=KEY_FUNC item=VALUE_ARRAY_PARAM}
     Params["{$KEY_MOD}", "{$KEY_FUNC}"]=[[-1,'reserved']{foreach from=$VALUE_ARRAY_PARAM key=KEY_PARAM item=VALUE_PARAM},[{$KEY_PARAM},'{$VALUE_PARAM}']{/foreach}];
  {/foreach}   
{/foreach}

{literal}

function loadFunctionsForModule(form) {	
    document.getElementById("table_params").innerHTML = '';	
    
	module_value = form.modules.options[form.modules.selectedIndex].text;
	form.functions.options.length=0;
	for (var i = 0; i < ModulesFunctions[module_value].length; i++) {
		newOpt = document.createElement("option");
		newOpt.text = ModulesFunctions[module_value][i][1];
		form.functions.options.add(newOpt);
	}	
}

function loadParamsForFunction() {
  document.getElementById("table_params").innerHTML = '';

  cbModules=document.getElementById("modules");
  cbFunctions=document.getElementById("functions");	  
  module_value = cbModules[cbModules.selectedIndex].text;
  function_value = cbFunctions[cbFunctions.selectedIndex].text;  


  if ( (module_value != "Choose module:") && (function_value != "Choose function:") ) {
     strHTML = '';     
     for (var i = 1; i < Params[module_value,function_value].length; i++) {
        str_key = 'value_param' + (i-1);
        
        strHTML = strHTML + "<tr><td>"+ Params[module_value,function_value][i][1] +
         		  "</td><td>= <input type='text' name='"+ Params[module_value,function_value][i][1] +"' id='"+ str_key +"' value='' class='text' size=15> </td></tr>";   		   						           		  
     } //for
     if (strHTML != '') {
     	strHTML = '<table class="fieldset" name="table_parameters">'+ strHTML + '</table>';
     	document.getElementById("table_params").innerHTML = strHTML;     
     }     
  } //if
}

function insertStr() {

   tArea = document.getElementById("template_content");
   tArea.focus();  

   cbModules=document.getElementById("modules");
   cbFunctions=document.getElementById("functions");	  
 
   module_value = cbModules[cbModules.selectedIndex].text;
   function_value = cbFunctions[cbFunctions.selectedIndex].text;  

   if ( (module_value != "Choose module:") && (function_value != "Choose function:") ) 
   {
     str_param = '';
     i = 0;
     while (1==1) {  
        param = document.getElementById("value_param" + i);    
	    if (param == null) break; 
	    key_param   = param.name;      
	    value_param = trim(param.value);
	    if (value_param != '') {
		    if (str_param == '')
		      str_param = key_param +"=\""+ value_param +"\"";
		    else
		      str_param = str_param +" "+ key_param +"=\""+ value_param +"\"";    
		}
//alert(str_param);
	    i++;    
	 }
	 if (str_param != '') str_param = " "+str_param; 
	 
	 str_ins = "{module name=\""+ module_value +"\" function=\"" + function_value +"\""+ str_param +"}"; 
     
	 if (document.selection) // IE
	 {   
		var s = document.selection.createRange(); 
		s.text = str_ins;
		s.select(); 	   
	 }
	 else 
	 {
		if (typeof(tArea.selectionStart) != "undefined")   // Mozilla
			cursor = tArea.selectionStart;	
		else											   // other browser
			cursor = tArea.length;	
			
		str = tArea.value;
		strBeg = str.substr(0, cursor);
		strEnd = str.substr(cursor, (str.length - cursor) );
				
		scrTop = tArea.scrollTop;	
		tArea.value = strBeg + str_ins + strEnd;
		tArea.scrollTop = scrTop;				
			
	 }   
   }
}

function saveTemplateSubmit() {
	var options = {
			target: "#messageBox",
			url:  $("#form1").attr("action"),
			success: function() {
					return false;
				}
			}; 
	$("#form1").ajaxSubmit(options);

	return false;
}

{/literal}
//-->
</script>
{if $ERROR}
	{if $ERROR eq "MODULE_DOES_NOT_EXIST"}
		There is no such module.
	{elseif $ERROR eq "TEMPLATE_DOES_NOT_EXIST"}
		There is no such template.
	{elseif $ERROR eq "NOT_ALLOWED_IN_DEMO"}
		Template is not editable in demo.
	{elseif $ERROR eq "CANNOT_FETCH_TEMPLATE"}
		Cannot fetch template "{$template_name}"
	{elseif $ERROR eq "TEMPLATE_IS_NOT_WRITEABLE"}
		Template is not writeable.
	{else}
		<p class="error">{$ERROR}</p>
	{/if}
{else}

	<form class="edit-template-simple" action="{$GLOBALS.site_url}/edit-templates/" method="POST" id="form1" onsubmit="return saveTemplateSubmit();">
	
		<table width="20%">
			<tr>
				<td valign="top">
					<table class="fieldset">
						<tr>
							<td class="td-name">Module</td>
							<td>
								<select size="1" name="module" id="modules" onchange="loadFunctionsForModule(this.form)" language="Javascript">
									<option selected>Choose module:</option>
									{foreach from=$LIST_MODULES key=KEY_MOD item=VALUE_MOD}
										<option> {$VALUE_MOD} </option>
									{/foreach}
								</select>
							</td>
							<td>&nbsp;&nbsp;</td>
							<td class="td-name">Function</td>
							<td>
								<select size="1" name="function" id="functions" onchange="loadParamsForFunction()" language="Javascript">
									<option selected>Choose function:</option>
									{foreach from=$LIST_FUNCTIONS[$user_page.module] key=KEY_FUNC item=VALUE_FUNC}
										<option> {$VALUE_FUNC} </option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr>
							<td valign="top" colspan="2" class="td-name">Parameters:</td>
							<td colspan="3"></td>
						</tr>
					</table>
					<a class="strong" href="javascript:void(0)" onclick="insertStr()">Insert</a>
					<table class="fieldset"><tr><td>
						<div id="table_params">
						</div>
					</td></tr></table>
				</td>
			</tr>
		</table>

		<table width="100%">
			<tr>
				<td>
					<table width="100%">
						<tr><td> <textarea id="template_content" name="template_content" style="width:100%;height:300px">{$template_content|escape}</textarea></td></tr>
						<tr><td align="right"><span class="greenButtonEnd"><input type="submit" value="Save" class="greenButton" /></span></td></tr>
					</table>
				</td>
			</tr>
		</table>

		<input type="hidden" name="template_name" value="{$template_name}">
		<input type="hidden" name="module_name" value="{$module_name}">
		<input type="hidden" name="action" value="save_template">
		<input type="hidden" name="simple_view" value="1">
	</form>
{/if}
