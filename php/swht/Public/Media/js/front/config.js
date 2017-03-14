Do.setConfig('js_version', '?v');
Do.setConfig('css_version', '?v');
Do.setConfig('root','/Public/Front/js/');
Do.global('/Public/Front/js/??prefixfree.js,cute.js,common.js,core/template.js,core/form.js,ui/dialog.js,define.js,init.js' + Do.getConfig('js_version'));
Do(function(){
    Cute.config = {
        SITEURL: "http://www.wan669.com",
        SITENAME: " wan669游戏平台",
        RESOURCEURL: "/static/home",
        SCRIPTPATH: "/static/home/js",
        SERVICEURL: "/api"
    };
    TKJ.config = {
    	user_id: "",
        username: ""
    };
});