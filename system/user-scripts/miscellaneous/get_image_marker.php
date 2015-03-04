<?php

class SJB_Miscellaneous_GetImageMarker extends SJB_Function
{

	public function execute()
	{
		$cachePath   = SJB_BASE_DIR . "system/cache/markers";
		if (SJB_Request::isAjax()) {
			$fileSystem = new SJB_Filesystem();
			$markers = $fileSystem->getFileNames($cachePath);
			$response = count($markers) ? json_encode($markers) : '';
			exit($response);
		}

		$grayImage   = SJB_BASE_DIR . "templates/_system/main/images/marker_gray.gif";
		$greenImage  = SJB_BASE_DIR . "templates/_system/main/images/marker_green.gif";
		$orangeImage = SJB_BASE_DIR . "templates/_system/main/images/marker_orange.gif";
		$blueImage   = SJB_BASE_DIR . "templates/_system/main/images/marker_blue.gif";
		$pinkImage   = SJB_BASE_DIR . "templates/_system/main/images/marker_pink.gif";

		$type     = SJB_Request::getVar('type', 'Job');
		$text     = SJB_Request::getVar('text', '');
		$filename = '';

		$assets = array(
			'Job'         => $orangeImage,
			'priority'    => $orangeImage,
			'indeed'      => $blueImage,
			'simplyHired' => $greenImage,
			'beyond'	  => $grayImage,
			'Resume'	  => $orangeImage
		);

		if (array_key_exists($type, $assets)) {
			$filename = $assets[$type];
		} else {
			$filename = $grayImage;
		}

		$cacheFilename = $cachePath . "/marker_" . $type . '_' . $text . ".gif";
		$img = null;

		if (!file_exists($cacheFilename)) {
			if ($filename == $pinkImage || (!empty($text) && is_string($text) && !empty($filename))) {
				$img     = imagecreatefromgif($filename);
				$imageSx = imagesx($img);
				$imageSy = imagesy($img);

				$fontSize = 8;
				$textLen  = strlen($text);

				$black = imageColorAllocate($img, 0, 0, 0);
				$white = imageColorAllocate($img, 255, 255, 255);

				if (!empty($text)) {
					imagettftext($img, $fontSize, 0, (($imageSx/2) - floor( ($fontSize/2)*$textLen )) +2, 13, $black, SJB_BASE_DIR . "templates/_system/main/images/arial.ttf", $text);
				}

				if (!file_exists($cachePath)) {
					mkdir($cachePath, 0777);
				}
				imagegif($img, $cachePath . "/marker_" . $type . '_' . $text . ".gif");
			}
		} else {
			$img = imagecreatefromgif($cacheFilename);
		}

		if (!is_null($img)) {
			header("Content-type: image/gif");
			imagegif($img);
		}

		exit;
	}

}