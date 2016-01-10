class FileManager
  constructor: (@path) ->

  cd: (path) ->
   @path += path
   this.getFiles @path 

  getFiles: (path) ->
  	fileManager = this
  	$.post "?req=getFilesList", {"path":path}, (data) ->
  		files = data
  		fileManager.showFiles files

  showFiles: (files) ->
  	html = "<ul>"
  	for file in files
  	  html += this.makeFileManagerLine(file.name, if file.is_dir then 'dir' else 'file')
  	html += "</ul>"
  	$(".filelist").html html

  makeFileManagerLine: (name, type) ->
  	if type is "dir"
  		code = "<li>
								<div class=\"icon icon-folder\">
								 <div class=\"icon-folder-rectangle-1\"></div>
								 <div class=\"icon-folder-rectangle-2\"></div>
								 <div class=\"icon-folder-rectangle-3\"></div>
								</div>
								<a href=\"#\">#{name}</a>
							</li>"
  	else
  		code = "<li>
								<div class=\"icon icon-picture\">
								 <div class=\"icon-picture-rectangle\"></div>
								 <div class=\"icon-picture-dune-1\"></div>
								 <div class=\"icon-picture-dune-2\"></div>
								 <div class=\"icon-picture-dot\"></div>
								</div>
								<a href=\"#\">#{name}</a>
							</li>"
