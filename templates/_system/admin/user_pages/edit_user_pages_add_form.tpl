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
         		  "</td><td>= <input type='text' name='"+ Params[module_value,function_value][i][1] +"' id='"+ str_key +"' value='' class='text'> </td></tr>";   		   						           		  
     } //for     
     if (strHTML != '') {
     	strHTML = '<table class="fieldset" name="table_parameters">'+ strHTML + '</table>';
     	document.getElementById("table_params").innerHTML = strHTML;     
     }
  } //if  
  
}

function formTextOfParams() {
  str_param = '';
  i = 0;
  while (1==1) {  
    param = document.getElementById("value_param" + i);    
    if (param == null)
        break;
    key_param   = param.name.replace('params_', '');
    value_param = trim(param.value);
    if (value_param != '') {
	    if (str_param == '')
	      str_param = key_param + "=" + value_param;
	    else
	      str_param = str_param + "\r\n" + key_param + "=" + value_param;
	}
    i++;    
  }
  document.getElementById("parameters").value = str_param;
}	

// *************************************************************************

function check() {
  obj_params = document.getElementById("table_params");
  obj_params.innerHTML = "";
} 


//-->
</script>

{if $IS_NEW == 1}
    {breadcrumbs}<a href="{$GLOBALS.site_url}/user-pages/">{tr}Site Pages{/tr}</a> &#187; {tr}Add User Page{/tr}{/breadcrumbs}
    <h1><img src="{image}/icons/linedpaperplus32.png" border="0" alt="" class="titleicon"/>{tr}Add User Page{/tr}</h1>
{else}
    {breadcrumbs}<a href="{$GLOBALS.site_url}/user-pages/">{tr}Site Pages{/tr}</a> &#187; {tr}Edit User Page{/tr}{/breadcrumbs}
    <h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>{tr}Edit User Page{/tr}</h1>
{/if}

    {foreach from=$ERRORS key=ERROR item=ERROR_DATA}
    	{if $ERROR == 'URI_NOT_SPECIFIED'}<p class="error">{tr}The page URI is not specified{/tr}</p>{/if}
    	{if $ERROR == 'MODULE_NOT_SPECIFIED'}<p class="error">{tr}Module is not specified{/tr}</p>{/if}
    	{if $ERROR == 'FUNCTION_NOT_SPECIFIED'}<p class="error">{tr}Function is not specified{/tr}</p>{/if}
    	{if $ERROR == 'ADD_ERROR'}<p class="error">{tr}Cannot add new User Page. (must be not exist module and function){/tr}</p>{/if}
    	{if $ERROR == 'CHANGE_ERROR'}<p class="error">{tr}Cannot change data of User Page. (must be not exist module and function){/tr}</p>{/if}
    	{if $ERROR == 'PAGE_EXISTS'}<p class="error">{tr}Page with such URI is already exist{/tr}</p>{/if}
    	{if $ERROR == 'DELETE_PAGE'}<p class="error">{tr}Page URI is not defined{/tr}</p>{/if}
    	{if $ERROR == 'NON_EXISTENT_MODULE'}<p class="error">{tr}Module named "$ERROR_DATA" does not exist.{/tr}</p>{/if}
    	{if $ERROR == 'PAGE_ALREADY_EXISTS'}<p class="error">{tr}User page with such uri already exists{/tr}</p>{/if}
    {/foreach}

<form name="form1" method="post">
	<input type="hidden" name="action" value="{$action}" />
    <input type="hidden" id="submit" name="submit" value="save_page" />
	<input type="hidden" name="special_page" value="{$user_page.special_page}" />
	<fieldset>
		<legend>{if $IS_NEW == 1}{tr}Add a New User Page{/tr}{else}{tr}Edit User Page{/tr}{/if}</legend>
		<table>
			<tr><td colspan="2"><input type="hidden" name="ID" value="{$user_page.ID}" /></td></tr>
			<tr>
				<td>{tr}URI{/tr}</td>
				<td><input type="text" name="uri" value="{$user_page.uri}" /></td>
			</tr>
			<tr>
				<td>{tr}Pass parameters via URI{/tr}</td>
				<td><input type="checkbox" name="pass_parameters_via_uri" {if $user_page.pass_parameters_via_uri} checked="checked" {/if} /></td>
			</tr>
			<tr>
				<td>{tr}Title{/tr}</td>
				<td><input type="text" name="title" value="{$user_page.title}" /></td>
			</tr>
			<tr>
				<td>{tr}Template{/tr}</td>
				<td><input type="text" name="template" value="{$user_page.template}" /></td>
			</tr>
			{if empty($user_page.special_page)}
				<tr>
					<td>{tr}Module{/tr}</td>
					<td>
						<select size="1" name="module" id="modules" onchange="loadFunctionsForModule(this.form)">
							<option selected>{tr}Choose module{/tr}:</option>
							{foreach from=$LIST_MODULES key=KEY_MOD item=VALUE_MOD}
								{if $VALUE_MOD == $user_page.module}
									<option selected="selected">{$VALUE_MOD}</option>
								{else}
									<option>{$VALUE_MOD}</option>
								{/if}
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Function{/tr}</td>
					<td>
						<select size="1" name="function" id="functions" onchange="loadParamsForFunction()">
							<option selected>{tr}Choose function{/tr}:</option>
							{foreach from=$LIST_FUNCTIONS[$user_page.module] key=KEY_FUNC item=VALUE_FUNC}
								{if $VALUE_FUNC == $user_page.function}
									<option selected="selected">{$VALUE_FUNC}</option>
								{else}
									<option>{$VALUE_FUNC}</option>
								{/if}
							{/foreach}
						</select>
					</td>
				</tr>
			{/if}
			<tr>
				<td valign=top>{tr}Parameters{/tr}:</td>
				<td> 
					<div id="table_params">
						<table class="fieldset" name="table_parameters">
					    {foreach from=$LIST_PARAMS key=KEY_MOD item=VALUE_ARRAY_FUNC}  				    
					      {if ($KEY_MOD == $user_page.module)}
					    	{foreach from=$VALUE_ARRAY_FUNC key=KEY_FUNC item=VALUE_ARRAY_PARAM}  				    	    
					      	  {if ($KEY_FUNC == $user_page.function)}	    	
								{foreach from=$VALUE_ARRAY_PARAM key=KEY_PARAM item=VALUE_PARAM}			
					    		  <tr><td> {$VALUE_PARAM} </td>
					    		  {assign var="flag_param" value="0"}
								  {foreach from=$a_params key=key_a item=value_a}			
								    {if ($key_a == $VALUE_PARAM)}  
					    		   		<td>= <input type="text" name="params_{$VALUE_PARAM}" id="value_param{$KEY_PARAM}" value="{$value_a}" /></td>
					    		   		{assign var="flag_param" value="1"}
								    {/if}
								  {/foreach}
								  {if ($flag_param == 0)}
				    		   		<td>= <input type="text" name="params_{$VALUE_PARAM}" id="value_param{$KEY_PARAM}" value="" /></td>
								  {/if}	
								  </tr>	      	  
					    		{/foreach}	      	  
				    	      {/if}				
					  	    {/foreach}
						  {/if}				
					  	{/foreach}
					  	</table>
				  	</div>
				</td>
			</tr>
			<tr>
				<td coslpan="2"><input type="hidden" name="parameters" id="parameters" value="{$user_page.parameters}" /></td>
			</tr>
			<tr>
				<td valign="top">{tr}Keywords{/tr}</td>
				<td><textarea name="keywords" cols="55" rows="4">{$user_page.keywords}</textarea></td>
			</tr>
			<tr>
				<td valign=top>{tr}Description{/tr}</td>
				<td><textarea name="description" cols="55" rows="4">{$user_page.description}</textarea></td>
			</tr>
			<tr>
				<td colspan="2">
                    <div class="floatRight">
                        {if ! $IS_NEW}
                            <input type="submit" id="apply" value="{tr}Apply{/tr}" class="grayButton" onclick="formTextOfParams()" />
                        {/if}
                        <input type="submit" value="{if $IS_NEW == 1}{tr}Add{/tr}{else}{tr}Save{/tr}{/if}" class="grayButton" onclick="formTextOfParams()" />
                    </div>
                </td>
			</tr>
		</table>
	</fieldset>
</form>

<script>
	$('#apply').click(
		function(){
			$('#submit').attr('value', 'apply_page');
		}
	);
</script>