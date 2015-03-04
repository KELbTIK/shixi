<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Container_xml class
 *
 * PHP versions 4 and 5
 *
 * LICENSE: Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Internationalization
 * @package   Translation2
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @author    Olivier Guilyardi <olivier@samalyse.com>
 * @copyright 2004-2007 Lorenzo Alberton, Olivier Guilyardi
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id: xml.php 7232 2013-01-24 03:37:53Z furiae $
 * @link      http://pear.php.net/package/Translation2
 */

/**
 * require Translation2_Container class
 */
require_once 'Translation2/Container.php';
/**
 * require XML_Unserializer class
 */
//require_once 'XML/Unserializer.php';
/**
 * Document Type Definition
 */
define('TRANSLATION2_DTD',
    "<!ELEMENT translation2 (languages,pages)>\n" .
    "<!ELEMENT languages (lang*)>\n" .
    "<!ELEMENT lang (name?,meta?,error_text?,encoding?)>\n" .
    "<!ATTLIST lang id ID #REQUIRED>\n" .
    "<!ELEMENT name (#PCDATA)>\n" .
    "<!ELEMENT meta (#PCDATA)>\n" .
    "<!ELEMENT error_text (#PCDATA)>\n" .
    "<!ELEMENT encoding (#PCDATA)>\n" .
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
 * @category  Internationalization
 * @package   Translation2
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @author    Olivier Guilyardi <olivier@samalyse.com>
 * @copyright 2004-2007 Lorenzo Alberton, Olivier Guilyardi
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Translation2
 */
class Translation2_Container_xml extends Translation2_Container
{
    // {{{ class vars

    /**
     * Unserialized XML data 
     * @var object
     */
    var $_data = null;

    /**
     * XML file name
     * @var string
     */
    var $_filename;
    
    // }}}
    // {{{ init

    /**
     * Initialize the container 
     *
     * @param array $options - 'filename': Path to the XML file
     *
     * @return boolean|PEAR_Error object if something went wrong
     */
    function init($options)
    {
        $this->_filename = $options['filename'];
        unset($options['filename']);
        $this->_setDefaultOptions();
        $this->_parseOptions($options);

        return $this->_loadFile();
    }
   

    function simpleUnserialize($file) {
		$parser = xml_parser_create();
		$vals = array();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, file_get_contents($file), $vals);
		xml_parser_free($parser);
	
		$currentPage = "";
		$lang = "";
		$key = "";
		$result = array();
		$result["languages"] = array();
		$result["pages"] = array();
		
		foreach ($vals as $value) {
			switch ($value["tag"]) {
				case "tr":
					$lang = $value["attributes"]["lang"];
					$result["pages"][$currentPage][$key] = array($lang => trim($value["value"]));
					break;
				case "string":
					if ($value["type"] == "open") {
						$key = $value["attributes"]["key"];
					}
					break;
				case "page":
					if ($value["type"] == "open" || $value["type"] == "complete") {
						$currentPage = $value["attributes"]["key"];
						$result["pages"][$currentPage] = array();
					}
					break;
					
				case "lang":
					if ($value["type"] == "open")
						$lang = $value["attributes"]["id"];
					break;
				case "name":
				case "meta":
				case "encoding":
					if ((isset($value["value"]) && !empty($value["value"])) || $value["tag"] == "error_text")
						$result["languages"][$lang][$value["tag"]] = trim($value["value"]);
					break;
			}
		}
		$result["languages"][$lang]["error_text"] = "";
		return $result;
    }
        
    /**
     * Load an XML file into memory, and eventually decode the strings from UTF-8
     *
     * @return boolean|PEAR_Error
     * @access private
     */
    
    function _loadFile()
    {
        $keyAttr = array (
            'lang'   => 'id',
            'page'   => 'key',
            'string' => 'key',
            'tr'     => 'lang'
        );
       // $unserializer = &new XML_Unserializer (array('keyAttribute' => $keyAttr));
        //if (PEAR::isError($status = $unserializer->unserialize(file_get_contents($this->_filename), false))) {
          //  return $status;
        //}

        // unserialize data
        //$this->_data = $unserializer->getUnserializedData();
        
        
        $this->_data = $this->simpleUnserialize($this->_filename);
        
        
        
        $this->fixEmptySets($this->_data);
        $this->_fixDuplicateEntries();
        
        
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
                $this->_data['languages'][$lang_id] =
                    array_merge($defaults, $this->_data['languages'][$lang_id]);
            }
        }

        // convert lang metadata from UTF-8
        if (PEAR::isError($e = $this->_convertLangEncodings('from_xml', $this->_data))) {
            return $e;
        }

        // convert encodings of the translated strings from xml (somehow heavy)
        return $this->_convertEncodings('from_xml', $this->_data);
    }
    
    // }}}
    // {{{ _convertEncodings()

    /** 
     * Convert strings to/from XML unique charset (UTF-8)
     *
     * @param string $direction ['from_xml' | 'to_xml']
     * @param array  &$data     Data buffer to operate on
     *
     * @return boolean|PEAR_Error
     */
    function _convertEncodings($direction, &$data)
    {
        if ($direction == 'from_xml')
            $source_encoding = 'UTF-8';
        else
            $target_encoding = 'UTF-8';
            
        foreach ($data['pages'] as $page_id => $page_content) {
            foreach ($page_content as $str_id => $translations) {
                foreach ($translations as $lang => $str) {
                    if ($direction == 'from_xml') {
                    	$target_encoding = 'UTF-8';
                    	if (isset($data['languages'][$lang]['encoding']))
                        	$target_encoding = strtoupper($data['languages'][$lang]['encoding']);
                    } else {
                    	$source_encoding = 'UTF-8';
                    	if (isset($data['languages'][$lang]['encoding']))
                        	$source_encoding = strtoupper($data['languages'][$lang]['encoding']);
                    }
                    if ($target_encoding != $source_encoding) {
                        $res = iconv($source_encoding, $target_encoding, $str);
                        if ($res === false) {
                            $msg = 'Encoding conversion error ' .
                                   "(source encoding: $source_encoding, ".
                                   "target encoding: $target_encoding, ".
                                   "processed string: \"$str\"";
                            return $this->raiseError($msg,
                                    TRANSLATION2_ERROR_ENCODING_CONVERSION,
                                    PEAR_ERROR_RETURN,
                                    E_USER_WARNING);
                        }
                        $data['pages'][$page_id][$str_id][$lang] = $res;
                    }
                }
            }
        }
        return true;
    }
         
    // }}}
    // {{{ _convertLangEncodings()

    /**
     * Convert lang data to/from XML unique charset (UTF-8)
     *
     * @param string $direction ['from_xml' | 'to_xml']
     * @param array  &$data     Data buffer to operate on
     *
     * @return boolean|PEAR_Error
     */
    function _convertLangEncodings($direction, &$data)
    {
        static $fields = array('name', 'meta', 'error_text');

        if ($direction == 'from_xml') {
            $source_encoding = 'UTF-8';
        } else {
            $target_encoding = 'UTF-8';
        }
        
        foreach ($data['languages'] as $lang_id => $lang) {
            if ($direction == 'from_xml') {
                $target_encoding = strtoupper($lang['encoding']);
            } else {
                $source_encoding = strtoupper($lang['encoding']);
            }
            //foreach (array_keys($lang) as $field) {
            foreach ($fields as $field) {
                if ($target_encoding != $source_encoding && !empty($lang[$field])) {
                    $res = iconv($source_encoding, $target_encoding, $lang[$field]);
                    if ($res === false) {
                        $msg = 'Encoding conversion error ' .
                               "(source encoding: $source_encoding, ".
                               "target encoding: $target_encoding, ".
                               "processed string: \"$lang[$field]\"";
                        return $this->raiseError($msg,
                                TRANSLATION2_ERROR_ENCODING_CONVERSION,
                                PEAR_ERROR_RETURN,
                                E_USER_WARNING);
                    }
                    $data['languages'][$lang_id][$field] = $res;
                }
            }
        }
        return true;
    }

    // }}}
    // {{{ _fixDuplicateEntries()
    
    /**
     * Remove duplicate entries from the xml data
     *
     * @return void
     */
    function _fixDuplicateEntries()
    {
        foreach ($this->_data['pages'] as $pagename => $pagedata) {
            foreach ($pagedata as $stringname => $stringvalues) {
                if (is_array(array_pop($stringvalues))) {
                    $this->_data['pages'][$pagename][$stringname] =
                        call_user_func_array(array($this, '_merge'), $stringvalues);
                }
            }
        }
    }
    
    // }}}
    // {{{ fixEmptySets()

    /**
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
    function fixEmptySets(&$data)
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
        } else {
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

    // }}}
    // {{{ _merge()

    /**
     * Wrapper for array_merge()
     *
     * @param array $arr1 reference
     *
     * @return array
     */
    function _merge()
    {
        $return = array();
        foreach (func_get_args() as $arg) {
            $return = array_merge($return, $arg);
        }
        return $return;
    }
    
    // }}}
    // {{{ _setDefaultOptions()

    /**
     * Set some default options
     *
     * @return void
     * @access private
     */
    function _setDefaultOptions()
    {
        //save changes on shutdown or in real time?
        $this->options['save_on_shutdown']  = true;
        $this->options['filename_pages']  	= '';
    }

    // }}}
    // {{{ fetchLangs()

    /**
     * Fetch the available langs
     *
     * @return void
     */
    function fetchLangs()
    {
        $res = array();
        foreach ($this->_data['languages'] as $id => $spec) {
            $spec['id'] = $id;
            $res[$id] = $spec;
        }
        $this->langs = $res;
    }

    // }}}
    // {{{ getPage()

    /**
     * Returns an array of the strings in the selected page
     *
     * @param string $pageID page/group ID
     * @param string $langID language ID
     *
     * @return array
     */
    function getPage($pageID = null, $langID = null)
    {
        $langID = $this->_getLangID($langID);
        if (PEAR::isError($langID)) {
            return $langID;
        }
        $pageID = (is_null($pageID)) ? '#NULL' : $pageID;
        $pageID = (empty($pageID) && (0 !== $pageID)) ? '#EMPTY' : $pageID;

        $result = array();
        foreach ($this->_data['pages'][$pageID] as $str_id => $translations) {
            $result[$str_id]  = isset($translations[$langID]) 
                                ? $translations[$langID] 
                                : null;
        }
        
        return $result;
    }

    // }}}
    // {{{ getOne()

    /**
     * Get a single item from the container
     *
     * @param string $stringID string ID
     * @param string $pageID   page/group ID
     * @param string $langID   language ID
     *
     * @return string
     */
    function getOne($stringID, $pageID = null, $langID = null)
	{
		$langID = $this->_getLangID($langID);
		$pageID = (is_null($pageID)) ? '#NULL' : $pageID;

		if ($this->_data) {
			// Nwy какая-то глупо-бестолковая функция проверяющая ключи и жрущая кучу ресурсов
			//$stringID = $this->getKeyByStringID($stringID, $pageID);
			return isset($this->_data['pages'][$pageID][$stringID][$langID]) ? $this->_data['pages'][$pageID][$stringID][$langID] : null;
		}
	}

	/**
	 * SJB-970
	 * 
	 * make translations case insensitive
	 * 
	 * @param string $stringID
	 * @param string $pageID
	 * @return string
	 */
	function getKeyByStringID($stringID, $pageID)
	{
		if (isset($this->_data['pages'][$pageID]) && is_array($this->_data['pages'][$pageID])) {
			foreach ($this->_data['pages'][$pageID] as $key => $val) {
				if (strcasecmp($stringID, $key) == 0) {
					return $key;
				}
			}
		}
		return $stringID;
	}
	
	
    // }}}
    // {{{ getStringID()

    /**
     * Get the stringID for the given string
     *
     * @param string $stringID string ID
     * @param string $pageID   page/group ID
     *
     * @return string
     */
    function getStringID($stringID, $pageID = null)
    {
        $pageID = (is_null($pageID)) ? '#NULL' : $pageID;                        
        
        foreach ($this->_data['pages'][$pageID] as $str_id => $translations) {
            if (array_search($string, $translations) !== false) {
                return $str_id;
            }
        }

        return '';
    }

	/**
	 * @param string $filename
	 */
	public function setFilename($filename)
	{
		$this->_filename = $filename;
	}

}
