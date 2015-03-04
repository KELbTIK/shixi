<?php
include("FCKeditor/fckeditor.php") ;
$oFCKeditor = new FCKeditor('html') ;
$oFCKeditor->BasePath = 'FCKeditor/';
$oFCKeditor->Value = 'Default text in editor';
$oFCKeditor->Create() ;
?>