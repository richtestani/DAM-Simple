<?php
date_default_timezone_set('America/New_York');
require_once('vendor/autoload.php');
$config = require_once('config.php');
$config['db']['dsn'] = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['database'];
$db = new \DAM\Query($config['db']);

if(isset($_FILES)) {
	
	list($type, $ext) = explode("/", $_POST['mime']);
	
	
	switch($type) {
		
		case 'image':
		$file = new \DAM\Files\ImageFactory();
		
		break;
		
		case 'text':
		$file = new \DAM\Files\TextFactory();
		
		break;
		
		case 'video':
		$file = new \DAM\Files\VideoFactory();
		
		break;
		
		default:
		$file = new \DAM\Files\NoFile();
	}
	
	$file = $file->start();
	$fileconfig = array_merge($config['files'], $config['paths'], $config['types']);
	
	$file->configure($fileconfig);
	$file->setFileData($_POST);
	$paths = $config['paths'];
	
	$upload = new \DAM\Upload();

	$upload->uploadNow($_FILES['image']['tmp_name'], $file);
	$db = new \DAM\Query($config['db']);
	if ($upload->success()) {
		$data = $file->getData();
		$data['caption'] = (array_key_exists('caption', $_POST)) ? $_POST['caption'] : '';
		$model = new \DAM\Models\Files($db);
		$id = $model->create($data);
		if(!empty($_POST['tags'])) {
			$tags = new \DAM\Models\Tags($db);
			$tags->createAll($_POST['tags'], $id);
		}
	} else {
		echo 'error';
	}

	$data['html'] = $file->getHTML(false);
	echo json_encode($data);
}






?>