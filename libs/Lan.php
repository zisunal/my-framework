<?php
class Lan extends Common{
	public static $db_table = "lan";
	public static $db_table_fields = ["code","name","created_at"];
	public $id;
	public $code;
	public $name;
	public $created_at;
	public $updated_at;

	public static function _full($code){
		$array = static::do_query("SELECT * FROM " . static::$db_table . " WHERE code = '$code' ");
		return !empty($array) ? array_shift($array)->name : false;
	}

	public static function _abbr($name){
		$array = static::do_query("SELECT * FROM " . static::$db_table . " WHERE name = '$name' ");
		return !empty($array) ? array_shift($array)->code : false;
	}

}
$lans = new Lan();