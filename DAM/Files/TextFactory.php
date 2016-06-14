<?php
	
namespace DAM\Files;

class TextFactory extends FilesFactory {
	
	public function create()
	{
		$file = new Text();
		return $file;
	}
}
?>