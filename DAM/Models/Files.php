<?php
	
namespace DAM\Models;

class Files {
	
	public function __construct($q)
	{
		$this->db = $q;
	}
	
	public function create($data) 
	{
		return $this->db->create('files', $data);
	}
	
	public function update($data, $id)
	{
		$this->db->update('files', $data, $id);
	}
	
	public function delete($id)
	{
		$where = [
			'where' => [
				['field'=>'uid', 'op'=>'=', 'value'=>$id]
			]
		];
		$this->db->delete('files', $where);
	}
	
	
}
?>