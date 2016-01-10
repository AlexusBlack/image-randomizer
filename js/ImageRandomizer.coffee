class ImageRandomizer
	@convert_list = []
	@task_size = 0
	@href = ""

	constructor: ->
		@initHandlers()
		@loadSettings()
		@checkAllCheckbox()
		setInterval @hrefCheckHandler, 50
		@showIntro()
	
	initHandlers: ->
		$("textarea[name=fileslist]").keyup @fileslistInputHandler
		$(".option-container input, select[name=format]").change @optionChangeHandler
		$("input[name=uncheck]").change @allCheckBoxChangeHandler
		$("#downloadButton").click @primaryButtonClickHandler
		$("#previewButton").click @previewButtonClickHandler
		$(".backToSettings").click @backToSettingsButtonClickHandler
		$("input[name=outdir]").change @resultFolderChangeHandler
		$(".showIntro").click @showIntroClickHandler
		$("#refreshPreview").click @refreshPreviewClickHandler
		$(".langlink").click @changeLangHandler

	changeLangHandler: ->
		lang = $(@).attr "lang"
		$.cookie 'translation', lang, { expires: 365, path: '/' }
		document.location.reload();

	fileslistInputHandler: (e) =>
		#проверка на многострочный ввод
		text = $("textarea[name=fileslist]").val().replace /\n*$/m, ''
		text_ = text
		text = text.split "\n"

		#для однострочного ввода генерим API ссылку
		if text.length <= 1
			@showApiLink @makeApiLink()

		if (text.length > 1 or text_.indexOf(":") isnt -1) and not $(".filelist").hasClass "multiline"
			$(".filelist").addClass "multiline"
			$("input[name=outdir]").css {opacity:1}
			#меняем нижнюю кнопку
			$("div.box.downloadlink button").text "%randomize%"
			$("div.box.apilink, div.box.downloadlink").animate {opacity:0},"fast",->
				$("div.box.apilink").hide()
				$("div.box.downloadlink").removeClass "box-2-8"
				$("div.box.apilink, div.box.downloadlink").animate {opacity:1},"fast"
		else if (text.length <= 1 and text_.indexOf(":") is -1) and $(".filelist").hasClass "multiline"
			$("#convert_progress").hide()
			$("div.box.downloadlink").css {height: "95px"}
			$(".filelist").removeClass "multiline"
			$("input[name=outdir]").css {opacity:0}
			#меняем нижнюю кнопку
			$("div.box.downloadlink button").text "%download-result%"
			$("div.box.apilink, div.box.downloadlink").animate {opacity:0},"fast",->
				$("div.box.apilink").show()
				$("div.box.downloadlink").addClass "box-2-8"
				$("div.box.apilink, div.box.downloadlink").animate {opacity:1},"fast"

	optionChangeHandler: (e) =>
		@checkAllCheckbox()
		@showApiLink @makeApiLink()

	allCheckBoxChangeHandler: (e) ->
		allischecked = $(@).is ':checked'
		checkboxes = $(".option-container input").toArray()
		for checkbox in checkboxes
			$(checkbox).prop "checked", allischecked

	primaryButtonClickHandler: (e) =>
		@saveSettings()
		if $(".filelist").hasClass "multiline"
			return alert "%mass-randomization-disabled%" if ImageRandomizerInDemoMode
			@convertList()
		else
			@downloadImage()

	previewButtonClickHandler: (e) =>
		@previewImage()

	backToSettingsButtonClickHandler: (e) =>
		document.location.hash = "#"

	hrefCheckHandler: (e) =>
		@showScreen document.location.hash

	resultFolderChangeHandler: (e) ->
		folder = $(@).val()
		if folder[folder.length-1] isnt "/"
			folder += "/"
			$(@).val folder

	showIntroClickHandler: (e) =>
		@showIntro true

	refreshPreviewClickHandler: (e) ->
		$(".preview.screen iframe")[0].contentWindow.location.reload()

	checkAllCheckbox: ->
		checked = false
		checkboxes = $(".option-container input").toArray()
		for checkbox in checkboxes
			checked = true if $(checkbox).is ':checked'
		if checked
			$("input[name=uncheck]").prop "checked", true
		else
			$("input[name=uncheck]").prop "checked", false

	showProgress: (percent, text) ->
		$("#convert_progress span:nth-child(1)").width percent+"%"
		$("#convert_progress span:nth-child(2)").text text

	makeApiLink: (file = "default", format = "default", outdir = "default") ->
		link = document.location.origin + document.location.pathname + "?req=randomizeImage"
		if file is "default"
			file = $("textarea[name=fileslist]").val().replace /\n*$/m, ''
		option_elements=$(".option-container input").toArray()
		options = {}
		for option_element in option_elements
			options[$(option_element).attr("name")] = $(option_element).is ':checked'

		link += "&path=#{file}"
		for option, value of options
			continue if not value
			link += "&#{option}=y"

		if format is "default"
			format = $("select[name=format]").val()		
		if format isnt "image"
			link += "&format=#{format}"

		if outdir isnt "default"
			link += "&outdir=#{outdir}"

		else
			link

	showApiLink: (link) ->
		$("#apilink").val link

	downloadImage: ->
		apilink = $("#apilink").val()
		return if apilink is ""
		apilink += "&download=y"
		window.open apilink

	previewImage: ->
		apilink = $("#apilink").val()
		return if apilink is ""
		#window.open apilink
		$(".preview.screen iframe").attr "src", apilink
		document.location.hash = "#preview"

	convertList: ->
		return if $("input[name=outdir]").val() is ""
		@showProgress(0, "Начинаю...")
		$("div.box.downloadlink").animate {height:"137px"}, "fast", =>
			$("#convert_progress").show()
			text = $("textarea[name=fileslist]").val().replace /\n+$/m, ''
			@convert_list = text.split "\n"
			@convert_list.reverse()
			console?.log @convert_list
			@task_size = @convert_list.length
			for i in [0]#..3]
				@convertItem()

	convertItem: =>
		if @convert_list.length is 0
			@showProgress(100, "%done%")
			return
		file = @convert_list.pop()
		#проверка на многократную рандомизацию
		if file.indexOf ":" isnt -1
			FilenameAndCount = file.split ":"
			count = parseInt FilenameAndCount[1]
			file = FilenameAndCount[0]
			for i in [1..count-1]
				@convert_list.push FilenameAndCount[0]
		outdir = $("input[name=outdir]").val()
		link = @makeApiLink file, "convert", outdir
		#тут типо отправка
		$.get link, (response) =>
			console?.log response
			if response.convert is false and response.reason is "not_exists"
				alert "%file-not-exist-p1% #{response.file} %file-not-exist-p2%"
				return setTimeout @convertItem, 0
			if response.convert is false and response.reason is "is_dir"
				@addDir response.files, @convertItem
			else
				percent = if @convert_list.length is 0 then 100 else (100 / (@task_size / (@task_size-@convert_list.length)))
				@showProgress(percent , file)
				setTimeout @convertItem, 0

	addDir: (data, handler) =>
		#$.post "?req=listDir", {path: path}, (data) =>
		#data=$.parseJSON data
		@convert_list = @convert_list.concat data
		@task_size += data.length
		handler()

	saveSettings: ->
		settings =
			outdir: $("input[name=outdir]").val()
			options: {}
		#сохраним опции
		options = $(".option-container input").toArray()
		for option in options
			option_name = $(option).attr "name"
			option_value = $(option).is ":checked"
			settings.options[option_name] = option_value
		data=JSON.stringify settings

		$.cookie 'ImRandSettings', data, { expires: 365, path: '/' }
		console?.log "Settings saved"

	loadSettings: ->
		data = $.cookie 'ImRandSettings'
		return if not data?
		settings = JSON.parse data
		$("input[name=outdir]").val settings.outdir
		for name, value of settings.options
			$(".option-container input[name=#{name}]").prop "checked", value
		console?.log "Settings loaded"

	showScreen: (name="") =>
		if name is @href
			return
		else
			@href = name
		name = name.replace "#", ""
		name = if name is "" then "main" else name
		console?.log name
		$(".screen").hide()
		$(".screen."+name).show();

	showIntro: (force=false) =>
		introViewed = $.cookie 'ImRandIntro'
		return if introViewed and not force
		document.location.hash = "#main"
		#@showScreen()
		options =
			'skipLabel': '%skip%'
			'nextLabel': '%next%'
			'prevLabel': '%prev%'
			'doneLabel': '%finish%'
			'disableInteraction': true
		intro=introJs().setOptions options
		intro.oncomplete @closeIntroHandler
		intro.onexit @closeIntroHandler
		intro.start()

	closeIntroHandler: ->
		$.cookie 'ImRandIntro', true, { expires: 365, path: '/' }
