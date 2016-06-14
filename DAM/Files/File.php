<?php
	
namespace DAM\Files;



abstract class File {
	
	protected $filename;
	protected $uid;
	protected $timestamp;
	protected $types;
	
	public function __construct()
	{
		$this->types = [];
	}
	
	public function addType($type)
	{
		$this->types[] = $type;
	}
	
	public function setTimestamp()
	{
		$date = new \DateTime();
		$this->timestamp = $date;
	}
	
	public function setFilename($name)
	{
		$this->filename = $name;
	}
	
	public function getFilename()
	{
		return $this->filename;
	}
	
	public function genUid()
	{
		$this->uid = uniqid();
	}
	
}
?>