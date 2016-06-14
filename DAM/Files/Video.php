<?php
	
namespace DAM\Files;

class Video extends File implements FilesInterface {
	
	protected $types;
	protected $mime;
	
	public function __construct()
	{
		$this->types = [
			'flv',
			'mp4',
			'mov'
		];
	}
	
	public function setMime($mime)
	{
		
	}
}
?>