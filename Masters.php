<?php
class Masters extends P3_Abstract {
	private static $_con;
	private static $_rules;
	private static $_table;
	private static $_prop;
	private static $_columns = array();
	
	public static function setController(P3_Controller $con, array $rules, array $tables) {
		self::$_con = $con;
		self::$_rules = $rules;
		self::$_table = self::$_con->url(0);
		self::$_prop = arrayValue(self::$_table, $tables);
		
		if (self::$_table) {
			if (!is_array(self::$_prop)) {
				throw new Exception('テーブル名が一致しませんでした');
			}
			
			foreach (self::getProp('columns', array()) as $k => $v) {
				if (is_int($k)) {
					self::$_columns[] = $v;
				} else {
					self::$_columns[] = $k;
				}
			}
		}
	}
	
	public static function getTable() {
		return self::$_table;
	}
	
	public static function getColumns() {
		return self::$_columns;
	}
	
	public static function getProp($key, $default = null) {
		return arrayValue($key, self::$_prop, arrayValue($key, self::$_rules, $default));
	}
	
	public static function checkValidColumn($name) {
		if (!preg_match('/^[_0-9a-z]+\z/iu', $name)) {
			throw new Exception("列名が不正です: $name");
		} else if (self::$_columns && !in_array($name, self::$_columns)) {
			throw new Exception("対象外の列です: $name");
		}
	}
}
