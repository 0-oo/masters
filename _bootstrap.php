<?php
if (DEBUG_FLG) {
	ini_set('error_reporting', E_ALL | E_STRICT);
}

require_once('P3/functions.php');
require_once('_conf.php');


$con = new P3_Controller();

$con->debug(DEBUG_FLG);

$con->baseUrl(BASE_URL);

$con->template();

$db = $con->db(DB_NAME, DB_USER, DB_PASS, DB_OTHER, DB_CREATED_AT, DB_UPDATED_AT);
$db->charset();

if ($con->url(0)) {
	$action = $con->url(1);
	
	if (!$action) {
		$action = 'index';
	}
} else {
	$action = 'top';
}

Masters::setController($con, $rules, $tables);

$con->run($action);
