<?php
	
namespace DAM\Models;

class Tags {
	
	public function __construct($q)
	{
		$this->db = $q;
	}
	
	public function createAll($data, $id) {
		
		$t = explode(",", $data);

		$this->deleteLinks($id);
		
		foreach($t as $v) {
			$found = $this->db->getOne(['from'=>'tags', 'select'=>'slug', 'value'=>$this->slug($v)]);
			if(empty($found)) {
				$tag = ['tag'=>trim($v), 'slug'=>$this->slug($v)];
				$tid = $this->db->create('tags', $tag);
				$link = ['tagid'=>$tid, 'itemid'=>$id];
				$this->db->create('taglinks', $link);
			} else {
				$link = ['tagid'=>$found['id'], 'itemid'=>$id];
				$this->db->create('taglinks', $link);
			}
		}
		
		return true;
	}
	
	public function deleteLinks($itemid)
	{
		$where = [
			['field'=>'itemid', 'op'=>'=', 'value'=>$itemid]
		];
		$this->db->deleteWhere('taglinks', $where);
	}
	
	private function slug($string)
	{
        $remove = array('!', '@', '$', '%', '?', '&', '*', '(', ')', '=', '{', '}', '[', ']', '|', '/', '<', '>', ',', ':', ';', '"');
        $replace = array('-', '.', '_', '+', '\'', ' ');
		$delimiter = '-';
    
        $string = str_replace($remove, '', trim($string));
        $string = trim(strtolower(str_replace($replace, $delimiter, $string)));
    
        return $string;
	}
	
	
	
}
?>