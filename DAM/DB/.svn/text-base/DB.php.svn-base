<?php

namespace RichApp\Core\DB;
use RedBeanPHP;

class DB {
	
	static $connection = false;
	
	public function __construct($config)
	{
		extract($config);
		$this->connect($dsn, $username, $password);
	}
	
	public function connect($dsn, $username, $password)
	{
		if(!self::$connection)
		{
			\R::setup($dsn, $username, $password, true);
		}
		
		if(\R::testConnection())
		{
			self::$connection = true;
		}
	}
	
	public function connection()
	{
		return self::$connection;
	}
	
	public function find($sql, $bindings = array(), $convert_table = null)
	{

		$result = \R::getAll($sql, $bindings);
		
		if(!is_null($convert_table))
		{
			$result = \R::convertToBeans( $convert_table, $result );
		}

		return $result;
	}
	
	public function getOne($table, $field, $value)
	{
		$result = \R::findOne($table, $field, $value);
		return $result;
	}
	
	public function exec($query, $bindings = array())
	{
		\R::exec($query, $bindings);
	}
	public function create($table, $data)
	{
		$record = $this->dispense($table);
		$id = $this->store( $this->buildRecordFromArray($record, $data) );
		return $id;		
	}
	public function update($table, $data, $id)
	{
		$record = $this->load($table, $id);
		$record = $this->buildRecordFromArray($record, $data);
		$this->store( $record );
	}
	
	public function count($table, $where, $binding)
	{
		return \R::count($table, $where, $binding);
	}
	
	private function buildRecordFromArray($record, $array)
	{
		
		foreach($array as $k => $v)
		{
			$record->{$k} = $v;
		}
		
		return $record;
	}
	
	private function dispense($table)
	{
		return \R::dispense($table);
	}
	
	private function load($table, $id)
	{
		return \R::load($table, $id);
	}
	
	private function store($data)
	{
		return \R::store($data);
	}
}

?>