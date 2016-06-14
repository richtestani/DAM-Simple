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
$tags_model->deleteLinks($file->uid);

$files->delete($file->uid);
$filesystem->deleteDir($file->path);


?>