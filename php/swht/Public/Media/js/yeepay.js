
// JavaScript Document
// 左侧切换
	$(function(){
		$('.pay_r .bb').eq(0).show().nextAll().hide();
		$('.pay_l ul li').eq(0).addClass('li_h').siblings().removeClass('li_h');
		
		$('.pay_l ul li').click(function(){
			if($("#subfm_pay").length){
				return false;
			}
            $('.pay_l ul li').attr('class','');
            // var n = $(this).find('span').attr('id');
            // $(".g").each(function(){
            //     var d = $(this).attr('id');
            //     $('#'+d).attr('class','g '+ d)
            // });
            // $('#'+n).attr('class', 'g '+n+'_1');  

            $(this).addClass('li_h');
			var cur = $('.pay_r .bb').eq($('.pay_l ul li').index(this));
            $('.pay_r .bb').hide().eq($('.pay_l ul li').index(this)).fadeIn().nextAll().hide();
			$('.close').click();
		});
	});
	

// 充值方式切换
$(function(){
	$('.pay_r_box .cc').eq(0).show().nextAll().hide();
	$('.pay_r_czfs ul li').eq(0).addClass('li_s').siblings().removeClass('li_s');		
	$('.pay_r_czfs ul li').click(function(){
		
        $('.pay_r_czfs ul li').attr('class','');
        $(this).addClass('li_s');
        $('.pay_r_box .cc').hide().eq($('.pay_r_czfs ul li').index(this)).show().nextAll().css("display","none");
		$('.close').click();
	});
});

	
	
// 更多银行
  $(document).ready(function(){
  $(".yh1").click(function(){
  $(".ycyh").show();
  $(".yh2").show();
  $(this).hide();
  });
  $(".yh2").click(function(){
  $(".ycyh").hide();
  $(".yh1").show();
  $(this).hide();
  }).hide();
});
  
  
 


function get_game_coin(){
	var money = parseInt($('#money2').val());
	if(!money){
		money = parseInt($('#yee input[name="money"]:checked').val());
	}else{
		$('#yee input[name="money"]:checked').attr('checked', false)
		.parent().removeClass('money_checked');
	}

	if(isNaN(money)) money = 0;
	//获取返利卷
	if(typeof(TM.user) != "undefined"){
		var obj = $('#flq_div1');
		var uid = TM.user.Uid;
		get_coupons(obj, uid, money);
	}

	$('#game_coin').html(money * 10);
}

function get_game_coin2(){
	var money = parseInt($('#money3').val());

	if(!money){
		money = parseInt($('#alipay input[name="money"]:checked').val());
	}else{
		$('#alipay input[name="money"]:checked').attr('checked', false).parent().removeClass('money_checked');
	}
	
	if(isNaN(money)) money = 0;
	$('#game_coin2').html(money * 1);
}

function get_game_coin3(){
	var money = parseInt($('#money4').val());
	if(!money){
		money = parseInt($('#wechat input[name="money"]:checked').val());
	}else{
		$('#wechat input[name="money"]:checked').attr('checked', false)
		.parent().removeClass('money_checked');
	}

	if(isNaN(money)) money = 0;
	//获取返利卷
	if(typeof(TM.user) != "undefined"){
		var obj = $('#flq_div3');
		var uid = TM.user.Uid;
		get_coupons(obj, uid, money);
	}
	$('#game_coin3').html(money * 1);
}


// function get_coupons(obj, uid, amount){
// 	$.ajax({
// 		type:'GET',
// 		dataType:"json",
// 		url:api_url+'/Common/PayWeb/displayCoupons',
// 		data:{'uid':uid, 'pay_amt':amount,device:3},
// 		success:function(res){
// 			if(res && res.Data.length){
// 				var _html = '';
// 				$.each(res.Data, function(_index, _val){
// 					if(_val.active == true){
// 						_html += '<span class="flqspan q100">'+parseInt(_val.amount)+'返利劵</span>';
// 					}else{
// 						_html += '<span class="flqspan">'+parseInt(_val.amount)+'返利劵</span>';
// 					}
// 				});
// 				obj.html(_html);
// 			}else{
// 				obj.html('没有可用的返利券');
// 			}
// 		}

// 	});
// }

function isLogin(){
	var status = false;
	$.ajax({
	   type: 'POST',
	  async: false,
  dataType : 'json',
		url: USER_URL+'isLogin',
	   data: '',
	success: function (data) {
				status = data;
			},
	  error: function () {
	  		alert("服务器异常");
	  		status = false;
		}
	});	
	return status;
}

$(function(){
	//pay_back();
	//get_token();
	//payLogin();
	if(typeof TM.cache == "undefined" || !TM.cache){
		TM.cache = $("#change_box").html();
	}
	//充值金额切换
	$(".pay_money_box span:not('.last_money')").click(function(){

			$(this).addClass("money_checked").siblings("span").removeClass("money_checked");
			$(this).find('input').attr('checked', 1);
			$(this).parent().find('.money2').val('');
			get_game_coin();
			get_game_coin2();
			get_game_coin3();
	});
	get_game_coin();
	get_game_coin2();
	get_game_coin3();

	$(".ptb").click(function(){
		$('#ptbtips').toggle();		
	});
	$(".ptb2").click(function(){
		$('#ptbtips2').toggle();		
	});
	$(".ptb3").click(function(){
		$('#ptbtips3').toggle();		
	});
	$(".ptbtipsclose").click(function(){
		$('#ptbtips,#ptbtips2,#ptbtips3').hide();		
	});

	
	
	//支付宝
	$("#alipay_action").on("click", function(){
		
		if($.trim($("#toaccount2").val()).length == 0){
			alert("充值账号不能为空!");
			return false;
		}

		if($.trim($("#retoaccount2").val()).length != $.trim($("#toaccount2").val()).length){
			alert("两次账号不相同!");
			return false;
		}

		if($('#alipay input[name="money"]:checked').val() == undefined){
			if($("#money3").val() == 0){
				alert("请输入充值金额");
				return false;
			}
		}
		var $amount = $('#alipay input[name="money"]:checked').val() == undefined?$("#money3").val():$('#alipay input[name="money"]:checked').val()
		$("#alipay_amount").val($amount);
		$("#form_alipay").submit();

	});
	
	//微信支付
	$("#wechat_action").on("click", function(){
		var that = $('#form_wxpay');
		if($.trim($("#toaccount3").val()).length == 0){
			alert("充值账号不能为空!");
			return false;
		}

		if($.trim($("#retoaccount3").val()).length != $.trim($("#toaccount3").val()).length){
			alert("两次账号不相同!");
			return false;
		}

		if($('#wechat input[name="money"]:checked').val() == undefined){
			if($("#money4").val() == 0){
				alert("请输入充值金额");
				return false;
			}
		}
		var $amount = $('#wechat input[name="money"]:checked').val() == undefined?$("#money4").val():$('#wechat input[name="money"]:checked').val()
		$("#wxpay_amount").val($amount);
		var loading = new Cute.ui.dialog().loading('加载中...',{mask:true});
		Cute.api.post("/media.php/Recharge/wxpay",that.serialize(), function(json){
			loading.close();
			if(json.status > 0){

				that.dialog = new Cute.ui.dialog().layer('微信扫码支付',{
					content: json.html,
					mask: true,
					open:{
						// callback: function(){
						// 	that.timer = setInterval(function(){
						// 		Cute.api.post('pay/wx_order_query',{'order_no':json.data.out_trade_no}, function(data){
						// 			if(data.status > 0){
						// 				pay_callback();
						// 				clearInterval(that.timer);
						// 			}
						// 		});
						// 	},4000);
						// }
					},
					buttons: [{
			            title: '已完成支付',
			            type: 'main',
			            close: true,
			            // func: function(){
			            // 	location.href = "/account/order.html";
			            // }
			        }, {
			            title: '重新选择',
			            type: 'cancel',
			            close: true
			        }]
				});
			}else{
				new Cute.ui.dialog().alert(json.info);
			}
		});

		//$("#form_wxpay").submit();
	});

	function pay(datas,amount){
		//var josn_data = {'uid':datas['uid'],'account':datas['username'],'amount':amount,'type':1};
			$.ajax({
		   type: 'POST',
		  async: false,
	  dataType : 'json',
			url: USER_URL+'books',
		   data: {'uid':datas['uid'],'account':datas['username'],'amount':amount,'type':1},
		success: function (data) {
					if(parseInt(data['status']) == 1){
						window.frames["qr_code_view"].src =data['url'];//"Subscriber/view_wx";
						XGUC.reg_close();
						$('#qr_code_mask').fadeIn(500);
						$('#qr_code').fadeIn(500);
					}else{
						status = false;
					}
				},
		  error: function () {
		  		alert("服务器异常");
		  		status = false;
			}
		});	
	}

	$('#qr_code .login_close,#qr_code_mask').click(function(){
		XGUC.login_close1();
	});


	XGUC.login_close1 = function() {
		$('#qr_code_mask').hide();
		$('#qr_code').hide();
	};
});
