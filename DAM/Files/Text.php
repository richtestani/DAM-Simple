<?php
	
namespace DAM\Files;

class Text implements FilesInterface {
	
	protected $types;
	protected $mime;
	
	public function __construct()
	{
		$this->types = [
			'txt',
			'md',
			'doc',
			'docx'
		];
	}
	
	public function create()
	{
		$file = new Image();
		return $file;
	}
	
	public function supportsMime($mime)
	{
		return (in_array($mime, $this->types)) ? true : false;
	}
	
	public function write($src, $dest)
	{
		
	}
	
	public function read($src)
	{
		
	}
}
?>