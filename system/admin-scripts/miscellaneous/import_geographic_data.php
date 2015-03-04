<?php

class SJB_Admin_Miscellaneous_ImportGeographicData extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_zipcode_database');
		return parent::isAccessible();
	}

	public function execute()
	{
		ini_set('max_execution_time', 0);
		$encodingFromCharset = SJB_Request::getVar('encodingFromCharset', 'UTF-8');
		$preview = SJB_Request::getVar('preview', false);
		$importedDataForPreview = array();
		$template_processor = SJB_System::getTemplateProcessor();
		$errors = null;
		$start_line        = SJB_Request::getVar('start_line', null);
		$name_column       = SJB_Request::getVar('name_column', null);
		$longitude_column  = SJB_Request::getVar('longitude_column', null);
		$latitude_column   = SJB_Request::getVar('latitude_column', null);
		$city_column       = SJB_Request::getVar('city_column', null);
		$state_column      = SJB_Request::getVar('state_column', null);
		$state_code_column = SJB_Request::getVar('state_code_column', null);
		$country_sid       = SJB_Request::getVar('country_sid', null);
		$file_format       = SJB_Request::getVar('file_format', null);
		$fields_delimiter  = SJB_Request::getVar('fields_delimiter', null);

		$imported_file_config['start_line'] = $start_line;
		$imported_file_config['name_column'] = $name_column;
		$imported_file_config['longitude_column'] = $longitude_column;
		$imported_file_config['latitude_column'] = $latitude_column;
		$imported_file_config['city_column'] = $city_column;
		$imported_file_config['state_column'] = $state_column;
		$imported_file_config['state_code_column'] = $state_code_column;
		$imported_file_config['file_format'] = $file_format;
		$imported_file_config['fields_delimiter'] = $fields_delimiter;

		$imported_location_count = null;

		if (isset($_FILES['imported_geo_file']) && !$_FILES['imported_geo_file']['error']) {

			$fileInfo = $_FILES['imported_geo_file'];
			$fileFormats  = array('csv', 'xls', 'xlsx');
			$pathInfo = pathinfo($fileInfo['name']);
			$fileExtension = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : '';
			if (!in_array(strtolower($fileExtension), $fileFormats)) {
				$errors['File'] = 'WRONG_FORMAT';
			}

			if (empty($_FILES['imported_geo_file']['name'])) {
				$errors['File'] = 'EMPTY_VALUE';
			}

			if (empty($start_line)) {
				$errors['Start Line'] = 'EMPTY_VALUE';
			} elseif (!is_numeric($start_line) || !is_int($start_line + 0)) {
				$errors['Start Line'] = 'NOT_INT_VALUE';
			}

			if (empty($name_column)) {
				$errors['Name Column'] = 'EMPTY_VALUE';
			} elseif (!is_numeric($name_column) || !is_int($name_column + 0)) {
				$errors['Name Column'] = 'NOT_INT_VALUE';
			}

			if (empty($longitude_column)) {
				$errors['Longitude Column'] = 'EMPTY_VALUE';
			} elseif (!is_numeric($longitude_column) || !is_int($longitude_column + 0)) {
				$errors['Longitude Column'] = 'NOT_INT_VALUE';
			}

			if (empty($latitude_column)) {
				$errors['Latitude Column'] = 'EMPTY_VALUE';
			}
			elseif (!is_numeric($latitude_column) || !is_int($latitude_column + 0)) {
				$errors['Latitude Column'] = 'NOT_INT_VALUE';
			}

			if (empty($country_sid)) {
				$errors['Country'] = 'EMPTY_VALUE';
			}
			if (!SJB_ImportFile::isValidFileExtensionByFormat($file_format, $_FILES['imported_geo_file'])) {
				$errors['File'] = 'DO_NOT_MATCH_SELECTED_FILE_FORMAT';
			}
			if (!SJB_ImportFile::isValidFileCharset($_FILES['imported_geo_file'], $encodingFromCharset)) {
				$errors['Charset'] = 'CHARSET_INCORRECT';
			}

			if (is_null($errors)) {
				set_time_limit(0);
				$file_info = SJB_Array::get($_FILES, 'imported_geo_file');
				if (!strcasecmp($file_format, 'excel')) {
					$import_file = new SJB_ImportFileXLS($file_info);
				} else {
					if ($fields_delimiter == 'semicolon') {
						$fields_delimiter = ';';
					} elseif ($fields_delimiter == 'tab') {
						$fields_delimiter = "\t";
					} else {
						$fields_delimiter = ',';
					}
					$import_file = new SJB_ImportFileCSV($file_info, $fields_delimiter);
				}
				$import_file->parse($encodingFromCharset);
				$imported_data = $import_file->getData();
				$imported_location_count = 0;
				$countryInfo = SJB_CountriesManager::getCountryInfoBySID($country_sid);
				foreach($imported_data as $key => $importedColumn) {
					if (empty($importedColumn[$name_column - 1]) || empty($importedColumn[$longitude_column - 1]) || empty($importedColumn[$latitude_column - 1]) || ($start_line > $key)) {
						continue;
					}

					$name = $importedColumn[$name_column - 1];
					$longitude = $importedColumn[$longitude_column - 1];
					$latitude = $importedColumn[$latitude_column - 1];
					$city = isset($importedColumn[$city_column - 1])?$importedColumn[$city_column - 1]:null;
					$state = isset($importedColumn[$state_column - 1])?$importedColumn[$state_column - 1]:null;
					$state_code = isset($importedColumn[$state_code_column - 1])?$importedColumn[$state_code_column - 1]:null;
					if ($preview) {
						if (count($importedDataForPreview) >= 10) {
							break;
						}
						$importedDataForPreview[] = array(
							'name' => $name,
							'longitude' => $longitude,
							'latitude' => $latitude,
							'city' => $city,
							'state' => $state,
							'stateCode' => $state_code,
							'country' => $countryInfo['country_name']
						);
					} else {
						$imported_location_count += SJB_LocationManager::addLocation($name, $longitude, $latitude, $city, $state, $state_code, $country_sid, $countryInfo);
					}
				}
			}
		}
		elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$errorSid = isset($_FILES['imported_geo_file']['error'])? $_FILES['imported_geo_file']['error']: 0;
			$errors['File'] = SJB_UploadFileManager::getErrorId($errorSid);
		}

		$countries = SJB_CountriesManager::getAllCountriesCodesAndNames();
		$template_processor->assign("charSets", SJB_HelperFunctions::getCharSets());
		$template_processor->assign("importedGeographicData", $importedDataForPreview);
		$template_processor->assign("countries", $countries);
		$template_processor->assign("country_sid", $country_sid);
		$template_processor->assign("errors", $errors);
		$template_processor->assign("imported_location_count", $imported_location_count);
		$template_processor->assign("imported_file_config", $imported_file_config);
		$template_processor->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());

		if ($preview) {
			$template_processor->display("import_geographic_data_preview.tpl");
		} else {
			$template_processor->display("import_geographic_data_form.tpl");
		}
	}
}
