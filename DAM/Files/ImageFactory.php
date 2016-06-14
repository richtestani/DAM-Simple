<?php
	
namespace DAM\Files;

class ImageFactory extends FilesFactory {
	
	public function create()
	{
		$file = new Image();
		return $file;
	}
}
?>