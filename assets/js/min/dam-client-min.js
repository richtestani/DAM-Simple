var DAM={init:function(t){},save:function(t){}},Database={file:""},Upload={types:["jpg","jpeg","png","gif","doc","docx","pdf"],preview:"preview",files:[],getPreview:function(){return document.getElementById(this.preview)},add:function(t){this.files.push(t)}},UploadedFile={type:null,size:0,name:null,caption:null,tags:[],initialized:!1,fileinput:"damfile",button:"dam-buttom",init:function(){input=document.getElementById(this.fileinput),button=document.getElementById(this.button),input.addEventListener("change",function(){handleFile(input.files[0])})},makeTags:function(t){allTags=t.split(","),this.tags=[],allTags.forEach(stripTag)},stripTag:function(t){this.tags.push(t.trim())},getFile:function(){return this},setProperties:function(t,i,e){this.name=t,this.type=i,this.size=e,this.initialized=!0}};UploadedFile.init();