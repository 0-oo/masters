<?php
$this->debug(false);

$params = $this->params();
$error = null;

try {
	foreach ($params as $k => $v) {
		Masters::checkValidColumn($k);
	}
	
	$db = $this->db();
	$db->delete(Masters::getTable(), $params);
} catch (Exception $e) {
	error_log($e);
	$error = $e->getMessage();
}

$http = new P3_Http();
$http->json(array('error' => $error));
