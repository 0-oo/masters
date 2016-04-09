<?php
$this->debug(false);

$params = $this->params();
$error = null;
$key = null;

try {
	foreach ($params as $k => $v) {
		Masters::checkValidColumn($k);
	}
	
	$db = $this->db();
	$key = $db->insert(Masters::getTable(), $params, null, true);
} catch (Exception $e) {
	error_log($e);
	$error = $e->getMessage();
}

$http = new P3_Http();
$http->json(array('error' => $error, 'key' => $key));
