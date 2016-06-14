var DAM = {
	
	init: function(db) {
		
	},
	
	save: function(id) {
		
	}
};

var Files = {
	
	importFile: function(data)
	{
		if(typeof data === 'string') {
			//attempt parse as json
			console.log(data);
			data = JSON.parse(data);
		}
		console.log(data)
		output = '';
		for (key in data) {
	        if (data.hasOwnProperty(key)) {
				
	        	output += '<div class="dam-output-item"><span class="label dam-'+key+'">'+key+'</span><span class="value">'+data[key]+'</span></div>';
	        }
		}
		document.getElementById('output').innerHTML = output;
	},

	
}

var Database = {
	file: ""
	
};

var Upload = {
	"preview" : "preview",
	"files" : [],
	"form" : null,
	
	init : function()
	{
		form = document.getElementById('damupload');
		form.addEventListener('submit', function(evt) {
			
			evt.preventDefault();
			
			overlay = document.getElementById('overlay');
			overlay.classList.add('active');
			thestatus = document.getElementById('status');
			thestatus.innerHTML = '<h2>Updating</h2>'
			
			Upload.buildFormData();
			
		});
	},
	getPreview : function()
	{
		return document.getElementById(this.preview);
	},
	
	getFiles: function()
	{
		return this.files;
	},
	
	buildFormData: function()
	{
		formData = new FormData();
		obj = Persist.getBlob();
		oblen = obj.length;

		for (key in obj) {
	        if (obj.hasOwnProperty(key)) {
	        	if(key == 'file') {
	        		formData.append('image', obj.file, obj.filename);
	        	} else {
	        		formData.append(key, obj[key]);
	        	}
	        }
		}

		formData.append('tagsarray', UploadedFile.tags);
		this.upload(formData);
	},
	
	upload: function(data) {
		xhr = Object.create(Requester);
		var onload = function()
		{
  			thestatus = document.getElementById('overlay');
  			thestatus.classList.remove('active');
			TheFile = Object.create(Files);
			TheFile.importFile(xhr.responseText)
			document.getElementById('damupload').innerHTML = '<a href="/">Upload another</a>';
  			
		}
		xhr.request('upload.php', onload);
		
		intId = window.setInterval(function(){
			if(xhr.ready) {
				data = xhr.getResponse();
				clearInterval(intId)
			} else {
	  			thestatus = document.getElementById('status');
	  			thestatus.innerHTML = '<h2>Loading...</h2>';
			}
		}, 100);
		
		xhr.send(formData);
	}
	
};

var Requester = {
	
	xhr: null,
	responseText: '',
	ready: false,
	
	request: function(url, onload)
	{
		this.xhr = new XMLHttpRequest();
		this.xhr.open('POST', url, true);
		
		if(typeof onload !== 'undefined') {
			console.log('onload here')
			this.xhr.onload = onload;
		}
		
		this.xhr.onreadystatechange = function() {
		    if (this.readyState == XMLHttpRequest.DONE) {
		        Requester.response(this.responseText);
				Requester.ready = true;
		    }
		}
	},
	
	send: function(data) 
	{
		this.xhr.send(data);
	},
	
	readyState: function()
	{
		return this.xhr.readyState;
	},
	
	response: function(r)
	{
		this.responseText = r;
	},
	
	getResponse: function()
	{
		return this.responseText;
	}
	
}

var UploadedFile = {
	
	"type": null,
	"size": 0,
	"name": null,
	"caption": null,
	"tags": [],
	"blob" : null,
	"initialized": false,
	"fileinput": "damfile",
	"button":"dam-buttom",
	
	init: function() {
		input = document.getElementById(this.fileinput);
		button = document.getElementById(this.button);
		caption = document.getElementById('damcaption');
		tags = document.getElementById('damtags');
		filename = document.getElementById('damfilename');
		
		updater = Persist.updateBlob();
		
		//file input
		input.addEventListener("change", function(){
			handleFile(input.files[0]);
		});
		
		caption.addEventListener("blur", function(){
			value = this.value;
			updater('caption', value);
			UploadedFile.caption = value;	
		});
		
		tags.addEventListener("blur", function(){
			value = this.value;
			updater('tags', value);
			UploadedFile.makeTags(value);
		});
		
		filename.addEventListener("blur", function(){
			value = this.value;
			updater('filename', value);
			UploadedFile.filename = value;	
		});
		
	
	},
	
	makeTags: function(tags) {
		allTags = tags.split(",");
		UploadedFile.tags = [];
		
		tagLen = allTags.length;
		for(i=0; i<tagLen; i++) {
			UploadedFile.tags.push( UploadedFile.stripTag(allTags[i]) );
		}
		
	},
	
	stripTag: function(tag) {
		return tag.trim();
	},
	
	getFile: function() {
		return this;
	},
	
	setProperties: function(name, type, size, blob) {
		this.name = name;
		this.type = type;
		this.size = size;
		this.blob = blob;
		this.initialized = true;
	}

};

var Persist = {
	
	"blob": {},
	
	getBlob: function()
	{
		return this.blob;
	},
	
	updateBlob: function()
	{
		return function(name, value)
		{
			Persist.blob[name] = value;
		}
	}
}


