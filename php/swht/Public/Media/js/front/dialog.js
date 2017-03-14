//模态框
Cute.ui.dialog = Cute.Class.create({
    initialize: function(options) {
        this.opt = $.extend(true, {
            title: "",
            content: "",
            className: "",
            css: null,
            foot: "",
            width: 400,
            height: "",
            pos: false,
            mask: false,
            blur: false,
            esc: true,
            close: null,
            maximize: false,
            minimize: false,
            open: null,
            drag: true,
            buttons: [],
            config: null,
            parentEl: null,
            func: $.noop
        }, options || {});
        this.buttons = [];
        this._config = $.extend({}, this.config);
        return this;
    },
    config: {
        title: "div.title",
        main: ".d_main",
        head: ".d_header",
        body: ".d_body",
        foot: ".d_footer",
        content: ".d_content",
        button: ".d_footer .btn",
        closeEl: "a.btn_close, .d_close",
        frame: "iframe.iframe_content",
        dialogBox: ".dialog",
        mainButtonClass: "fbold",
        subButtonsClass: "btn_cancel",
        buttonClass: "btn",
        loaddingHtml: "<div class='loading_bar'></div>"
    },
    _getEls: function() {
        this.els = {
            title: $(this._config.title, this.pannel),
            main: $(this._config.main, this.pannel),
            head: $(this._config.head, this.pannel),
            body: $(this._config.body, this.pannel),
            foot: $(this._config.foot, this.pannel),
            content: $(this._config.content, this.pannel),
            closeEl: $(this._config.closeEl, this.pannel),
            dialogBox: $(this._config.dialogBox, this.pannel)
        }
    },
    _init: function() {
        $("#dialog_loading").remove();
        this.pannel = this.pannel || $('<div />').appendTo(this.opt.parentEl || document.body);
        if (this.opt.mask) {
            if ($.browser.msie6) {
                this.mask = this.mask || $('<iframe class="mask_layout" id="dialog_mask" scrolling="no" frameborder="0" hspace="0" frameSpacing="0" marginHeight="0" marginWidth="0" UNSELECTABLE="on"></iframe>').appendTo(document.body).attr("src", "/aboutblank.htm").hide();
            } else
                this.mask = this.mask || $('<div class="mask_layout" id="dialog_mask"></div>').appendTo(document.body).hide();
        }
        var _html = [];
        _html.push('<div class="dialog ');
        _html.push(this.opt.className);
        _html.push('" style="width:');
        _html.push(this.opt.width);
        _html.push('px;"><div class="d_layout"></div><div class="d_main">');
        if (typeof this.opt.title != null) {
            _html.push('<div class="d_header" style="display:none"><div class="d_header_tl"> </div><div class="d_header_tr"></div><h4 class="title">');
            _html.push(this.opt.title);
            _html.push('</h4>');
            _html.push('<div class="options">');
            if (this.opt.minimize) _html.push('<a href="javascript:void(0)" class="icon btn_minimize" title="最小化"></a>');
            if (this.opt.maximize) _html.push('<a href="javascript:void(0)" class="icon btn_maximize" title="最大化"></a>');
            if (this.opt.close) _html.push('<a href="javascript:void(0)" class="icon btn_close" title="关闭"></a>');
            _html.push('</div></div>');
        }
        _html.push('<div class="d_body" style="height:')
        _html.push(this.opt.height);
        _html.push('px;"><div class="d_content">');
        _html.push(this.opt.content);
        _html.push('</div></div>');
        _html.push('<div class="d_footer">');
        _html.push(this.opt.foot);
        _html.push('</div>');
        _html.push('</div></div>');
        this.pannel.hide().html(_html.join(""));
        this.body = $(this._config.body, this.pannel).length > 0 || this.pannel;
        this._getEls();
        this.els.dialogBox.data("dialog", this);
        this.opt.css && this.els.dialogBox.css(this.opt.css);
        this.setButtons(this.opt.buttons);
        this.inUse = true;
        this._regEvent();
        if (this.opt.height && this.opt.height != "auto") {
            this.els.body.css("overflow", "hidden");
        }
        if (this.opt.foot) {
            this.els.foot.show();
        }
        if (this.opt.title != null && this.opt.title) {
            this.els.head.show();
        }
    },
    resize: function(options) {
        if (options) {
            if (options.width)
                this.els.dialogBox.width(options.width);
            if (options.height)
                this.els.body.height(options.height);
        }
        if (this.opt.mask && !$.browser.msie6)
            this.els.dialogBox[0].style.position = "fixed";
        return this;
    },
    _regEvent: function() {
        var _dialog = this.els.dialogBox;
        if (this.els.closeEl && this.els.closeEl.length > 0) {
            this.els.closeEl.click(this.close.bind(this));
        }
        $(document).bind('keydown', this._keyEvent.bindEvent(this));
        $(window).resize(function() {
            if (!this.windowSize) {
                this.windowSize = {
                    width: $(window).width(),
                    height: $(window).height()
                };
            }
            if ($.browser.msie) { //ie resize bug
                if ($(window).width() == this.windowSize.width && $(window).height() == this.windowSize.height)
                    return;
            }
            this.setPos.call(this, this.opt.pos);
        }.bind(this));
        if (this.opt.drag) this.els.head.drag(window, _dialog, {
            x: 10,
            y: 10
        });
        if (this.opt.blur) {
            this._outHandler = function(e) {
                if (this.pannel.has(e.target).length == 0)!this.hideStatus && this.close.bind(this)();
            }.bind(this);
            setTimeout(function() {
                $(document).on('click.dialog', this._outHandler);
            }.bind(this), 10);
        }
    },
    _show: function() {
        this.pannel.show();
        this.resize();
        this.setPos(this.opt.pos);
        if (this.opt.mask) {
            this.mask.show();
        }
        if (this.opt.close && this.opt.close.time) {
            this.close(this.opt.close.time);
        }
        if (this._mainButton != undefined && this.buttons[this._mainButton] && this.buttons[this._mainButton].length > 0) {
            this.buttons[this._mainButton].focus();
        }
        if (this.opt.open && this.opt.open.callback) {
            this.opt.open.callback.bind(this)();
        }
        return this;
    },
    _close: function() {
        this.inUse = false;
        if (this.opt.close && this.opt.close.callback) {
            this.opt.close.callback.bind(this)();
        }
        if (this.opt.mask && this.mask && this.mask.length > 0) {
            this.mask.remove();
        }
        if (this.opt.blur) {
            $(document).off('click.dialog', this._outHandler || false);
        }
        if (this.pannel && this.pannel.length > 0) {
            this.pannel.remove();
            this._clearDom();
        }
    },
    _clearDom: function() {
        this.els.dialogBox.removeData("dialog");
        this.els = null;
        this.body = null;
        this.pannel = null;
        this.buttons = null;
        this.mask = null;
        this.timer = null;
    },
    _keyEvent: function(e) {
        if (e.keyCode == 27 && this.inUse && this.opt.esc) {
            this.close();
        }
    },
    setClassName: function(name, reset) {
        reset = reset || false;
        if (reset)
            this.els.dialogBox.attr("class", name);
        else
            this.els.dialogBox.addClass(name);
        return this;
    },
    setButtons: function(_buttons) {
        if (_buttons && _buttons != [] && _buttons != {}) {
            if (_buttons.constructor == Object) {
                _buttons = [_buttons];
            }
            if (_buttons.length > 0) {
                $.each(_buttons, function(i, item) {
                    if (item && item.constructor == String) {
                        var _title = item;
                        item = {};
                        item.title = _title;
                        item.classType = this._config.subButtonsClass;
                        item.type = '';
                    }
                    if (!item.type) {
                        item.type = '';
                    }
                    if (item && item.constructor == Object) {
                        item.classType = item.type.indexOf("main") > -1 ? this._config.mainButtonClass : this._config.subButtonsClass;
                        item.buttonType = item.form ? item.form : 'button';
                    }
                    this.setFoot($("<button type='" + item.buttonType + "' class='" + this._config.buttonClass + " " + item.classType + "' title='" + item.title + "' hideFocus='true'><span>" + item.title + "</span></button>"));
                }.bind(this));
            }
            var buttons = this.pannel.find(this._config.button);
            if (buttons.length > 0) {
                this.buttons = [];
                buttons.each(function(i, item) {
                    if (_buttons[i]) {
                        this.buttons.push($(item));
                        if (_buttons[i].func && _buttons[i].func.constructor == Function) {
                            $(item).click(_buttons[i].func.bind(this));
                        }
                        if (_buttons[i].close == true) {
                            $(item).click(this.close.bind(this));
                        }
                        if (_buttons[i].focus || _buttons[i].type == 'main') {
                            if (this.pannel.is(":visible")) {
                                $(item).find('button').focus();
                            } else {
                                this._mainButton = i;
                            }
                        }
                    }
                }.bind(this));
            }
        } else {
            this.setFoot(this.opt.foot);
            this._mainButton = undefined;
        }
        return this;
    },
    setPos: function(pos) {
        if (!this.inUse) {
            return;
        };
        var pannelBox = (this.els.dialogBox && this.els.dialogBox.length > 0) ? this.els.dialogBox : this.pannel;
        if (pos) {
            pannelBox.css(pos);
            this.opt.pos = pos;
        } else {
            if (this.opt.parentEl) {
                pannelBox.css({
                    "top": "auto",
                    "left": "auto",
                    "bottom": "auto",
                    "right": "auto"
                });
            } else {
                var top = pannelBox.offset().top;
                var dHeight = pannelBox.height() == 0 ? 180 : pannelBox.height();
                var dWidth = pannelBox.width() == 0 ? 180 : pannelBox.width();
                var bHeight = $(window).height();
                var bWidth = $(window).width();
                var bTop = (this.opt.mask && !$.browser.msie6) ? 0 : $(document).scrollTop();
                pannelBox.css("left", (bWidth - dWidth) / 2 + "px");
                if (dHeight < bHeight - 30) {
                    pannelBox.css("top", (bHeight - dHeight) / 2 + bTop + "px");
                } else {
                    pannelBox.css("top", "30px");
                }
            }
        }
        return this;
    },
    setTitle: function(html) {
        if (this.els.title && this.els.title.length > 0) {
            this.els.title.html(html);
        }
        return this;
    },
    setFoot: function(html, isreset) {
        if (this.els.foot && this.els.foot.length > 0) {
            if ((html.constructor == Object && html.length == 0) || (html.constructor == String && html.trim() == "")) {
                this.els.foot.empty().hide();
                this._mainButton = null;
                return this;
            } else {
                this.els.foot.show();
            }
            if (isreset)
                this.els.foot.html(html);
            else {
                this.els.foot.append(html);
            }
        }
        return this;
    },
    setContent: function(html) {
        if (this.els.body && this.els.body.length > 0 && html) {
            if (html.constructor == Object)
                this.els.content.append(html);
            else
                this.els.content.html(html);
        }
        this.setPos(this.opt.pos);
        var _iframe = this.els.content.find(this.config.frame);
        if (_iframe.length > 0) {
            _iframe.css('height', this.opt.height + "px");
        }
        return this;
    },
    setHtml: function(html) {
        if (this.els.body && this.els.body.length > 0) {
            this.els.body.empty().append(html);
        }
        this.setPos(this.opt.pos);
        var _iframe = this.els.body.find(this.config.frame);
        if (_iframe.length > 0) {
            _iframe.css('height', this.opt.height + "px");
        }
        return this;
    },
    _optionsExtend: function(opt, options) {
        var _options = options;
        if (options.buttons) {
            var _temp = _options.buttons;
            delete _options.buttons;
            if (_temp.constructor == Array) {
                if (!opt.buttons) {
                    opt.buttons = [];
                } else if (opt.buttons.constructor == Object) {
                    opt.buttons = [opt.buttons];
                };
                for (var i = 0; i < _temp.length; i++) {
                    opt.buttons[i] = $.extend(opt.buttons[i], _temp[i]);
                }
            } else if (_temp.constructor == Object) {
                if (!opt.buttons) {
                    opt.buttons = {};
                };
                opt.buttons = $.extend(opt.buttons, _temp)
            }
        };
        if (options.close) {
            var _temp = _options.close;
            delete _options.close;
            if (!opt.close) {
                opt.close = {}
            };
            opt.close = $.extend(opt.close, _temp);
        };
        return $.extend(opt, _options);
    },
    show: function() {
        this.inUse = true;
        if (this.timer) clearTimeout(this.timer);
        if (this.opt.open && this.opt.open.time) {
            this.show.timeout(this.opt.open.time);
        } else {
            this._show();
        }
        //if (this.opt.mask) $(document).bind("DOMMouseScroll.dialog", function() { return false; });
        this.hideStatus = false;
        return this;
    },
    hide: function(time) {
        this.inUse = false;
        if (time && time.constructor == Number) {
            this.timer = this.hide.bind(this).timeout(time);
            return;
        }
        this.pannel.hide();
        if (this.mask) {
            this.mask.hide();
        }
        //if (this.opt.mask) $(document).unbind("DOMMouseScroll.dialog");
        this.hideStatus = true;
        return this;
    },
    toggle: function() {
        if (this.hideStatus)
            return this.show.call(this);
        else
            return this.hide.call(this);
    },
    close: function(time) {
        if (time && time.constructor == Number) {
            if (!$.browser.msie && this.opt.close.duration) {
                this.timer = function() {
                    this.els.dialogBox.animate({
                        opacity: 0.1
                    }, function() {
                        this.close.bind(this)();
                    }.bind(this));
                }.bind(this).timeout(time);
            } else {
                this.timer = this.close.bind(this).timeout(time);
            }
            return;
        }
        //if (this.opt.mask) $(document).unbind("DOMMouseScroll.dialog");
        clearTimeout(this.timer);
        this._close();
    },
    setClose: function(num) {
        var _num = num || 2;
        setTimeout(function() {
            this.close();
        }.bind(this), _num * 1000);
    },
    setCloseOptions: function(options) {
        if (!this.opt) this.opt = {};
        this.opt.close = options
    },
    alert: function(info, options) {
        var options = options || {};
        this.opt.content = info;
        this.opt.mask = true;
        this.opt.buttons = {
            title: '确定',
            type: 'main',
            close: true,
            func: options.callback || $.noop
        };
        this.opt.title = "提示";
        this._optionsExtend(this.opt, options);
        this._init();
        this.show();
        return this;
    },
    notice: function(info, options) {
        var options = options || {};
        this.opt.content = info;
        this.opt.mask = true;
        this.opt.close = {
            duration: true,
            time: 2
        };
        this.opt.buttons = {
            title: '关闭',
            type: 'main',
            close: true,
            func: options.callback || $.noop
        };
        this.opt.title = "提示";
        this._optionsExtend(this.opt, options);
        this._init();
        this.show();
        return this;
    },
    confirm: function(info, options) {
        var options = options || {};
        this.opt.content = info;
        this.opt.mask = true;
        this.opt.buttons = [{
            title: '确定',
            type: 'main',
            close: true,
            func: options.yes || $.noop
        }, {
            title: '取消',
            type: 'cancel',
            close: true,
            func: options.no || $.noop
        }];
        this.opt.title = "提示";
        this._optionsExtend(this.opt, options);
        this._init();
        this.show();
        return this;
    },
    loading: function(title, options) {
        var options = options || {};
        this.opt.title = title || "加载中...";
        this.opt.drag = false;
        this.opt.content = this._config.loaddingHtml;
        this.opt.close = {};
        this.opt.buttons = [];
        this._optionsExtend(this.opt, options);
        this._init();
        this.pannel.attr("id", "dialog_loading");
        this.show();
        return this;
    },
    ajax: function(title, options) {
        var options = options || {};
        if (title) {
            this.opt.title = title;
        }
        this.opt.content = this._config.loaddingHtml;
        this.opt.mask = true;
        this.opt.close = {};
        this._optionsExtend(this.opt, options);
        if (this.opt.action) {
            this.ajax_request = Cute.api.get(this.opt.action, this.opt.params || {}, function(html) {
                this._init();
                this.setContent($.trim(html));
                this.show();
            }.bind(this), false, true, this.opt.ajaxoptions || {});
        }
        return this;
    },
    layer: function(title, options) {
        var options = options || {};
        this.opt.title = title;
        this.opt.close = true;
        var olayer = $(options.content);
        options.content = "";
        this._optionsExtend(this.opt, options);
        this._init();
        if (olayer.data("tpl_dialog") == null) {
            olayer.data("tpl_dialog", $("<div/>").wrapInner(olayer).contents().clone(true));
        }
        this.setHtml(olayer.data("tpl_dialog"));
        this.show();
        return this;
    },
    iframe: function(title, options) {
        var options = options || {};
        if (title) {
            this.opt.title = title;
        }
        this.opt.close = {};
        this.opt.mask = true;
        this.opt.content = this._config.loaddingHtml;
        this.opt.buttons = options.buttons || [];
        this._optionsExtend(this.opt, options);
        this._init();
        $(this._config.loaddingHtml, this.dialogBox).remove();
        if (this.opt.url) {
            this.setHtml($('<iframe />', {
                "class": "iframe_content",
                "src": this.opt.url,
                "css": {
                    "border": "none",
                    "width": "100%",
                    "height": this.opt.height || "auto"
                },
                "frameborder": "0"
            }).clone());
        }
        this.show();
        return this;
    },
    tooltip: function(tiptype, title, options) {
        if (!Cute.isString(title)) options = title;
        options = $.extend({
            mask: false,
            className: "tooltip",
            drag: false,
            buttons: []
        }, options || {});
        return this[tiptype](title, options);
    },
    suggest: function(info, options) {
        this.opt.title = "";
        this.opt.content = "";
        this.opt.mask = false;
        this.opt.width = 230;
        this.opt.close = null;
        this.opt.head = null;
        this.opt.drag = false;
        this.opt.className = "suggest";
        this._optionsExtend(this.opt, options || {});
        this._init();
        this.setHtml(info);
        this.show();
        var bTop = $(document).scrollTop();
        this.els.dialogBox.stop(true, true).css({
            "top": ($(window).height() - this.pannel.height()) / 3 * 1 + bTop,
            "opacity": 0.1
        }).animate({
            "opacity": 1,
            "top": this.els.dialogBox.position().top + 30
        }, 800, function() {
            this.els.dialogBox.delay(1800).animate({
                "opacity": 0.1,
                "top": this.els.dialogBox.position().top + 30
            }, 1200, function() {
                this.close();
            }.bind(this))
        }.bind(this));
        return this;
    },
    growl: function(info, options){
        this.opt.title = "";
        this.opt.content = "";
        this.opt.mask = false;
        this.opt.width = 230;
        this.opt.close = null;
        this.opt.head = null;
        this.opt.drag = false;
        this.opt.pos = {
            right: 10,
            left: 'auto',
            top: 10
        };
        this.opt.className = "growl";
        if(Cute.ui.dialog.growl && Cute.ui.dialog.growl.length > 0){
            var top = 0;
            $.each(Cute.ui.dialog.growl, function(i, item){
                top += item.outerHeight() + 10;
            });
            this.opt.css = {marginTop:top};
        }else{
            Cute.ui.dialog.growl = [];
        }
        this._optionsExtend(this.opt, options || {});
        this._init();
        this.setHtml(info);
        this.show();
        Cute.ui.dialog.growl[Cute.ui.dialog.growl.length] = this.els.dialogBox;
        this.els.main.delay(2000).fadeOut(800, function(){
            this.close();
            Cute.ui.dialog.growl.shift();
        }.bind(this));
        return this;
    }
});