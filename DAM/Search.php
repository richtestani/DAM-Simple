<?php

namespace DAM;

class Search {
	
	public function __construct($db, $config)
	{
		$this->db = $db;
		$this->config = $config;
	}
	
	public function getOne($field, $value)
	{
		$result = $this->db->getOne(['from'=>'files', 'select'=>$field, 'value'=>$value]);
		$args = [
			'select'=>'t.tag',
			'limit' => 100,
			'from'=>'tags t',
			'join' => ['table'=>'taglinks tl', 'on'=>'t.id=tl.tagid'],
			'where' => [
				['field'=>'tl.itemid', 'op'=>'=', 'value'=>$result['id']]
			]
		];
		$q = $this->db->getAll($args);
		$tagresult = $this->db->getResults($q);
		$tags = [];
		foreach($tagresult as $t) {
			$tags[] = $t['tag'];
		}

		$result['tags'] = implode(", ", $tags);
		return $result;
	}
	
	public function getRecent($limit, $order)
	{
		$args = [
			'select' => '*',
			'from' => 'files',
			'limit' => $limit,
			'order' => ['by'=>$order[0], 'dir'=>$order[1]]
		];
		$query = $this->db->getAll($args);
		$records = $this->db->getResults($query, false);
		$records = $this->parseFiles($records);
		return $records;
	}
	
	private function parseFiles($records)
	{
		$files = [];
		foreach($records as $file) {
			
			switch($file['filetype']) {
				
				case 'image':
				$f = new Files\ImageFactory();
				$f = $f->start();
				$f->configure($this->config);
				$f->setFileData($file);
				$files[] = $f;
				
				break;
				
			}
			
		}
		
		return $files;
	}
	
}	

?>