var USER_URL="index.php?s=/Home/Index/";
$(function() {
    jQuery.validator.addMethod('isMobile',function(values,element) {
        var mobile = /^1[3|5|7|8][0-9]{9}$/;
        return this.optional(element) || (mobile.test(values));
    },'手机格式不正确，请重新输入');
    
	$('#login_form').validate({
        rules: {
            
            account: {
                required: true,
				rangelength: [6, 30],
                remote: {
                    url: USER_URL+'checkAccount',
                    type: 'post',
                    dataType: 'json',
                    data: {account:function(){return $('#account').val();} }
                }
            },
            password: {
                required: true,
                rangelength: [6, 30]
            },
            repassword: {
                required: true,
				rangelength: [6, 30],
                equalTo: "#password"
            },
            real_name: {
                required: true,
                rangelength: [2, 30]
            },
            mobile_phone: {
                required: true,
                rangelength:[11,11],
                isMobile: true
                // remote: {
                //     url: USER_URL+'checkMobile',
                //     type: 'post',
                //     dataType: 'json',
                //     data: {phone:function(){return $('#mobile_phone').val();} }
                // }
            },
            agreement: {
                required: true
            }
        },
        messages: {
            account: {
                required: '请输入您的用户名',
				rangelength: '长度应为{0}-{1}个字符',
				remote:'用户名已经被注册'
            },
            password: {
                required: '请输入密码',
                rangelength: '长度应为{0}-{1}个字符'
            },
            repassword: {
				required: '请输入确认密码',
				rangelength: '长度应为{0}-{1}个字符',
				equalTo: '两次输入密码不一致'
			},
            real_name: {
                required: '请输入联系人',
                rangelength: '长度应为{0}-{1}个字符'
            },
            mobile_phone: {
                required: '请输入您的联系电话',
                rangelength: '请输入{0}位手机号码'
                //remote: '手机已被占用',
            },
            agreement: {
                required: '您还未同意梦创渠道合作协议'
            }
        },
        errorPlacement: function(error, elemt) {
            $('#' + elemt.attr('id') + '_tip').addClass('error').html(error.html()).show();
        },
        success: function(label, elemt) {
            $('#' + $(elemt).attr('id') + '_tip').removeClass('error').html('').hide();
        },
        onsubmit: false,
        ignore : '.ignore'
    });
	
	/* 提交验证 */
    $('#login_form').submit(function() {
        if ($(this).valid()) {
            $.ajax({
                type: 'POST',
                async: true,
                dataType : 'json',
                url: USER_URL+'register',
                data: $('#login_form').serialize(),
                beforeSend: function(){
                    $('#login_form .btn').attr('disabled',true).val('提交中...');
                },
                success: function (request) {
                    switch (parseInt(request.status)) {
                        case 1:
							$('#login_form .btn').attr('disabled',false).val('立即注册');
                            layer.msg('注册成功!账号需要审核请耐心等候', {icon: 4,time:3000});
							setTimeout(function() {
                                window.location.href=request.url;                               
                            },3000);
                            break;
                        default:
                            layer.msg(request.info, {icon: 1,time:2600});
                            $('#login_form .btn').attr('disabled',false).val('立即注册');
                            break;
                    }
                },
                error: function () {
                    layer.msg('服务器故障，请稍后再试', {icon: 1,time:2600});
                    $('#login_form .btn').attr('disabled',false).val('立即注册');
                },
                cache: false
            });

            return false;
        } else {
            return false;
        }
    });
	
});

