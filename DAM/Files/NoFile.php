<?php
	
namespace DAM\Files;

class NoFile extends File implements FilesInterface {
	
	protected $types;
	protected $mime;
	
	public function __construct()
	{
		$this->types = [];
	}
	
	public function supportsMime($mime)
	{
		return (in_array($mime, $this->types)) ? true : false;
	}
}
?>