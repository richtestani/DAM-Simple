<?php
date_default_timezone_set('America/New_York');
require_once './vendor/autoload.php';
$config = require_once('config.php');
$config['db']['dsn'] = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['database'];
$fileconfig = array_merge($config['files'], $config['paths'], $config['types']);

$archive = new \DAM\Search(new DAM\Query($config['db']), $fileconfig);

$files = $archive->getRecent(50, ['created', 'desc']);

?>
<html>
<head>
	<title>DAM Simple</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/bulma.min.css" type="text/css" media="screen">
	<link rel="stylesheet" href="assets/css/dam.css" type="text/css" media="screen">
</head>
<body>
	<div id="overlay"><div id="status"></div></div>
	<?php include 'nav.php'; ?>
	
	<div class="container" id="archive">
		<?php
		foreach($files as $file) {
			echo '<div class="dam-item card" id="'.$file->getUid().'">';
			echo '<a href="javascript:;" class="card-image dam-edit-link">';
			echo '<figure class="image" data-dam-id="'.$file->getUid().'">';
			echo htmlspecialchars_decode($file->getHTML()).'<br>';
			echo '</figure>';
			echo '</a>';
			echo '<div class="card-content">';
			echo '<span class="caption title is-5 is-block"><strong>'.$file->caption().'</strong></span>';
			echo '<a class="button dam-edit-button" data-dam-id="'.$file->getUid().'"><strong>Edit</strong></a>';
			echo '</div>';
			echo '</div>';
		}
		?>
	</div>
	<div id="edit-template">
		<div class="container">
			<div class="columns box">
				<div class="column"><a href="javascript:;" onclick="closeForm()"><span class="icon"><i class="fa fa-close">Close</i></span></a></div>
				<div class="column">
					<label class="label" id="loader">Loading <img src="/assets/img/gif.gif" /></label>
					<div id="dam-message"></div>
					<label class="label">Caption</label>
					<input type="text" name="damcaption" id="damcaption" />
					<label class="label">Search Tags (comma seperate)</label>
					<input type="text" name="damtags" id="damtags" />
					<label class="label">Filename</label>
					<input type="text" name="damfilename" id="damfilename" value="" />
					<input type="hidden" name="damuid" value="" id="damuid">
					<div><button class="button" class="dam-save-button" id="dam-save-button">Save</button></div>
				</div>
				<div class="column"><div id="dam-preview"></div></div>
				<div class="column"><a href="javascript:;" onclick="closeForm()" id="dam-delete" class="is-hidden"><span class="icon"><i class="fa fa-trash">Delete</i></span></a></div>
			</div>
		</div>
	</div>
</body>
<script src="assets/js/dam-client.js" type="text/javascript" charset="utf-8"></script>
<script>

items = document.querySelectorAll('.dam-item .dam-edit-button, .dam-item img');

itemLen = items.length;
for(i=0; i<itemLen; i++) {
	item = items[i];
	item.addEventListener('click', showForm);
}

function closeForm()
{
	overlay = document.getElementById('overlay');
	overlay.classList.remove('active');
}

function showForm(evt)
{
	item = evt.target;
	itemid = item.parentNode.getAttribute('data-dam-id');
	request = Object.create(Requester);
	formData = new FormData();
	formData.append('uid', itemid);
	request.request('find.php');
	request.send(formData);
	response = {};
	form = document.getElementById('edit-template');
	overlay = document.getElementById('overlay');
	overlay.classList.add('active');
	overlay.innerHTML = form.innerHTML;
	//insert values
	intId = window.setInterval(function(){
		if(request.ready) {
			data = request.getResponse();
			clearInterval(intId);
			populateForm(data);
			document.getElementById('loader').parentNode.removeChild(document.getElementById('loader'));
			
			save = document.getElementById('dam-save-button');
			save.addEventListener('click', saveImage);
			
			deleteThis = document.getElementById('dam-delete');
			deleteThis.classList.remove('is-hidden');
			deleteThis.addEventListener('click', function() {
				choice = confirm('are you sure?');
				if(choice) {
					deleteImage(itemid);
					console.log(itemid);
					document.getElementById(itemid).classList.add('is-hidden');
				}
			})
		}
		else
		{
			thestatus = document.getElementById('status');
			thestatus.innerHTML = '<h2>Loading..</h2>'
		}
	}, 1000);
	
}

function saveImage()
{
	saveRequest = Object.create(Requester);
	saveRequest.request('update.php');
	
	form = new FormData();
	form.append('uid', document.getElementById('damuid').value);
	form.append('filename', document.getElementById('damfilename').value);
	form.append('caption', document.getElementById('damcaption').value);
	form.append('tags', document.getElementById('damtags').value);
	
	var onload = function() {
		document.getElementById('dam-message').innerHTML = '<h4>Saved</h4>';
	};
	
	saveRequest.send(form);
}

function deleteImage(id)
{
	deleteRequest = Object.create(Requester);
	deleteRequest.request('delete.php');
	
	form = new FormData();
	form.append('uid', id);
	
	var onload = function() {
		document.getElementById('overlay').classList.remove('active')
	};
	
	deleteRequest.send(form);
}

function populateForm(data)
{
	response = JSON.parse(data);
	document.getElementById('damcaption').value = (response.caption != '') ? response.caption : '';
	document.getElementById('damfilename').value = (response.filename != '') ? response.filename : '';
	document.getElementById('damtags').value = (response.tags != '') ? response.tags : '';
	document.getElementById('damuid').value = (response.uid != '') ? response.uid : '';
	image = document.createElement('img');
	path = response.path+'/'+response.filename;
	image.src = path;
	image.classList.add('dam-image');
	document.getElementById('dam-preview').appendChild(image);
	button = document.querySelector('button.dam-save-button');

}
</script>
</html>