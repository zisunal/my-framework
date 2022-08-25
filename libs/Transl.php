<?php
class Transl extends Common{
	public static $db_table = "transl";
	public static $db_table_fields = ["str_key","trans","created_at"];
	public $id;
	public $str_key;
	public $trans;
	public $created_at;
	public $updated_at;

	public static function translate($str, $lan){
		global $database;
		$str = $database->escape_string($str);
		$array = static::do_query("SELECT * FROM " . static::$db_table . " WHERE str_key = '$str' ");
		if (!empty($array)){
			$rtr = true;
			$ar = array_shift($array);
			$trs = explode("|||", $ar->trans);
			unset($trs[count($trs) - 1]);
			foreach($trs as $tr) {
				if(explode("->", $tr)[0] == $lan) {
					return explode("->", $tr)[1];
					$rtr = false;
					break;
				}
			}
			if($rtr) {
				foreach($trs as $tr) {
					if(explode("->", $tr)[0] == "en") {
						return explode("->", $tr)[1];
						break;
					}
				}
			}
		} else {
			return $str;
		}
	}

}
$transls = new Transl();