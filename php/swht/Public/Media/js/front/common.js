//应用扩展
var TKJ = {
    config: {
        NOTICEFREQUENCY: 30 * 1000,
        AVATARSIZE: {
            "small": 30,
            "normal": 45,
            "big": 90
        }
    },
    common: {
        copy: function(obj, txt, success) {
            var self = this;
            $(obj).each(function() {
                var content = txt;
                if (typeof ZeroClipboard == "undefined") return false;
                this.clip = null;
                if ($.isFunction(txt)) {
                    content = txt.call(this);
                }
                ZeroClipboard.setMoviePath(Cute.config.SCRIPTPATH + "ZeroClipboard.swf");
                this.clip = new ZeroClipboard.Client();
                this.clip.setHandCursor(true);
                this.clip.setText(content);
                this.clip.addEventListener('onComplete', success);
                $(window).resize(function() {
                    this.clip.reposition();
                }.bind(this));
                this.clip.glue(this);
                return this.clip;
            });
        },
        //ajax加载ajax/write里的页面，一样的内容，只会加载一次
        write: function(id, params, fn) {
            if (!id) return;
            if (arguments.length < 3) {
                cache = fn;
                fn = params;
                params = {};
            }
            var cachekey = "write" + id;
            if (params.cachekey) cachekey = params.cachekey; //设置cache的key值，默认为write + 文件ID
            if (!$(document).data(cachekey)) {
                Cute.api.get("write", $.extend({
                    id: id
                }, params || {}), function(html) {
                    if (html) {
                        $(document).data(cachekey, html);
                        fn(html);
                    }
                }, false, true);
            } else {
                fn($(document).data(cachekey));
            }
        },
        bgcolorAnimation: function(obj, color) {
            Do('animatecolors', function() {
                obj = $(obj);
                obj.stop(false, true).focus();
                var _o_bgcolor = obj.css("backgroundColor");
                obj.css({
                    backgroundColor: color || "#FFC8C8"
                }).animate({
                    backgroundColor: _o_bgcolor
                }, 1000, function() {
                    this.style.cssText = this.style.cssText.replace(/background\-color[^;]+;/i, '');
                });
            });
            return false;
        },
        showEmoji: function(obj, input, parentEl) {
            var obj = $(obj);
            if (obj[0].dialog) {
                obj[0].dialog.toggle();
            } else {
                Cute.api.get('common/get_emoji', {}, function(json) {
                    var html = "";
                    $.each(json.data, function(i, item) {
                        html += '<a rel="' + item[0] + '" href="javascript:;" title="' + item[0] + '"><span class="e e' + item[1] + '"></span></a>';
                    });
                    obj[0].dialog = new Cute.ui.dialog().tooltip("layer", "", {
                        content: '<div class="d_emoji d_content">' + html + '</div>',
                        className: "tooltip emoji_dialog",
                        parentEl: parentEl,
                        width: 326,
                        pos: {
                            "top": function() {
                                return (parentEl ? obj.position().top : obj.offset().top) + obj.outerHeight() + 2;
                            },
                            "left": function() {
                                return (parentEl ? obj.position().left : obj.offset().left) - 1;
                            }
                        }
                    });
                    var form = obj.closest("form");
                    $(".d_emoji a", obj[0].dialog.pannel).click(function(e) {
                        var _this = $(this);
                        Cute.common.insertSelection(form.find(input || "textarea[name=content]")[0], "[" + _this.attr("rel") + "]");
                        form.find(input || "textarea[name=content]").trigger("keyup").css("color", "#000").trigger("keyup");
                        obj[0].dialog.hide();
                        e.preventDefault();
                        e.stopPropagation();
                    });
                    form.out("click.emoji", function(e) {
                        if (!obj[0].dialog.pannel) {
                            form.unout("click.emoji");
                            return;
                        }
                        if ($(this).has(e.target).length == 0) {
                            obj[0].dialog.hide();
                        }
                    }, true);
                });
            }
            return false;
        },
        emoji: function(str) {
            if (window['jEmoji']) {
                if (!jEmoji.EXT_EMOJI_MAP) jEmoji.EXT_EMOJI_MAP = TKJ.config.EXT_EMOJI_MAP || {};
                return jEmoji.unifiedToHTML(jEmoji.softbankToUnified(jEmoji.googleToUnified(str)));
            } else {
                return str;
            }
        },
        blockSlide: function(options) {
            var self = this;
            var opt = $.extend(true, {
                width: 468, //宽度
                height: null, //高度
                data: [], //广告列表，例：[{url:"",image:"",title:"",target:""}],
                random: false,
                showpage: true,
                interval: 5, //轮播间隔
                transition: 1000,
                style: '',
                styleurl: "" //特殊样式URL
            }, options);
            var iframe = $('<iframe />', {
                frameborder: 0,
                width: opt.width
            }).css("visibility", 'hidden').load(function() {
                var iDoc = iframe.contents();
                var _html = [];
                var _head = "<style>" +
                    "html{overflow:hidden}" +
                    "body{margin:0;padding:0;font-family:Arial;-webkit-text-size-adjust:none;overflow:hidden;position:relative;width:100%;height:100%}" +
                    "img{border:0;}" +
                    ".ad_list,.ad_ids{margin:0;padding:0;list-style:none;}" +
                    ".ad_list li{ position:absolute;top:0;left:0;display:none;}" +
                    ".ad_ids{position:absolute;bottom:10px; right:10px;z-index:50;}" +
                    ".ad_ids li{float:left;margin-left:4px;}" +
                    ".ad_ids li a{display:inline-block;font-size:9px;padding:2px 4px; border:1px solid #c5c3c4;background-color:#c5c3c4;text-decoration:none;color:#c5c3c4	;zoom:1;}" +
                    ".ad_ids li a:hover{text-decoration:none;}" +
                    ".ad_ids li a.curr{border-color:#da8630;color:#da8630;background-color:#da8630;}" +
                    opt.style +
                    "</style>";
                if (opt.styleurl) _head += '<link type="text/css" rel="stylesheet" href="' + opt.styleurl + '" />';
                iDoc.find("head").html(_head);
                var content = iDoc.find("body");
                var flag=0;
                if (opt.data.length > 0) {
                    if (opt.random)
                        opt.data = Cute.Array.shuffle(opt.data);
                    $(this).css('visibility', 'visible');
                    
                    _html.push('<ul class="ad_list">');
                    $.each(opt.data, function(i, item) {
                        _html.push('<li><a href="' + item.url + '" target="' + (item.target ? item.target : "_blank") + '"><img dynamic-src="' + item.image + '" alt="' + item.title + '" title="' + item.title + '" width="' + opt.width + '" ' + (opt.height ?  'height="' + opt.height + '"' : '') + ' /></a></li>');
                    });
                    _html.push('</ul>');
                    if (opt.data.length > 1 && opt.showpage) {
                        _html.push('<ul class="ad_ids">');
                        $.each(opt.data, function(i, item) {
                            _html.push('<li><a href="javascript:void(0)">' + (i + 1) + '</a></li>');
                        });
                        _html.push('</ul>');
                    }
                    var html = $(_html.join(''));
                    html.find('img').on('load', function(){
                        if(!opt.height && this.height)
                            iframe.height(this.height);
                    });
                    content.append(html).find(".ad_ids a").click(function() {
                        setAdItem(parseInt($(this).text()) - 1);
                    });
                    $(".preBg").click(function() {
                        flag-=1;
                        if(flag<0){
                        	flag=opt.data.length-1;
                        }
                        setAdItem(flag);
                    });
                    $(".nextBg").click(function() {
                        flag+=1;
                        if(flag>opt.data.length-1){
                        	flag=0;
                        }
                        setAdItem(flag);
                    });
                    setAdItem(0);
                }
                function setAdItem(num) {
                    if (num > opt.data.length - 1) {
                        num = 0;
                    }
                    var _ulList = iDoc.find(".ad_list");
                    var _ulIds = iDoc.find(".ad_ids");
                    _ulList.children("li").filter(":visible").stop(true, true).fadeOut(opt.transition, function() {
                        $(this).css("z-index", 0);
                    }).end().eq(num).css("z-index", 1).stop(true, true).fadeIn(opt.transition);
                    var img = _ulList.find("img").filter(":eq(" + num + "),:eq(" + (num + 1 > opt.data.length - 1 ? 0 : (num + 1)) + ")").attr("src", function() {
                        var src = $(this).attr("dynamic-src");
                        $(this).removeAttr("dynamic-src");
                        return src;
                    }).trigger('load');
                    if (opt.data.length > 1) {
                        _ulIds.find("a").removeClass().eq(num).addClass("curr");
                        clearTimeout(self.timer);
                        self.timer = setTimeout(function() {
                            setAdItem(num + 1);
                        }, opt.interval * 1000);
                    }
                    flag=num
                }
            });
            if(opt.height){
                iframe.height(opt.height);
            }
            return iframe;
        },
        makeLink: function(str) {
            // http://, https://, ftp://
            var urlPattern = /\b(?:https?|ftp):\/\/[a-z0-9-+&@#\/%?=~_|!:,.;]*[a-z0-9-+&@#\/%=~_|]/gim;
            // www. sans http:// or https://
            var pseudoUrlPattern = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
            // Email addresses
            var emailAddressPattern = /([a-z0-9\-_\.]+?)@([{a-z0-9\/\-_+=.~!%@?#&;:$\|}]+)/gim;
            // if(!str)
            //     return '';
            return str
                .replace(urlPattern, function(s, t) {
                    return '<a href="' + s + '" target="_blank" title="' + s + '">' + Cute.String.cut(s, 50, '...') + '</a>';
                })
                .replace(pseudoUrlPattern, '$1<a href="http://$2" target="_blank" title="$2">$2</a>')
                .replace(emailAddressPattern, '<a href="mailto:$1@$2">$1@$2</a>');
        },
        editor: function(el, options) {
            // var policy = {
            //     'bucket' : 'tukeji-upload',
            //     'expiration' : Math.floor(new Date().getTime() / 1000) + 1800,
            //     'save-key': '/tipsimg/{year}{mon}/{random32}{.suffix}',
            //     'allow-file-type' : 'jpg,jpeg,png,gif',
            //     'content-length-range' : '0,10240000',
            //     'return-url' :  TKJ.config.SITEURL + '/api/tips/upload_callback',
            //     'x-gmkerl-quality'  : 100,
            //     'x-gmkerl-unsharp' : false
            // };
            //policy = Cute.String.base64_encode(Cute.Json.stringify(policy))
            var policy = {
                'scope': 'upload',
                'deadline': Math.floor(new Date().getTime() / 1000) + 7200,
                'returnUrl': TKJ.config.SITEURL + '/api/tips/upload_callback/trip_id',
                'returnBody': '{"hash": $(etag), "key": $(key), "image-width": $(imageInfo.width), "image-height": $(imageInfo.height)}'
            };
            policy = Cute.String.base64_encode(Cute.Json.stringify(policy)).replace(/\+/gi, '-').replace(/\//gi, '_');
            var encodedSign = Cute.String.base64_encode(CryptoJS.HmacSHA1(policy, 'O_zZSj1oD9017pzPooIGxx1SBdW9OGv8m57BQELv').toString(CryptoJS.enc.Latin1)).replace(/\+/gi, '-').replace(/\//gi, '_');
            var token = 'I_HIbxjxSz6e71PfGl08HUqNQIJTTE-evRJuwDPG:' + encodedSign + ':' + policy;
            return $(el)[0].editor = UE.getEditor($(el).attr('id'), $.extend({
                localDomain: [TKJ.config.SITEURL.replace('http://', '')],
                initialContent: '',
                contextMenu: [],
                wordCount: false,
                removeFormatTags:'b,big,code,del,dfn,em,font,i,ins,kbd,q,samp,small,span,strike,strong,sub,sup,tt,u,var',
                autoHeightEnabled: false,
                indentValue: '0em',
                allowDivTransToP: true,
                initialFrameWidth: $(el).outerWidth(),
                initialFrameHeight: $(el).outerHeight(),
                //policy: policy,
                //signature: md5(policy + '&gFh8MLAgV4WytOjDEizHn07k3DQ='),
                token: token,
                initialStyle: 'body{font-size:14px; margin:8px 7px; }.silver{color:#999}.word_img{background:url(/static/js/plugin/upyun-ueditor/lang/zh-cn/images/localimage.png) no-repeat center center;border:1px solid #ddd}.edui-faked-video{background:url(/static/js/plugin/upyun-ueditor/themes/default/images/videologo.gif) no-repeat center center;border:1px solid #ddd}img{max-width:580px;border:none;}p{line-height:1.6;}',
                scaleEnabled: true,
                removeFormatAttributes: 'class,style,lang,width,height,hspace',
                elementPathEnabled: true,
                catchRemoteImageEnable: false,
                toolbars: [
                    ['fullscreen', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'strikethrough', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|', 'link', 'unlink', 'anchor', '|', 'insertimage', 'insertvideo', 'gmap', 'wordimage', '|', 'horizontal', 'spechars', 'removeformat', '|', 'searchreplace', '|', 'preview']
                ],
                retainOnlyLabelPasted: true,
                pasteplain:true,
                autotypeset: {
                   mergeEmptyline: true,           //合并空行
                   removeClass: true,              //去掉冗余的class
                   removeEmptyline: true,         //去掉空行
                   textAlign:"left",               //段落的排版方式，可以是 left,right,center,justify 去掉这个属性表示不执行排版
                   imageBlockLine: 'center',       //图片的浮动方式，独占一行剧中,左右浮动，默认: center,left,right,none 去掉这个属性表示不执行排版
                   pasteFilter: true,             //根据规则过滤没事粘贴进来的内容
                   clearFontSize: true,           //去掉所有的内嵌字号，使用编辑器默认的字号
                   clearFontFamily: true,         //去掉所有的内嵌字体，使用编辑器默认的字体
                   removeEmptyNode: false,         // 去掉空节点
                   //可以去掉的标签
                   removeTagNames: {},
                   indent: false,                  // 行首缩进
                   indentValue : '2em',            //行首缩进的大小
                   bdc2sb: false,
                   tobdc: false
                },
                "filterTxtRules": function(){
                   function transP(node){
                       node.tagName = 'p';
                       node.setStyle();
                   }
                   return {
                       //直接删除及其字节点内容
                       '-' : 'script style object iframe embed input select table',
                       'p': {$:{}},
                       'br':{$:{}},
                       'img':{
                            $: {
                                'width': 1,
                                'height': 1,
                                'word_img': 1,
                                'src': 1,
                                'class': 1,
                                '_url': 1
                            }
                        },
                       'div':transP,
                       'li':{'$':{}},
                       'caption':transP,
                       'th':transP,
                       'tr':transP,
                       'h1':{'$':{}},'h2':{'$':{}},'h3':{'$':{}},'h4':{'$':{}},'h5':{'$':{}},'h6':{'$':{}},
                       'td':function(node){
                           //没有内容的td直接删掉
                           var txt = !!node.innerText();
                           if(txt){
                               node.parentNode.insertAfter(UE.uNode.createText(' &nbsp; &nbsp;'),node);
                           }
                           node.parentNode.removeChild(node,node.innerText())
                       }
                   }
                },
                filterRules: {
                    br: {},
                    b: function(node) {
                        node.tagName = 'strong'
                    },
                    strong: {
                        $: {}
                    },
                    img: {
                        $: {
                            'width': 1,
                            'height': 1,
                            'word_img': 1,
                            'src': 1,
                            'class': 1,
                            '_url': 1
                        }
                    },
                    p: {
                        'br': 1,
                        'BR': 1,
                        'img': 1,
                        'IMG': 1,
                        'embed': 1,
                        'object': 1,
                        $: {}
                    },
                    span: {
                        $: {
                            'class': 1
                        }
                    },
                    strong: {
                        $: {}
                    },
                    i: function(node) {
                        node.tagName = 'em'
                    },
                    a: function(node) {
                        var url = node.getAttr('href');
                        var title = node.getAttr('title');
                        if(!node.firstChild()){
                           node.parentNode.removeChild(node);
                           return;
                        }
                        node.setAttr();
                        node.setAttr('href', url);
                        node.setAttr('title', title);
                        node.setAttr('target', '_blank');
                        //a:{$:{'href': 1,'title': 1, 'target':1}},
                    },
                    object: 1,
                    embed: 1,
                    dl: function(node) {
                        node.tagName = 'ul';
                        node.setAttr()
                    },
                    dt: function(node) {
                        node.tagName = 'li';
                        node.setAttr()
                    },
                    dd: function(node) {
                        node.tagName = 'li';
                        node.setAttr()
                    },
                    li: function(node) {
                        var className = node.getAttr('class');
                        if (!className || !/list\-/.test(className)) {
                            node.setAttr()
                        }
                        var tmpNodes = node.getNodesByTagName('ol ul');
                        UE.utils.each(tmpNodes, function(n) {
                            node.parentNode.insertAfter(n, node);
                        })
                    },
                    div: function(node) {
                        node.tagName = 'p';
                        node.setAttr();
                    },
                    ol: {
                        $: {}
                    },
                    ul: {
                        $: {}
                    },
                    table: function(node) {
                        UE.utils.each(node.getNodesByTagName('table'), function(t) {
                            UE.utils.each(t.getNodesByTagName('tr'), function(tr) {
                                var p = UE.uNode.createElement('p'),
                                    child, html = [];
                                while (child = tr.firstChild()) {
                                    html.push(child.innerHTML());
                                    tr.removeChild(child);
                                }
                                p.innerHTML(html.join('&nbsp;&nbsp;'));
                                t.parentNode.insertBefore(p, t);
                            })
                            t.parentNode.removeChild(t)
                        });
                        var val = node.getAttr('width');
                        node.setAttr();
                        if (val) {
                            node.setAttr('width', val);
                        }
                    },
                    tbody: {
                        $: {}
                    },
                    caption: {
                        $: {}
                    },
                    th: {
                        $: {}
                    },
                    td: {
                        $: {
                            valign: 1,
                            align: 1,
                            rowspan: 1,
                            colspan: 1,
                            width: 1,
                            height: 1
                        }
                    },
                    tr: {
                        $: {}
                    },
                    h3: {
                        $: {}
                    },
                    h2: {
                        $: {}
                    },
                    '-': 'script style meta iframe select input button'
                },
                onready: function(){
                    this.on('showmessage', function(type, m){
                        if (m['content'] == '本地保存成功') {
                            return true;
                        }
                    });
                }
            }, options || {}));
        }
    },
    get_upload_url: function(url, type, size) {
        if (!url) {
            if (size !== undefined) {
                var thumPrefix = TKJ.config[type.toLowerCase()]['thumbPrefix'].split(',');
                return TKJ.config.UPLOAD + "/images/no_" + type.toLowerCase() + ".png!" + thumPrefix[size];
            } else {
                return TKJ.config.UPLOAD + "/images/no_" + type.toLowerCase() + ".png";
            }
        } else {
            if (size !== undefined) {
                var thumPrefix = TKJ.config[type.toLowerCase()]['thumbPrefix'].split(',');
                var filename = url + '!' + thumPrefix[size];
            } else {
                var filename = url;
            }
            return TKJ.config.UPLOAD + "/" + TKJ.config[type.toLowerCase()]['path'] + "/" + filename;
        }
    },
    validator_fun: function(msg, o, cssctl) {
        var parent = o.obj.closest('td,p,div');
        if (parent.find(".Validform_checktip").length > 0) {
            var obj = parent.find(".Validform_checktip").html(msg);
        } else {
            var obj = $('<span class="Validform_checktip">' + msg + '</span>').insertAfter(o.obj);
        }
        if (o.type != 2 || o.obj.attr('ajaxurl')) {
            cssctl(obj, o.type);
        } else {
            obj.remove();
        }
    }
};