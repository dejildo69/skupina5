<?php
class Database {
	private static $host 						= '127.0.0.1';
	private static $user 						= 'sloeddy';
	private static $pass 						= 'D4v1D123';
	private static $db 							= 'david_aod';
	private static $character_set 				= 'UTF8';
	private static $names 						= 'UTF8';
	private static $prefix 						= 'aod_';
	
	/* Server location variables */
	private static $base_folder = '\\www\\Praktikum\\';
	
	/* Dynamic variables */
	protected static $connection 		= '';
	protected static $debug_mode		= false;
	
	public function __construct($debug = false) 
	{
		/* Establish connection */
		self::$connection = mysql_connect(self::$host, self::$user, self::$pass);
		self::$debug_mode = $debug;
		mysql_select_db(self::$db);
		mysql_query("SET NAMES ".self::$names);
		mysql_set_charset(self::$character_set);
	}	
	public function load($className) 
	{
		require_once($className.".class.php");
		return new $className;
	}
	public function select($target, $table, $conditions = '1', $getArray = false, $limit = false)
	{
		$query = "SELECT ";
		if(is_array($target)) 
		{ 
			$query .= "(";
			foreach($target as $key => $value) { $query .= "`".$value."`, "; }
			$query = substr($query,0,-2).") ";
		}
		else
		{
			$query .= $target." ";
		}
		$query .= "FROM `".self::$prefix.$table."` ";		
		$query .= "WHERE ";
		if(is_array($conditions)) 
		{ 
			foreach($conditions as $key => $value) { $query .= "`".$key."` = '".$value."' AND "; }
			$query = substr($query,0,-4)." ";
		} else { $query .= $conditions; }
		
		if($limit !== false) { $query .= " LIMIT ".$limit; }
		$result = mysql_query($query);
		if(self::$debug_mode) { echo("Select(".$target.", ".$table.", ".$conditions.") querying: ".$query."<br/>"); echo(mysql_error()); }
		if(!$result) { return false; }
		$result_set = array();
		while($item = mysql_fetch_assoc($result)) { array_push($result_set, $item); }
		if(count($result_set) == 1 AND $getArray == false) { return $result_set[0]; } 
		return $result_set;
	}
	
	public function update($table, $data, $conditions)
	{
		$query = "UPDATE `".self::$prefix.$table."` SET ";
		if(is_array($data)) 
		{ 
			foreach($data as $key => $value) { $query .= "`".$key."` = '".$value."', "; }
			$query = substr($query,0,-2)." ";
		} 
		else { $query .= $data." "; } 
		
		$query .= "WHERE 1 AND ";
		if(is_array($conditions)) 
		{ 
			foreach($conditions as $key => $value) { $query .= "`".$key."` = '".$value."' AND "; }
			$query = substr($query,0,-4)." ";
		}
		else { $query .= $conditions." "; }		
		$result = mysql_query($query);
		if(self::$debug_mode) { echo("Update(".$table.", ".$data.", ".$conditions.") querying: ".$query."<br/>");  echo(mysql_error()); }
		if(!$result) { return false; } else { return true; }
	}
	
	public function insert($table, $data)
	{
		if(!is_array($data)) { return false; }
		$query = "INSERT INTO `".self::$prefix.$table."` (";
		foreach($data as $key => $value) { $query .= "`".$key."`, "; }
		$query = substr($query,0,-2).") VALUES (";
		foreach($data as $key => $value) { $query .= "'".$value."', "; }
		$query = substr($query,0,-2).")";
	
		$result = mysql_query($query);
		if(self::$debug_mode) { echo("Insert(".$table.", ".$data.") querying: ".$query."<br/>"); echo(mysql_error()); }
		if($result) { return mysql_insert_id(); } else { return false; }
	}
}
?>