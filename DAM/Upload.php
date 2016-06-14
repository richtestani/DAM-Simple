<?php
	
namespace DAM;

class Upload {
	
	protected $mime;
	protected $path;
	protected $destination;
	protected $success;
	
	public function setUploadData($data)
	{
		foreach($data as $k => $v)
		{
			$this->file->set($k, $v);
		}
	}
	
	public function setDestination($path)
	{
		$filename = $this->file->getFilename();
		$this->destination = $path;
	}
	
	public function uploadNow($src, $file = null)
	{
		if(!is_null($file)) {
			$dest = $file->getPath() . '/'. $file->getFilename();
			$this->success = $file->write($src, $dest);
		} else {
			if( move_uploaded_file($src, $destination) ) {
				$this->success = true;
			}
		}
	}
	
	public function success()
	{
		return $this->success;
	}
	
}
?>