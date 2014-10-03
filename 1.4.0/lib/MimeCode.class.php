<?php
class MimeCode {
	
	//http://stackoverflow.com/questions/6595183/docx-file-type-in-php-finfo-file-is-application-zip
	private function getOpenXMLMimeType($file_name){
		$ext = pathinfo($file_name,PATHINFO_EXTENSION);
		$openXMLExtension = array(
				'xlsx' => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
				'xltx' => "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
				'potx' =>  "application/vnd.openxmlformats-officedocument.presentationml.template",
				'ppsx' =>  "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
				'pptx'   =>  "application/vnd.openxmlformats-officedocument.presentationml.presentation",
				'sldx'   =>  "application/vnd.openxmlformats-officedocument.presentationml.slide",
				'docx'   =>  "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
				'dotx'   =>  "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
				'xlam'   =>  "application/vnd.ms-excel.addin.macroEnabled.12",
				'xlsb'   =>  "application/vnd.ms-excel.sheet.binary.macroEnabled.12");
		if (isset($openXMLExtension[$ext])){
			return $openXMLExtension[$ext];
		}
		return false;
	}
	
	public function getContentType($file_path){
		if (! file_exists($file_path)){
			return;
		}
		$fileInfo = new finfo();
		$result = $fileInfo->file($file_path,FILEINFO_MIME_TYPE);
	
		if ($result == 'application/zip'){
			$file_name = basename($file_path);
			$result = $this->getOpenXMLMimeType($file_name)?:'application/zip';
		}
		return $result;
	}
}