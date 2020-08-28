_Bbc(function ($) {
	if ($.autocomplete = function (a, i) {
		var r = $(a).attr("autocomplete", "off"),
		n = $("." + i.resultsClass);
		if (i.inputClass && r.addClass(i.inputClass), 0 < $("." + i.resultsClass).length) n = (l = n).get(0);
		else {
			n = document.createElement("div");
			var l = $(n);
			l.hide().addClass(i.resultsClass).css({
				position: "absolute",
				"z-index": "999999"
			}),
			l.css("min-width", 150),
			0 < i.width && l.css("width", i.width),
			$("body").append(n);
		}
		a.autocompleter = this;
		var t = null,
		modalID = i.mainName+"modal_id",
		modalField = i.mainName+"modal_ac",
		modalTbl = i.mainName+"modal_table",
		s = "",
		c = -1,
		o = {},
		u = !1,
		f = null,
		h = "input" == r.prop("tagName").toLowerCase() ? r : r.parent();
		function d() {
			o = {
				data: {},
				length: 0
			}
		}
		if (d(), null != i.data) {
			var e = "",
			p = {},
			m = [];
			"string" != typeof i.url && (i.cacheLength = 1);
			for (var g = 0; g < i.data.length; g++) 0 < (m = "string" == typeof i.data[g] ? [i.data
				[g]] : i.data[g])[0].length && (p[e = m
				[0].substring(0, 1).toLowerCase()] || (p[e] = []), p[e].push(m));
			for (var v in p) i.cacheLength++,
			F(v, p[v])
		}
		function highlightOption(e) {
			var t = $("li", n);
			t && ((c += e) < 0 ? c = 0 : c >= t.size() && (c = t.size() - 1), t.removeClass("active"), $(t[c]).addClass("active"))
		}
		function addOneOption(e) {
			e || ((e = document.createElement("li")).className = "list-group-item", e.extra = [], e.selectValue = "");
			var t = $.trim(e.selectValue ? e.selectValue : e.innerHTML);
			a.lastSelected = t,
			s = t,
			l.html(""),
			r.val(t),
			L(),
			i.onItemSelect && setTimeout(function () {
				i.onItemSelect(e)
			},
			1)
		}
		function w(e) {
			8 != f && (r.val(r.val() + e.substring(s.length)), function (e, t) {
				var a = r.get(0);
				if (a.createTextRange) {
					var n = a.createTextRange();
					n.collapse(!0),
					n.moveStart("character", e),
					n.moveEnd("character", t),
					n.select()
				} else a.setSelectionRange ? a.setSelectionRange(e, t) : a.selectionStart && (a.selectionStart = e, a.selectionEnd = t);
				a.focus()
			} (s.length, e.length))
		}
		function displayOptions(r) {
			var e = function (e) {
				if ($(e).offset) var t = $(e).offset(),
				a = t.left || 0,
				n = t.top || 0;
				else for (var a = e.offsetLeft || 0, n = e.offsetTop || 0; e = e.offsetParent;) a += e.offsetLeft,
				n += e.offsetTop;
				return {
					x: a,
					y: n
				}
			} (r),
			t = 0 < i.width ? i.width : r.width();
			l.css({
				width: parseInt(t) + "px",
				top: e.y + a.offsetHeight + "px",
				left: e.x + "px"
			}).show()
		}
		function L() {
			(t && clearTimeout(t), h.removeClass(i.loadingClass), l.is(":visible") && l.hide(), i.mustMatch) && (r.val() != a.lastSelected && addOneOption(null))
		}
		function addOptions(e, t, r) {
			// e = keyword
			// t = result
			// r = ac_input
			if (t) {
				if (h.removeClass(i.loadingClass), n.innerHTML = "", !u || 0 == t.length) return L();
				0 < window.navigator.userAgent.indexOf("MSIE ") && l.append(document.createElement("iframe")),
				addRows(e,t,r),
				n.appendChild(function (e) {
					var t = document.createElement("ul");
					t.className = "list-group";
					var a = e.length;
					0 < i.maxItemsToShow && i.maxItemsToShow < a && (a = i.maxItemsToShow);
					for (var n = 0; n < a; n++) {
						var r = e[n];
						if (r) {
							var l = document.createElement("li");
							l.className = "list-group-item",
							i.formatItem ? l.innerHTML = i.formatItem(r, n, a) : l.innerHTML = r[0],
							l.selectValue = r[0];
							var s = null;
							if (1 < r.length) {
								s = [];
								for (var o = 1; o < r.length; o++) s[s.length] = r[o]
							}
							l.extra = s,
							t.appendChild(l),
							$(l).hover(function () {
								$("li", t).removeClass("active"),
								$(this).addClass("active"),
								c = $("li", t).indexOf($(this).get(0))
							},
							function () {
								$(this).removeClass("active")
							}).click(function (e) {
								e.preventDefault(),
								e.stopPropagation(),
								addOneOption(this)
							})
						}
					}
					return t
				} (t)),
				i.autoFill && r.val().toLowerCase() == e.toLowerCase() && w(t[0][0]),
				displayOptions(r)
			} else L()
		}
		function addRows(e,t,r) {
			// e = keyword
			// t = result
			// r = ac_input
			if (t) {
				// console.log([e,t,r])
				// $("#"+modalField).val("");
				var tbl = $("#"+modalTbl);
				tbl.html("");
				for (var n = 0; n < t.length; n++) {
					tbl.append('<tr data-id="'+t[n][2]+'" data-title="'+t[n][1]+'" style="cursor: pointer;"><td>'+t[n][0]+'</td><td>'+t[n][1]+'</td></tr>');
				}
				$("tr", tbl).on("click", function(e){
					$("#"+modalID).modal("hide");
					r.val($(this).data("title"));
					$("#"+r.prop("id").substr(0, r.prop("id").length-3)).val($(this).data("id"));
					r.blur();
				});
			}
		}
		function Separate(e) {
			if (!e) return null;
			for (var t = [], a = e.split(i.lineSeparator), n = 0; n < a.length; n++) {
				var r = $.trim(a[n]);
				r && (t[t.length] = r.split(i.cellSeparator))
			}
			return t
		}
		function T(e) {
			var t = i.url + encodeURI(e);
			for (var a in i.extraParams) t += "&" + a + "=" + encodeURI(i.extraParams[a]);
			return i.parent && (t += "&parent=" + $(i.parent).val()),
			t
		}
		function fetchData(e) {
			if (!e) return null;
			if (o.data[e]) return o.data[e];
			if (i.matchSubset) for (var t = e.length - 1; t >= i.minChars; t--) {
				var a = e.substr(0, t),
				n = o.data[a];
				if (n) {
					for (var r = [], l = 0; l < n.length; l++) {
						var s = n[l];
						_(s[0], e) && (r[r.length] = s)
					}
					return r
				}
			}
			return null
		}
		function _(e, t) {
			i.matchCase || (e = e.toLowerCase());
			var a = e.indexOf(t);
			return -1 != a && (0 == a || i.matchContains)
		}
		function k(e, t) {
			t && h.removeClass(i.loadingClass);
			for (var a = t ? t.length : 0, n = null, r = 0; r < a; r++) {
				var l = t[r];
				if (l[0].toLowerCase() == e.toLowerCase()) {
					(n = document.createElement("li")).className = "list-group-item",
					i.formatItem ? n.innerHTML = i.formatItem(l, r, a) : n.innerHTML = l[0],
					n.selectValue = l[0];
					var s = null;
					if (1 < l.length) {
						s = [];
						for (var o = 1; o < l.length; o++) s[s.length] = l[o]
					}
					n.extra = s
				}
			}
			i.onFindValue && setTimeout(function () {
				i.onFindValue(n)
			},
			1)
		}
		function F(e, t) {
			t && e && i.cacheLength && (!o.length || o.length > i.cacheLength ? (d(), o.length++) : o[e] || o.length++, o.data[e] = t)
		}
		$("#"+modalField).keydown(function(e){
			switch (f = e.keyCode, e.keyCode) {
				case 9: // tab
				case 13: // Enter
					e.preventDefault();
					var v = $(this).val();
					if (v == s) return;
					var e = i.cacheLength ? fetchData(v) : null;
					e ? addRows(v, e, r) : ("string" == typeof i.url && 0 < i.url.length) ? $.get(T(v), function (e) {
						e = Separate(e),
						F(v, e),
						addRows(v, e, r)			// v = keyword, e = result, r = ac_input
					}) : h.removeClass(i.loadingClass);
					break;
			}
		}),
		r.keydown(function (e) {
			switch (f = e.keyCode, e.keyCode) {
			case 38: // Up row
				e.preventDefault(),
				highlightOption(-1);
				break;
			case 40: // Down row
				e.preventDefault(),
				highlightOption(1);
				break;
			case 9: // tab
			case 13: // Enter
				!
				function () {
					var e = $("li.active", n)[0];
					if (!e) {
						var t = $("li", n);
						i.selectOnly ? 1 == t.length && (e = t[0]) : i.selectFirst && (e = t[0])
					}
					return !! e && (addOneOption(e), !0)
				} () ? (e.preventDefault(), "1" == $(r).data("isallowednew") && "" != $(r).text() && ($(r).prev().append(' <span rel="new"><span class="glyphicon glyphicon-remove-circle"></span> <i>' + $(r).text().trim() + '</i><input type="hidden" name="' + $(r).attr("name") + '[]" value="' + $(r).text().trim() + '" data-title="' + $(r).text().trim() + '" data-href="" /></span>'), $(r).text("").focus(), eFormTags($(r).parent()))) : (r.get(0).blur(), e.preventDefault());
				break;
			default: // Others
				c = -1,
				t && clearTimeout(t),
				t = setTimeout(function () { !
					function () {
						// 46=delete, 8=backspace, 32=spacebar
						if (46 == f || 8 < f && f < 32) return l.hide();
						var e = "input" == r.prop("tagName").toLowerCase() ? r.val() : r.text();
						if (e == s) return;
						(s = e).length >= i.minChars ? (h.addClass(i.loadingClass), function (t) {
							i.matchCase || (t = t.toLowerCase());
							var e = i.cacheLength ? fetchData(t) : null;
							e ? addOptions(t, e, r) : "string" == typeof i.url && 0 < i.url.length ? $.get(T(t), function (e) {
								e = Separate(e),
								F(t, e),
								$("#"+modalField).val(t),
								addOptions(t, e, r)			// t = keyword, e = result, r = ac_input
							}) : h.removeClass(i.loadingClass)
						} (e)) : (h.removeClass(i.loadingClass), l.hide())
					} ()
				},
				i.delay)
			}
		}).focus(function () {
			u = !0
		}).blur(function () {
			u = !1,
			function () {
				t && clearTimeout(t);
				t = setTimeout(L, 200)
			} ()
		}),
		L(),
		this.flushCache = function () {
			d()
		},
		this.setExtraParams = function (e) {
			i.extraParams = e
		},
		this.findValue = function () {
			var t = r.val();
			i.matchCase || (t = t.toLowerCase());
			var e = i.cacheLength ? fetchData(t) : null;
			e ? k(t, e) : "string" == typeof i.url && 0 < i.url.length ? $.get(T(t), function (e) {
				e = Separate(e),
				F(t, e),
				k(t, e)
			}) : k(t, null)
		}
	},
	// Mulai membuat autocomplete
	$.fn.autocomplete = function (c, d) {
		if ("string" != typeof c || "flush" != c && "clean" != c && "clear" != c) return this.each(function () {
			$(this).prop("id") || $(this).prop("id", $(this).prop("name").replace(/[^0-9a-z]/gi, "_"));
			var e = c || $(this).data(),
			f = $(this).prop("id"),
			g = encodeURIComponent($(this).data("token")),
			h = $(this).data("url") || _URL + "user/selecttable";
			if (h += -1 === h.indexOf("?") ? "?" : "&", 0 == $("#" + f + "_ac").length) {
				// Mulai membuat inputfield baru yang akan ditampilkan
				var d = $(this).get(0),
				newInput = '<div class="input-group" id="'+f+'_group">\
					<input type="text" class="'+d.className+'" placeholder="'+d.placeholder+'" value="" id="'+f+'_ac" />\
					<span class="input-group-addon" id="'+f+'_addon" \
					data-toggle="modal" href="#'+f+'modal_id" style="cursor: pointer;">\
					<span class="caret"></span></span>\
				</div>\
				<div class="modal fade" tabindex="-1" id="'+f+'modal_id">\
					<div class="modal-dialog">\
						<div class="modal-content">\
							<div class="modal-header">\
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
								<div class="form-group">\
									<input type="text" class="form-control" placeholder="'+d.placeholder+'" id="'+f+'modal_ac" />\
								</div>\
							</div>\
							<div class="modal-body">\
								<table class="table table-striped table-bordered table-hover">\
									<tbody id="'+f+'modal_table"></tbody>\
								</table>\
							</div>\
						</div>\
					</div>\
				</div>';
				$(this).before(newInput),
				$(newInput).click(function () {
					$(this).select()
				}),
				$(this).data("token", ""),
				$(this).hide(),
				$('#'+f+'modal_id').on('shown.bs.modal', function () {
					$('#'+f+'modal_ac').focus()
				}).on("hide.bs.modal", function(){
					$('#'+f+'_ac').focus()
				// }),
				// $('#'+f+'modal_ac').on("keydown", function(e){
				// 	var key = ('which' in e) ? e.which : e.keyCode;
				// 	if (key == 13) {
				// 		e.preventDefault();
				// 	}
				})
			}
			if ("" != $(this).val() && "" == $("#" + f + "_ac").val() && (e.value ? newInput.value = e.value : ($(this).data("parent") && (g += "&parent=" + $("#" + $(this).closest("form").attr("name") + "_" + $(this).data("parent")).val()), $.get(h + "id=" + $(this).val() + "&q=1&token=" + g, function (e) {
				var t = e.split("|");
				t[1] && $("#" + f + "_ac").val(t[1])
			}))), $(this).data("parent")) {
				var i = $(this).closest("form").find('input[name="' + $(this).data("parent") + '"]');
				i.length && (e.parent = i)
			}
			e.url = h + "token=" + g + "&q=",
			e.data = "object" == typeof d && d.constructor == Array ? d : null,
			e.inputClass = e.inputclass || "ac_input",
			e.resultsClass = e.resultsclass || "ac_results",
			e.loadingClass = e.loadingclass || "ac_loading",
			e.lineSeparator = e.lineseparator || "\n",
			e.cellSeparator = e.cellseparator || "|",
			e.minChars = e.minchars || 2,
			e.delay = e.delay || 10,
			e.matchCase = e.matchcase || 0,
			e.matchSubset = e.matchsubset || 1,
			e.matchContains = e.matchcontains || 1,
			e.cacheLength = e.cachelength || 10,
			e.mustMatch = e.mustmatch || 0,
			e.extraParams = e.extraparams || {},
			e.selectFirst = e.selectfirst || !0,
			e.selectOnly = e.selectonly || !1,
			e.maxItemsToShow = e.maxitemstoshow || -1,
			e.autoFill = e.autofill || !1,
			e.mainName = f;
			e.onFindValue = e.onfind ||
			function (e) {
				if (null == e) return alert("No match!");
				if (e.extra) {
					$("#" + f + "_ac").val(e.extra[0]),
					$("#" + f).val(e.extra[1]).trigger("change");
					var t, a = $("#" + f).closest("form").attr("name"),
					n = !1,
					r = $("#" + f).attr("name");
					if ((t = $('input[data-parent="' + r.save() + '"]')).length) n = $(t).prop("id");
					else r = r.replace(new RegExp("^" + a + "_"), ""),
					(t = $('input[data-parent="' + r.save() + '"]')).length && (n = $(t).prop("id"));
					n && ($("#" + n).val(""), $("#" + n + "_ac").get(0).autocompleter.flushCache(), $("#" + n + "_ac").val("").select())
				} else e.selectValue
			},
			e.onItemSelect = e.onselect || e.onFindValue,
			e.formatItem = e.format ||
			function (e) {
				return e[0]
			},
			e.width = parseInt(e.width, 10) || 0,
			window.isautocompleteloaded || ($("body").prepend('<style type="text/css">.' + e.loadingClass + '{background : #EBEBEB url("' + _URL + 'templates/admin/bootstrap/img/spinner.gif") right center no-repeat}</style>'), window.isautocompleteloaded = 1),
			"string" == typeof e.onFindValue && (e.onFindValue = eval("(" + e.onFindValue + ")")),
			"string" == typeof e.onItemSelect && (e.onItemSelect = eval("(" + e.onItemSelect + ")")),
			"string" == typeof e.formatItem && (e.formatItem = eval("(" + e.formatItem + ")")),
			"string" == typeof e.data && (e.data = eval("(" + e.data + ")")),
			"string" == typeof e.extraParams && (e.extraParams = eval("(" + e.extraParams + ")")),
			new $.autocomplete($("#" + f + "_ac").get(0), e)
		}),
		this;
		var a = $(this).prop("id"),
		A = a.replace(/[^0-9a-z]/gi, "_");
		return a != A && ($(this).prop("id", A), a = A),
		$("#" + a + "_ac").unbind(),
		$("#" + a + "_ac").get(0).autocompleter.flushCache(),
		!1
	},
	$.fn.autocompleteArray = function (e, t) {
		return this.autocomplete(t, e)
	},
	$.fn.indexOf = function (e) {
		for (var t = 0; t < this.length; t++) if (this[t] == e) return t;
		return -1
	},
	$.fn.etags = function (t, e) {
		return "string" != typeof t || "flush" != t && "clean" != t && "clear" != t ? (this.each(function () {
			var n = n || $(this).get(0),
			e = t || $(this).data();
			void 0 === e.url ? e.url = _URL + "user/tags?token=" + $(this).data("token") + "&q=" : (e.url += -1 === e.url.indexOf("?") ? "?" : "&", e.url += "token=" + $(this).data("token") + "&q="),
			e.data = "object" == typeof n && n.constructor == Array ? n : null,
			e.inputClass = e.inputclass || "ac_input",
			e.resultsClass = e.resultsclass || "ac_results",
			e.loadingClass = e.loadingclass || "ac_loading",
			e.lineSeparator = e.lineseparator || "\n",
			e.cellSeparator = e.cellseparator || "|",
			e.minChars = e.minchars || 2,
			e.delay = e.delay || 10,
			e.matchCase = e.matchcase || 0,
			e.matchSubset = e.matchsubset || 1,
			e.matchContains = e.matchcontains || 1,
			e.cacheLength = e.cachelength || 10,
			e.mustMatch = e.mustmatch || 0,
			e.extraParams = e.extraparams || {},
			e.selectFirst = e.selectfirst || !0,
			e.selectOnly = e.selectonly || !1,
			e.maxItemsToShow = e.maxitemstoshow || -1,
			e.autoFill = e.autofill || !0,
			e.onFindValue = e.onfind ||
			function (e) {
				if (null == e) return alert("No match!");
				if (e.extra) {
					var t = $(n).data("href") ? $(n).data("href") + e.extra[1] : "",
					a = t ? '<a href="' + t + '">' + e.extra[0] + "</a>" : "<i>" + e.extra[0] + "</i>";
					$(n).prev().append(' <span rel="new"><span class="glyphicon glyphicon-remove-circle"></span> ' + a + '<input type="hidden" name="' + $(n).attr("name") + '[]" value="' + e.extra[1] + '" data-title="' + e.extra[0] + '" data-href="' + t + '"/></span>'),
					$(n).text("").focus(),
					eFormTags($(n).parent())
				} else e.selectValue
			},
			e.onItemSelect = e.onselect || e.onFindValue,
			e.formatItem = e.format ||
			function (e) {
				return e[0]
			},
			e.width = parseInt(e.width, 10) || 0,
			window.isetagsloaded || ($("body").prepend('<style type="text/css">.form-control.tags.' + e.loadingClass + '{background:#EBEBEB url("' + _URL + 'templates/admin/bootstrap/img/spinner.gif") right center no-repeat}div.form-control.tags{min-height:34px;height:auto;}div.form-control.tags>span{margin:0 5px 2px 0;}div.form-control.tags>span>span{padding:2px 5px;margin:2px 0;background:#EBEBEB;border-radius:5px;}div.form-control.tags>span>span .glyphicon{cursor:pointer;}#idxbbc {height: auto !important;}</style>'), window.isetagsloaded = 1),
			new $.autocomplete(this, e)
		}), this) : ($(this).unbind(), $(this).get(0).autocompleter.flushCache(), !1)
	},
	$.fn.etagsArray = function (e, t) {
		return this.etags(t, e)
	},
	$('input[rel="ac"]').autocomplete(), 0 < $(".form-control.tags").length) {
		var r = $(".form-control.tags");
		$.each(r, function (e) {
			eFormTags($(r[e]))
		}),
		$(".form-control.tags").click(function () {
			$("span:last-child", this).focus()
		}),
		$(".form-control.tags > span:last-child").etags()
	}
	function eFormTags(e) {
		var t = e.children("span").first().children("input");
		if (t.length) {
			for (var a = "", n = "", r = e.children("span").first(), l = e.children("span").last(), s = 0; s < t.length; s++) {
				$(t[s]).remove();
				var o = $(t[s]).attr("title");
				o || (o = $(t[s]).data("title")),
				n = o,
				a += '<span><span class="glyphicon glyphicon-remove-circle"></span> ' + (o = l.data("href") ? '<a href="' + l.data("href") + $(t[s]).val() + '">' + o + "</a>" : "<i>" + o + "</i>") + '<input type="hidden" name="' + l.attr("name") + '[]" value="' + $(t[s]).val() + '" data-title="' + n + '" /></span>'
			}
			r.append(a)
		}
		e.find('[rel="new"]').length ? (a = (t = e.find('[rel="new"]')).find("a"), n = t.find(".glyphicon"), t.removeAttr("rel")) : (a = e.find("a"), n = e.find(".glyphicon")),
		window.FormTags ? window.FormTags(a, n) : FormTags(a, n)
	}
	function FormTags(e, t) {
		e.on("click", function (e) {
			var t = $(this).attr("href");
			t && (e.preventDefault(), "undefined" != typeof adminLink ? adminLink(t) : (t += /\?/.test(t) ? "&" : "?", t += "return=" + escape(document.location.href), document.location.href = t))
		}),
		t.on("click", function (e) {
			e.preventDefault(),
			confirm("Do you want to remove this tags?") && $(this).parent().remove()
		})
	}
}),
String.prototype.save = function () {
	return this.replace(/\]/g, "\\\\]").replace(/\[/g, "\\\\[")
};