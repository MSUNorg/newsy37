window.addEventListener('DOMContentLoaded', function (e) {
    var count = 0,
        timer = setInterval(function(){
            if(typeof $ !== 'undefined'){
                clearInterval(timer);
                zeptoLoadedCallback();
                return;
            }

            // 4.5秒还没加载出来，就不管了
            if(count > 15){
                clearInterval(timer);
            }
        },300);

    function zeptoLoadedCallback(){
        $('body').on('click','a',function(e){
            var GameId,
                me = $(this),
                IsDownloadUrl = false,
                linkUrl = me.attr("href");
            /*
             * 统计代码
             */
            if (linkUrl.match(/\.apk$|\.plist$|apple\.com|\.ipa$|360\.cn\/redirect\/down\/|uc\.cn\/download\/package\//)) {
                IsDownloadUrl = true;
            }
            if (IsDownloadUrl) {
                var GameId = me.attr("href");
                GameId = window.btoa(GameId);
                var source = window.location.href.replace("http://" + window.location.host, "");
                var source = source.replace(/\/so\/.*/, "/so/");
                source = source.replace(/\d+(\/|$)/, "");
                _czc.push(["_trackEvent", source, "down", GameId]);
            }

            // 拷贝的detect.js
            var os = {},
                browser = {},
				ucweb = {},
                ua = navigator.userAgent,
                isHeight,
                ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
                ipod = ua.match(/(iPod)(.*OS\s([\d_]+))?/),
                android = ua.match(/(Android)\s+([\d.]+)/),
                iphone = !ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
				
				ucAndroid = ua.match(/^UCWEB.+?(Adr|Android)/),
				ucIphone = ua.match(/^UCWEB.+?(iPh|iPhone)/),
				ucIpad = ua.match(/iPad/);
				
            if (iphone && !ipod) os.ios = os.iphone = true, os.version = iphone[2].replace(/_/g, '.');
            if (ipad) os.ios = os.ipad = true, os.version = ipad[2].replace(/_/g, '.');
            if (ipod) os.ios = os.ipod = true, os.version = ipod[3] ? ipod[3].replace(/_/g, '.') : null;
            if (android) os.android = true, os.version = android[2];
			if (ucAndroid) ucweb.ucAndroid = true;
			if (ucIphone) ucweb.ucIphone = true;
			if (ucIpad) ucweb.ucIpad = true;
			
			//alert(ucweb.ucAndroid);
            // 新增对下载逻辑的更改
            // 如果当前点击的a标签的地址里面有plist，则针对iPad和iPhone进行不同的跳转

            if (linkUrl.match(/\.(plist)$/)) {
                // 目前规则仅针对list页有效
                //if(location.href.match('list') === null){ return; }
                if(os && os.iphone){
                    linkUrl = linkUrl.replace('ssl.naitang.com/plist/app','ssl.naitang.com/plist/iphone');
                }
                if (os && os.ipad) {
                    linkUrl = linkUrl.replace('ssl.naitang.com/plist/app', 'ssl.naitang.com/plist/ipad');
                }
                // android和其他情况，linkUrl不处理
             
				//isImgHeight = me.parent().attr("class");
				//if((isImgHeight=='item-top') && ((location.href =='http://www.7k7k.com/m-android/') || (location.href =='http://www.7k7k.com/m-android/index/'))){
				//	$parentNode = me.parents(".item");
				//	isHeight = $parentNode.find(".info a").next().attr("data-is-fast-download");
				//}else{
				//	isHeight = me.attr("data-is-fast-download");
				//}
                // 设定了data-is-fast-download并且值不为0的时候
                // 认为是需要高速下载的，这个时候弹窗
                //if(isHeight>0){
                //    callPopup(isHeight,me,os,ucweb,linkUrl);
                //}else{
                //    return true;
                //}

                // return false 在zepto里面，会最后阻止冒泡和默认行为
                // 这样a标签的默认点击就会被阻止
                return true;
                // 其他情况下该怎么走就怎么走
            }
        });
    }
	
}, false);

