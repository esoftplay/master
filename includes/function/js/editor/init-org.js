function aceEditor(element) {
	var config = JSON.parse(element.dataset.config);
	var options = JSON.parse(element.dataset.options);
	var name = element.dataset.id;
	var syntax = element.dataset.syntax;
	var syntaxes = JSON.parse(element.dataset.syntaxes);
	function htmlEntities(str) {
		return String(str).replace(/&quot;/g, '"').replace(/&gt;/g, '>').replace(/&lt;/g, '<').replace(/&amp;/g, '&');
	};
	function unHtmlEntities(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	};
	element.innerHTML = unHtmlEntities(document.getElementById("input_" + name).value);
	element.innerHTML = element.innerHTML.replace(/&nbsp;/g, ' ');
	document.getElementById("input_" + name).style.display = "none";
	/*
		var jses = [
			"emmet.js",
			// "ext-emmet.js",
			// "ext-language_tools.js",
			"ext-settings_menu.js",
			];
		for (var i = 0; i < jses.length; i++) {
			var a  = document.createElement("script");
			a.type = "text/javascript";
			a.src  = _URL+"includes/function/js/editor/"+jses[i];
			document.getElementsByTagName('head')[0].appendChild(a);
		}
	*/
	var maxlines = config.maxLines || 30;
	var isFullScreen = config.fullscreen || false;
	delete config.fullscreen;
	var saveCallBack = config.save_callback || null;
	delete config.save_callback;
	var isReadOnly = (config.readOnly||options.readOnly) || false;
	delete config.readOnly;
	delete options.readOnly;
	var cfg = {
		// "theme": "ace/theme/esoftplay",
		"mode": "ace/mode/php",
		"wrap": false,
		"maxLines": 30,
		"minLines": isFullScreen ? 2 : 30,
		"autoScrollEditorIntoView": true
	};
	config = Object.assign({}, cfg, config);
	ace.require("ace/ext/emmet");
	ace.require("ace/ext/language_tools");
	var editor = ace.edit("editor_" + name, config);
	editor.setOptions(options);
	editor.getSession().on("change", function(e) {
		document.getElementById("input_" + name).value = htmlEntities(editor.getSession().getValue());
	});
	editor.on("changeMode", function(e) {
		var mode = editor.getOption("mode");
		var opt = {
			"ace/mode/css": {
				"enableLiveAutocompletion": options.enableLiveAutocompletion || true
			},
			"ace/mode/html": {
				"enableEmmet": options.enableEmmet || true
			},
			"ace/mode/php": {
				"enableEmmet": options.enableEmmet || true,
				"inline": options.inline || true
			},
			"other": {
				"enableBasicAutocompletion": options.enableBasicAutocompletion || true,
				"enableSnippets": options.enableSnippets || true,
				"enableLiveAutocompletion": options.enableLiveAutocompletion || false,
				"useSoftTabs": options.useSoftTabs || false,
				"showPrintMargin": options.showPrintMargin || false,
				"tabSize": options.tabSize || 2
			}
		};
		if (typeof opt[mode] != 'undefined') {
			var opts = Object.assign({}, opt['other'], opt[mode]);
		} else {
			var opts = opt['other'];
		}
		editor.setOptions(opts);
	});
	editor.commands.addCommands([{
		name: "showSettingsMenu",
		bindKey: "F1",
		exec: function(editor) {
			editor.cfg = {
				"theme": config.theme,
				"syntax": syntax,
				"syntaxes": syntaxes.map(function(a) {
					return "ace/mode/" + a;
				})
			};
			ace.require('ace/ext/settings_menu').init(editor);
			editor.showSettingsMenu();
		},
		readOnly: true
	}]);
	editor.commands.addCommand({
		name: "showFullscreen",
		bindKey: "F2",
		exec: function(editor) {
			var dom = ace.require("ace/lib/dom");
			var fullScreen = dom.toggleCssClass(document.body, "fullScreen");
			var max = fullScreen ? "auto" : maxlines;
			dom.setCssClass(editor.container, "fullScreen", fullScreen);
			editor.setAutoScrollEditorIntoView(!fullScreen);
			editor.setOptions({
				maxLines: max
			});
			editor.resize();
		}
	});
	if (isFullScreen) {
		editor.execCommand("showFullscreen");
		editor.focus();
	}
	if (isReadOnly) {
		editor.setOptions({readOnly: true});
	}
	editor.commands.addCommand({
		name: "submitForm",
		bindKey: {
			win: "F3|Ctrl-S",
			mac: "F3|Command-S"
		},
		exec: function(editor) {
			var call = true;
			if (saveCallBack) {
				if (typeof window[saveCallBack] == "function") {
					window[saveCallBack]();
					call = false;
				}
			}
			if (call) {
				var a = document.getElementById("input_" + name).closest("form");
				var r = a.querySelectorAll('[type="submit"]');
				if (r.length > 0) {
					r[0].click();
				}else{
					a.submit();
				}
			}
		}
	});
	window.editAreaLoader = {};
	window.editAreaLoader.getValue = function(a) {
		return document.getElementById("input_" + a).value;
	};
	return editor;
};
(function(funcName, baseObj) {
	"use strict";
	funcName = funcName || "docReady";
	baseObj = baseObj || window;
	var readyList = [];
	var readyFired = false;
	var readyEventHandlersInstalled = false;

	function ready() {
		if (!readyFired) {
			readyFired = true;
			for (var i = 0; i < readyList.length; i++) {
				readyList[i].fn.call(window, readyList[i].ctx);
			}
			readyList = [];
		}
	};

	function readyStateChange() {
		if (document.readyState === "complete") {
			ready();
		}
	};

	baseObj[funcName] = function(callback, context) {
		if (typeof callback !== "function") {
			throw new TypeError("callback for docReady(fn) must be a function");
		}
		if (readyFired) {
			setTimeout(function() {
				callback(context);
			}, 1);
			return;
		} else {
			readyList.push({
				fn: callback,
				ctx: context
			});
		}
		if (document.readyState === "complete" || (!document.attachEvent && document.readyState === "interactive")) {
			setTimeout(ready, 1);
		} else if (!readyEventHandlersInstalled) {
			if (document.addEventListener) {
				document.addEventListener("DOMContentLoaded", ready, false);
				window.addEventListener("load", ready, false);
			} else {
				document.attachEvent("onreadystatechange", readyStateChange);
				window.attachEvent("onload", ready);
			}
			readyEventHandlersInstalled = true;
		}
	};
})("docReady", window);
docReady(function(){
	ace.require("ace/lib/dom").importCssString('.ace_editor.fullScreen{height:auto!important;width:auto;border:0;margin:0;position:fixed!important;top:0;bottom:0;left:0;right:0;z-index:10;} .fullScreen{overflow:hidden}');
	if (typeof editor1 == "undefined") {
		document.querySelectorAll('[rel="editor_code"]').forEach(function(element, i){
			window["editor"+(i+1)] = aceEditor(element);
		});
	}
});
