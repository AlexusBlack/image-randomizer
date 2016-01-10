(function() {
  var ImageRandomizer,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  ImageRandomizer = (function() {
    ImageRandomizer.convert_list = [];

    ImageRandomizer.task_size = 0;

    ImageRandomizer.href = "";

    function ImageRandomizer() {
      this.showIntro = __bind(this.showIntro, this);
      this.showScreen = __bind(this.showScreen, this);
      this.addDir = __bind(this.addDir, this);
      this.convertItem = __bind(this.convertItem, this);
      this.showIntroClickHandler = __bind(this.showIntroClickHandler, this);
      this.hrefCheckHandler = __bind(this.hrefCheckHandler, this);
      this.backToSettingsButtonClickHandler = __bind(this.backToSettingsButtonClickHandler, this);
      this.previewButtonClickHandler = __bind(this.previewButtonClickHandler, this);
      this.primaryButtonClickHandler = __bind(this.primaryButtonClickHandler, this);
      this.optionChangeHandler = __bind(this.optionChangeHandler, this);
      this.fileslistInputHandler = __bind(this.fileslistInputHandler, this);
      this.initHandlers();
      this.loadSettings();
      this.checkAllCheckbox();
      setInterval(this.hrefCheckHandler, 50);
      this.showIntro();
    }

    ImageRandomizer.prototype.initHandlers = function() {
      $("textarea[name=fileslist]").keyup(this.fileslistInputHandler);
      $(".option-container input, select[name=format]").change(this.optionChangeHandler);
      $("input[name=uncheck]").change(this.allCheckBoxChangeHandler);
      $("#downloadButton").click(this.primaryButtonClickHandler);
      $("#previewButton").click(this.previewButtonClickHandler);
      $(".backToSettings").click(this.backToSettingsButtonClickHandler);
      $("input[name=outdir]").change(this.resultFolderChangeHandler);
      $(".showIntro").click(this.showIntroClickHandler);
      $("#refreshPreview").click(this.refreshPreviewClickHandler);
      return $(".langlink").click(this.changeLangHandler);
    };

    ImageRandomizer.prototype.changeLangHandler = function() {
      var lang;
      lang = $(this).attr("lang");
      $.cookie('translation', lang, {
        expires: 365,
        path: '/'
      });
      return document.location.reload();
    };

    ImageRandomizer.prototype.fileslistInputHandler = function(e) {
      var text, text_;
      text = $("textarea[name=fileslist]").val().replace(/\n*$/m, '');
      text_ = text;
      text = text.split("\n");
      if (text.length <= 1) {
        this.showApiLink(this.makeApiLink());
      }
      if ((text.length > 1 || text_.indexOf(":") !== -1) && !$(".filelist").hasClass("multiline")) {
        $(".filelist").addClass("multiline");
        $("input[name=outdir]").css({
          opacity: 1
        });
        $("div.box.downloadlink button").text("%randomize%");
        return $("div.box.apilink, div.box.downloadlink").animate({
          opacity: 0
        }, "fast", function() {
          $("div.box.apilink").hide();
          $("div.box.downloadlink").removeClass("box-2-8");
          return $("div.box.apilink, div.box.downloadlink").animate({
            opacity: 1
          }, "fast");
        });
      } else if ((text.length <= 1 && text_.indexOf(":") === -1) && $(".filelist").hasClass("multiline")) {
        $("#convert_progress").hide();
        $("div.box.downloadlink").css({
          height: "95px"
        });
        $(".filelist").removeClass("multiline");
        $("input[name=outdir]").css({
          opacity: 0
        });
        $("div.box.downloadlink button").text("%download-result%");
        return $("div.box.apilink, div.box.downloadlink").animate({
          opacity: 0
        }, "fast", function() {
          $("div.box.apilink").show();
          $("div.box.downloadlink").addClass("box-2-8");
          return $("div.box.apilink, div.box.downloadlink").animate({
            opacity: 1
          }, "fast");
        });
      }
    };

    ImageRandomizer.prototype.optionChangeHandler = function(e) {
      this.checkAllCheckbox();
      return this.showApiLink(this.makeApiLink());
    };

    ImageRandomizer.prototype.allCheckBoxChangeHandler = function(e) {
      var allischecked, checkbox, checkboxes, _i, _len, _results;
      allischecked = $(this).is(':checked');
      checkboxes = $(".option-container input").toArray();
      _results = [];
      for (_i = 0, _len = checkboxes.length; _i < _len; _i++) {
        checkbox = checkboxes[_i];
        _results.push($(checkbox).prop("checked", allischecked));
      }
      return _results;
    };

    ImageRandomizer.prototype.primaryButtonClickHandler = function(e) {
      this.saveSettings();
      if ($(".filelist").hasClass("multiline")) {
        if (ImageRandomizerInDemoMode) {
          return alert("%mass-randomization-disabled%");
        }
        return this.convertList();
      } else {
        return this.downloadImage();
      }
    };

    ImageRandomizer.prototype.previewButtonClickHandler = function(e) {
      return this.previewImage();
    };

    ImageRandomizer.prototype.backToSettingsButtonClickHandler = function(e) {
      return document.location.hash = "#";
    };

    ImageRandomizer.prototype.hrefCheckHandler = function(e) {
      return this.showScreen(document.location.hash);
    };

    ImageRandomizer.prototype.resultFolderChangeHandler = function(e) {
      var folder;
      folder = $(this).val();
      if (folder[folder.length - 1] !== "/") {
        folder += "/";
        return $(this).val(folder);
      }
    };

    ImageRandomizer.prototype.showIntroClickHandler = function(e) {
      return this.showIntro(true);
    };

    ImageRandomizer.prototype.refreshPreviewClickHandler = function(e) {
      return $(".preview.screen iframe")[0].contentWindow.location.reload();
    };

    ImageRandomizer.prototype.checkAllCheckbox = function() {
      var checkbox, checkboxes, checked, _i, _len;
      checked = false;
      checkboxes = $(".option-container input").toArray();
      for (_i = 0, _len = checkboxes.length; _i < _len; _i++) {
        checkbox = checkboxes[_i];
        if ($(checkbox).is(':checked')) {
          checked = true;
        }
      }
      if (checked) {
        return $("input[name=uncheck]").prop("checked", true);
      } else {
        return $("input[name=uncheck]").prop("checked", false);
      }
    };

    ImageRandomizer.prototype.showProgress = function(percent, text) {
      $("#convert_progress span:nth-child(1)").width(percent + "%");
      return $("#convert_progress span:nth-child(2)").text(text);
    };

    ImageRandomizer.prototype.makeApiLink = function(file, format, outdir) {
      var link, option, option_element, option_elements, options, value, _i, _len;
      if (file == null) {
        file = "default";
      }
      if (format == null) {
        format = "default";
      }
      if (outdir == null) {
        outdir = "default";
      }
      link = document.location.origin + document.location.pathname + "?req=randomizeImage";
      if (file === "default") {
        file = $("textarea[name=fileslist]").val().replace(/\n*$/m, '');
      }
      option_elements = $(".option-container input").toArray();
      options = {};
      for (_i = 0, _len = option_elements.length; _i < _len; _i++) {
        option_element = option_elements[_i];
        options[$(option_element).attr("name")] = $(option_element).is(':checked');
      }
      link += "&path=" + file;
      for (option in options) {
        value = options[option];
        if (!value) {
          continue;
        }
        link += "&" + option + "=y";
      }
      if (format === "default") {
        format = $("select[name=format]").val();
      }
      if (format !== "image") {
        link += "&format=" + format;
      }
      if (outdir !== "default") {
        return link += "&outdir=" + outdir;
      } else {
        return link;
      }
    };

    ImageRandomizer.prototype.showApiLink = function(link) {
      return $("#apilink").val(link);
    };

    ImageRandomizer.prototype.downloadImage = function() {
      var apilink;
      apilink = $("#apilink").val();
      if (apilink === "") {
        return;
      }
      apilink += "&download=y";
      return window.open(apilink);
    };

    ImageRandomizer.prototype.previewImage = function() {
      var apilink;
      apilink = $("#apilink").val();
      if (apilink === "") {
        return;
      }
      $(".preview.screen iframe").attr("src", apilink);
      return document.location.hash = "#preview";
    };

    ImageRandomizer.prototype.convertList = function() {
      if ($("input[name=outdir]").val() === "") {
        return;
      }
      this.showProgress(0, "Начинаю...");
      return $("div.box.downloadlink").animate({
        height: "137px"
      }, "fast", (function(_this) {
        return function() {
          var i, text, _i, _len, _ref, _results;
          $("#convert_progress").show();
          text = $("textarea[name=fileslist]").val().replace(/\n+$/m, '');
          _this.convert_list = text.split("\n");
          _this.convert_list.reverse();
          if (typeof console !== "undefined" && console !== null) {
            console.log(_this.convert_list);
          }
          _this.task_size = _this.convert_list.length;
          _ref = [0];
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            i = _ref[_i];
            _results.push(_this.convertItem());
          }
          return _results;
        };
      })(this));
    };

    ImageRandomizer.prototype.convertItem = function() {
      var FilenameAndCount, count, file, i, link, outdir, _i, _ref;
      if (this.convert_list.length === 0) {
        this.showProgress(100, "%done%");
        return;
      }
      file = this.convert_list.pop();
      if (file.indexOf(":" !== -1)) {
        FilenameAndCount = file.split(":");
        count = parseInt(FilenameAndCount[1]);
        file = FilenameAndCount[0];
        for (i = _i = 1, _ref = count - 1; 1 <= _ref ? _i <= _ref : _i >= _ref; i = 1 <= _ref ? ++_i : --_i) {
          this.convert_list.push(FilenameAndCount[0]);
        }
      }
      outdir = $("input[name=outdir]").val();
      link = this.makeApiLink(file, "convert", outdir);
      return $.get(link, (function(_this) {
        return function(response) {
          var percent;
          if (typeof console !== "undefined" && console !== null) {
            console.log(response);
          }
          if (response.convert === false && response.reason === "not_exists") {
            alert("%file-not-exist-p1% " + response.file + " %file-not-exist-p2%");
            return setTimeout(_this.convertItem, 0);
          }
          if (response.convert === false && response.reason === "is_dir") {
            return _this.addDir(response.files, _this.convertItem);
          } else {
            percent = _this.convert_list.length === 0 ? 100 : 100 / (_this.task_size / (_this.task_size - _this.convert_list.length));
            _this.showProgress(percent, file);
            return setTimeout(_this.convertItem, 0);
          }
        };
      })(this));
    };

    ImageRandomizer.prototype.addDir = function(data, handler) {
      this.convert_list = this.convert_list.concat(data);
      this.task_size += data.length;
      return handler();
    };

    ImageRandomizer.prototype.saveSettings = function() {
      var data, option, option_name, option_value, options, settings, _i, _len;
      settings = {
        outdir: $("input[name=outdir]").val(),
        options: {}
      };
      options = $(".option-container input").toArray();
      for (_i = 0, _len = options.length; _i < _len; _i++) {
        option = options[_i];
        option_name = $(option).attr("name");
        option_value = $(option).is(":checked");
        settings.options[option_name] = option_value;
      }
      data = JSON.stringify(settings);
      $.cookie('ImRandSettings', data, {
        expires: 365,
        path: '/'
      });
      return typeof console !== "undefined" && console !== null ? console.log("Settings saved") : void 0;
    };

    ImageRandomizer.prototype.loadSettings = function() {
      var data, name, settings, value, _ref;
      data = $.cookie('ImRandSettings');
      if (data == null) {
        return;
      }
      settings = JSON.parse(data);
      $("input[name=outdir]").val(settings.outdir);
      _ref = settings.options;
      for (name in _ref) {
        value = _ref[name];
        $(".option-container input[name=" + name + "]").prop("checked", value);
      }
      return typeof console !== "undefined" && console !== null ? console.log("Settings loaded") : void 0;
    };

    ImageRandomizer.prototype.showScreen = function(name) {
      if (name == null) {
        name = "";
      }
      if (name === this.href) {
        return;
      } else {
        this.href = name;
      }
      name = name.replace("#", "");
      name = name === "" ? "main" : name;
      if (typeof console !== "undefined" && console !== null) {
        console.log(name);
      }
      $(".screen").hide();
      return $(".screen." + name).show();
    };

    ImageRandomizer.prototype.showIntro = function(force) {
      var intro, introViewed, options;
      if (force == null) {
        force = false;
      }
      introViewed = $.cookie('ImRandIntro');
      if (introViewed && !force) {
        return;
      }
      document.location.hash = "#main";
      options = {
        'skipLabel': '%skip%',
        'nextLabel': '%next%',
        'prevLabel': '%prev%',
        'doneLabel': '%finish%',
        'disableInteraction': true
      };
      intro = introJs().setOptions(options);
      intro.oncomplete(this.closeIntroHandler);
      intro.onexit(this.closeIntroHandler);
      return intro.start();
    };

    ImageRandomizer.prototype.closeIntroHandler = function() {
      return $.cookie('ImRandIntro', true, {
        expires: 365,
        path: '/'
      });
    };

    return ImageRandomizer;

  })();

  $(function() {
    var imageRandomizer;
    imageRandomizer = new ImageRandomizer;
    if (window.ImageRandomizerInDemoMode) {
      $("textarea[name=fileslist]").val("picard.jpg");
      imageRandomizer.fileslistInputHandler();
    }
    return $(".options table tbody").tableDnD({
      onDrop: function() {
        return imageRandomizer.optionChangeHandler();
      }
    });
  });

}).call(this);
