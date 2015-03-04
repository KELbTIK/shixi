<?php

class SJB_UploadPictureManager extends SJB_UploadFileManager
{
	var $height;
	var $width;

	function setWidth($width)
	{
		$this->width = $width;
	}

	function setHeight($height)
	{
		$this->height = $height;
	}

	function isValidUploadedPictureFile($file_id)
	{
		if (empty($_FILES[$file_id]['tmp_name']) && empty($_FILES[$file_id]['name'])) {
			return true;
		}
		else if (empty($_FILES[$file_id]['tmp_name']) && (!empty($_FILES[$file_id]['name']))) {
			$this->error = 'UPLOAD_ERR_INI_SIZE';
			return false;
		}

		$image_info = getimagesize($_FILES[$file_id]['tmp_name']);		// $image_info['2'] = 1 {GIF}, 2 {JPG}, 3 {PNG}, 4 {SWF}, 5 {PSD}, 6 {BMP}, 7 {TIFF}, 8 {TIFF}, 9 {JPC}, 10 {JP2}, 11 {JPX}		
		$image_size = $_FILES[$file_id]['size'];

		$maxFilesize = $this->getIniUploadMaxFilesize('b');
		
		if ($image_size > $maxFilesize) {
			$this->error = 'UPLOAD_ERR_INI_SIZE';
			return false;
		}
		else if ( $image_info['2'] >= 1 && $image_info['2'] <= 3 ) {
			return true;
		}

		$this->error = 'NOT_SUPPORTED_IMAGE_FORMAT';
		return false;
	}

	function uploadPicture($file_id, $property_info = false)
	{
		if (is_null($this->uploaded_file_id)) {
			return false;
		} elseif (!empty($_FILES[$file_id]['name'])) {
			$this->file_group = "pictures";
			$image_file_name = $_FILES[$file_id]['tmp_name'];
			$image_info = getimagesize($image_file_name);		// $image_info['2'] = 1 {GIF}, 2 {JPG}, 3 {PNG}, 4 {SWF}, 5 {PSD}, 6 {BMP}, 7 {TIFF}, 8 {TIFF}, 9 {JPC}, 10 {JP2}, 11 {JPX}
			if ($image_info['2'] == 1) {
				$image_resource = imagecreatefromgif($image_file_name);
				$iii = imagecolorallocate($image_resource, 255, 255, 255);
				imagecolortransparent($image_resource, $iii);
			} elseif ($image_info['2'] == 2) {
				$image_resource = imagecreatefromjpeg($image_file_name);
			} else {
				$image_resource = imagecreatefrompng($image_file_name);
			}

			$picture_max_size['width']  = $this->width;
			$picture_max_size['height'] = $this->height;
			$picture_resource = $this->getResizedImageResource($image_resource, $picture_max_size);
			$second_picture_resource = false;
			if ($property_info) {
				$picture_max_size['width']  = $property_info['second_width'];
				$picture_max_size['height'] = $property_info['second_height'];
				$second_picture_resource = $this->getResizedImageResource($image_resource, $picture_max_size);
			}
			$this->_uploadPictureToFileSystem($file_id, $picture_resource, $second_picture_resource);
		}
	}

	function _uploadPictureToFileSystem($file_id, $picture_resource, $second_picture_resource = false)
	{
		$upload_file_directory = SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY');
		$file_basename = str_replace(array('%', ' '), '_', $_FILES[$file_id]['name']);
		$file_extension = strrchr($file_basename, ".");
		if (!empty($file_extension)) {
			$file_name_without_ext = substr($file_basename, 0, -strlen($file_extension));
		} else {
			$file_name_without_ext = $file_basename;
		}

		$saved_file_name = $file_name_without_ext . ".png";
		$file_name = $upload_file_directory . "/" . $this->file_group . "/" . $saved_file_name;
		$i = 0;

		while (file_exists($file_name)) {
			$saved_file_name = $file_name_without_ext . "_" . ++$i . ".png";
			$file_name = $upload_file_directory . "/" . $this->file_group . "/" . $saved_file_name;
		}

		if (@imagepng($picture_resource, $file_name)) {
			SJB_UploadPictureManager::deleteUploadedFileByID($this->uploaded_file_id);
			SJB_DB::query("INSERT INTO uploaded_files(id, file_name, file_group, saved_file_name, mime_type, creation_time)"
				." VALUES(?s, ?s, ?s, ?s, ?s, ?s)", $this->uploaded_file_id, $_FILES[$file_id]['name'], $this->file_group, $saved_file_name, $_FILES[$file_id]['type'], time());
		}
		if ($second_picture_resource) {
			$file_name = str_replace('.png', '', $file_name)."_thumb.png";
			if (@imagepng($second_picture_resource, $file_name)) {
				$saved_file_name = str_replace('.png', '', $saved_file_name)."_thumb.png";
				SJB_UploadPictureManager::deleteUploadedFileByID($this->uploaded_file_id."_thumb");
				SJB_DB::query("INSERT INTO uploaded_files(id, file_name, file_group, saved_file_name, mime_type, creation_time)"
					." VALUES(?s, ?s, ?s, ?s, ?s, ?s)", $this->uploaded_file_id."_thumb", $_FILES[$file_id]['name'], $this->file_group, $saved_file_name, $_FILES[$file_id]['type'], time());
			}
		}
	}

	function getResizedImageResource($image_resource, $image_max_size)
	{
		$image_width = imagesx($image_resource);
		$image_height = imagesy($image_resource);

		$imageMaxWidth = SJB_Array::get($image_max_size, 'width');
		$imageMaxHeight = SJB_Array::get($image_max_size, 'height');

		if (($imageMaxWidth && $image_width > $imageMaxWidth) || ($imageMaxHeight && $image_height > $imageMaxHeight)) {
			$k_w = $image_width / $imageMaxWidth;
			$k_h = $image_height / $imageMaxHeight;
			$k = max($k_w, $k_h);
			$picture_width = round($image_width / $k);
			$picture_height = round($image_height / $k);
		} else {
			$picture_width = $image_width;
			$picture_height = $image_height;
		}

		$resized_image_resource = imagecreatetruecolor($picture_width, $picture_height);
		$transparent = imagecolorallocatealpha( $resized_image_resource, 0, 0, 0, 127 );
		imagefill( $resized_image_resource, 0, 0, $transparent );
		imagecopyresampled($resized_image_resource, $image_resource, 0, 0, 0, 0, $picture_width, $picture_height, $image_width, $image_height);
		imagesavealpha($resized_image_resource,true);

		return $resized_image_resource;
	}

	public static function getUploadedPictureInfo($picture_id)
	{
		if (empty($picture_id)) {
			return null;
		}
		$picture_info = SJB_DB::query("SELECT * FROM uploaded_files WHERE id = ?s", $picture_id);
		return empty($picture_info) ? null : array_pop($picture_info);
	}

	public static function getUploadedFileLink($uploaded_file_id, $file_info = false, $noHost = false)
	{
		if ($file_info === false)
			$file_info = SJB_UploadFileManager::getUploadedFileInfo($uploaded_file_id);
		if (!empty($file_info)) {
			$upload_files_directory = SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY');
			$file_group = $file_info['file_group'];
			$saved_file_name = $file_info['saved_file_name'];
			$file_name = $upload_files_directory . "/" . $file_group . "/" . $saved_file_name;
			$site_url = SJB_System::getSystemSettings("SITE_URL");
			$link = $site_url . "/" . $file_name;
			return $link;
		}
		return null;
	}

	function getError()
	{
		return $this->error;
	}
}

