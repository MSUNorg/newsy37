var g_ajax_url = {
		'create_order':'/pay/create_order.php',
		'get_server_list':'http://app.5gwan.com:9000/server/server_list_ajax.php'
	};
var g_form_data = {pay_for:'',pay_id:'0',app_id:0,sid:0,username:'',money:0,bank:'',card_no:'',card_pwd:'',form_token:'', card_number:'',card_pwd:''};
var money_list_all = {
						'12':[10,20,50,100,200,500,1000,2000,5000,10000,20000], // 银行卡
						'7' :[20,50,100,200,500,1000,2000,5000,10000,20000], // 银联卡
						'11':[10,20,50,100,200,500,1000,2000,5000,10000,20000],// 支付宝
						'1':[10,20,30,50,100,300,500,1000],// 神州行
						'2':[20,30,50,100,300,500], // 联通卡支付
						'3':[50,100],	// 电信卡
						'10':[5,10,30,35,45,100,350,1000], // 盛大充值卡
						'4':[5,10,15,20,30,50,100,200,500,1000], // 骏网充值卡
						'13':[5,10,15,20,30,60,100,200], // Q币卡
						'14':[10,15,30,40,50], // 网易卡
						'15':[15,30,50,100], // 完美卡
						'16':[5,6,10,15,30,50,100], // 天下通
						'17':[5,10,15,20,30,50,100,500], // 天宏一卡通
                        '18':[ 1,5,10,15,30,50,100], // 汇付宝(骏网充值卡)
                        '19':[1,5,10,15,30,50,100,500,1000]
						};

/*手机号码验证*/
function checkMobile(mobile){
	 var reg=/^13[0-9]{1}[0-9]{8}$|14[57]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|18[02356789]{1}[0-9]{8}$/;   //130--139。至少7位
	 return reg.test(mobile);
}


function setCookie(name,value,seconds)//两个参数，一个是cookie的名子，一个是值
{
    var exp = new Date();    //new Date("December 31, 9998");
    exp.setTime(exp.getTime()+seconds*1000);
    document.cookie = name + "="+ escape (value)+ ";expires=" + exp.toGMTString()
}

function getCookie(name)//取cookies函数        
{
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
    if(arr != null) return unescape(arr[2]); return null;
}


function set_money_list(pay_id){
	var display_all = getCookie('display_all');
	var money_list = money_list_all[pay_id];
	html = '';
	if( money_list!='undefined' ){
		for(var i in money_list){
			html +='<a class="main_money_a" val="'+money_list[i]+'" id="money_'+i+'" onclick="select_money('+i+');">'+money_list[i]+'元</a>';
		}
	}
	$("#main_money_list").html(html);
	if(html){
		select_money(0);
	}
}

//选择金额 -1表输入金额
function select_money(index)
{
	var money = 0;
	$(".main_money .on,.main_other_money .on").removeClass('on');
	if(index==-1){
		money = $("#other_money").val();
		$("#money_9999").addClass('on');		
	}else{
		var o = $("#money_"+index);
		money = o.attr('val');	
		o.addClass("on");	
		$("#other_money").val("");		
	}
    
	g_form_data.money = money;
}

/*选择银行*/
function select_bank(key)
{
	if($('#bank_'+key).size()==0)
	{
		return false;
	}
	g_form_data.bank=key; // 保存选择的银行
	$("#main_bank a").removeClass("on");
	$('#bank_'+key).addClass("on");
}


/*确认订单提交*/
function confirm_submit(){
	$("#create_user_pay_order").submit();
}

function select_pay_for(select){
	select = select==''?'game':select;
	$(".main_payFor_a").removeClass('on');
	var o = $("#pay_for_"+select);
	if( o!='undefined' ){
		g_form_data.pay_for = select;
		o.addClass('on');
		g_form_data.pay_for = $("#pay_for_"+select).attr('val');
		select_pay_for_cnt(select);
	}
}

function select_pay_for_cnt(select){
	var o = $("#pay_for_"+select+"_cnt");
	if(o.is(":hidden")){
		$(".pay_for_cnt").hide();
		$("#pay_for_"+select+"_cnt").show();
	}
		
}

function more_bank(){
	$(".main_bank > a").each(function(){
		$(this).show();
	});
	$("#main_more_bank").hide();
}

// 确认订单(手游平台生成订单)
function confirm_order()
{
	g_form_data.username=$("[name='username']").val();
    g_form_data.card_number=$("[name='card_number']").val();
    g_form_data.card_pwd=$("[name='card_pwd']").val();
	var username_confirm=$("[name='username_confirm']").val();

/*    if(!$("#IKnow").attr("checked")){
        alert("请仔细阅读钱包使用说明！");
        return ;
    }*/
    if( g_form_data.username == '' ){
		alert('账户不能为空!');
		return ;
	}
	if( g_form_data.username != username_confirm ){
		alert('两次输入的账户不正确!');
		return ;
	}
    if(parseInt(g_form_data.pay_id) == 7){
        if( parseInt(g_form_data.money) < 15 ){
            alert('亲，最低充值15元!');
            return ;
        }    
    }else if(parseInt(g_form_data.pay_id) == 18){
        if( parseInt(g_form_data.money) < 1 ){
            alert('亲，最低充值1元!');
            return ;
        }

        if( g_form_data.card_number == '' ){
            alert('卡号不能为空!');
            return ;
        }
        if( g_form_data.card_pwd == '' ){
            alert('卡密不能为空!');
            return ;
        }      
    }else if(parseInt(g_form_data.pay_id) == 19){
        if( parseInt(g_form_data.money) < 1 ){
            alert('亲，最低充值1元!');
            return ;
        }  
        if( g_form_data.card_number == '' ){
            alert('卡号不能为空!');
            return ;
        }
        if( g_form_data.card_pwd == '' ){
            alert('卡密不能为空!');
            return ;
        }
    }else{
        if( parseInt(g_form_data.money) < 5 ){
            alert('亲，最低充值5元!');
            return ;
        }
    }
	$.ajax({
		type:"POST",
		async:false,
		url:g_ajax_url.create_order,
		data:g_form_data,
		dataType:'json',
		success:function(data){
			if(data.code==1){
				location.href="/pay/step2.php";
			}else{
				alert(data.msg);
				return;
			}
		}
	});
}

function get_server_list(app_id_main,sid){
	g_form_data.sid=sid;
	$.sendAjax({
		type:"GET",
		bridge:'/bridge/bridge.html',
		cache:false,
		url:g_ajax_url.get_server_list+"?app_id_main="+app_id_main,
		dataType:'json',
		success:function(data){
			var html = "<option value=''>请选择服务器</option>";
			if(data.state==1){
				var server_list = data.data;
				for(var i in server_list){
					html += "<option value='"+server_list[i].sid+"'>"+server_list[i].server_name+"</option>"
				}
				$("#game_server_list").html(html);
				$("#game_server_list").val(sid);
			}else{
				$("#game_server_list").html(html);
				alert('网络异常!');
				return;
			}
		}
	});
}

$(function(){
    
	$(".main_payFor_a").click(function(){
		select_pay_for($(this).attr('val'));
	})

    $("#game_select").change(function(){
		g_form_data.app_id = $(this).val();
		get_server_list($(this).find('option:selected').attr('lang'),'');
	})
	
	$("#game_server_list").change(function(){
		g_form_data.sid = $(this).val();
	})
	
	//其他金额a标签
	$("#money_9999").click(function(){
		select_money(-1);
	
	});
	//其他金额输入框
	$("#other_money").focus(function(){
		select_money(-1);
	});	
	
	$("#other_money").change(function(){
		var o = $(this);
		var v = o.val();
		v = v.replace(/[^0-9]/ig,'');
		o.val(v);
		select_money(-1);
	});		
			
	//选择银行
	$(".main_bank a").click(function(){
		select_bank($(this).attr('val'));
	});
	
	//提交充值 生成订单
	$(".main_confirm a").click(function(){
			confirm_order();			
	});
	
	$("#confirm_submit").click(function(){
		confirm_submit();
	});	

	$(".main_bank a:eq(0)").click();
	$(".main_money a:eq(0)").click();
	
});