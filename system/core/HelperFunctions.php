<?php

class SJB_HelperFunctions
{
	/**
	 * unquoting data if 'magic_quotes_gpc' is turn on
	 * Function unquotes data, if 'magic_quotes_gpc' is turn on
	 * @param array $arr array of data to unquote
	 */
	public static function unquote(&$arr)
	{
		if (!@ini_get('magic_quotes_gpc'))
			return;
		foreach ($arr as $index => $value) {
			if (is_array ($arr[$index]))
				SJB_HelperFunctions::unquote($arr[$index]);
			else
				$arr[$index] = stripslashes($arr[$index]);
		}
	}
	
	public static function hideStructureText($structure_name, &$output)
	{
	    $structure_text_entry_pos_array = array();
	    $current_pos = 0;
	
	    while ($structure_text_pos = strpos($output, $structure_name, $current_pos)) {
	        $structure_text_entry_pos_array[] = $structure_text_pos;
	        $current_pos = $structure_text_pos + strlen($structure_name);
	    }
	
	    $structure_text_entry_pos_array = array_reverse($structure_text_entry_pos_array);
	
	    foreach ($structure_text_entry_pos_array as $structure_text_pos) {
	        $structure_text_begin_pos = strpos($output, '(', $structure_text_pos);
	        $pos = $structure_text_begin_pos + 1;
	        $begin_bracket_number = 1;
	        $end_bracket_number   = 0;
	
	        while ($begin_bracket_number != $end_bracket_number) {
	            $begin_bracket_pos = strpos($output, '(', $pos);
	            $end_bracket_pos = strpos($output, ')', $pos);
	
	            if ($begin_bracket_pos < $end_bracket_pos) {
	            	$pos = $begin_bracket_pos + 1;
	            	$begin_bracket_number++;
	            }
	            else {
	            	$pos = $end_bracket_pos + 1;
	            	$end_bracket_number++;
	            }
	        }
	
	        $structure_text_end_pos = $pos;
	        $output = substr_replace($output, '(...)', $structure_text_begin_pos, $structure_text_end_pos-$structure_text_begin_pos);  
		}
	}
	
	public static function d()
	{
		$args = func_get_args();
		$die = (end($args) === 1) && array_pop($args);
	
		echo '<pre>';
		foreach($args as $v) {
			$output = print_r($v, true);
	        SJB_HelperFunctions::hideStructureText('TemplateProcessor',$output);
			echo $output . "\n";
		}
		echo '</pre>';
	
		if ($die)
			die();
	}
	
	public static function dd()
	{
		$args = func_get_args();
		$die = (end($args) === 1) && array_pop($args);
	
		echo '<pre>';
		foreach($args as $v) {
			self::do_dump($v);
			echo "\n";
		}
		echo '</pre>';
	
		if ($die)
			die();
	}

	/*
	 * Function:         do_dump
	 * Description: Better GI than print_r or var_dump
	 */
	function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL)
	{
		$do_dump_indent = '<span style="color:#eeeeee;">|</span> &nbsp;&nbsp; ';
		$reference = $reference . $var_name;
		$keyvar = 'the_do_dump_recursion_protection_scheme';
		$keyname = 'referenced_object_name';

		if (is_array($var) && isset($var[$keyvar])) {
			$real_var = &$var[$keyvar];
			$real_name = &$var[$keyname];
			$type = ucfirst(gettype($real_var));
			echo "{$indent}{$var_name} <span style=\"color:#a2a2a2\">{$type}</span> = <span style=\"color:#e87800;\">&amp;{$real_name}</span><br />";
		}
		else {
			$var = array($keyvar => $var, $keyname => $reference);
			$avar = &$var[$keyvar];

			$type = ucfirst(gettype($avar));
			$type_color = '';
			switch ($type) {
				case 'String':
					$type_color = '<span style="color:green">';
					break;
				case 'Integer':
					$type_color = '<span style="color:red">';
					break;
				case 'Double':
					$type_color = '<span style="color:#0099c5">';
					$type = "Float";
					break;
				case 'Boolean':
					$type_color = '<span style="color:#92008d">';
					break;
				case 'NULL':
					$type_color = '<span style="color:black">';
					break;
			}

			if (is_array($avar)) {
				$count = count($avar);
				echo $indent . ($var_name ? "{$var_name} => " : '') . "<span style=\"color:#a2a2a2\">{$type} ({$count})</span><br />{$indent}(<br />";
				$keys = array_keys($avar);
				foreach ($keys as $name) {
					$value = &$avar[$name];
					self::do_dump($value, "['{$name}']", $indent . $do_dump_indent, $reference);
				}
				echo "{$indent})<br />";
			}
			elseif (is_object($avar)) {
				echo "{$indent}{$var_name} <span style=\"color:#a2a2a2\">{$type}</span><br />{$indent}(<br />";
				foreach ($avar as $name => $value)
					self::do_dump($value, $name, $indent . $do_dump_indent, $reference);
				echo "{$indent})<br />";
			}
			elseif (is_int($avar)) echo "{$indent}{$var_name} = <span style=\"color:#a2a2a2\">{$type}(" . strlen($avar) . ")</span> {$type_color}{$avar}</span><br />";
			elseif (is_string($avar)) echo "{$indent}{$var_name} = <span style=\"color:#a2a2a2\">{$type}(" . strlen($avar) . ")</span> {$type_color}\"{$avar}\"</span><br />";
			elseif (is_float($avar)) echo "{$indent}{$var_name} = <span style=\"color:#a2a2a2\">{$type}(" . strlen($avar) . ")</span> {$type_color}{$avar}</span><br />";
			elseif (is_bool($avar)) echo "{$indent}{$var_name} = <span style=\"color:#a2a2a2\">{$type}(" . strlen($avar) . ")</span> {$type_color}" . ($avar == 1 ? 'TRUE' : 'FALSE') . "</span><br />";
			elseif (is_null($avar)) echo "{$indent}{$var_name} = <span style=\"color:#a2a2a2\">{$type}(" . strlen($avar) . ")</span> {$type_color}NULL</span><br />";
			else
				echo "{$indent}{$var_name} = <span style=\"color:#a2a2a2\">{$type}(" . strlen($avar) . ")</span> $avar<br />";

			$var = $var[$keyvar];
		}
	}

	/**
	 * redirecting user to another page with "303 See Other" status
	 *
	 * Function redirects user to another page indicated in $url
	 *
	 * @param string $url URL where it will redirects
	 */
	public static function redirect($url)
	{
		if (empty($url)) {
			$request_uri = $_SERVER['REQUEST_URI'];
			$query_string = $_SERVER['QUERY_STRING'];
			$url = str_replace ('?' . $query_string, '', $request_uri);
		}
		header("{$_SERVER['SERVER_PROTOCOL']} 303 See Other");
		header("Location: {$url}");
		die;
	}
	
	/**
	 * generating hidden items of request form
	 *
	 * Function generates hidden items of request form
	 *
	 * @param array $newparam data for hidden items,
	 * where keys of array are names of variables
	 * and values of arry are values of variables
	 * @param bool $pass_all defines unsetting of value of request data named 'action'
	 */
	public static function form($newparam = array(), $pass_all = false)
	{
		if ($pass_all) {
			$arr = $_REQUEST;
			unset($arr['action']);
		}
		else
			$arr = SJB_HelperFunctions::unset_unnecessary($_REQUEST);
		foreach ($newparam as $name=>$value)
			$arr[$name] = $value;
		foreach($arr as $k => $v) {
			if (is_array ($v))
				continue;
			$arr[$k] = htmlspecialchars($v);
		}
		return SJB_HelperFunctions::array_to_string($arr, '<input type="hidden" name="','" value="','" />' . "\n");
	}
	
	/**
	 * getting requested data as array
	 * Function gets requested data as array
	 * @return array requested data
	 */
	public static function get_request_data_params()
	{
		$arr = $_REQUEST;
		$brr = array();
		foreach($arr as $k => $v)
			if (!is_array($v))
				$brr[$k] = $v;
		return $brr;
	}
	
	/**
	 * unsetting unnecessary values of array
	 * Function unsets unnecessary values of array
	 * @param array $arr processing array
	 * @return array processed array
	 */
	public static function unset_unnecessary($arr)
	{
		$required_variables = array('sessid');
		if (is_array($arr)) {
			$tt = array();
			foreach ($required_variables as $r)
				if (isset($arr[$r]))
					$tt[$r] = $arr[$r];
			return $tt;
		}
	}

	/**
	 * converting array to string
	 *
	 * Function converts array to string based on begining,
	 * middle and ending strings. It adds them to begining,
	 * middle and ending for each item of array
	 *
	 * @param array $arr converting array
	 * @param string $begining begining string
	 * @param string $middle middle string
	 * @param string $ending ending string
	 * @return string converted string
	 */
	public static function array_to_string($arr, $begin, $middle, $ending)
	{
		$str = '';
		if (isset($arr) && is_array($arr))
			foreach($arr as $name => $value)
				$str .= $begin . $name . $middle . $value . $ending;
		return $str;
	}

	public static function array_sort($array)
	{
		ksort($array);
		if (is_array(current($array))) {
			foreach ($array as $key => $value)
				$sorted_array_keys[$key] = count($value, COUNT_RECURSIVE);
			asort($sorted_array_keys);
			foreach ($sorted_array_keys as $key => $value)
				$sorted_array[$key] = $array[$key];
			return $sorted_array;
		}
		else {
			asort($array);
			return $array;
		}
	}

	public static function array_sort_reverse($array)
	{
		$sorted_array = SJB_HelperFunctions::array_sort($array);
		return array_reverse($sorted_array, true);
	}

	/**
	 * take media field's keys
	 *
	 * @param array $aFields
	 * @param string $mediaType
	 * @return array
	 */
	public static function takeMediaFields($aFields, $mediaType = 'video')
	{
		$aMediaFieldsKeys = array();
		foreach ($aFields as $key => $aField) {
			if ($mediaType === $aField['type'])
				array_push($aMediaFieldsKeys, $key);
		}
		return $aMediaFieldsKeys;
	}

    public static function getUrlContentByCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $xmlString = curl_exec($ch);
		if ($xmlString == false) {
			throw new Exception("Curl error: " . curl_error($ch));
		}
        curl_close($ch);
        return $xmlString;
    }

	public static function runScriptByCurl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_exec($ch);
		curl_close($ch);
	}

	/**
	 * @static
	 * @param int $filesize filesize in bytes
	 * @return array
	 */
	public static function getFileSizeAndSizeToken($filesize = 0)
	{
		// set filesize for template
		$sizeTokens = array('bytes', 'Kb', 'Mb', 'Gb');
		$sizeToken  = $sizeTokens[0];
		$i = 0;
		while ($filesize > 1024) {
			$i++;
			$filesize  = $filesize / 1024;
			$sizeToken = isset($sizeTokens[$i]) ? $sizeTokens[$i] : '';
		}
		return array('filesize' => $filesize, 'size_token' => $sizeToken);
	}

	public static function makeXLSExportFile($exportData, $exportFileName, $title)
	{
		$excel = new PHPExcel();
		$excel->getActiveSheet()->setTitle($title);
		
		$row = 1;
		foreach ($exportData as $exportItem) {
			$col = 0;
			if ($row == 1) {
				foreach ($exportItem as $name => $value) {
					$excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $name);
					$excel->getActiveSheet()->getStyleByColumnAndRow($col, 1)->applyFromArray(array(
						'font' => array(
							'bold' => true
						),
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
						),
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					));
					$col++;
				}
				$row = 2;
				$col = 0;
			}
			foreach($exportItem as $fieldKey => $fieldVal) {
				if (strpos($fieldKey, '.ZipCode') !== false) {
					$excel->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $fieldVal, PHPExcel_Cell_DataType::TYPE_STRING);
				} else {
					$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $fieldVal);
				}
				$excel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->applyFromArray(array(
					'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				));
				$col++;
			}
			$row++;
		}
		
		$export_files_dir = SJB_System::getSystemSettings('EXPORT_FILES_DIRECTORY');
		
		$objWriter = new PHPExcel_Writer_Excel5($excel);
		$objWriter->save($export_files_dir.'/'.$exportFileName);
	}

	public static function makeCSVExportFile($exportData, $exportFileName, $title)
	{
		$exportFilesDir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");
		$filePath       = $exportFilesDir . '/' . $exportFileName;
		$file           = fopen($filePath, 'wb+');
		fwrite($file, "\xEF\xBB\xBF");
		
		$row = 1;
		foreach ($exportData as $exportFields) {
			if ($row == 1) {
				$exportProperties = array_keys($exportFields);
				fputcsv($file, $exportProperties, ';');
			}
			fputcsv($file, $exportFields, ';');
			$row++;
		}
	}

	/**
	 * put info into debug stack
	 * @static
	 * @param $value
	 * @param string $index
	 */
	public static function debugInfoPush($value, $index = 'OTHER')
	{
		global $DEBUG;
		$DEBUG[$index][] = $value;
	}

	/**
	 * retrieve if debug mode is turned on
	 * @static
	 * @return bool
	 */
	public static function debugModeIsTurnedOn()
	{
		return SJB_System::getSystemSettings('DEBUG_MODE');
	}

	/**
	 * print debug info from global $DEBUG
	 * @static
	 */
	public static function debugInfoPrint()
	{
		if (SJB_HelperFunctions::debugModeIsTurnedOn()) {
			global $DEBUG;
			$DEBUG['OPERATING_SYSTEM'] = PHP_OS;
			echo '<pre>';
			print_r($DEBUG);
			echo '</pre>';

			echo 'REQUEST<br/><pre>';
			print_r($_REQUEST);
			echo '</pre>';
		}
	}

	/**
	 * @static
	 * @param SJB_TemplateProcessor $tp
	 * @param $string
	 * @return string
	 */
	public static function findSmartyRestrictedTagsInContent(SJB_TemplateProcessor $tp, $string)
	{
		$restrictedTags = array(
			$tp->left_delimiter . 'php' . $tp->right_delimiter,
			$tp->left_delimiter . 'include_php',
			$tp->left_delimiter . 'eval',
		);

		foreach ($restrictedTags as $tag) {
			if (stristr($string, $tag))
				return true;
		}

		return false;
	}

	/**
	 * @static
	 * @param $string
	 * @return string
	 * Modifies a string to remove al non ASCII characters and spaces.
	 * http://snipplr.com/view/22741/
	 */
	public static function slugify($string)
	{
		// replace non letter or digits by -
		$string = preg_replace('~[^\\pL\d]+~u', '-', $string);

		// trim
		$string = trim($string, '-');

		// transliterate
		if (function_exists('iconv'))
		{
			$string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
		}

		// lowercase
		$string = strtolower($string);

		// remove unwanted characters
		$string = preg_replace('~[^-\w]+~', '', $string);

		if (empty($string))
		{
			return 'n-a';
		}
		return $string;
	}
	
	public static function getClearVariablesToAssign($value) 
	{
		if (is_array($value)) {
			$result = array();
			foreach ($value as $key => $val) {
				if (is_array($val)) {
					$result[strip_tags($key)] = self::getClearVariablesToAssign($val);
				} else {
					$result[strip_tags($key)] = htmlentities($val, ENT_QUOTES, "UTF-8");
				}
			}
			return $result;
		}
		return htmlentities($value, ENT_QUOTES, "UTF-8");
	}

	public static function getTextFromZippedXML($archiveFile, $contentFile)
	{
		$zip = new ZipArchive;
		if ($zip->open($archiveFile)) {
			if (($index = $zip->locateName($contentFile)) !== false) {
				$content = $zip->getFromIndex($index);
				$zip->close();

				$dom = new DOMDocument();
				$xml = $dom->loadXML($content, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
				return strip_tags($dom->saveXML());
			}
			$zip->close();
		}
		return '';
	}

	public static function docx2text($filename)
	{
		return self::getTextFromZippedXML($filename, 'word/document.xml');
	}

	public static function trimValue($value)
	{
		return trim($value);
	}

	public static function getSiteUrl()
	{
		return SJB_System::getSystemSettings('SITE_URL');
	}

	public static function getCharSets()
	{
		return array('ARMSCII-8', 'ASCII', 'BIG5', 'BIG5-HKSCS', 'C99', 'CP850', 'CP862', 'CP866', 'CP874', 'CP932', 'CP936', 'CP949', 'CP950', 'CP1131', 'CP1133', 'CP1250', 'CP1251', 'CP1252', 'CP1253', 'CP1254', 'CP1255', 'CP1256', 'CP1257', 'CP1258', 'EUC-CN', 'EUC-JP', 'EUC-KR', 'EUC-TW', 'GB18030', 'GBK', 'Georgian-Academy', 'Georgian-PS', 'HP-ROMAN8', 'HZ', 'ISO-2022-CN', 'ISO-2022-CN-EXT', 'ISO-2022-JP', 'ISO-2022-JP-1', 'ISO-2022-JP-2', 'ISO-2022-KR', 'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-11', 'ISO-8859-11', 'ISO-8859-12', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16', 'JAVA', 'JOHAB', 'KOI8-R', 'KOI8-RU', 'KOI8-T', 'KOI8-U', 'MacArabic', 'MacCentralEurope', 'MacCroatian', 'MacCyrillic', 'MacGreek', 'MacHebrew', 'MacIceland', 'Macintosh', 'MacRoman', 'MacRomania', 'MacThai', 'MacTurkish', 'MacUkraine', 'MuleLao-1', 'NEXTSTEP', 'PT154', 'RK1048', 'SHIFT_JIS', 'TCVN', 'TIS-620', 'UCS-2', 'UCS-2BE', 'UCS-2LE', 'UCS-4', 'UCS-4BE', 'UCS-4LE', 'UTF-7', 'UTF-8', 'UTF-16', 'UTF-16BE', 'UTF-16LE', 'UTF-32', 'UTF-32BE', 'UTF-32LE', 'VISCII');
	}

	public static function html2pdf($html, $filename, $footerText = false)
	{
		for ($i = 0; $i < ob_get_level(); $i++) {
			ob_end_clean();
		}
		$pdf = new ExceptionThrowingTCPDF();
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->setPrintHeader(false);
		if ($footerText != false) {
			$pdf->footerText = $footerText;
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		} else {
			$pdf->setPrintFooter(false);
		}
		switch (SJB_I18N::getInstance()->getCurrentLanguage()) {
			case 'ja':
				$font = 'cid0jp';
				break;
			case 'zh':
				$font = 'cid0cs';
				break;
			case 'hi':
				$font = 'akshar';
				break;
			case 'fr':
			case 'pt':
				$html = str_replace('à', '&agrave;', $html);
			default:
				$font = 'dejavusans';
		}
		$pdf->SetFont($font, '', 14, '', true);
		$pdf->AddPage();
		$pdf->writeHTML($html, true, false, false, false, '');
		$pdf->Output($filename, 'D');
	}

	/**
	 * @param string $fileName
	 * @param string $data
	 */
	public static function writeCronLogFile($fileName, $data)
	{
		if ($logFile = fopen(SJB_System::getSystemSettings('CRON_LOG_DIR') . $fileName, 'a+')) {
			fwrite($logFile, $data);
			fclose($logFile);
		}
	}
}
