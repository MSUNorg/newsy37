/**
**/
var TM = {};

TM.registerHandle = function(_funcname, func){
	if(_funcname){
		if(typeof TM[_funcname] != "undefined" && typeof TM[_funcname] != "null"){
			var _fun = TM[_funcname];
			if(typeof _fun == "function"){
				return true;
			}
		}
		eval("TM."+_funcname+" = func");
	}
};

/* var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?01cedff5de5dbdd357db23b17eb29007";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})(); */

$(function(){
	
	if(/cookieflag=1/.test(window.location.search)){
		$.cookie(COOKIE_REFERRER, 'http://def.vlcms.com/', {domain:'.vlcms.com', path:'/', expires:30});
	}
	//防止跳转到平台币充值页面
	if(document.referrer == ''){
		if(window.location.href=='http://def.vlcms.com/'){
			$.cookie(COOKIE_REFERRER, window.location.href, {domain:'.vlcms.com', path:'/', expires:30});			
			$.ajax({
				url:'http://def.vlcms.com',
				crossDomain:true,
				dataType: 'jsonp',
				success:function(){}
			});			
		}
	}else if(document.referrer != ''){
		var _referrer = $.cookie(COOKIE_REFERRER);
		if(_referrer){
			$(".header_nav_nei li").each(function(_index, _val){
				if($(this).find("a").text()== '充值中心'){
					$(this).find("a").attr("href", _referrer);
				}
			});
		}
	}
	
	
	
	var cdomain = document.domain.split('.');
	
	if(cdomain.length > 2){
		cookie_domain = '.'+cdomain.slice(1).join('.');
	}
	/* //搜索
	$("#onsearchbtn").siblings("input").focus(function(){
		if($(this).val()=='热门搜索  ｜  刀塔传奇'){
			$(this).val('');
		}
	}).blur(function(){
		  if ($(this).val() ==''){
			  $(this).val('热门搜索  ｜  刀塔传奇');
		  }
	}); */
	
	/* $("#onsearchbtn").click(function(){
		alert("此功能暂未开放");
		return false;
		
	});
	
	$("#loginul,#regul,#wxul,#wxul2").find(".Jq_menu").hover(
		function(){$(this).find("div").show();},
		function(){$(this).find("div").hide();}
	);
 
	//
	$(".header_bar2").hover(
	     function(){
	    	 $(".mardiv").show();
	     },
	     function(){
	    	 $(".mardiv").hide();
	     }
	); */
	
	//客户端变换登录状态
	//login_change();

	//fleshCode($(".regcodes"));
	
	/* $('#userPwd').live('keyup',function(event) {
	    if (event.keyCode == 13) {
	        //登录
	    	$("#Jq_login").click();
	    }
	}); */
	
	
	
	/* $("#Jq_login").click(function(){
		console.log(321);
		var name = $("#userName").val();
		var pwd = $("#userPwd").val();
		if(name.length < 1 || name == '帐号'){
			alert("请输入账号");
			return false;
		}
		if(pwd.length < 6 || pwd.length > 16){
			alert("密码长度为6-16位");
			return false;
		}
		var _params = {userName:name, password:pwd};
		TM.loginCallBack = loginTip;
		init_ajax('/Accounts/LogOn', api_params('LogOn', _params), "loginCallBack");

	});


	$("#userName").blur(function(){
		if($(this).val() == ""){
			$(this).val("帐号");
		}
	}).focus(function(){
		if($(this).val() == "帐号"){
			$(this).val("");
		}
	});

	//注册
	$("#J_register").click(function(){
		
		register_login(true);
	}); */

});

//api异步ajax
function init_ajax(_url, _params, _callback, _type){
	
	var settings = {
		url:(_url.indexOf('http') != -1 ? _url : api_url + "/V7" +_url),
		data:_params,
		dataType:(_type ? "json" : "jsonp"),
		type:(_type ? "POST" : "GET"),
		crossDomain:true,
		success:function(list, status, xhr){
			if(list.Result == true){
				 if (_callback){
				 	var func = TM[_callback];
					if (typeof func == "function") {
						func(list.Data, status, xhr);
					}
				 }
			}else if(!list.Result && list.Msg === 'PLEASELOGIN'){
				logout();
			}else{
				if(list.Msg){
					alert(list.Msg);
				}else{
					alert("操作失败");
				}
			}
		},
		error:function(){
			alert("网络错误，请稍后重试");
		}
	};

	$.ajax(settings);
}

/* 
function loginTip(retval){
	TM.user = {UserName:retval.UserName, Uid:retval.Uid, Timestamp:retval.Timestamp, VerifySign:retval.VerifySign, LogRefer:window.location.href};
	$.cookie.json = true;
	$.cookie(COOKIE_USER, TM.user, {domain:cookie_domain, path:'/', expires:30});			
	login_change(retval.UserName);
} */


// function register_login(_handler){
	// var name = $("#regname").val();
	// var pwd = $("#regpwd").val();
	// var repwd = $("#regrepwd").val();
	// var code = $("#regcode").val();
    
	// if(name.length < 6 || name > 20 || /.*[\u4e00-\u9fa5]+.*$/.test(name)){
		// alert("注册账号由6-16位字母和数字组成，不区分大小写!");
		// return false;
	// }else{
		// $("#regname").show_message("", true);
	// }
	

	// if(pwd.length < 6 || pwd.length > 16){
		// alert("密码长度为6-16位");
		// return false;
	// }else{
		// $("#regpwd").show_message("", true);
	// }

	// if(repwd !== pwd){
		// alert("确认密码不正确");
		// return false;
	// }else{
		// $("#regrepwd").show_message("", true);
	// }
	// var _tname = $("#real_name").val(),_idcard = $("#id_card").val();
	// if(!_tname.match(/^[\u4e00-\u9fa5]{2,6}$/g)){
		// alert('你输入的姓名有误');
		// return false;
	// }
	// if(!isIdCardNo(_idcard)){
		// alert('你输入的身份证有误');
		// return false;
	// }
	// if(code.length == 4){
		//$("#regcode").siblings("span").show_message("", true);
	// }else{
		// alert("验证码必须为4个字符");
		// return false;
	// }
	// if(_handler){
		// TM.registerHandle("WebRegister", function(retval){
			// alert('注册成功');
			// loginTip(retval);
			// login_change();
		// });
		// init_ajax("/Accounts/WebRegister", api_params('WebRegister', {name:name,password:pwd,code:code}), "WebRegister");
	// }

// }

function api_params(gkey, _ext_param){
	var _fixedData = '233';
	var _params = {
	    "LogOn":{requestId:"",sign:"",ver:"",userName:"",password:"",appId:"" },
		"ChangePassword":{uid:"",newPassword:"",requestId:"",sign:"",password:"",appId:""},
		"GetCodeBoundPhone":{uid:"",mobile:"",requestId:"",sign:"",appId:""},
		"BoundPhone":{uid:"",mobile:"",code:"",requestId:"",sign:"",appId:""},
		"GetMemberInfo":{uid:"",requestId:"",sign:"",appId:""},
		"SetMemberInfo":{uid:"",requestId:"",sign:"",appId:"",nickname:"",sex:"",birthday:"",qq:"",address:"",education:"",occupation:""},
		"SetMemberInfo1":{uid:"",requestId:"",sign:"",appId:"",truename:"",idcard:""},
		"SetMemberInfo2":{uid:"",requestId:"",sign:"",appId:"",email:""},
		"ResetSecurityQuestion":{uid:"",requestId:"",sign:"",appId:"",answer:"",newanswer:"",newquestion:"",oldquestion:"",pwd:""},
		"validate_pwd_email":{requestId:"",sign:"",appId:"",email:"",code:"",username:"", SID:""},
		"FindSecurityQuestion":{requestId:"",sign:"",appId:"",username:""},
		"WebAnswerChangePassword":{requestId:"",sign:"",appId:"",username:"",answer:"",newPassword:"",code:"", SID:""},
		"validate_pwd_sms":{requestId:"",sign:"",appId:"",username:"",field:"",newPassword:"",pwdsign:""},
		"BoundEmail":{requestId:"",sign:"",appId:"",email:"",uid:""},
		"WebRegister":{requestId:"",sign:"",appId:"",password:"",code:"",name:""},
		"webpay":{requestId:"",sign:"",appId:"",uid:"",mobile:"",amount:"",toaccount:""},
		"getCard":{appId:"",uid:"",giftId:"",requestId:"",sign:""},
		"CrossLogin":{},
		"UniqueUsername":{appId:"",username:"",requestId:"",sign:""}
	}
	var _appid = $.cookie('appId');
	if(_params[gkey]){
		_params[gkey].device = 3;
		for(var i in _params[gkey]){
			if(_params[gkey][i] == ""){
				_params[gkey][i] = typeof _appid != "undefined" && _appid ? _appid : _fixedData;
			}			
		}
		return $.extend({}, _params[gkey], _ext_param);
	}

}

function login_change(username){
	var $tab = $("#Jq_login_tab");
	if(!username){
		if(TM.user){
			username = TM.user.UserName;
		}else{
			var users = $.cookie(COOKIE_USER);
			if(users){
				users = eval("("+users+")");
				if(users){
					TM.user = users;
					username = users.UserName;
				}
			}
		}
	}
	if(username){
		$tab.find(".J_td_toggle").hide();
	$tab.find(".td02").html(username+"<a href='javascript:;' onclick=\"logout();\">[注销]</a>");
		if(typeof payLogin == 'function'){
			payLogin();
		}
	}
}
/* 
//检查登录状态
function check_login(){

	if(typeof TM.user == 'undefined' || typeof TM.user == 'null'){
		logout();
		
	}

}
 */
/* 
//登出
function logout(_fresh_page){
	$.removeCookie(COOKIE_USER,{ path: '/', domain:cookie_domain});
	if(_fresh_page){
		window.location.reload();
	}else{		
		
		if(window.location.href.indexOf('pay') != -1){
			window.location.reload();
		}else if(typeof TM.user != 'undefined' && typeof TM.user.LogRefer != 'undefined'){			
			window.location.href=TM.user.LogRefer;
		}else{
			window.location.href=site_url;
		}
	}
	return true;
} */


/* 
function show_regs(_obj){
	if($("#header_login").is(":visible")){
		$("#header_login").hide();
		$("#header_reg").show().children().show();
	}else{
		$("#header_login").show().children().show();
		$("#header_reg").hide();
	}
}
 */
/* function fleshCode($obj){
	$obj.attr("src", api_url+"/V7/Accounts/VerifyCode?device=3&t="+Math.random());
}
 */
function queryString(val){
    var uri = window.location.search;
    var re = new RegExp("" +val+ "=([^&?]*)", "ig");
    return ((uri.match(re)) ? (uri.match(re)[0].substr(val.length+1)) : null);
}
/* 
String.prototype.validateEmail = function (){
	return (/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(this));
}

function validateMobile(val){
	return (/^(1)[0-9]{10,12}$/.test(val));
}

function isIdCardNo(num){  
	  num = num.toUpperCase(); 
	 //身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X。  
	  if (!(/(^\d{15}$)|(^\d{17}([0-9]|X)$)/.test(num)))  
	  {
		   //alert('输入的身份证号长度不对，或者号码不符合规定！\n15位号码应全为数字，18位号码末位可以为数字或X。');
		  return false;
	 }
	//校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
	//下面分别分析出生日期和校验位
	var len, re;
	len = num.length;
	if (len == 15)
	{
	re = new RegExp(/^(\d{6})(\d{2})(\d{2})(\d{2})(\d{3})$/);
	var arrSplit = num.match(re);
	
	//检查生日日期是否正确
	var dtmBirth = new Date('19' + arrSplit[2] + '/' + arrSplit[3] + '/' + arrSplit[4]);
	var bGoodDay;
	bGoodDay = (dtmBirth.getYear() == Number(arrSplit[2])) && ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));
	if (!bGoodDay)
	{
			  //alert('输入的身份证号里出生日期不对！');  
			   return false;
	}
	else
	{
	//将15位身份证转成18位
	//校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
			  var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
			   var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
			   var nTemp = 0, i;  
				num = num.substr(0, 6) + '19' + num.substr(6, num.length - 6);
			   for(i = 0; i < 17; i ++)
			  {
					nTemp += num.substr(i, 1) * arrInt[i];
			   }
			   num += arrCh[nTemp % 11];  
				return num;  
	}  
	}
	if (len == 18)
	{
	re = new RegExp(/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/);
	var arrSplit = num.match(re);
	
	//检查生日日期是否正确
	var dtmBirth = new Date(arrSplit[2] + "/" + arrSplit[3] + "/" + arrSplit[4]);
	var bGoodDay;
	bGoodDay = (dtmBirth.getFullYear() == Number(arrSplit[2])) && ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));
	if (!bGoodDay)
	{
	//alert(dtmBirth.getYear());
	//alert(arrSplit[2]);
	//alert('输入的身份证号里出生日期不对！');
	return false;
	}
	else
	{
	//检验18位身份证的校验码是否正确。
	//校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
	var valnum;
	var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
	var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
	var nTemp = 0, i;
	for(i = 0; i < 17; i ++)
	{
	nTemp += num.substr(i, 1) * arrInt[i];
	}
	valnum = arrCh[nTemp % 11];
	if (valnum != num.substr(17, 1))
	{
	//alert('18位身份证的校验码不正确！应该为：' + valnum);
	return false;
	}
	return num;
	}
	}
	return false;
}

//立即领取礼包
function getCard(_cid){
	alert('敬请期待！');
	return false;
	if(typeof TM.user == 'undefined' || typeof TM.user == 'null'){
		art.dialog({id:'gift', content:"<p style=\"line-height:80px;height:80px;font-size:16px;color:#b80408;\">请先登录，再领取礼包！</p>", title:'领取礼包', width:'350', height:'80', lock:true})
		return false;
	}
	if(!_cid){
		return false;
	}
	TM.getcard = function(retval){
		art.dialog({id:'gift', content:"<p style=\"line-height:80px;height:80px;font-size:16px;color:#b80408\">您好，请您到游戏内点击浮点进行领取！</p>", title:'领取礼包', width:'350', height:'80', lock:true})
		
	}
	init_ajax('/Games/GetGift', api_params('getCard', {giftId:_cid,uid:TM.user.Uid}), "getcard");
	return true;
}
 */
//立即下载坦克窗口

TM.registerHandle("PopupCallBack", function($pop, _obj){
	if($(_obj).attr("data-andriod") != ""){
		$pop.find(".androidxz_a").show().attr("href", $(_obj).attr("data-andriod"));
		$pop.find(".androidmar_a ").show().attr("src", $(_obj).attr("data-qrcode")).css({width:115,height:115});
		if($(_obj).attr("data-apple") == ""){
			$pop.find(".androidmar_a ").css("margin-left",90);
			$pop.find(".androidxz_a ").css("margin-left",96);
		}
	}else{
		$pop.find(".androidxz_a").hide();
		$pop.find(".androidmar_a ").hide();
	}
	if($(_obj).attr("data-apple") != ""){
		$pop.find(".applemar_a").show().attr("href", $(_obj).attr("data-apple"));
		$pop.find(".iosidmar_a ").show().attr("src", $(_obj).attr("data-qrcode-ios")).css({width:115,height:115});
		if($(_obj).attr("data-andriod") == ""){
			$pop.find(".androidmar_a ").css("margin-left",90);
			$pop.find(".androidxz_a ").css("margin-left",96);
		}
	}else{
		$pop.find(".applemar_a").hide();
		$pop.find(".iosidmar_a ").hide();
	}
	
	var _pop = $pop.find(".xzbtn_p");
	if(_pop.eq(0).find(".androidxz_a").filter(":visible").length == 2){
		_pop.eq(0).find(".androidxz_a").css("margin-left", 0);
		_pop.eq(0).find(".applemar_a").css("margin-left", 27);
	}
	if(_pop.eq(1).find("img").filter(":visible").length == 2){
		_pop.eq(1).find("img").css("margin", 0);
	}
	
});

/* 
function AddFavorite(sURL, sTitle) {
  try {
    window.external.addFavorite(sURL, sTitle);
  } catch (e) {
    try {
      window.sidebar.addPanel(sTitle, sURL, "");
    } catch (e) {
      alert("加入收藏失败，请使用Ctrl+D进行添加");
    }
  }
} */

//弹出层
function $popup(arg1, arg2, _obj, _callback) {
	if(!$("#downloadnow").length){
		var _html = '<div class="rule_up" id="downloadnow"><p class="up_close"></p><div class="up_content01" style="height:335px;"><p class="xzbtn_p"><a href="javascript:;" class="androidxz_a fl"></a><a style="margin-left:27px" href="javascript:;" class="androidxz_a applemar_a fl"></a></p>';
			_html += '<p class="xzbtn_p" style="margin-top:30px;height:115px;"><a href="javascript:;" class="fl" style="margin:0 31px"><img src="'+image_url+'/images/weixin_side.jpg" class="androidmar_a "></a>';
			_html += '<a href="javascript:;" class="fl" style="margin:0 31px"><img src="'+image_url+'images/weixin_side.jpg" class="iosidmar_a fl"></a></p><p class="xztips_p" style="margin-top:10px;">温馨提示：使用手扫描二维码下载可更快捷方便</p></div></div>';
		$("body").append(_html);
	}
	
	var $arg1 = arg1;
	var $arg2 = arg2;
	var $pLeft = ($(window).width() - $($arg1).width()) / 2 + $(window).scrollLeft();
	var $pTop = ($(window).height() - $($arg1).height()) / 2 + $(window).scrollTop();
	$pTop = $pTop > 0 ? $pTop : 40;
	if($.browser.msie && parseInt($.browser.version) == 6) {
		$("html,body").css("overflow", "hidden");
	}
	$("<div class='gray'></div>").appendTo($("body")).height($(document).height()).fadeTo("fast", 0.4);
	$($arg1).css({
		display : 'block',
		position : 'absolute',
		left : $pLeft,
		top : $pTop,
		zIndex : 10000
	});
	
	_callback = _callback ? _callback : 'PopupCallBack';	
	var func = TM[_callback];
	if (typeof func == "function") {
		func($($arg1), _obj);
	}

	$($arg2 + ',' + ".gray").click(function() {
		$($arg1).hide();
		if($.browser.msie && parseInt($.browser.version) == 6) {
			$("html,body").css("overflow", "")
		};
		$(".gray").fadeOut(500, cb);
		function cb() {
			$(this).remove();
		}

		return false;
	});
	//窗口大小变化时调用
	$(window).bind('scroll resize', function(event) {
		var $pLeft = ($(window).width() - $($arg1).width()) / 2 + $(window).scrollLeft();
		var $pTop = ($(window).height() - $($arg1).height()) / 2 + $(window).scrollTop();
		$($arg1).animate({
			left : $pLeft,
			top : $pTop
		}, {
			duration : 500,
			queue : false
		})
	})
}


var $_GET = (function() {
	  var url = window.document.location.href.toString();
	  var u = url.split("?");
	  if (typeof(u[1]) == "string") {
	    u = u[1].split("&");
	    var get = {};
	    for (var i in u) {
	      var j = u[i].split("=");
	      get[j[0]] = j[1];
	    }
	    return get;
	  } else {
	    return {};
	  }
})();


/**
 * cookie 1.4.1
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD
		define(['jquery'], factory);
	} else if (typeof exports === 'object') {
		// CommonJS
		factory(require('jquery'));
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {

	var pluses = /\+/g;

	function encode(s) {
		return config.raw ? s : encodeURIComponent(s);
	}

	function decode(s) {
		return config.raw ? s : decodeURIComponent(s);
	}

	function stringifyCookieValue(value) {
		return encode(config.json ? JSON.stringify(value) : String(value));
	}

	function parseCookieValue(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape...
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}

		try {
			// Replace server-side written pluses with spaces.
			// If we can't decode the cookie, ignore it, it's unusable.
			// If we can't parse the cookie, ignore it, it's unusable.
			s = decodeURIComponent(s.replace(pluses, ' '));
			return config.json ? JSON.parse(s) : s;
		} catch(e) {}
	}

	function read(s, converter) {
		var value = config.raw ? s : parseCookieValue(s);
		return $.isFunction(converter) ? converter(value) : value;
	}
	$.fn.show_message = function(_message, status){
		if(status){
			$(this).html(_message);
		}else{
			$(this).html(_message).css({color:"red"});
		}
	}

	var config = $.cookie = function (key, value, options) {

		// Write

		if (value !== undefined && !$.isFunction(value)) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setTime(+t + days * 864e+5);
			}

			return (document.cookie = [
				encode(key), '=', stringifyCookieValue(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// Read

		var result = key ? undefined : {};

		// To prevent the for loop in the first place assign an empty array
		// in case there are no cookies at all. Also prevents odd result when
		// calling $.cookie().
		var cookies = document.cookie ? document.cookie.split('; ') : [];

		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = parts.join('=');

			if (key && key === name) {
				// If second argument (value) is a function it's a converter...
				result = read(cookie, value);
				break;
			}

			// Prevent storing a cookie that we couldn't decode.
			if (!key && (cookie = read(cookie)) !== undefined) {
				result[name] = cookie;
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) === undefined) {
			return false;
		}

		// Must not alter options, thus extending a fresh object...
		$.cookie(key, '', $.extend({}, options, { expires: -1 }));
		return !$.cookie(key);
	};

}));



(function($){
	var win = {
			height : parseInt($(window).height()),
			width :	parseInt($(window).width())
		};
	$(window).resize(function(){
		win = {
			height : parseInt($(window).height()),
			width :	parseInt($(window).width())
		};
	})





	//图片按需加载
	$.fn.LoadImg = function(option){
		var $_this = $(this),
			$_default= {
				src:"data-src"
			},
			$_options = $.extend($_default,option);
		$_this.each(function(){
			var _that = $(this),
				_top = parseInt(_that.offset().top),
				_src = _that.attr($_options.src),
			imgsroll = function(){
				_src = _that.attr($_options.src);
				if(_src=="" || typeof _src ==="undefined"){
						return false;
				}
				if(parseInt($(window).scrollTop())+win.height>=_top){
					_that.attr("src",_src);
					_that.removeAttr($_options.src);
				}
			};
			imgsroll();
			$(window).bind("scroll",function(){
				imgsroll();
			});
		});
	}



})(jQuery);