<?php

/**
 * WYSIWYGEditorprovider - wrapper for using different
 * WYSIWYG editor with the same interface
 */

class SJB_WYSIWYGEditorProvider
{
	var $editor;
	var $editorsInfo;

	/**
	 * Set the specific editor type from available editors list
	 * or set default editor
	 */
	function SJB_WYSIWYGEditorProvider($type)
	{
		$this->editorsInfo = new SJB_WYSIWYGEditorsInfo();
		if (array_key_exists($type, $fullEditorsInfo = $this->editorsInfo->getAvailableEditorsFullInfo())) {
			$this->editor = new $fullEditorsInfo[$type]['class_name'];
		} else {
			$defaultEditor = $this->editorsInfo->getDefaultEditor();
			$this->editor = new $defaultEditor;
		}
	}

	/**
	 * return HTML code due to specific editor type and
	 * function parameters
	 */
	function getEditorHTML($name, $content = '', $height = 200, $width = '100%', $conf = null, $params = null)
	{
		return $this->editor->getHTML($name, $content, $height, $width, $conf, $params);
	}
}


class SJB_WYSIWYGEditorsInfo
{

	var $defaultEditor;
	var $availableEditors;

	function getAvailableEditorsList()
	{
		$result = array();
		foreach ($this->availableEditors as $type => $editor)
			$result[$type] = $editor['name'];
		return $result;
	}

	function getDefaultEditor()
	{
		return $this->defaultEditor;
	}

	function getAvailableEditorsFullInfo()
	{
		return $this->availableEditors;
	}

	function SJB_WYSIWYGEditorsInfo()
	{
		$this->defaultEditor = 'SJB_ckeditorWrapper';
		$this->availableEditors = array(
			'ckeditor' => array(
				'name' => 'CKEditor',
				'class_name' => 'SJB_ckeditorWrapper'
			),
			'none' => array(
				'name' => 'Simple TextArea',
				'class_name' => 'SJB_textareaWrapper'
			),
		);
	}

}


/**
 * Parent class for different WYSWYG Editors
 */
class SJB_WYSIWYGWrapper
{
	var $editorDir; //Directory with editor package files

	function SJB_WYSIWYGWrapper()
	{
		$this->editorDir = SJB_System::getSystemSettings('EXTERNAL_COMPONENTS_DIR');
	}

	function requireInitFile($initFile)
	{
	}

	function setEditorPath($relativeEditorPath)
	{
		$this->editorDir .= $relativeEditorPath;
	}

	/**
	 * return path from current URL to
	 * document root directory
	 */
	function correctPath()
	{
		return SJB_System::getSystemSettings("SITE_URL") . "/";
	}
}


/**
 * Type of WYSIWYG editor, displays simple textarea
 * with specific name and content from function arguments
 */
class SJB_textareaWrapper
{
	function getHTML($name, $content, $height, $width, $conf = null, $params = null)
	{
		if (strpos($width, '%') === false && strpos($width, 'px') === false)
			$width .= 'px';
		if (strpos($height, '%') === false && strpos($height, 'px') === false)
			$height .= 'px';
		$class = !empty($params['class']) ? $params['class'] : '';
		return "<textarea name='{$name}' style='width:{$width}; height:{$height}' class='{$class}'>"
				. $content . "</textarea>";
	}
}

class SJB_ckeditorWrapper extends SJB_WYSIWYGWrapper
{
	function SJB_ckeditorWrapper()
	{
		$this->SJB_WYSIWYGWrapper();
		$this->setEditorPath('ckeditor/');
		$this->requireInitFile('ckeditor.php');
	}

	function getHTML($name, $content, $height, $width, $conf = null)
	{
		$CKeditor = new CKEditor();
		$CKeditor->returnOutput = true;

		$CKeditor->basePath = $this->correctPath() . $this->editorDir;
		$CKeditor->config['width'] = $width;
		$CKeditor->config['height'] = $height;
		$CKeditor->config['toolbar'] = $conf;
		$CKeditor->config['enterMode'] = 2;
		$lang_data = SJB_I18N::getInstance()->getLanguageData(SJB_I18N::getInstance()->getCurrentLanguage());
		$CKeditor->config['contentsLangDirection'] = $lang_data['rightToLeft'] == 1 ? 'rtl' : 'ltr';

		if ($conf == 'BasicAdmin') {
			// kcfinder
			$CKeditor->config['filebrowserBrowseUrl'] = SJB_System::getSystemSettings('SITE_URL') . '/system/miscellaneous/kcfinder/browse.php?type=files';
			$CKeditor->config['filebrowserImageBrowseUrl'] = SJB_System::getSystemSettings('SITE_URL') . '/system/miscellaneous/kcfinder/browse.php?type=images';
			$CKeditor->config['filebrowserFlashBrowseUrl'] = SJB_System::getSystemSettings('SITE_URL') . '/system/miscellaneous/kcfinder/browse.php?type=flash';
			$CKeditor->config['filebrowserUploadUrl'] = SJB_System::getSystemSettings('SITE_URL') . '/system/miscellaneous/kcfinder/upload.php?type=files';
			$CKeditor->config['filebrowserImageUploadUrl'] = SJB_System::getSystemSettings('SITE_URL') . '/system/miscellaneous/kcfinder/upload.php?type=images';
			$CKeditor->config['filebrowserFlashUploadUrl'] = SJB_System::getSystemSettings('SITE_URL') . '/system/miscellaneous/kcfinder/upload.php?type=flash';
		}

		$CKeditor->config['toolbar_Full'] = array
		(
			array('name' => 'document', 'items' => array('Source', '-', 'Save', 'NewPage', 'DocProps', 'Preview', 'Print', '-', 'Templates')),
			array('name' => 'clipboard', 'items' => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo')),
			array('name' => 'editing', 'items' => array('Find', 'Replace', '-', 'SelectAll', '-', 'SpellChecker', 'Scayt')),
			array('name' => 'forms', 'items' => array('Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField')),
			'/',
			array('name' => 'basicstyles', 'items' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat')),
			array('name' => 'paragraph', 'items' => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv',
				'-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl')),
			array('name' => 'links', 'items' => array('Link', 'Unlink', 'Anchor')),
			array('name' => 'insert', 'items' => array('Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe')),
			'/',
			array('name' => 'styles', 'items' => array('Styles', 'Format', 'Font', 'FontSize')),
			array('name' => 'colors', 'items' => array('TextColor', 'BGColor')),
			array('name' => 'tools', 'items' => array('Maximize', 'ShowBlocks', '-', 'About'))
		);

		$CKeditor->config['toolbar_BasicAdmin'] = array
		(
			array('name' => 'document', 'items' => array('Source', 'Templates')),
			array('name' => 'clipboard', 'items' => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo')),
			array('name' => 'editing', 'items' => array('SpellChecker', 'Scayt')),
			'/',
			array('name' => 'basicstyles', 'items' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat')),
			array('name' => 'paragraph', 'items' => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock')),
			array('name' => 'links', 'items' => array('Link', 'Unlink', 'Anchor')),
			array('name' => 'insert', 'items' => array('Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe')),
			array('name' => 'tools', 'items' => array('About')),
			'/',
			array('name' => 'styles', 'items' => array('Styles', 'Format', 'Font', 'FontSize')),
			array('name' => 'colors', 'items' => array('TextColor', 'BGColor'))
		);

		$CKeditor->config['toolbar_Basic'] = array
		(
			array('name' => 'clipboard', 'items' => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo')),
			array('name' => 'editing', 'items' => array('SpellChecker', 'Scayt')),
			array('name' => 'basicstyles', 'items' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat')),
			'/',
			array('name' => 'paragraph', 'items' => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote',
				'-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock')),
			array('name' => 'links', 'items' => array('Link', 'Unlink')),
			array('name' => 'insert', 'items' => array('Table', 'HorizontalRule', 'PageBreak')),
			'/',
			array('name' => 'styles', 'items' => array('Styles', 'Format', 'Font', 'FontSize')),
			array('name' => 'colors', 'items' => array('TextColor', 'BGColor')),
			array('name' => 'tools', 'items' => array('About'))
		);

		$CKeditor->addEventHandler('instanceReady', "function (ev) {
			ev.editor.dataProcessor.writer.indentationChars = '';
			ev.editor.dataProcessor.writer.lineBreakChars = '';
		}");

		return $CKeditor->editor($name, $content, $conf);
	}
}

