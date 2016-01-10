<?php
$translation->add("en", array(
	#Интерфейс
	'yes'=>'Yes',
	'no'=>'No',
	'files-for-rand-by-line'=>'File(s) for randomization by line',
	'result-folder'=>'Result folder',
	'api-link'=>'API link',
	'done'=>'Done',
	'file-not-exist-p1'=>'Fine',
	'file-not-exist-p2'=>'not exists!',

	##Главное меню
	'main-screen'=>'Home',
	'help-screen'=>'Help',
	'settings-screen'=>'Settings',
	##Резульаты
	'preview'=>'Preview',
	'randomize'=>'Randomize',
	'download-result'=>'Download result',
	#Методы
	'method'=>'Method',
	'horizontal-mirror'=>'Flip horizontal',
	'vertical-mirror'=>'Flip vertical',
	'invert-colors'=>'Invert colors',
	'grayscale'=>'Grayscale',
	'crop'=>'Crop image',
	'fixed-resize'=>'Fixed change size',
	'unfixed-resize'=>'Unfixed change size',
	'add-noise'=>'Add noise',
	'rotate'=>'Rotate image',
	'add-border'=>'Add border',
	'change-contrast'=>'Change color levels',
	'blur'=>'Blur image',
	'eskiz'=>'Sketch',
	'pixelization'=>'Pixelization',
	'add-move'=>'Add shift',
	#Форматы ответа
	'response-format'=>'Response format',
	'image'=>'Image',
	'img-tag'=>'IMG tag',
	'background-and-css'=>'background+style',
	'base64'=>'Base64',




	#Предпросмотр
	'back-to-settings'=>'Back to settings',
	'refresh'=>'Refresh result',




	#Настройки
	'full-version'=>'Full version',
	'php-version'=>'PHP version',
	##Самодиагностика
	'chache-folder-is-writable'=>'Cache folder is is writeable (folder rights 666 or 777)',
	'needed-for-api'=>'Needed for API',
	'images-folder-is-writable'=>'Images folder is writable (folder rights 666 or 777)',
	'needed-for-mass-randomization'=>'Needed for randomization of file list',
	'images-folder-is-readable'=>'Images folder is readable (folder 555 or higher)',
	'needed-for-image-randomization'=>'Needed for randomization of images',
	'phpdg-installed'=>'PHP GD extension installed',
	'needed-for-script'=>'Needed for script',
	'php-version-is-or-eq-55'=>'PHP verion is equal of higher than 5.5 (recomended)',
	'needed-for-perfect-functionality'=>'Needed for perfect functionality',




	#Помощь
	'help'=>'
		<button class="btn showIntro">Start interactiove tour</button>
		<p>
			<dl>What is it and why you need it?</dl>
			<dd>This is a script for image randomization (unicalization), which can help you to make many unique images from one by the opinion of search or email robot. it can be email services, search bots or other simular systems. Script can be useful for SEO specialist and web masters who use email subscriptions and whant to make them uniq for every user.</dd>
			<dl>How can i make my image unique?</dl>
			<dd>Upload it "images" folder, then type file name to the big text box at the left side of main screen. Choose randomization methods in block at right side of main screen. You can check the result by clicking on "Preview" button at bottom block. Click "Download result" to save result on your hard drive.</dd>
			<dl>How can I randomize a lot of images at one time?</dl>
			<dd>
				<u>Way 1.</u> Upload several images into "images" folder. Type their names in left block. At the bottom of left block enter destination folder (subfolder in "images" folder), "result/" as example and click "Randomize". Unique images will appear in "images/result".
				<br>
				<u>Way 2.</u> Upload folder with your images to "images" images folder. Type your folder name in left block, "my_folder/" for example.  At the bottom of left block enter destination folder (subfolder in "images" folder), "result/" as example and click "Randomize". Unique images will appear in "images/result/".
				<br>
				<u>Ways 1 и 2 can be used together.</u>
			</dd>
			<dl>How can I integrate Image Randomizer with another software?<dl>
			<dd>
				In single image mode you can get API link at bottom left block. Type image name in left block, choose randomization methods. Than you can choose responce format, Image method returns a generated image. Other methods work different:
				<br><u>&lt;IMG&gt; tag</u>. Image generation result will be saved to "cache" folder and Image Randomizer will return ready to use img tag for use on your web site or in your email template.
				<br><u>Atribute background+style</u>. Image generation result will be saved to "cache" folder and Image Randomizer will return background and css atributes. You must use it for shift method "сдвиг", css code compensate shift and human will see the image as it was before randomization.
			</dd>
			<dl>Which file right I need to work with Image Randomizer?</dl>
			<dd>For simple work read rights are more then enough. For file list randomization you need write rights on destination folder. If you plan to use API you need write rights on "cache" folder.</dd>
			<dl>What server type Image Randomizer needs?</dl>
			<dd>You will need PHP 5+ and PHP GD, different methods need different versions, up to 5.5 version included. You can check your PHP version on Settings page.</dd>
		</p>
	',





	#Интерактивный тур
	'skip'=>'Skip',
	'next'=>'Next',
	'prev'=>'Prev',
	'finish'=>'Finish',
	'welcome-to-imrand'=>'Welcome to Image Randomizer',
	'this-script-is-for-randomization'=>'This script can be used for image randomization. It is useful in SEO and email subscriptions.',
	'files-for-rand-block'=>'Use this block to enter file names you want to randomize. <b>This files must be uploaded to \'images\' folder.</b>',
	'chose-methods'=>'Choose randomization options, you can change order my moving them.',
	'api-available'=>'API link is avalable for integration with another software, like <a href=\'http://mailer.a-l-e-x-u-s.ru/en/\'>alexusMailer</a>',
	'use-preview'=>'Preview result of your randomization',
	'you-can-download-result'=>'If you like it, you can download result to your hard drive',
	'thx-view-help'=>'Thanks for your attention =). Recommended to read the documentation.',




	#Демонстрационный режим
	'mass-randomization-disabled'=>'Массовая обработка отключена в демонстрационном режиме.\nПожалуйста введите имя одного файла.',
));
?>