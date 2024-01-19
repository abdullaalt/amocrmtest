<?php

class DB{
	
	private $name = 'fb7960l1_test3';
	private $user = 'fb7960l1_test3';
	private $password = 'y6S0v&uv';
	private $host = 'localhost';
	private $post = 3306;
	
	private $desc;
	
	public function __construct(){
		$this->desc = new mysqli($this->host, $this->user, $this->password, $this->name, $this->post);
		$this->desc->set_charset('utf8');
	}
	
	public function insert($table, $data){
		
		$keys = [];
		$values = [];
		
		foreach ($data as $key=>$val){
			$keys[] = $key;
			$values[] = $val;
		}
		
		$s = $this->desc->prepare('INSERT INTO '.$table.' ('.implode(',', $keys).') VALUES('.trim(str_repeat('?,', count($keys)), ',').')');
		$s->execute($values);
		
		return $s->insert_id;
		
	}
	
	public function first($table, $field, $value){
		
		$row = $this->
					desc->
					execute_query('SELECT * FROM '.$table.' WHERE '.$field.' = ?', [$value])->
					fetch_assoc();
		
		return $row;
		
	}
	
	public function get($table, $field, $value){
		
		$row = $this->
					desc->
					execute_query('SELECT * FROM '.$table.' WHERE '.$field.' = ?', [$value])->
					fetch_all(MYSQLI_ASSOC);
		
		return $row;
		
	}
	
}