//基础框架
(function() {
	var $C = window.$ = jQuery;
	var Cute = window.Cute = {
		set$: function($){
			window.$C = $;
		},
		config: {
			SITEURL: "",
			RESOURCEURL: "",
			SCRIPTPATH: "/static/js/",
			SERVICEURL: "/api/",
			DEBUG: true
		},
		init: function() {
			// 可以通过在 url 上加 ?cute-DEBUG 来开启 DEBUG 模式
			if (window.location.search && window.location.search.indexOf('cute-DEBUG') !== -1) {
				this.config.DEBUG = true;
			}
			return this;
		},
		log: function(msg, src) {
			if (this.config.DEBUG) {
				if (src) {
					msg = src + ': ' + msg;
				}
				if (window['console'] !== undefined && console.log) {
					console.log(msg);
				}
			}
			return this;
		},
		error: function(msg) {
			if (this.config.DEBUG) {
				throw msg;
			}
		},
		common: {
			confirm: function(msg, url, options) {
				if (arguments.length < 2) {
					url = msg;
					msg = "真的要删除吗？";
				}
				new Cute.ui.dialog().confirm(msg, $C.extend(options || {}, {
					yes: function() {
						if (url.constructor == String) {
							location.href = url;
							return false;
						} else if (url.constructor == Function) {
							url();
							return false;
						}
						return true;
					}
				}));
				return false;
			},
			copy: function(txt) {
				if (window.clipboardData) {
					window.clipboardData.clearData();
					window.clipboardData.setData("Text", txt);
				} else if (navigator.userAgent.indexOf("Opera") != -1) {
					window.location = txt;
				} else if (window.netscape) {
					try {
						netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
					} catch (e) {
						alert("您的firefox安全限制限制您进行剪贴板操作，请打开'about:config'将signed.applets.codebase_principal_support'设置为true'之后重试");
						return false;
					}
					var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
					if (!clip) return false;
					var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
					if (!trans) return false;
					trans.addDataFlavor('text/unicode');
					var str = new Object();
					var len = new Object();
					var str = Components.classes['@mozilla.org/supports-string;1'].createInstance(Components.interfaces.nsISupportsString);
					var copytext = txt;
					str.data = copytext;
					trans.setTransferData("text/unicode", str, copytext.length * 2);
					var clipid = Components.interfaces.nsIClipboard;
					if (!clip) return false;
					clip.setData(trans, null, clipid.kGlobalClipboard);
				} else {
					return false;
				}
				return true;
			},
            flashCopy: function(obj, txt, success) {
            	var that = this;
                $C(obj).each(function() {
                    var content = txt;
                    if ($C.isFunction(txt)) {
                        content = txt.call(this);
                    }
                    // if($C.support.cssFloat){
	                    if (typeof ZeroClipboard == "undefined") return false;
	                    this.clip = null;
	                    ZeroClipboard.setMoviePath(Cute.config.RESOURCEURL + "/js/ZeroClipboard.swf");
	                    this.clip = new ZeroClipboard.Client();
	                    this.clip.setHandCursor(true);
	                    this.clip.setText(content);
	                    this.clip.addEventListener('onComplete', success);
	                    $C(window).resize(function() {
	                        this.clip.reposition();
	                    } .bind(this));
	                    this.clip.glue(this);
	                // }else{
	                // 	$C(this).on('click', function(){
	                // 		that.copy(content);
	                // 		success();
	                // 	});
	                // }
                    return this.clip;
                });
            },
			checkAll: function(obj, elName) {
				$C(obj).closest("form").find("input:checkbox[name=" + elName + "]").prop("checked", $C(obj).porp("checked"));
			},
			insertSelection: function(obj, str) {
				var obj = $C(obj)[0];
				obj.focus();
				var pos = this.getRangPos(obj);
				if(pos.start == 0 && pos.end == 0){
					this.selectText(obj, $(obj).val().length);
				}
				if (typeof document.selection != "undefined") {
					document.selection.createRange().text = str;
					obj.createTextRange().duplicate().moveStart("character", -str.length);
				} else {
					var tclen = obj.value.length;
					var m = obj.selectionStart;
					obj.value = obj.value.substr(0, obj.selectionStart) + str + obj.value.substring(obj.selectionStart, tclen);
					obj.selectionStart = m + str.length;
					obj.setSelectionRange(m + str.length, m + str.length);
				}
			},
			selectText: function(obj, start, end) {
				var obj = $C(obj)[0];
				obj.focus();
				if(end == undefined){
					end = start;
				}
				if (typeof document.selection != "undefined") {
					var range = obj.createTextRange();
					range.collapse(true);
					range.moveEnd("character", end);
					range.moveStart("character", start);
					range.select();
				} else {
					obj.setSelectionRange(start, end);  //设光标
				}
			},
			getRangPos: function(obj) {
				var obj = $C(obj)[0];
				var pos = {};
				if (typeof document.selection != "undefined") {
					var range = document.selection.createRange();
					if (obj != range.parentElement()) return
					var range_all = document.body.createTextRange();
					range_all.moveToElementText(obj);
					for (var sel_start = 0; range_all.compareEndPoints('StartToStart', range) < 0; sel_start++) {
						range_all.moveStart('character', 1);
					}
					for (var i = 0; i <= sel_start; i++) {
						if (obj.value.charAt(i) == '\n')
							sel_start++;
					}
					pos.start = sel_start;
					var range_all = document.body.createTextRange();
					range_all.moveToElementText(obj);
					for (var sel_end = 0; range_all.compareEndPoints('StartToEnd', range) < 0; sel_end++)
						range_all.moveStart('character', 1);
					for (var i = 0; i <= sel_end; i++) {
						if (obj.value.charAt(i) == '\n')
							sel_end++;
					}
					pos.end = sel_end;
				} else if (obj.selectionStart || obj.selectionStart == '0') {
					pos.start = obj.selectionStart;
					pos.end = obj.selectionEnd;
				}
				return pos;
			},
			scrolling: function(obj, options, func) {
				var defaults = {
					target: 1,
					timer: 800,
					offset: 0,
					parent: 'html,body'
				};
				func = func || $C.noop;
				var o = $C.extend(defaults, options || {});
				$C(obj).each(function(i) {
					switch (o.target) {
						case 1:
							var targetTop = $C(obj).offset().top + o.offset;
							$C(o.parent).animate({
								scrollTop: targetTop
							}, o.timer, Cute.Function.bind(func, obj, o));
							break;
						case 2:
							var targetLeft = $C(obj).offset().left + o.offset;
							$C(o.parent).animate({
								scrollLeft: targetLeft
							}, o.timer, Cute.Function.bind(func, obj, o));
							break;
					}
					return false;
				});
				return this;
			}
		},
		ui: {},
		plugin: {}
	};

	Cute.params = {	//参数操作
		init: function(str) {
			this.list = {};
			var params = str ? str : location.search;
			$C.each(params.match(/(?:[\?|\&])[^\=]+=[^\&|#|$]*/gi) || [], function(n, item) {
				var _item = item.substring(1);
				var _key = _item.split("=", 1)[0];
				var _value = _item.replace(eval("/" + _key + "=/i"), "");
				this.list[_key.toLowerCase()] = Cute.String.urldecode(_value);
			} .bind(this));
			return this;
		},
		get: function(item) {
			if (typeof this.list == "undefined") this.init();
			var _item = this.list[item.toLowerCase()];
			return _item ? _item : "";
		},
		set: function(options) {
			if (typeof this.list == "undefined") this.init();
			$C.extend(true, this.list, options || {});
			return this;
		},
		serialize: function() {
			if (typeof this.list == "undefined") this.init();
			return $C.param(this.list, true);
		},
		hashString: function(item) {
			if (!item) return location.hash.substring(1);
			var sValue = location.hash.match(new RegExp("[\#\&]" + item + "=([^\&]*)(\&?)", "i"));
			sValue = sValue ? sValue[1] : "";
			return sValue == location.hash.substring(1) ? "" : sValue == undefined ? location.hash.substring(1) : Cute.String.urldecode(sValue);
		}
	};

	Cute.api = {	//接口调用方法
		ajax: function(type, action, data, callback, cache, async, options) {
			if (typeof data == 'function' && typeof callback == 'undefined') {
				callback = data;
				data = undefined;
			}
			var url = "";
			if(!options) options = {};
			if (action != undefined) {
				if (action.substring(0,1) == "/" || action.indexOf('http://') !== -1) {
					url = action;
					if(action.indexOf('http://') !== -1)
						options['dataType'] = 'jsonp';
				} else {
					url = Cute.config.SERVICEURL + '/' + action;
					if(Cute.config.SERVICEURL.substring(0,1) !== "/" && Cute.config.SERVICEURL.indexOf(location.hostname) == -1){
						if(!options) options = {};
						if(type == 'GET'){
							options['dataType'] = 'jsonp';
						}else if(type == 'POST'){
							options['crossDomain'] = true;
						}
					}
				}
			} else {
				url = location.pathname;
			}
			return $C.ajax($C.extend({
				url: url,
				data: data,
				async: typeof async !== "undefined" ? async : true,
				type: typeof type !== "undefined" ? type : "GET",
				//dataType: "json",
				ifModified: false,
				timeout: 20000,
				traditional: false,
				headers:{
				},
				cache: typeof cache != "undefined" ? cache : false,
				success: callback,
				//				dataFilter: function(data) {
				//					return eval("(" + data + ")");
				//				},
				error: function() {
					if (async == false) {
						new Cute.ui.dialog().alert("出错");
					}
					$C("#dialog_loading").remove();
					return false;
				},
				beforeSend: function(request) {
				}
			}, options || {}));
		},
		get: function(action, data, callback, cache, async, options) {
			return this.ajax("GET", action, data, callback, cache, async, options);
		},
		post: function(action, data, callback, cache, async, options) {
			return this.ajax("POST", action, data, callback, cache, async, options);
		}
	};

	Cute.Class = {
		/*
		*创建一个命名空间
		*/
		namespace: function(module) {
			var space = module.split('.');
			var s = '';
			for (var i in space) {
				if (space[i].constructor == String) {
					if (0 == s.length)
						s = space[i];
					else
						s += '.' + space[i];
					eval("if ((typeof(" + s + ")) == 'undefined') " + s + " = {};");
				}
			}
		},
		/*
		*创建一个类，并执行构造函数
		*/
		create: function() {
			var f = function() {
				this.initialize.apply(this, arguments);
			};
			for (var i = 0, il = arguments.length, it; i < il; i++) {
				it = arguments[i];
				if (it == null) continue;
				$C.extend(f.prototype, it);
			}
			return f;
		},
		/*
		*继承一个类，暂不支持多重继承
		*/
		inherit: function(superC, opt) {
			function temp() { };
			temp.prototype = superC.prototype;

			var f = function() {
				this.initialize.apply(this, arguments);
			};

			f.prototype = new temp();
			$C.extend(f.prototype, opt);
			f.prototype.superClass_ = superC.prototype;
			f.prototype.super_ = function() {
				this.superClass_.initialize.apply(this, arguments);
			};
			return f;
		}
	};
	Cute.Object = {
		/*
		* 对象的完全克隆
		*/
		clone: function(obj) {
			var con = obj.constructor, cloneObj = null;
			if (con == Object) {
				cloneObj = new con();
			} else if (con == Function) {
				return Cute.Function.clone(obj);
			} else cloneObj = new con(obj.valueOf());

			for (var it in obj) {
				if (cloneObj[it] != obj[it]) {
					if (typeof (obj[it]) != 'object') {
						cloneObj[it] = obj[it];
					} else {
						cloneObj[it] = arguments.callee(obj[it])
					}
				}
			}
			cloneObj.toString = obj.toString;
			cloneObj.valueOf = obj.valueOf;
			return cloneObj;
		}
	};
	Cute.Function = {
		before: function(self, fun){
			return function(){
				if(fun.apply(this, arguments) === false){
					return false;
				}
				return self.apply(this, arguments);
			}
		},
		after: function(self, fun){
			return function(){
				var ret = self.apply(this, arguments);
				if(ret)
					return ret;
				return fun.apply(this, arguments);
			}
		},
		timeout: function(fun, time) {
			return setTimeout(fun, time * 1000);
		},
		interval: function(fun, time) {
			return setInterval(fun, time * 1000);
		},
		//域绑定，可传参
		bind: function(fun) {
			var _this = arguments[1], args = [];
			for (var i = 2, il = arguments.length; i < il; i++) {
				args.push(arguments[i]);
			}
			return function() {
				var thisArgs = args.concat();
				for (var i = 0, il = arguments.length; i < il; i++) {
					thisArgs.push(arguments[i]);
				}
				return fun.apply(_this || this, thisArgs);
			}
		},
		// 域绑定，可传事件
		bindEvent: function(fun) {
			var _this = arguments[1], args = [];
			for (var i = 2, il = arguments.length; i < il; i++) {
				args.push(arguments[i]);
			}
			return function(e) {
				var thisArgs = args.concat();
				thisArgs.unshift(e || window.event);
				return fun.apply(_this || this, thisArgs);
			}
		},
		clone: function(fun) {
			var clone = function() {
				return fun.apply(this, arguments);
			};
			clone.prototype = fun.prototype;
			for (prototype in fun) {
				if (fun.hasOwnProperty(prototype) && prototype != 'prototype') {
					clone[prototype] = fun[prototype];
				}
			}
			return clone;
		}
	};
	Cute.Cookie = {
		get: function(name) {
			var v = document.cookie.match('(?:^|;)\\s*' + name + '=([^;]*)');
			return v ? decodeURIComponent(v[1]) : null;
		},
		set: function(name, value, expires, path, domain) {
			var str = name + "=" + encodeURIComponent(value);
			if (expires != null && expires != '') {
				if (expires == 0) {
					expires = 100 * 365 * 24 * 60 * 60;
				}
				var exp = new Date();
				exp.setTime(exp.getTime() + expires * 1000);
				str += "; expires=" + exp.toUTCString();
			}
			if (path) {
				str += "; path=" + path;
			}
			if (domain) {
				str += "; domain=" + domain;
			}
			document.cookie = str;
		},
		del: function(name, path, domain) {
			document.cookie = name + "=" +
			((path) ? "; path=" + path : "") +
			((domain) ? "; domain=" + domain : "") +
			"; expires=Thu, 01-Jan-70 00:00:01 GMT";
		}
	};
	Cute.Json = {
	    stringify: function(obj) {
	    	if (window['JSON']) {
	    		return JSON.stringify(obj);
	    	}else{
		        var t = typeof (obj);
		        if (t != "object" || obj === null) {
		            // simple data type
		            if (t == "string") obj = '"' + obj + '"';
		            return String(obj);
		        } else {
		            // recurse array or object
		            var n, v, json = [], arr = (obj && obj.constructor == Array);
		            for (n in obj) {
		                v = obj[n];
		                t = typeof(v);
		                if (obj.hasOwnProperty(n)) {
		                    if (t == "string") v = '"' + v + '"'; else if (t == "object" && v !== null) v = this.stringify(v);
		                    json.push((arr ? "" : '"' + n + '":') + String(v));
		                }
		            }
		            return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
		        }
			}
	    }
	};
	Cute.String = {
		//去除空格
		trim: function(str) {
			return str.replace(/^\s+|\s+$/g, '');
		},
		urldecode: decodeURIComponent,
		urlencode: encodeURIComponent,
		//过滤HTML
		filterHTML: function(str){
			return str.replace(/<\/?[^>]*>/g,'');
		},
		//格式化HTML
		escapeHTML: function(str) {
			return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		},
		//反格式化HTML
		unescapeHTML: function(str) {
			return str.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
		},
		// 取得字符的字节长度，汉字认为是两个字符
		byteLength: function(str) {
			return str.replace(/[^\x00-\xff]/g, "**").length;
		},
		//截断
		cut: function(str, length, padding){
			var r =/[^\x00-\xff]/g;
			if(str.replace(r, "mm").length > length){
				var m = Math.floor(length/2);
				for(var i=m; i<str .length; i++){
					if(str.substr(0, i).replace(r, "mm").length>=length){
						return str.substr(0, i) + padding;
					}
				}
			}
			return str;
		},
		rnd: function (a, b) {
            return Math.floor((b - a + 1) * Math.random() + a)
        },
		// 除去最后一个字符
		delLast: function(str) {
			return str.substring(0, str.length - 1);
		},
		// String to Int
		toInt: function(str) {
			return Math.floor(str);
		},
		// 取左边多少字符，中文两个字节
		left: function(str, n) {
			var s = str.replace(/\*/g, " ").replace(/[^\x00-\xff]/g, "**");
			s = s.slice(0, n).replace(/\*\*/g, " ").replace(/\*/g, "").length;
			return str.slice(0, s);
		},
		// 取右边多少字符，中文两个字节
		right: function(str, n) {
			var len = str.length;
			var s = str.replace(/\*/g, " ").replace(/[^\x00-\xff]/g, "**");
			s = s.slice(s.length - n, s.length).replace(/\*\*/g, " ").replace(/\*/g, "").length;
			return str.slice(len - s, len);
		},
		// 除去HTML标签
		removeHTML: function(str) {
			return str.replace(/<\/?[^>]+>/gi, '');
		},
		//"<div>{0}</div>{1}".format(txt0,txt1);
		format: function() {
			var str = arguments[0], args = [];
			for (var i = 1, il = arguments.length; i < il; i++) {
				args.push(arguments[i]);
			}
			return str.replace(/\{(\d+)\}/g, function(m, i) {
				return args[i];
			});
		},
		// toString(16)
		on16: function(str) {
			var a = [], i = 0;
			for (; i < str.length; ) a[i] = ("00" + str.charCodeAt(i++).toString(16)).slice(-4);
			return "\\u" + a.join("\\u");
		},
		// unString(16)
		un16: function(str) {
			return unescape(str.replace(/\\/g, "%"));
		},
		base64_encode: function(data) {
			var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
			var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
				ac = 0,
				enc = "",
				tmp_arr = [];

			if(!data) {
				return data;
			}

			do { // pack three octets into four hexets
				o1 = data.charCodeAt(i++);
				o2 = data.charCodeAt(i++);
				o3 = data.charCodeAt(i++);

				bits = o1 << 16 | o2 << 8 | o3;

				h1 = bits >> 18 & 0x3f;
				h2 = bits >> 12 & 0x3f;
				h3 = bits >> 6 & 0x3f;
				h4 = bits & 0x3f;

				// use hexets to index into b64, and append result to encoded string
				tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
			} while (i < data.length);

			enc = tmp_arr.join('');

			var r = data.length % 3;

			return(r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
		},
		base64_decode: function(data) {
		    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
		    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
		        ac = 0,
		        dec = "",
		        tmp_arr = [];

		    if (!data) {
		        return data;
		    }

		    data += '';

		    do { // unpack four hexets into three octets using index points in b64
		        h1 = b64.indexOf(data.charAt(i++));
		        h2 = b64.indexOf(data.charAt(i++));
		        h3 = b64.indexOf(data.charAt(i++));
		        h4 = b64.indexOf(data.charAt(i++));

		        bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

		        o1 = bits >> 16 & 0xff;
		        o2 = bits >> 8 & 0xff;
		        o3 = bits & 0xff;

		        if (h3 == 64) {
		            tmp_arr[ac++] = String.fromCharCode(o1);
		        } else if (h4 == 64) {
		            tmp_arr[ac++] = String.fromCharCode(o1, o2);
		        } else {
		            tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
		        }
		    } while (i < data.length);

		    dec = tmp_arr.join('');
		    dec = this.utf8_decode(dec);

		    return dec;
		},
		uniqid: function(prefix, more_entropy) {
		  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		  // +    revised by: Kankrelune (http://www.webfaktory.info/)
		  // %        note 1: Uses an internal counter (in php_js global) to avoid collision
		  // *     example 1: uniqid();
		  // *     returns 1: 'a30285b160c14'
		  // *     example 2: uniqid('foo');
		  // *     returns 2: 'fooa30285b1cd361'
		  // *     example 3: uniqid('bar', true);
		  // *     returns 3: 'bara20285b23dfd1.31879087'
		  if (typeof prefix == 'undefined') {
		    prefix = "";
		  }

		  var retId;
		  var formatSeed = function (seed, reqWidth) {
		    seed = parseInt(seed, 10).toString(16); // to hex str
		    if (reqWidth < seed.length) { // so long we split
		      return seed.slice(seed.length - reqWidth);
		    }
		    if (reqWidth > seed.length) { // so short we pad
		      return Array(1 + (reqWidth - seed.length)).join('0') + seed;
		    }
		    return seed;
		  };

		  // BEGIN REDUNDANT
		  if (!this.php_js) {
		    this.php_js = {};
		  }
		  // END REDUNDANT
		  if (!this.php_js.uniqidSeed) { // init seed with big random int
		    this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
		  }
		  this.php_js.uniqidSeed++;

		  retId = prefix; // start with prefix, add current milliseconds hex string
		  retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
		  retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
		  if (more_entropy) {
		    // for more entropy we add a float lower to 10
		    retId += (Math.random() * 10).toFixed(8).toString();
		  }

		  return retId;
		}
	};
	Cute.Array = {
		//	判断是否包含某个值或者对象
		include: function(arr, value) {
			if (arr.constructor != Array) return false;
			return this.index(arr, value) != -1;
		},
		//	判断一个对象在数组中的位置
		index: function(arr, value) {
			for (var i = 0, il = arr.length; i < il; i++) {
				if (arr[i] == value) return i;
			}
			return -1;
		},
		//	过滤重复项
		unique: function(arr) {
			if (arr.length && typeof (arr[0]) == 'object') {
				var len = arr.length;
				for (var i = 0, il = len; i < il; i++) {
					var it = arr[i];
					for (var j = len - 1; j > i; j--) {
						if (arr[j] == it) arr.splice(j, 1);
					}
				}
				return arr;
			} else {
				var result = [], hash = {};
				for (var i = 0, key; (key = arr[i]) != null; i++) {
					if (!hash[key]) {
						result.push(key);
						hash[key] = true;
					}
				}
				return result;
			}
		},
		//移去某一项
		remove: function(arr, o) {
			if (typeof o == 'number' && !Cute.Array.include(arr, o)) {
				arr.splice(o, 1);
			} else {
				var i = Cute.Array.index(arr, o);
				if (i >= 0) arr.splice(i, 1);
			}
			return arr;
		},
		//取随机一项
		random: function(arr) {
			var i = Math.round(Math.random() * (arr.length - 1));
			return arr[i];
		},
		//乱序
		shuffle: function(arr) {
			return arr.sort(function(a, b) {
				return Math.random() > .5 ? -1 : 1;
			});
		}
	};
	Cute.Date = {
		// new Date().format('yyyy年MM月dd日');
		format: function(date, f) {
			var o = {
				"M+": date.getMonth() + 1,
				"d+": date.getDate(),
				"h+": date.getHours(),
				"m+": date.getMinutes(),
				"s+": date.getSeconds(),
				"q+": Math.floor((date.getMonth() + 3) / 3),
				"S": date.getMilliseconds()
			};
			if (/(y+)/.test(f))
				f = f.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
			for (var k in o)
				if (new RegExp("(" + k + ")").test(f))
					f = f.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
			return f;
		}
	};
	Cute.Event = {
		out: function(el, name, func, canMore) {
			var callback = function(e) {
				var src = e.target,
					isIn = false;
				while (src) {
					if (src == el) {
						isIn = true;
						break;
					}
					src = src.parentNode;
				}
				if (!isIn) {
					func.call(el, e);
					if (!canMore) {
						$C.event.remove(document.body, name, c);
						if (el._EVENT && el._EVENT.out && el._EVENT.out.length) {
							var arr = el._EVENT.out;
							for (var i = 0, il = arr.length; i < il; i++) {
								if (arr[i].efunc == c && arr[i].name == name) {
									arr.splice(i, 1);
									return;
								}
							}
						}
					}
				}
			}
			var c = callback.bindEvent(window);
			if (!el._EVENT) {
				el._EVENT = {
					out: []
				}
			}
			el._EVENT.out.push({
				name: name,
				func: func,
				efunc: c
			});
			$C.event.add(document.body, name, c);
		},
		unout: function(el, name, func) {
			if (el._EVENT && el._EVENT.out && el._EVENT.out.length) {
				var arr = el._EVENT.out;
				if(func === undefined){
					$C.event.remove(document.body, name);
				}else{
					for (var i = 0; i < arr.length; i++) {
						if (func == arr[i].func && name == arr[i].name) {
							$C.event.remove(document.body, name,arr[i].efunc);
							arr.splice(i, 1);
							return;
						}
					}
				}
			}
		}
	};
	$C.extend(Cute, {
		isUndefined: function(o) {
			return o === undefined;
		},
		isBoolean: function(o) {
			return typeof o === 'boolean';
		},
		isString: function(o) {
			return typeof o === 'string';
		},
		isNumber: function(o) {
			return !isNaN(Number(o)) && isFinite(o);
		},
		include: function(url, callback, media) {
			var afile = url.toLowerCase().replace(/^\s|\s$/g, "").match(/([^\/\\]+)\.(\w+)$/);
			if (!afile) return false;
			switch (afile[2]) {
				case "css":
					var el = $C('<link rel="stylesheet" id="' + afile[1] + '" href="' + url + '" type="text/css" media="' + (media ? media : 'all') + '" />').appendTo("head");
					if (!$C.support.cssFloat) {
						el.load(function() {
							if (typeof callback == 'function') callback();
						});
					} else {
						var i = 0;
						var checkInterval = setInterval(function() {
							if ($C("head>link").index(el) != -1) {
								if (i < 10) clearInterval(checkInterval)
								if (typeof callback == 'function') callback();
								i++;
							}
						}, 200);
					}
					break;
				case "js":
					$C.ajax({
						global: false,
						cache: true,
						ifModified: true,
						dataType: "script",
						url: url,
						success: callback
					});
					break;
				default:
					break;
			}
		}
	}, true);

	Cute.Widget = {
		drag: function(obj, position, target, offset, func) {
			func = func || $C.noop;
			target = $C(target || obj);
			position = position || window;
			offset = offset || {
				x: 0,
				y: 0
			};
			return obj.css("cursor", "move").bind("mousedown.drag", function(e) {
				e.preventDefault();
				e.stopPropagation();
				//if (e.which && (e.which != 1)) return;
				//if (e.originalEvent.mouseHandled) { return; }
				if (document.defaultView) {
					var _top = document.defaultView.getComputedStyle(target[0], null).getPropertyValue("top");
					var _left = document.defaultView.getComputedStyle(target[0], null).getPropertyValue("left");
				} else {
					if (target[0].currentStyle) {
						var _top = target.css("top");
						var _left = target.css("left");
					}
				}
				var width = target.outerWidth(),
				height = target.outerHeight();
				if (position === window) {
					position = $C.browser.msie6 ? document.body : window;
					var mainW = $C(position).width() - offset.x,
					mainH = $C(position).height() - offset.y;
				} else {
					var mainW = $C(position).outerWidth() - offset.x,
					mainH = $C(position).outerHeight() - offset.y;
				}
				target.posX = e.pageX - parseInt(_left);
				target.posY = e.pageY - parseInt(_top);
				if (target[0].setCapture) target[0].setCapture();
				else if (window.captureEvents) window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
				$C(document).unbind(".drag").bind("mousemove.drag", function(e) {
					var posX = e.pageX - target.posX,
					posY = e.pageY - target.posY;
					target.css({
						left: function() {
							if (posX > 0 && posX + width < mainW)
								return posX;
							else if (posX <= 0)
								return offset.x;
							else if (posX + width >= mainW)
								return mainW - width
						},
						top: function() {
							if (posY > 0 && posY + height < mainH)
								return posY;
							else if (posY <= 0)
								return offset.y;
							else if (posY + height >= mainH)
								return mainH - height;
						}
					});
					func(_top, _left, width, height, posY, posX);
				}).bind("mouseup.drag", function(e) {
					if (target[0].releaseCapture) target[0].releaseCapture();
					else if (window.releaseEvents) window.releaseEvents(Event.MOUSEMOVE | Event.MOUSEUP);
					$C(this).unbind(".drag");
				});
			});
		}
	};
	var ext = function(target, src, is) {
		if (!target) target = {};
		for (var it in src) {
			if (is) {
				target[it] = Cute.Function.bind(function() {
					var c = arguments[0], f = arguments[1];
					var args = [this];
					for (var i = 2, il = arguments.length; i < il; i++) {
						args.push(arguments[i]);
					}
					return c[f].apply(c, args);
				}, null, src, it);
			} else {
				target[it] = src[it];
			}
		}
	};
	ext(window.Class = {}, Cute.Class, false);
	ext(Function.prototype, Cute.Function, true);
	ext(String.prototype, Cute.String, true);
	//ext(Array.prototype, Cute.Array, true);
	ext(Date.prototype, Cute.Date, true);
})();


jQuery.fn.extend({	//jQuery 扩展
	out: function(name, listener, canMore) {
		return this.each(function() {
			Cute.Event.out(this, name, listener, canMore);
		});
	},
	unout: function(name, listener) {
		return this.each(function() {
			Cute.Event.unout(this, name, listener);
		});
	},
	drag: function(position, target, offset, func) {
		Cute.Widget.drag(this,position, target, offset, func)
	},
	scrolling: function(options, func) {
		Cute.common.scrolling(this,options, func);
	}
});
jQuery.extend(true, {
	browser: {
		msie: /msie/.test(navigator.userAgent.toLowerCase()),
		msie6: /msie/.test(navigator.userAgent.toLowerCase()) && /MSIE 6\.0/i.test(window.navigator.userAgent) && !/MSIE 7\.0/i.test(window.navigator.userAgent) && !/MSIE 8\.0/i.test(window.navigator.userAgent)
	},
	support : {
		pjax : window.history && window.history.pushState && window.history.replaceState && !navigator.userAgent.match(/(iPod|iPhone|iPad|WebApps\/.+CFNetwork)/),
		storage : !!window.localStorage
	}
});