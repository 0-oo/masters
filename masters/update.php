<?php
$this->debug(false);

$set = array();
$where = null;
$error = null;

try {
	foreach ($this->params() as $k => $v) {
		Masters::checkValidColumn($k);
		
		if ($where) {
			$set[$k] = $v;
		} else {	// 主キー
			$where = array($k => $v);
		}
	}
	
	$db = $this->db();
	$db->update(Masters::getTable(), $set, array_merge($where, Masters::getProp('where', array())));
} catch (Exception $e) {
	error_log($e);
	$error = $e->getMessage();
}

$http = new P3_Http();
$http->json(array('error' => $error));
