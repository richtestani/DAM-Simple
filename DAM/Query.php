<?php
	
namespace DAM;
use DAM\DB;

require_once $_SERVER['DOCUMENT_ROOT'].'/DAM/DB/RedBean/rb.php';

class Query {
	
	public $query;
	protected $select = null;
	protected $from = null;
	protected $where = null;
	protected $group = null;
	protected $join = null;
	protected $order = null;
	protected $limit = null;
	protected $offset = 0;
	protected $binding = array();
	protected $db;
	static $connection = false;
	protected $table_name = null;
	static protected $default_limit;
	
	public function __construct($config)
	{
		$this->db = new DB\DB($config);
	}
	
	public function connected()
	{
		return $this->db->connection();
	}
	
	public function setDefaultLimit($limit)
	{
		self::$default_limit = $limit;
	}
	
	public function select($select)
	{
		$string = '';
		if(is_array($select))
		{
			if(!in_array('id', $select))
			{
				$select[] = 'id';
			}
			$select = implode(',', $select);
		}
		$string = "SELECT ".$select;
		
		$this->select = $string;
		return $this;
	}
	
	public function from($from)
	{
		$this->from = " FROM ".$from;
		$this->table_name = $from;
		return $this;
	}
	
	public function where($where)
	{
		
		if(is_null($where))
		{
			return $this;
		}
		$string = '';
		$values = array();
		if(!empty($where))
		{
			$string .= " WHERE ";
		}
		
		if(!is_array($where))
		{
			return $this;
		}
		
		$counter = 1;
		foreach($where as $w)
		{
			$op = (array_key_exists('op', $w)) ? $w['op'] : '';
			$andor = (array_key_exists('andor', $w)) ? " ".$w['andor']." " : '';
			$string .= $andor.$w['field'].' ';
			if($op == 'IN')
			{
				$string .= $op." (".$w['value'].")";
			}
			else
			{
				$string .= $op.' ? ';
				$values[] = $w['value'];
			}
			
			$counter++;
		}

		$this->where = $string;
		$this->binding = $values;
		
		return $this;
	}
	
	public function group($group)
	{
		if(empty($group))
		{
			return $this;
		}
		$string = " GROUP BY ".$group;
		$this->group = $string;
		return $this;
	}
	
	public function order($order)
	{
		$string = '';
		
		if(array_key_exists('by', $order))
		{
			$order_by = " ORDER BY ".$order['by'];
			$order_dir = (array_key_exists('dir', $order)) ? ' '.$order['dir'] : '';
			
			$string = $order_by.$order_dir;
		}
		
		$this->order = $string;
		return $this;
	}
	
	public function limit($limit=null)
	{
		$limit = (is_null($limit)) ? self::$default_limit : $limit;
		$offset = $this->offset * $limit;
		$string = " LIMIT $offset, $limit";
		$this->limit = $string;
		
		return $this;
	}
	
	public function join($join)
	{
		if(is_null($join))
		{
			return $this;
		}
		$string = '';
		//loop thorugh multiple arrays
		if(array_key_exists(0, $join))
		{
			foreach($join as $j)
			{
				if(!array_key_exists('table', $j))
				{
					$this->join = '';
					return $this;
				}

				$type = (array_key_exists('type', $j)) ? $j['type']." JOIN" : " LEFT JOIN";
				$on = " ON ".$j['on'];
				$table = " ".$j['table'];
				$string .= $type.$table.$on;
			}
		}
		else
		{
			extract($join);
			
			if(!isset($table))
			{
				$this->join = '';
				return $this;
			}
			
			$type = (!isset($type)) ? " LEFT JOIN " : " ".strtoupper($type)." JOIN ";
			$on = " ON ".$on;
		
			$table = " $table";
			$string .= $type.$table.$on;
		}
		
		$this->join = $string;
		return $this;
	}
	
	public function getAll($args)
	{
		extract($args);

		$select = (isset($select)) ? $select : '*';
		$join = (isset($join)) ? $join : $this->join;
		$where = (isset($where)) ? $where : $this->where;
		$group = (isset($group)) ? $group : $this->group;
		$order = (isset($order)) ? $order : array();
		$limit = (isset($limit)) ? $limit : null;
		$this->offset = (isset($offset) && $offset > 0) ? $offset-1 : $this->offset;
		
		$query = $this->select($select)
			->from($from)
			     ->order($order)
				 ->limit($limit)
				 ->join($join)
				 ->where($where)
				 ->group($group)
				 ->buildQuery();
		
		$this->resetQuery();
		return $query;

	}
	
	public function getOne($args)
	{
		extract($args);
		$this->from($from);
		$result = $this->db->getOne($from, $select.' = ?', [$value]);
		return $result;
	}
	
	public function getResults($query, $convert = true)
	{
		if(!$convert)
		{
			$this->table_name = null;
		}
		
		$result = $this->db->find($query, $this->binding, $this->table_name);

		return $result;
	}
	
	public function getBindings()
	{
		return $this->binding;
	}
	
	public function deleteWhere($table, $args)
	{
		if(array_key_exists('where', $args))
		{
			$this->where($args['where']);
		}
		
		$sql = "DELETE FROM $table".$this->where;
		
		$this->db->exec($sql, $this->binding);
	}
	
	private function buildQuery()
	{
		$query = '';
		
		$this->query = '';
		
		$this->query .= (!is_null($this->select)) ? $this->select : "";
		
		if(is_null($this->from))
		{
			$this->error = 'A table was not provided';
		}
		else
		{
			$this->query .= $this->from;
		}
		
		if(!is_null($this->join))
		{
			$this->query .= $this->join;
		}
		
		if(!is_null($this->where))
		{
			$this->query .= $this->where;
		}
		
		if(!is_null($this->group))
		{
			$this->query .= $this->group;
		}
		
		$this->query .= (!is_null($this->order)) ? $this->order : "";
		
		$this->query .= $this->limit;
		
		return $this->query;
		
	}
	
	public function update($table, $data, $id)
	{
		return $this->db->update($table, $data, $id);		
	}
	
	public function create($table, $data)
	{
		$id = $this->db->create($table, $data);
		return $id;
	}
	
	public function delete($table, $data)
	{
		if(array_key_exists('where', $data))
		{
			$this->where($data['where']);
		}
		
		$sql = "DELETE FROM $table".$this->where;
		$this->db->exec($sql, $this->binding);
	}
	
	public function count($table, $args=array())
	{
		extract($args);
		
		if(isset($where))
		{
			$this->where($where);
		}
		else
		{
			$where = '';
		}

		return $this->db->count($table, $this->where, $this->binding);
	}
	
	public function exec($sql)
	{
		$this->db->exec($sql);
	}
	
	public function query($sql, $bindings=array())
	{
		$records = $this->db->find($sql, $bindings);
		return $records;
	}
	
	private function resetQuery()
	{
		$this->query = '';
		$this->select = null;
		$this->from = null;
		$this->where = null;
		$this->group = null;
		$this->join = null;
		$this->order = null;
		$this->limit = null;
		$this->table_name = null;
	}
	
}
	
?>