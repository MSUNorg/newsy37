var indexFn = function() {

    var e = function(e, n) {

        var i = 0,

        t = 0,

        a = Math.floor(e / n),

        r = a - 1,

        n = n,

        o = 0;

        this.per = n;

        var s = function() {

            var e = !1;

            return i == r && (e = !0),

            e

        },

        c = function() {

            var e = !1;

            return i == t && (e = !0),

            e

        };

        this.getActiveIndex = function() {

            return i

        },

        this.getNextIndex = function() {

            var e = 0;

            return s() ? (o = -1, e = -1) : e = i + 1,

            e

        },

        this.getOldIndex = function() {

            return o

        },

        this.getPrevIndex = function() {

            var e = 0;

            return c() ? (o = -1, e = -1) : (o = i, e = i - 1),

            e

        };

        var l = function(e) {

            o = i,

            i = e

        };

        this.updateActiveIndex = l;

        var d = function() {

            var e = 0;

            return s() ? e = -1 : (l(i + 1), e = i),

            e

        };

        this.next = d;

        var u = function() {

            var e = 0;

            return c() ? e = -1 : (l(i - 1), e = i),

            e

        };

        this.prev = u

    },

    n = {

        page: null,

        bannerWrap: null,

        bannerUl: null,

        bannerNav: null,

        itemW: 0,

        interval: void 0,

        speed: 600,

        delay: 3e3,

        init: function() {

            this.bannerWrap = $("#topBanner"),

            this.bannerUl = this.bannerWrap.find(".pics"),

            this.bannerNav = this.bannerWrap.find(".navs"),

            this.itemW = this.bannerWrap.outerWidth(!0);

            var i = this.bannerUl.find("li");

            this.page = new e(i.length, 1),

            i.css({

                width: this.itemW

            }),

            this.bannerUl.css({

                width: this.itemW * i.length

            });

            for (var t = "",

            a = 0; a < i.length; a++) t += 0 == a ? '<li><a href="#" class="active" data-index="' + a + '">.</a></li>': '<li><a href="#" data-index="' + a + '">.</a></li>';

            this.bannerNav.empty().append(t),

            this.bannerNav.css({

                marginLeft: -this.bannerNav.width() / 2

            }),

            n.event(),

            n.moveTo(0, n.moveToCallback),

            n.play()

        },

        rebuild: function() {

            var e = this.bannerUl.find("li");

            this.itemW = this.bannerWrap.outerWidth(!0),

            e.css({

                width: this.itemW

            }),

            this.bannerUl.css({

                width: this.itemW * e.length

            }),

            this.bannerNav.css({

                marginLeft: -this.bannerNav.width() / 2

            })

        },

        moveTo: function(e, i) {

            var t = n.page.getActiveIndex() - n.page.getOldIndex();

            if (void 0 !== e) if (t > 0) this.bannerUl.animate({

                marginLeft: -this.itemW * t

            },

            this.speed,

            function() {

                n.bannerNav.find("a").removeClass("active"),

                n.bannerNav.find("a").eq(e).addClass("active");

                for (var a = 0; t > a; a++) n.bannerUl.children("li:first").insertAfter(n.bannerUl.children("li:last"));

                n.bannerUl.css({

                    marginLeft: 0

                }),

                i && i.call(n.bannerUl.children("li:first"))

            });

            else {

                for (var a = 0; a < Math.abs(t); a++) n.bannerUl.children("li:last").insertBefore(n.bannerUl.children("li:first")),

                n.bannerUl.css({

                    marginLeft: -this.itemW * (a + 1)

                });

                n.bannerUl.animate({

                    marginLeft: 0

                },

                this.speed,

                function() {

                    n.bannerNav.find("a").removeClass("active"),

                    n.bannerNav.find("a").eq(e).addClass("active"),

                    i && i.call(n.bannerUl.children("li:first"))

                })

            } else this.bannerUl.animate({

                marginLeft: -this.itemW

            },

            this.speed,

            function() {

                n.bannerNav.find("a").removeClass("active"),

                n.bannerNav.find("a").eq(n.page.getActiveIndex()).addClass("active"),

                n.bannerUl.children("li:first").insertAfter(n.bannerUl.children("li:last")),

                n.bannerUl.css({

                    marginLeft: 0

                }),

                i && i.call(n.bannerUl.children("li:first"))

            })

        },

        moveToCallback: function() {

            var e = $(this).data("src");

            void 0 != e && ($(this).data("src", void 0), $(this).css({

                backgroundImage: "url('" + e + "')"

            }))

        },

        next: function() { - 1 == n.page.getNextIndex() ? (n.page.updateActiveIndex(0), n.moveTo(void 0, n.moveToCallback)) : (n.page.next(), n.moveTo(void 0, n.moveToCallback))

        },

        prev: function() { - 1 == n.page.getPrevIndex() || n.moveTo(n.page.prev())

        },

        play: function() {

            void 0 === n.interval && (n.interval = setInterval(function() {

                n.next()

            },

            this.delay))

        },

        pause: function() {

            clearInterval(n.interval),

            n.interval = void 0

        },

        event: function() {

            n.bannerWrap.on("click", ".navs a",

            function() {

                var e = $(this).data("index");

                return n.page.updateActiveIndex(e),

                n.moveTo(e, n.moveToCallback),

                !1

            }),

            n.bannerWrap.on("mouseenter",

            function() {

                n.pause()

            }),

            n.bannerWrap.on("mouseleave",

            function() {

                n.play()

            });

            var e;

            $(window).on("resize",

            function() {

                clearTimeout(e),

                e = setTimeout(function() {

                    n.rebuild()

                },

                100)

            })

        }

    },

    i = {

        page: null,

        itemW: 0,

        recoListWrap: null,

        recoListUl: null,

        pageW: 0,

        init: function() {

            this.recoListWrap = $("#recoListWrap");

            var n = this.recoListWrap.find("li");

            this.recoListUl = this.recoListWrap.find(".reco-list"),

            this.page = new e(n.length, 4),

            this.itemW = n.eq(0).outerWidth(!0),

            this.pageW = this.itemW * i.page.per,

            this.recoListUl.css({

                width: this.itemW * n.length

            }),

            i.event(),

            n.hide();

            var t = n.filter(function(e) {

                return e < 0 * i.page.per + i.page.per && e >= 0 * i.page.per ? !0 : void 0

            });

            t.show()

        },

        moveTo: function(e) {

            var n = i.recoListWrap.find("li"),

            t = i.page.getOldIndex(),

            a = n.filter(function(e) {

                return e < t * i.page.per + i.page.per && e >= t * i.page.per ? !0 : void 0

            });

            a.fadeOut(100,

            function() {

                var t = n.filter(function(n) {

                    return n < e * i.page.per + i.page.per && n >= e * i.page.per ? !0 : void 0

                });

                t.fadeIn(300)

            })

        },

        next: function() { - 1 == i.page.getNextIndex() ? (i.page.updateActiveIndex(0), i.moveTo(0)) : i.moveTo(i.page.next())

        },

        buildQrcode: function() {

            var e = this.recoListWrap.find("li");

            e.each(function() {

                var e = $(this).find(".code"),

                n = "table";

                i.isWebkit() && (n = "canvas"),

                e.qrcode({

                    render: n,

                    text: e.data("uri"),

                    width: 86,

                    height: 86

                })

            })

        },

        event: function() {

            $("#change").on("click",

            function() {

                return i.next(),

                !1

            }),

            $(".imgwrap").on("mouseenter",

            function() {

                $(this).find(".down-slide").stop().animate({

                    top: 0

                },

                150)

            }),

            $(".imgwrap").on("mouseleave",

            function() {

                $(this).find(".down-slide").stop().animate({

                    top: "-100%"

                },

                150)

            })

        },

        isWebkit: function() {

            var e = navigator.userAgent.toLowerCase();

            return e.indexOf("webkit") > 0 ? !0 : !1

        }

    },

    t = function() {

        function e() {

            a.on("mouseenter", "li",

            function(e) {

                n.call(this, e)

            }),

            t.on("mouseenter", "li",

            function(e) {

                n.call(this, e)

            })

        }

        function n() {

            $(this).addClass("active"),

            $(this).siblings("li").removeClass("active")

        }

        function i() {

            e()

        }

        var t = $("#hotGameReco"),

        a = $("#newGameReco");

        return {

            init: i

        }

    } (),

    a = {

        msiderNav: null,

        init: function() {

            this.msiderNav = $("#msiderNav"),

            this.event()

        },

        event: function() {

            function e() {

                u.removeClass("active"),

                v.removeClass("active"),

                h.removeClass("active")

            }

            function n() {

                a.msiderNav.children("li").each(function(e) {

                    if (4 == e) return ! 1;

                    var n = this;

                    $(this).show(),

                    $(n).stop().delay(50 * e).animate({

                        height: 48

                    },

                    200)

                })

            }

            function i() {

                a.msiderNav.children("li").each(function(e) {

                    if (4 == e) return ! 1;

                    var n = this;

                    $(n).stop().delay(150 - 50 * e).animate({

                        height: 0

                    },

                    200,

                    function() {

                        $(n).hide()

                    })

                })

            }

            function t() {

                a.msiderNav.fadeIn()

            }

            function r() {

                a.msiderNav.fadeOut()

            }

            function o(e, n, i, t, a) {

                var r = "",

                o = encodeURIComponent(n),

                s = encodeURIComponent(i);

                switch (e) {

                case "sinaW":

                    r = "http://service.weibo.com/share/share.php?title=" + o + "&url=" + s + "&source=bookmark&pic=" + t + "&content=" + a;

                    break;

                case "pengyou":

                    r = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?to=pengyou&url=" + s + "&title=" + p.title + "&pics=" + t + "&desc=" + a + "&summary=" + o;

                    break;

                case "tecentW":

                    r = "http://share.v.t.qq.com/index.php?c=share&a=index&title=" + n + "," + a + "&url=" + s + "&pic=" + t;

                    break;

                case "qzone":

                    r = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=" + s + "&title=" + p.title + "&pics=" + t + "&desc=" + a + "&summary=" + o;

                    break;

                case "renren":

                    r = "http://widget.renren.com/dialog/share?resourceUrl=" + s + "&title=" + p.title + "&images=" + t + "&description=" + a;

                    break;

                case "kaixin":

                    r = "http://www.kaixin001.com/repaste/share.php?rurl=" + s + "&rtitle=" + p.title;

                    break;

                default:

                    r = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=" + s + "&title=" + p.title + "&pics=" + t + "&desc=" + a + "&summary=" + o

                }

                window.open(r)

            }

            var s = $(".wechat-link"),

            c = $(".wapbox-link"),

            l = $(".share-link"),

            d = $(".top-link"),

            u = this.msiderNav.find(".wechat-box"),

            v = this.msiderNav.find(".wapbox-box"),

            h = this.msiderNav.find(".share-box");

            s.on("mouseenter",

            function() {

                u.addClass("active")

            }),

            c.on("mouseenter",

            function() {

                v.addClass("active")

            }),

            l.on("mouseenter",

            function() {

                h.addClass("active")

            }),

            u.on("mouseenter",

            function() {

                u.addClass("active")

            }),

            v.on("mouseenter",

            function() {

                v.addClass("active")

            }),

            h.on("mouseenter",

            function() {

                h.addClass("active")

            }),

            s.on("mouseleave",

            function() {

                e()

            }),

            c.on("mouseleave",

            function() {

                e()

            }),

            l.on("mouseleave",

            function() {

                e()

            }),

            u.on("mouseleave",

            function() {

                e()

            }),

            v.on("mouseleave",

            function() {

                e()

            }),

            h.on("mouseleave",

            function() {

                e()

            }),

            $("#msiderNav").on("click", "a",

            function() {

                return $(this).hasClass("gift-link") ? void 0 : !1

            }),

            d.on("click",

            function() {

                return $("html,body").animate({

                    scrollTop: 0

                }),

                !1

            });

            var f = null,

            m = !1;

            $(window).scroll(function() {

                clearTimeout(f),

                f = setTimeout(function() {

                    var e = $(document).scrollTop();

                    e > 10 ? t() : r(),

                    e > $("#topBanner").offset().top ? m === !1 && (m = !0, n()) : m === !0 && (m = !1, i())

                },

                100)

            });

            var p = {

                title: "37手游平台",

                desc: "37手游_致力成为中国第一手机娱乐平台，玩游戏，上m.37.com",

                url: "http://m.37.com/",

                imgurl: ""

            };

            $(".share-box").on("click", "a",

            function() {

                var e = $(this).data("tag");

                return o(e, p.title, p.url, p.imgurl, p.desc),

                !1

            })

        }

    },

    r = function() {

        function e() {

            var e = $(".new-year-float"),

            n = new Date,

            i = new Date(2016, 1, 6, 24, 0, 0),

            t = new Date(2016, 1, 23, 9, 0, 0);

            n.getTime() >= i.getTime() && n.getTime() < t.getTime() && e.addClass("active"),

            e.on("click", ".close",

            function() {

                e.removeClass("active")

            })

        }

        return {

            init: e

        }

    } (),

    o = function() {

        $("img").lazyload({

            effect: "fadeIn",

            placeholder: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAFoEvQfAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OEU1NzA1NkZCNURBMTFFNTg0MzFBNDVCMjEyNzEyMzYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OEU1NzA1NzBCNURBMTFFNTg0MzFBNDVCMjEyNzEyMzYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo4RTU3MDU2REI1REExMUU1ODQzMUE0NUIyMTI3MTIzNiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo4RTU3MDU2RUI1REExMUU1ODQzMUE0NUIyMTI3MTIzNiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PiGF72AAAAANSURBVHjaY/j//z8DAAj8Av54xLKwAAAAAElFTkSuQmCC"

        }),

        r.init(),

        n.init(),

        i.init(),

        t.init(),

        a.init()

    },

    s = {

        init: o

    };

    return s

} ();

indexFn.init();