<?php
date_default_timezone_set('America/New_York');
require_once './vendor/autoload.php';

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$config = require_once('config.php');
$config['db']['dsn'] = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['database'];
$fileconfig = array_merge($config['files'], $config['paths'], $config['types']);

$search = new \DAM\Search(new DAM\Query($config['db']), $fileconfig);
$files = new \DAM\Models\Files(new DAM\Query($config['db']));
$tags_model = new \DAM\Models\Tags(new DAM\Query($config['db']));

$adapter = new Local(__DIR__);
$filesystem = new Filesystem($adapter);

$result = [];

$file = $search->getOne('uid', $_POST['uid']);
foreach($_POST as $k => $v) {
	$result[$k] = $v;
}
unset($result['tags']);
$tags = $_POST['tags'];

list($basefile, $basefileext) = explode(".", $result['filename']);

$list = $filesystem->listContents($file->path);
$basename = $list[0]['basename'];
$path = $list[0]['path'];
$ext = $list[0]['extension'];
$fileext = (empty($basefileext)) ? $ext : $basefileext;
$result['filename'] = $basefile.'.'.$fileext;
$new = $file->path.'/'.$result['filename'];
$filesystem->rename($path, $new);

$files->update($result, $file->id);
$tags_model->createAll($tags, $file->id);

//echo json_encode($file, JSON_FORCE_OBJECT);

?>