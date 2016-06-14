<?php
	
namespace DAM\Files;

class Image implements FilesInterface {
	
	protected $types;
	protected $mime;
	protected $ext;
	protected $size;
	protected $filename;
	protected $original;
	protected $prefix;
	protected $suffix;
	protected $separator;
	protected $path;
	protected $is_random = false;
	protected $datetime;
	protected $public_basepath = '/files/images/';
	protected $filetype;
	protected $caption;
	
	public function __construct()
	{
		$this->types = [
			'gif',
			'png',
			'jpg',
			'jpeg',
			'bmp',
			'tif',
			'psd',
			'webp'
		];
	}
	
	public function configure($config)
	{
		$this->config = $config;
		$date = new \DateTime();
		$this->datetime = $date->format('Y-m-d');
		$this->separator = '-';
		$this->uid = uniqid();
	}

	
	public function supportsMime($mime)
	{
		return (in_array($mime, $this->types)) ? true : false;
	}
	
	public function getPath()
	{
		return $this->path;
	}
	
	public function getFilename()
	{
		return $this->filename;
	}
	
	public function getUid()
	{
		return $this->uid;
	}
	
	public function setFileData($data)
	{
		foreach ($data as $k => $v) {
			if (property_exists($this, $k)) {
				$this->{$k} = $v;
			}
		}

		list($filetype, $ext) = explode("/", $this->mime);
		
		$this->filetype = $filetype;
		$this->ext = $this->config[$this->mime];
		
		
		if ($this->config['ramdomize']) {
			$this->is_random = true;
			if( empty($this->filename) ) {
				$this->filename = $this->genRandomFilename() . $this->ext;
			}
			else
			{
				list($basefile, $basefileext) = explode(".", $this->filename);
				$fileext = (empty($basefileext)) ? $this->config[$this->mime] : '.'.$basefileext;
				$this->filename = $basefile . $fileext;
			}
			
		} else {
			$this->filename = $this->original;
		}
		
		if(empty($this->path)) {
			$this->parsePath($this->config['template']);
		}
	}
	
	
	public function write($src, $dest)
	{
		if (!file_exists( $_SERVER['DOCUMENT_ROOT'] . $this->path )) {
			mkdir($_SERVER['DOCUMENT_ROOT'] . $this->path, 0777, true);
		}

		if( move_uploaded_file($src, $_SERVER['DOCUMENT_ROOT'].$dest) ) {
			return true;
		} else {
			return false;
		}
	}
	
	public function read($src)
	{
		return file_get_contents($src);
	}
	
	public function getData()
	{
		$data = [];
		$data['filename'] = $this->filename;
		$data['path'] = $this->path;
		$data['original'] = $this->original;
		$data['ext'] = $this->ext;
		$data['mime'] = $this->mime;
		$data['uid'] = $this->uid;
		$data['filetype'] = $this->filetype;
		$data['size'] = $this->size;
		$data['created'] = date('Y-m-d H:i:s');
		
		if($this->ext == '.jpg' || $this->ext == '.tif') {
			$data['exif'] = serialize($this->getExif());
		}

		return $data;
	}
	
	public function getHTML($decode = true)
	{
		return htmlspecialchars('<img src="'.$this->path.'/'.$this->filename.'" class="dam image rendered" />');
	}
	
	public function caption($wrap = '')
	{
		return $this->caption;
	}
	
	private function parsePath($path)
	{
		
		//variables
		$vars = [
			'uid',
			'datetime',
			'filename'
		];
		
		$temp = '';
		foreach ($vars as $k) {
			$path = str_replace('{'.$k.'}', $this->{$k}, $path);
		}

		$this->path = $this->public_basepath.$path;
	}
	
	private function isImage($src)
	{
		$file = file_get_contents($src);
	}
	
	private function genRandomFilename()
	{
		$random = uniqid();
		return $random;
	}
	
	private function getExif()
	{
		$data = exif_read_data($_SERVER['DOCUMENT_ROOT'] . $this->path . '/' . $this->filename);
		return $data;
	}
}
?>