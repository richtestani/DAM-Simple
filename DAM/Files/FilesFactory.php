<?php

namespace DAM\Files;

abstract class FilesFactory {
	
	abstract protected function create();
	
	public function start()
	{
		$product = $this->create();
		return $product;
	}
	
}