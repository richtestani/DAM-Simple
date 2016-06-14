<?php

namespace DAM\Files;

interface FilesInterface {
	public function supportsMime($mime);
	public function write($src, $dest);
	public function read($src);
	public function configure($config);
}
?>