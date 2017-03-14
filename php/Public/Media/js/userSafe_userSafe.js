var userSafeFn = function(e) {
    function n(e) {
        userCenterHeaderFn.init({
            userSafeCall: t
        }, e)
    }
    var i = function() {
        var n = {
            ajaxFn: {},
            mScore: function() {
                function n(e) {
                    var n = e ? value : a.score
                      , i = r.find(".score-bar-inner");
                    i.attr("style", "background-position:-" + (Math.floor((1 - n / 100) * i.parent().width()) - 8) + "px 0")
                }
                function i(e) {
                    var n = e ? e : a.score;
                    r.find(".score-val").text(n + "分!")
                }
                function t(t) {
                    a = e.extend(a, t),
                    n(),
                    i()
                }
                var a = {
                    score: 60,
                    totalScore: 100
                }
                  , r = e(".m-score");
                return {
                    init: t
                }
            }(),
            safeTips: function() {
                function n(e) {
                    var n = e ? value : o.setting
                      , i = !1;
                    for (var t in n)
                        if (1 == n[t]) {
                            i = !0;
                            break
                        }
                    return i
                }
                function i() {
                    c.show(),
                    f.hide()
                }
                function t() {
                    c.hide(),
                    f.show()
                }
                function a(n) {
                    var i = n ? value : o.setting;
                    for (var t in i)
                        1 == i[t] ? (e(".js-" + t).show(),
                        e(".js-" + t).find("a").attr("href", e.router.get("forgetPassVerifySetting") + "?w=" + t + "&type=m&name=" + o.name)) : e(".js-" + t).hide()
                }
                function r(e) {
                    var r = e ? value : n();
                    r ? (t(),
                    a()) : i()
                }
                function s(n) {
                    o = e.extend(o, n),
                    r()
                }
                var o = {
                    name: "",
                    setting: {
                        q: 1,
                        p: 1,
                        e: 0
                    }
                }
                  , c = e(".with-no-one-setting")
                  , f = e(".has-one-setting");
                return {
                    init: s
                }
            }(),
            safeItem: function() {
                function n(n) {
                    var i = n ? value : r.setting;
                    for (var t in i)
                        1 == i[t] ? e(".safe-item-" + t).removeClass("doing").addClass("done") : e(".safe-item-" + t).addClass("doing")
                }
                function i() {
                    return 0 == r.setting.q && 0 == r.setting.p && 0 == r.setting.e ? !1 : !0
                }
                function t(n) {
                    var i = e(".safe-item").find(".btn-bind");
                    i.each(n ? function() {
                        e(this).attr("href", r.verifyBySelUri + "?redirectURL=" + e(this).data("redirect"))
                    }
                     : function() {
                        e(this).attr("href", r.verifyByPassUri + "?redirectURL=" + e(this).data("redirect"))
                    }
                    )
                }
                function a(a) {
                    r = e.extend({}, r, a),
                    n(),
                    t(i())
                }
                var r = {
                    setting: {
                        q: 1,
                        p: 1,
                        e: 0,
                        i: 0
                    },
                    verifyByPassUri: e.router.get("userSafeValidatePass"),
                    verifyBySelUri: e.router.get("userSafeValidateSel")
                };
                return {
                    init: a
                }
            }(),
            events: function() {}
        }
          , i = function(e) {
            n.mScore.init({
                score: e.safe_score
            }),
            n.safeTips.init({
                name: e.login_account,
                setting: {
                    q: e.is_mibao,
                    p: e.is_phone_bind,
                    e: e.is_email
                }
            }),
            n.safeItem.init({
                setting: {
                    q: e.is_mibao,
                    p: e.is_phone_bind,
                    e: e.is_email,
                    i: "" == e.id_card_number ? 0 : 1
                }
            })
        }
        ;
        return {
            init: i
        }
    }()
      , t = function(e) {
        var n = a();
        switch (n) {
        case "/allinone":
            i.init(e);
            break;
        case "/allinoneValidatePass":
            allinoneValidatePass.init(e.login_account);
            break;
        case "/allinoneValidateSel":
            mAllineoneValidateSel.init(e)
        }
    }
      , a = function() {
        var e = location.pathname.match(/\/[^\/]+/g);
        return e[e.length - 1]
    }
    ;
    e.service.infoActionRegister.actionRegister(n)
}(jQuery);
