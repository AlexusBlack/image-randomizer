@@include('ImageRandomizer.coffee');
$ ->
	imageRandomizer = new ImageRandomizer
	if window.ImageRandomizerInDemoMode
		$("textarea[name=fileslist]").val "picard.jpg"
		imageRandomizer.fileslistInputHandler()

	$(".options table tbody").tableDnD {
		onDrop: ->
			imageRandomizer.optionChangeHandler()
	}