<?php
/**
 * require Translation2_Admin_Container_xml class
 */
require_once 'Translation2/Admin/Container/xml.php';

/**
 * Document Type Definition (LANGUAGES)
 * <!DOCTYPE translation2 [
 <!ELEMENT translation2 (languages)>
 <!ELEMENT languages (lang*)>
 <!ELEMENT lang (name?,meta?,error_text?,encoding?)>
 <!ATTLIST lang id ID #REQUIRED>
 <!ELEMENT name (#PCDATA)>
 <!ELEMENT meta (#PCDATA)>
 <!ELEMENT error_text (#PCDATA)>
 <!ELEMENT encoding (#PCDATA)>
 ]>
 */
define('TRANSLATION2_DTD_LANGUAGES',
    "<!ELEMENT translation2 (languages)>\n" .
    "<!ELEMENT languages (lang*)>\n" .
    "<!ELEMENT lang (name?,meta?,error_text?,encoding?)>\n" .
    "<!ATTLIST lang id ID #REQUIRED>\n" .
    "<!ELEMENT name (#PCDATA)>\n" .
    "<!ELEMENT meta (#PCDATA)>\n" .
    "<!ELEMENT error_text (#PCDATA)>\n" .
    "<!ELEMENT encoding (#PCDATA)>\n"
);

/**
 * Document Type Definition
 * <!ELEMENT translation2 (languages,pages)>
 <!ELEMENT languages (lang*)>
 <!ELEMENT lang (#PCDATA)>
 <!ATTLIST lang id ID #REQUIRED>
 <!ELEMENT pages (page*)>
 <!ELEMENT page (string*)>
 <!ATTLIST page key CDATA #REQUIRED>
 <!ELEMENT string (tr*)>
 <!ATTLIST string key CDATA #REQUIRED>
 <!ELEMENT tr (#PCDATA)>
 <!ATTLIST tr lang IDREF #REQUIRED>
 */
define('TRANSLATION2_DTD_PAGES',
    "<!ELEMENT translation2 (languages,pages)>\n" .
    "<!ELEMENT languages (lang*)>\n" .
    "<!ELEMENT lang (#PCDATA)>\n" .
    "<!ATTLIST lang id ID #REQUIRED>\n" .
    "<!ELEMENT pages (page*)>\n" .
    "<!ELEMENT page (string*)>\n" .
    "<!ATTLIST page key CDATA #REQUIRED>\n" .
    "<!ELEMENT string (tr*)>\n" .
    "<!ATTLIST string key CDATA #REQUIRED>\n" .
    "<!ELEMENT tr (#PCDATA)>\n" .
    "<!ATTLIST tr lang IDREF #REQUIRED>\n"
);

/**
 * Storage driver for fetching data from a XML file
 *
 * Example file :
 * <pre>
 * <?xml version="1.0" encoding="iso-8859-1"?>
 * <translation2>
 *     <languages>
 *         <lang id='fr_FR'>
 *             <name> English </name>
 *             <meta> Custom meta data</meta>
 *             <error_text> Non disponible en fran�ais </error_text>
 *             <encoding> iso-8859-1 </encoding>
 *         </lang>
 *         <!-- some more <lang>...</lang> -->
 *     </languages>
 *     <pages>
 *         <page key='pets'>
 *             <string key='cat'>
 *                 <tr lang='fr_FR'> Chat </tr>
 *                 <!-- some more <tr>...</tr> -->
 *             </string>
 *             <!-- some more <string>...</string> -->
 *         </page>
 *         <!-- some more <page>...</page> -->
 *     </pages>
 * </translation2>
 * </pre>
 *
 */
class Translation2_Admin_Container_xml_ex extends Translation2_Admin_Container_xml
{
	/**
	 * add translations to container
	 */
	function initPages()
	{
		$this->_loadFilePages();
	}

	function simpleUnserializePages($file)
	{
		$parser = xml_parser_create();
		$vals = array();

		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, file_get_contents($file), $vals);
		xml_parser_free($parser);

		$currentPage = '';
		$lang = '';
		$key = '';
		$result = array();
		$result['pages'] = array();

		foreach ($vals as $value) {
			switch ($value['tag']) {
				case 'tr':
					$lang = $value['attributes']['lang'];
					$result['pages'][$currentPage][$key] = array($lang => trim($value['value']));
					break;
				case 'string':
					if ($value['type'] == 'open') {
						$key = $value['attributes']['key'];
					}
					break;
				case 'page':
					if ('open' == $value['type'] || 'complete' == $value['type']) {
						$currentPage = $value['attributes']['key'];
						$result['pages'][$currentPage] = array();
					}
					break;

				case 'lang':
					if ($value['type'] == 'open')
						$lang = $value['attributes']['id'];
					break;
			}
		}
		return $result['pages'];
	}

	function simpleUnserializeConfigFile($file)
	{
		$parser = xml_parser_create();
		$vals = array();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, file_get_contents($file), $vals);
		xml_parser_free($parser);

		$lang = '';
		$result = array();
		$result['languages'] = array();
		$result['pages'] = array();

		foreach ($vals as $value) {
			switch ($value['tag']) {
				case 'lang':
					if ($value['type'] == 'open')
						$lang = $value['attributes']['id'];
					break;
				case 'name':
				case 'meta':
				case 'encoding':
					if ((isset($value['value']) && !empty($value['value'])) || $value['tag'] == 'error_text')
						$result['languages'][$lang][$value['tag']] = trim($value["value"]);
					break;
			}
		}
		if (!empty($lang)) {
			$result['languages'][$lang]['error_text'] = '';
		}
		return $result;
	}

    /**
     * Load an XML file into memory, and eventually decode the strings from UTF-8
     *
     * @return boolean|PEAR_Error
     * @access private
     */
    public function _loadFile()
    {
        $this->_data = $this->simpleUnserializeConfigFile($this->_filename);
        $this->fixEmptySets($this->_data);

        // Handle default language settings.
        // This allows, for example, to rapidly write the meta data as:
        //
        // <lang key="fr"/>
        // <lang key="en"/>

        $defaults = array(
            'name'       => '',
            'meta'       => '',
            'error_text' => '',
            'encoding'   => 'iso-8859-1'
        );

        foreach ($this->_data['languages'] as $lang_id => $settings) {
            if (empty($settings)) {
                $this->_data['languages'][$lang_id] = $defaults;
            } else {
                $this->_data['languages'][$lang_id] = array_merge($defaults, $this->_data['languages'][$lang_id]);
            }
        }

        return true;
    }

	function _loadFilePages()
	{
		$this->_data['pages'] = $this->simpleUnserializePages($this->getPagesFileName());

		$this->fixEmptySets($this->_data);
		$this->_fixDuplicateEntries();

		// Handle default language settings.
		// This allows, for example, to rapidly write the meta data as:
		//
		// <lang key="fr"/>
		// <lang key="en"/>

		// convert encodings of the translated strings from xml (somehow heavy)
		return $this->_convertEncodings('from_xml', $this->_data);
	}

	/**
	 * @return string
	 */
	public function getPagesFileName()
	{
		return $this->options['filename_pages'];
	}

	public function setPagesFileNameOption($fileName)
	{
		$this->options['filename_pages'] = $fileName;
	}

	/**
	 * !!! используем только для конфигов
	 *
	 * Turn empty strings returned by XML_Unserializer into empty arrays
	 *
	 * Note: this method is public because called statically by the t2xmlchk.php
	 * script. It is not meant to be called by user-space code.
	 *
	 * @param array &$data array of languages/pages
	 *
	 * @return void
	 * @access public
	 * @static
	 */
	function fixEmptySetsInConfig(&$data)
	{
		if (PEAR::isError($this->_data) && ($this->_data->code == XML_UNSERIALIZER_ERROR_NO_UNSERIALIZATION)) {
			//empty file... create skeleton
			$this->_data = array(
				'languages' => array(),
				'pages'     => array(),
			);
		}
		if (is_string($data['languages']) and trim($data['languages']) == '') {
			$data['languages'] = array();
		}
		if (is_string($data['pages']) and trim($data['pages']) == '') {
			$data['pages'] = array();
		}
	}

	/**
	 * !!! используем только для pages
	 *
	 * Turn empty strings returned by XML_Unserializer into empty arrays
	 *
	 * Note: this method is public because called statically by the t2xmlchk.php
	 * script. It is not meant to be called by user-space code.
	 *
	 * @param array &$data array of languages/pages
	 *
	 * @return void
	 * @access public
	 * @static
	 */
	function fixEmptySetsInPages(&$data)
	{
		if (PEAR::isError($this->_data) && ($this->_data->code == XML_UNSERIALIZER_ERROR_NO_UNSERIALIZATION)) {
			//empty file... create skeleton
			$this->_data['pages'] = array();
		}

		if (is_string($data['pages']) and trim($data['pages']) == '') {
			$data['pages'] = array();
		}
		else {
			foreach ($data['pages'] as $pageName => $strings) {
				//if (is_string($strings) and trim($strings) == '') {
				if (is_string($strings)) {
					$data['pages'][$pageName] = array();
				} else {
					foreach ($strings as $stringName => $translations) {
						if (is_string($translations) and trim($translations) == '') {
							$data['pages'][$pageName][$stringName] = array();
						}
					}
				}
			}
		}
	}

	/**
	 * Serialize and save the updated tranlation data to the XML file
	 *
	 * @return boolean | PEAR_Error
	 * @access private
	 * @see Translation2_Admin_Container_xml::_scheduleSaving()
	 */
	function _saveData()
	{
		if ($this->options['save_on_shutdown']) {
			$data =& $this->_data;
		} else {
			$data =  $this->_data;
		}

		$this->_saveDataLanguages($data);
		$this->_saveDataPages($data);

		return true;
	}

	/**
	 * @param array $data
	 * @return PEAR_Error|void
	 */
	private function _saveDataLanguages(&$data)
	{
		// Serializing

		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n" .
				"<!DOCTYPE translation2 [\n" . TRANSLATION2_DTD_LANGUAGES . "]>\n\n" .
				"<translation2>\n" .
				"  <languages>\n";

		foreach ($data['languages'] as $lang => $spec) {
			extract ($spec);
			$xml .= "    <lang id=\"$lang\">\n" .
					"      <name>" .
					($name ? ' ' . XML_Util::replaceEntities($name) . ' ' : '') .
					"</name>\n" .
					"      <meta>" .

					// FIX: form libxml2 bug
					//($meta ? ' ' . XML_Util::replaceEntities($meta) . ' ' : "") .
					($meta ? ' ' . str_replace('__quote__', '"', XML_Util::replaceEntities(str_replace('"', "__quote__", $meta))) . ' ' : "") .

					"</meta>\n" .
					"      <error_text>" .
					($error_text
							? ' ' . XML_Util::replaceEntities($error_text) . ' '
							: "") .
					"</error_text>\n" .
					"      <encoding>" . ($encoding ? " $encoding " : "") .
					"</encoding>\n" .
					"    </lang>\n";
		}

		$xml .= "  </languages>\n";
		$xml .=	"</translation2>\n";

		unset ($data);

		// Saving

		if (!$f = fopen ($this->_filename, 'w')) {
			return $this->raiseError(sprintf(
					'Unable to open the XML file ("%s") for writing',
					$this->_filename
				),
				TRANSLATION2_ERROR_CANNOT_WRITE_FILE,
				PEAR_ERROR_TRIGGER,
				E_USER_ERROR
			);
		}
		@flock($f, LOCK_EX);
		fwrite ($f, $xml);
		//@flock($f, LOCK_UN);
		fclose ($f);
	}

	/**
	 * @param array $data
	 * @return bool|PEAR_Error|void
	 */
	private function _saveDataPages($data)
	{
		$fileName = !empty($this->options['filename_pages']) ? $this->options['filename_pages'] : false;
		if (!$fileName) {
			return false;
		}

		$this->_convertEncodings('to_xml', $data);

		// Serializing

		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n" .
				"<!DOCTYPE translation2 [\n" . TRANSLATION2_DTD_PAGES . "]>\n\n" .
				"<translation2>\n" .
				"  <languages>\n";

		foreach ($data['languages'] as $lang => $spec) {
			extract ($spec);
			$xml .= "    <lang id=\"$lang\"/>\n";
		}

		$xml .= "  </languages>\n" .
				"  <pages>\n";

		foreach ($data['pages'] as $page => $strings) {
			$xml .= "    <page key=\"" . XML_Util::replaceEntities($page) .
					"\">\n";
			foreach ($strings as $str_id => $translations) {
				$xml .= "      <string key=\"" .
						XML_Util::replaceEntities($str_id) . "\">\n";
				foreach ($translations as $lang => $str) {
					$xml .= "        <tr lang=\"$lang\"> " .
							XML_Util::replaceEntities($str) . " </tr>\n";
				}
				$xml .= "      </string>\n";
			}
			$xml .= "    </page>\n";
		}

		$xml .= "  </pages>\n" .
				"</translation2>\n";

		unset ($data);

		// Saving

		if (!$f = fopen ($fileName, 'w')) {
			return $this->raiseError(sprintf(
					'Unable to open the XML file ("%s") for writing',
					$fileName
				),
				TRANSLATION2_ERROR_CANNOT_WRITE_FILE,
				PEAR_ERROR_TRIGGER,
				E_USER_ERROR
			);
		}
		@flock($f, LOCK_EX);
		fwrite ($f, $xml);
		//@flock($f, LOCK_UN);
		fclose ($f);
	}
}
?>