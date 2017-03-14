/**
* 弹窗函数 lwx
* title 弹窗标题，content 内容，bottom 按钮，close 关闭，delay 等待时间，url 链接数组
* pop('提示信息','提示内容',{ok:'确定'},true,1000,{url:'',time:1000})
*/
function pop(title,content,bottom,close,delay,url) {
	close = close?close:false;
	title = title?title:'提示';
	var closebtn='';
	if (close) {
		closebtn = '<a href="javascript:;" class="popclose">X</a>';
	}
	if (bottom) {
        if (typeof(bottom) == 'function') {
            bottom = bottom();
        }else if (typeof(bottom) == 'undefined'){
            bottom = '';
        } else {
            var b = bottom,bottom='';
            for(var i in b) {
                if (i.toLowerCase() == 'ok') {
                    bottom += '<a href="javascript:;" class="btn ok" >'+b[i]+'</a>';
                }
            };
        }        
	} else {bottom='';}
	$('body').append('<div class="pop active"><div class="m-mask"></div><div class="m-box ">'+closebtn+'<div class="notice"><div class="title">'+title+'</div><div class="content"><div class="subcontent">'+content+'</div></div><div class="bottom">'+bottom+'</div></div></div></div>');
	$('.popclose').on('click',function() {
		$(this).closest('.pop').remove();
		if (url) {
			setTimeout(function() {
				window.location = url.url;
			},url.time);
		}
	});
	$('.ok').on('click',function() {
		$('.popclose').click();
	});
	
	if (delay) {
		setTimeout(function(){
			$('.popclose').click();
		},delay);
	}
}

// ajax异步post请求
function ajaxpost(url,data,callback,beforeback,errorback) {
	$.ajax({
		type: 'POST',
	  async: true,
  dataType : 'json',
		url: url,
	   data: data,
 beforeSend: beforeback,
	success: callback,
	error: errorback,
	cache: false	
	});
}
// 身份证号码验证
function checkidcard(idcard) {
	if ((/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/).test(idcard)) {
		if (idcard.length == 18) {
			var idcardwi = new Array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
			var idcardy = new Array(1,0,10,9,8,7,6,5,4,3,2);
			var idcardwisum=0;
			for(var i=0;i<17;i++) {
				idcardwisum += idcard.substring(i,i+1)*idcardwi[i];
			}
			var idcardmod = idcardwisum%11;
			var idcardlast = idcard.substring(17);
			// 如果等于2，则说明效验码时10，最后一位为X
			if (idcardmod == 2) {
				if (idcardlast == 'X' || idcardlast == 'x') {
					return true;
				} else {
					return false;
				}
			} else {
				if (idcardlast == idcardy[idcardmod]) {
					return true;
				} else {
					return false;
				}
			}
		}
	} else {
		return false;
	}
}

// 更换验证码
function verify(that) {
    var imgsrc = UURL+'verify/'+(new Date().getTime());
    $(that).attr('src',imgsrc);
}

function sesend(id,content,time) {
    $that = $('#'+id);
    if (!$that.hasClass('disabled')) {$that.addClass('disabled').attr('disabled',true);}
    var ebr = time || 111,si = setInterval(function() {
        ebr--;
        if (ebr == 0) {
            clearInterval(si);
            $that.text(content).removeClass('disabled').removeAttr('disabled').on('click',function() {
                window.location.reload();
            });
        } else
            $that.text(ebr+'秒后'+content);
    },1000);   
}








