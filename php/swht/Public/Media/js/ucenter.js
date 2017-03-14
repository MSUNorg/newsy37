//login_change();
//check_login();

$(function(){
	
	
	//修改密码
	$("#btn_mod_psd").click(function(){
		check_userform(4, true);
		$(this).closest(".infor_con").find("input").val("");
	});
	$("#oldpwd,#newpwd,#confirmpwd").keyup(function(){
		check_userform(4);
	}).blur(function(){
		$(this).trigger("keyup");
	});
	
	
	//获取手机验证码
	$("#getcode").click(function(){
		var mobile = $("#mobname").val();
		if(!(/^(1)[0-9]{10,12}$/.test(mobile))){
			$("#mobname").siblings(".tips").show_message("手机号码错误");
			return false;
		}else{
			$("#mobname").siblings(".tips").show_message("", true);
		}
		TM.registerHandle("GetCodeBoundPhone", function(retval){
			alert("已将验证码发送到你手机上");
		});
		init_ajax("/Accounts/GetCodeBoundPhone", api_params('GetCodeBoundPhone',{mobile:mobile, uid:TM.user.Uid}), "GetCodeBoundPhone");
	});
	
	//手机认证
	$("#btn_mobile").click(function(){
		check_userform(2, true);
		$("#yz_code").val("");
	});
	$("#mobname,#yz_code").keyup(function(){
		check_userform(2);
	}).blur(function(){
		$(this).trigger("keyup");
	});
	
	//个人信息保存
	$("#J_sub_info").click(function(){
		check_userform(0, true);
	});
	$("#J_nickname").keyup(function(){
		check_userform(0);
	}).blur(function(){
		$(this).trigger("keyup");
	});
	
	//身份证
	$("#btn_record").click(function(){
		check_userform(1, true);
	});
	$("#J_truename, #J_idcard").keyup(function(){
		check_userform(1);
	}).blur(function(){
		$(this).trigger("keyup");
	});
	
	
	//邮箱验证
	$("#btn_email").click(function(){
		check_userform(3, true);
	});
	$("#user_email").keyup(function(){
		check_userform(3);
	}).blur(function(){
		$(this).trigger("keyup");
	});
	
	//密保问题
	$("#btn_ask").click(function(){
		check_userform(5, true);
		
	});
	$("#oldanswer,#oldquestion,#newquestion,#answer1,#vapwd").keyup(function(){
		check_userform(5);
	}).blur(function(){
		$(this).trigger("keyup");
	});
	
});


/**
 * 用户中心表单检查
 * _handler 是否直接触发
 */
function check_userform(tabin, _handler){
	
	if(tabin == 0){
		var nickname = $("#J_nickname").val();
		if(nickname.length < 2 || nickname.length > 20){
			$("#J_nickname").next(".tips").show_message("昵称必须大于2而小于20个字符");
			return false;
		}else{
			$("#J_nickname").next(".tips").show_message("", true);
		}
		if(_handler){
			var _params = {
				nickname:nickname,
				sex:$("input[name=sex]").val(),
				birthday:$("select[name=year]").val()+"-"+$("select[name=month]").val()+"-"+$("select[name=day]").val(),
				education:$("#J_education").val(),
				occupation:$("#J_job").val(),
				qq:$("#J_qq").val(),
				address:$("#J_address").val(),
				uid:TM.user.Uid
			};
			TM.registerHandle("SetMemberInfo", function(retval){
				alert("保存成功");
			});
			init_ajax("/Accounts/SetMemberInfo", api_params("SetMemberInfo", _params), "SetMemberInfo");
		}
		
	}else if(tabin == 1){//身份证
		
		var truename = $("#J_truename").val();
		var idcard = $("#J_idcard").val();
		if(truename.length < 2){
			$("#msgname").show_message("真实姓名长度必须大于1个字符");
			return false;
		}else{
			$("#msgname").show_message("", true);
		}
		if(/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/.test(idcard)){
			$("#msgsfz").show_message("", true);
		}else{
			$("#msgsfz").show_message("身份证件号码不正确");
			return false;
		}
		if(_handler){
			var _params = {
				idcard:idcard,
				truename:truename,
				uid:TM.user.Uid
			};
			TM.registerHandle("SetMemberInfo", function(retval){
				alert("保存成功");
			});
			init_ajax("/Accounts/SetMemberInfo", api_params("SetMemberInfo1", _params), "SetMemberInfo");
		}
		
	}else if(tabin == 3){//邮箱验证
		var email = $("#user_email").val();
		if(email.validateEmail()){
			$("#user_email").next("span").show_message("", true);
		}else{
			if($("#user_email").next("span").length){
				$("#user_email").next("span").show_message("邮箱格式不正确");
			}else{
				$("#user_email").after("<span style='color:red'>邮箱格式不正确</span>");
			}
			return false;
		}
		
		if(_handler){
			art.dialog({id:'smail', content:"<p style=\"line-height:80px;height:80px;font-size:16px;color:#b80408;\" class='dialog_msg_tips'>请稍后，正在发送邮件...</p>", title:'发送邮件', width:'350', height:'80', lock:true})
			
			TM.registerHandle("SetMemberInfo2", function(retval){
				$(".dialog_msg_tips").text('已将验证码发送到你的邮箱，请前往查收。');
			});
			init_ajax("/Accounts/BoundEmail", api_params('SetMemberInfo2', {email:email,uid:TM.user.Uid}), "SetMemberInfo2");
		}
		
	}else if(tabin == 4){//修改密码
		var oldpwd = $("#oldpwd").val();
		var newpwd = $("#newpwd").val();
		var confirmpwd = $("#confirmpwd").val();
		if(!check_pwd($("#oldpwd").next(".tips"), oldpwd) || !check_pwd($("#newpwd").next(".tips"), newpwd)){
			return false;
		}
		if(newpwd != confirmpwd){
			$("#confirmpwd").next(".tips").show_message("确认密码与新密码不一致");
			return false;
		}else{
			$("#confirmpwd").next(".tips").show_message("", true);
		}
		if(_handler){
			TM.registerHandle("ChangePassword", function(retval){
				alert("密码修改成功");
			});
			init_ajax("/Accounts/ChangePassword", api_params('ChangePassword', {newPassword:newpwd,uid:TM.user.Uid,password:oldpwd}), "ChangePassword", true);
		}
		
	}else if(tabin == 2){//手机认证
		var mobile = $("#mobname").val();
		var verify_code = $("#yz_code").val();
		if(!validateMobile(mobile)){
			$("#mobname").siblings(".tips").show_message("手机号码错误");
			return false;
		}else{
			$("#mobname").siblings(".tips").show_message("", true);
		}
		if(verify_code.length != 6){
			$("#yz_code").siblings(".tips").show_message("验证码长度必须是6个字符");
			return false;
		}else{
			$("#yz_code").siblings(".tips").show_message("", true);
		}
		if(_handler){
			TM.registerHandle("BoundPhone", function(retval){
				init_member();
				alert("手机绑定成功");
			});
			init_ajax("/Accounts/BoundPhone", api_params('BoundPhone', {code:verify_code,uid:TM.user.Uid,mobile:mobile}), "BoundPhone", true);
		}
		
	}else if(tabin == 5){//密保问题
		var oldanswer = $("#oldanswer").val();
		var newquestion = $("#newquestion").val();
		var answer1 = $("#answer1").val();
		var oldquestion = $("#oldquestion").val();
		var pwd = $("#vapwd").val();
		
		if($.trim(oldanswer)){
			$("#oldanswer").next(".tips").show_message("", true);
		}else{
			$("#oldanswer").next(".tips").show_message("请输入密保答案");
			return false;
		}
		if($("#newquestion").length){
			if($.trim(newquestion)){
				$("#newquestion").next(".tips").show_message("", true);
			}else{
				$("#newquestion").next(".tips").show_message("请输入新密保问题");
				return false;
			}
		}
		if($("#answer1").length){
			if($.trim(answer1)){
				$("#answer1").next(".tips").show_message("", true);
			}else{
				$("#answer1").next(".tips").show_message("请输入新密保答案");
				return false;
			}
		}
		if($("#vapwd").length){
			if(pwd.length < 6 || pwd.length > 16){
				$("#vapwd").next("span").show_message("密码长度为6-16位");
				return false;
			}else{
				$("#vapwd").next("span").show_message("", true);
			}
		}
		
		if(_handler){
			TM.registerHandle("ResetSecurityQuestion", function(retval){
				$("#oldanswer,#newquestion,#answer1,#vapwd").val("");
				alert("密保问题设置成功");
			});
			init_ajax("/Accounts/ResetSecurityQuestion", api_params('ResetSecurityQuestion', {newquestion:newquestion,oldquestion:oldquestion,uid:TM.user.Uid,newanswer:answer1,answer:oldanswer,pwd:pwd}), "ResetSecurityQuestion");
		}
		
	}
	
}


function check_pwd($obj, str){
	if(/^\w{6,16}$/.test(str)){
		$obj.show_message("", true);
		return true;
	}
	$obj.show_message("密码为6位以上16位以下字母、数字、下划线");
	return false;
}



;(function($){
	  $.fn.birthday = function(options){
	  var opts = $.extend({}, $.fn.birthday.defaults, options);//整合参数
	  var $year = $(this).children("select[name="+ opts.year +"]");
	  var $month = $(this).children("select[name="+ opts.month +"]");
	  var $day = $(this).children("select[name="+ opts.day +"]");
	  MonHead = [31,28,31,30,31,30,31,31,30,31,30,31];
	  return this.each(function(){
	    var y = new Date().getFullYear();
	    var con = "";
	    //添加年份
	    for(i = y; i >= (y-55); i--){
	    con += "<option value='"+i+"'>"+i+""+"</option>";
	    }
	    $year.append(con);
	    con = "";
	    //添加月份
	    for(i = 1;i <= 12; i++){
	    con += "<option value='"+i+"'>"+i+""+"</option>";
	    }
	    $month.append(con);
	    con = "";
	    //添加日期
	    var n = MonHead[0];//默认显示第一月
	    for(i = 1; i <= n; i++){
	    con += "<option value='"+i+"'>"+i+""+"</option>";
	    }
	    $day.append(con);
	    $.fn.birthday.change($(this));
	    
	  });
	  };
	  $.fn.birthday.change = function(obj){
	  obj.children("select[name="+ $.fn.birthday.defaults.year +"],select[name="+ $.fn.birthday.defaults.month +"]").change(function(){
	    var $year = obj.children("select[name="+ $.fn.birthday.defaults.year +"]");
	    var $month = obj.children("select[name="+ $.fn.birthday.defaults.month +"]");
	    var $day = obj.children("select[name="+ $.fn.birthday.defaults.day +"]");
	    $day.empty();
	    var selectedYear = $year.find("option:selected").val();
	    var selectedMonth = $month.find("option:selected").val();
	    if(selectedMonth == 2 && $.fn.birthday.IsRunYear(selectedYear)){//如果是闰年
	    var c ="";
	    for(var i = 1; i <= 29; i++){
	      c += "<option value='"+i+"'>"+i+""+"</option>";
	    }
	    $day.append(c);
	    }else {//如果不是闰年也没选2月份
	    var c = "";
	    for(var i = 1; i <= MonHead[selectedMonth-1]; i++){
	      c += "<option value='"+i+"'>"+i+""+"</option>";
	    }
	    $day.append(c);
	    }
	  });
	  };
	  $.fn.birthday.IsRunYear = function(selectedYear){
	  return(0 == selectedYear % 4 && (selectedYear%100 != 0 || selectedYear % 400 == 0));
	  };
	  $.fn.birthday.defaults = {
	  year:"year",
	  month:"month",
	  day:"day"
	  };
})(jQuery);


