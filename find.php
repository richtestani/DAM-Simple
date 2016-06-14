<?php
date_default_timezone_set('America/New_York');
require_once './vendor/autoload.php';
$config = require_once('config.php');
$config['db']['dsn'] = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['database'];
$fileconfig = array_merge($config['files'], $config['paths'], $config['types']);

$search = new \DAM\Search(new DAM\Query($config['db']), $fileconfig);

$file = $search->getOne('uid', $_POST['uid']);
$result = [];
foreach($file as $k => $v) {
	$result[$k] = $v;
}
echo json_encode($result, JSON_FORCE_OBJECT);

?>