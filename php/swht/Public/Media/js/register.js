var register = function () {
    (function i() {
        $('.g-register-popp').find('input[type=text]').val('');
        $('.g-register-popp').find('input[type=password]').val('');
        $('.g-register-popp').find('.input-optimize').removeClass('error').removeClass('correct').find('.placeholder').show();
        
    })();		
    $('.checkcode').on('click',function() {
        var e = (new Date).getTime();
        $(this).attr('src', UURL+'verify/t/' + e);
    });
    $('.m-register-box').find('.checkbox-optimize').on('click',function() {
        $(this).hasClass('active') ? ($(this).removeClass('active'), $(this).children('input[type=hidden]').val(0), $(this).siblings('.agree-txt').removeClass('active')) : ($(this).addClass('active'), $(this).children('input[type=hidden]').val(1), $(this).siblings('.agree-txt').addClass('active'))
    });
    $('#imeLogin').on('click',function() {
        login('xglogin');
    });
    var e = $('.input-optimize'),i = $('#getSafeCode');
    e.on('click','.placeholder',function() {
        $(this).hide(),
        $(this).siblings('input').focus(),
        $(this).siblings('input').val().match(/^\s*|\s*$/g) && $(this).siblings('input').val('')
    });
    e.on('blur', 'input',function() {
        var e = $(this).siblings('.placeholder');
        0 != e.length && ('' == $(this).val() || $(this).val() == e.text()) && ($(this).val(''), e.show())
        $(this).closest('.input-optimize').removeClass('error');
        $(this).siblings('.error-msg').text('');
    });
    e.on('focus', 'input',function() {
        var e = $(this).siblings('.placeholder');
        0 != e.length && e.hide()
    });
    e.on('click', '.clear-text',function() {
        $(this).siblings('input').val('');
        var e = $(this).siblings('.placeholder');
        $(this).parents('.input-optimize').removeClass('error').removeClass('correct'),
        $(this).parents('.input-optimize').find('.error-msg').text(''),
        0 != e.length && ('' == $(this).val() || $(this).val() == e.text()) && ($(this).val(''), e.show()),
        i.addClass('disabled')
    });			
    var form_id = 'mPhoneRegisterForm';
    if (form_id == 'mPhoneRegisterForm')
        phone_reg(form_id);
    else 
        name_reg('mNameRegisterForm');			
    $('.tab-trigger-bar a').on('click',function() {
        $(this).addClass('active').siblings().removeClass('active');
        $id = $(this).attr('data-target');
        $('#'+$id).addClass('active').siblings().removeClass('active');
        form_id = $id+'Form';
        if (form_id == 'mPhoneRegisterForm')
            phone_reg(form_id);
        else 
            name_reg('mNameRegisterForm');
    });	
},
phone_reg = function(form_id) {
    if (!form_id) {
        form_id = 'mPhoneRegisterForm';
    }
    var s = function() {
        that = $('#getSafeCode');
        if (that.text() !== '免费获取安全码') {
            return ;
        }
        that.removeClass('disabled');       
        that.on('click',function() {
            // 发送安全码
            if (!$(this).hasClass('disabled')) {
                var e = this,p = $.trim($('#registerPhone').val());
                $(e).addClass('disabled');
                $.ajax({
                    type:'post',
                    url: UURL+'telsvcode',
                    data: 'phone='+p,
                    dataType: 'json',
                    success: function(d) {
                        if (parseInt(d.status) == 1) {
                            $(e).siblings('span').addClass('msg-success').text(d.msg);
                            r && r(parseInt(d.status))
                        } else {
                            $(e).siblings('span').addClass('msg-fail').text(d.msg);
                        }   
                    }
                });
                var r = function(i) {
                    if (1 == i) {
                        var r = 60;
                        $(e).addClass('disabled');
                        var a = setInterval(function() {
                            r--;
                            $(e).text(r + '秒后重发'),
                            0 == r && ($(e).removeClass('disabled'), $(e).text('免费获取安全码'), clearInterval(a))
                        },1e3)
                    }
                };						
            }
            return false;
        });
    },f = function(id,msg,flag) {
        $('#'+id).siblings('.error-msg').text(msg);
        $('#'+id).closest('.input-optimize').addClass('error');
        if (flag) {
            $('#getSafeCode').addClass('disabled');	
            $('#getSafeCode').unbind('click');
        }
    };			
    $('#' + form_id + ' #registerByPhoneSubmit').val('注册').attr('disabled', false);
    $('#registerPhone').blur(function() {
        var phone = $.trim($(this).val());
        if (phone == '') {f('registerPhone','手机号码不能为空',true);return;}
        if (!(/^[1][358][0-9]{9}/.test(phone))) {
            f('registerPhone','手机号码格式不正确',true);return;
        }
        $.post(UURL+'checkPhone',{username:phone},function(data){
            if (parseInt(data.status) != 1) {
                $('#getSafeCode').addClass('disabled');
                f('registerPhone',data.msg);
                $('#registerPhone').closest('.input-optimize').removeClass('correct').addClass('error');
                return;
            }
            s();
            $('#registerPhone').closest('.input-optimize').removeClass('error').addClass('correct');
            return;
        });
    });
    $('#registerSafeCode').blur(function() {
        var code = $.trim($(this).val());
        if (code == '') {f('registerSafeCode','安全码不能为空',false);return;}
    });
    $('#registerPhonePass').blur(function() {
        var pwd = $.trim($(this).val());
        if (pwd == '') {f('registerPhonePass','密码不能为空',false);return;}
    });
    
    $('#' + form_id).unbind('submit').bind('submit',function(event) {
        event.preventDefault();
        var phone = $.trim($('#registerPhone').val()),
        code = $.trim($('#registerSafeCode').val()),
        pwd = $.trim($('#registerPhonePass').val());
        if (phone == '') {f('registerPhone','手机号码不能为空',true);return false;}
        if (!(/^[1][358][0-9]{9}/.test(phone))) {f('registerPhone','手机号码格式不正确',true);return false;}
        if (code == ''){f('registerSafeCode','安全码不能为空',false);return false;}
        if (pwd == ''){f('registerPhonePass','密码不能为空',false);return false;}
        if (!$('#' + form_id + ' #registerByPhoneAgreeTxt').hasClass('active')) {
            alert('还没有同意注册协议呢！');
            return false;
        }				
        $.ajax({
            type: 'POST',
            async: true,
            dataType: 'json',
            url: UURL + 'telregister',
            data: $('#' + form_id + '').serialize(),
            beforeSend: function() {
                $('#' + form_id + ' #registerByPhoneSubmit').val('注册中').attr('disabled', true);
            },
            success: function(data) {
                switch (parseInt(data.status)) {
                case 1:
                    $('#notice').removeClass('fail').addClass('success').text(data.msg);
                    setTimeout(function() {
                        var reurl = data.reurl;
                        if (reurl) {
                            location.href = reurl;
                        } else {
                            location.href = UURL+'plogin';
                        }
                    },2000);
                    break;
                default:
                    $('#notice').removeClass('success').addClass('fail').text(data.msg);
                    $('#' + form_id + ' #registerByPhoneSubmit').val('注册').attr('disabled', false);
                    break;
                }
            },
            error: function() {
                alert('服务器故障，稍后再试')
                $('#' + form_id + ' #registerByPhoneSubmit').val('注册').attr('disabled', false);
            },
            cache: false
        }); 
    });
},
		
name_reg = function(form_id) {
    if (!form_id) {
        form_id = 'mNameRegisterForm';
    }
    var f=function(id,msg) {
        $('#'+id).siblings('.error-msg').text(msg);
        $('#'+id).closest('.input-optimize').addClass('error');
    };
    $('#' + form_id + ' #registerByNameSubmit').val('注册').attr('disabled', false);	
    
    $('#userNameByName').blur(function() {
        var name = $.trim($(this).val());
        if (name == '') {f('userNameByName','用户名不能为空！');return;}
        if (name.length<6 || name.length>30){f('userNameByName','6~30位数字、字母或下划线');return;}
        if (!(/^[a-zA-Z]+[0-9a-zA-Z_]{5,29}$/.test(name))){f('userNameByName','用户名必须由字母和数字组成,以字母开头');return;}
        $.post(UURL+'checkUser',{username:name},function(data){
            if (parseInt(data.status) != 1) {
                f('userNameByName',data.msg);
                $('#userNameByName').closest('.input-optimize').removeClass('correct').addClass('error');
                return;
            }
            $('#userNameByName').closest('.input-optimize').removeClass('error').addClass('correct');
            return;
        });
    });
    $('#userPass').blur(function() {
        var pwd = $.trim($(this).val());
        if (pwd == '') {f('userPass','密码不能为空！');return;}
        if (pwd.length<6 || pwd.length>30){f('userPass','6~30位数字、字母或特殊字符组成');return;}
    });
    $('#userConfirmPasss').blur(function() {
        var repwd = $.trim($(this).val()),
        pwd = $.trim($('#userPass').val());
        if (repwd == '') {f('userConfirmPasss','重复密码不能为空！');return;}
        if (pwd != repwd){f('userConfirmPasss','两次密码不一致');return;}
    });
    $('#registerNameVcode').blur(function() {
        var code = $.trim($(this).val());
        if (code == '') {f('registerNameVcode','验证码不能为空！');return;}
    });
    
    $('#' + form_id).unbind('submit').bind('submit',function(event) {
        event.preventDefault();
        var name = $.trim($('#userNameByName').val()),
            pwd = $.trim($('#userPass').val()),
            repwd = $.trim($('#userConfirmPasss').val()),
            code = $.trim($('#registerNameVcode').val());
        if (name == '') {f('userNameByName','用户名不能为空！');return false;}
        if (name.length<6 || name.length>30){f('userNameByName','6~30位数字、字母或下划线');return false;}
        if (!(/^[a-zA-Z]+[0-9a-zA-Z_]{5,29}$/.test(name))){f('userNameByName','用户名必须由字母和数字组成,以字母开头');return false;}
        if (pwd == '') {f('userPass','密码不能为空！');return false;}
        if (pwd.length<6 || pwd.length>30){f('#userPass','6~30位数字、字母或特殊字符组成');return false;}
        if (repwd == '') {f('userConfirmPasss','重复密码不能为空！');return false;}
        if (pwd != repwd){f('userConfirmPasss','两次密码不一致');return false;}
        if (code == '') {f('registerNameVcode','验证码不能为空！');return false;}
        if (!$('#' + form_id + ' #registerByNameAgreeTxt').hasClass('active')) {
            alert('还没有同意注册协议呢！');
            return false;
        }				
        $.ajax({
            type: 'POST',
            async: true,
            dataType: 'json',
            url: UURL + 'register',
            data: $('#' + form_id + '').serialize(),
            beforeSend: function() {
                $('#' + form_id + ' #registerByNameSubmit').val('注册中').attr('disabled', true);
            },
            success: function(data) {
                switch (parseInt(data.status)) {
                case 1:
                    $('#notice').removeClass('fail').addClass('success').text(data.msg);
                    setTimeout(function() {
                        var reurl = data.reurl;
                        if (reurl) {
                            location.href = reurl;
                        } else {
                            location.href = UURL+'plogin';
                        }                                                                
                    },2000);
                    break;
                default:
                    $('#notice').removeClass('success').addClass('fail').text(data.msg);
                    $('#' + form_id + ' #registerByNameSubmit').val('注册').attr('disabled', false);
                    break;
                }
            },
            error: function() {
                alert('服务器故障，稍后再试')
                $('#' + form_id + ' #registerByNameSubmit').val('注册').attr('disabled', false);
            },
            cache: false
        }); 
    });
};
register();
        