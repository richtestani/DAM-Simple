<?php
$config = require_once('config.php');
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
	
	<div class="container">
		<div class="columns">
			<div class="column" id="dam-form">
				<form method="post" id="damupload">
					<label class="label">Choose a File</label>
					<input type="file" name="damfile" id="damfile" />
					<label class="label">Caption</label>
					<input type="text" name="damcaption" id="damcaption" />
					<label class="label">Search Tags (comma seperate)</label>
					<input type="text" name="damtags" id="damtags" />
					<label class="label">Filename</label>
					<input type="text" name="damfilename" id="damfilename" value="" />
					<button class="button" id="da-button">Upload</button>
					<div id="output"></div>
				</form>
				
			</div>
			<div class="column" id="daminfo">
				<div id="preview"></div>
				<div id="type"></div>
				<div id="name"></div>
				<div id="size"></div>
			</div>
		</div>
	</div>
</body>
<script src="assets/js/dam-client.js" type="text/javascript" charset="utf-8"></script>
<script>

DAMUpload = Object.create(Upload);

function handleFile(file) {
	
	var myFile = Object.create(UploadedFile);

	filesize = file.size;
	filetype = file.type;
	filename = file.name;
	fileblob = file;
	
	//filesize as megabytes
	filesizeMB = Math.round(((filesize/1000)/1024)*100)/100;
	
	//the preview node
	preview = DAMUpload.getPreview();
	
	myFile.setProperties(filename, filetype, filesize, fileblob);
	
	
	document.getElementById('size').innerHTML = 'File Size: '+filesizeMB+' MB';
	document.getElementById('type').innerHTML = 'File Type: '+filetype;
	document.getElementById('name').innerHTML = 'File Size: '+filename;
	
	var reader = new FileReader();
	if(!preview.hasChildNodes()) {
		var img = document.createElement("img");
		    img.classList.add("file-preview");
			img.id = "preview-img";
			preview.appendChild(img);
	} else {
		img = document.getElementById('preview-img');
	}
	
	//return a closure with a file as the single argument
	//https://developer.mozilla.org/en-US/docs/Using_files_from_web_applications
	reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
	reader.readAsDataURL(file);
	update = Persist.updateBlob();
	update('size', filesize);
	update('mime', filetype);
	update('original', filename);
	update('file', file);
	
}

Upload.init();
UploadedFile.init();

</script>
</html>