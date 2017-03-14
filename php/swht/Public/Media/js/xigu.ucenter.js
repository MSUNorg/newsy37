// xigu.ucenter lwx
(function () {

    var XGUCENTER = window.XGUCENTER = window.XGUCENTER || {};

    (function () {

        /* 修改密码 */
        XGUCENTER.edit_password = function () {

            /* 验证 */
            $('#af_password_form').validate({
                rules: {
                    old_password: {
                        required: true,
                        rangelength: [1, 20]
                    },
                    password: {
                        required: true,
                        rangelength: [6, 20]
                    },
                    confirm_password: {
                        required: true,
                        rangelength: [6, 20],
                        equalTo: '#password'
                    },
                    vcode: {
                        required: true,
                        remote: {
                            url: '/ajax/ucenter/check_vcode',
                            type: 'post',
                            dataType: 'text',
                            data: {
                                vcode: function() { return $('#vcode').val();  }
                            }
                        }
                    }
                },
                messages: {
                    old_password: {
                        required: '请输入您的旧密码',
                            rangelength: '长度应为{0}-{1}个字符'
                    },
                    password: {
                        required: '请输入新密码',
                            rangelength: '长度应为{0}-{1}个字符'
                    },
                    confirm_password: {
                        required: '请再输入一次密码',
                            rangelength: '长度应为{0}-{1}个字符',
                            equalTo: '两次密码输入不一致'
                    },vcode: {
                        required: '请输入验证码',
                        rangelength: '长度应为{0}-{1}个字符'
                    }
                },
                errorPlacement: function(error, elemt) {

                    $('#' + elemt.attr('id') + '_tip').removeClass('error').addClass('error').html(error.html()).show();
                },
                success: function(label, elemt) {

                    $('#' + $(elemt).attr('id') + '_tip').removeClass('error').html('').show();
                },
                onsubmit: false,
                focusInvalid: true,
                focusCleanup: false
            });

            /* 提交验证 */
            $('#af_password_form').submit(function() {

                if ($(this).valid()) {

                    $.ajax({
                        type: 'POST',
                        async: true,
                        dataType : 'json',
                        url: '/ajax/ucenter/edit_password',
                        data: $('#af_password_form').serialize(),
                        beforeSend: function(){
                            $('#af_password_form .sum').attr('disabled',true).val('保存中...');
                        },
                        success: function (request) {
                            switch (parseInt(request.status)) {
                                case 1:
                                    alert('修改成功，需要您重新登录');
                                    location.reload();
                                    break;
                                default:
                                    alert(request.info);
                                    $('#af_password_form .sum').attr('disabled',false).val('保存');
                                    break;
                            }
                        },
                        error: function () {
                            $('#af_password_form .sum').attr('disabled',false).val('保存');
                            alert('服务器故障，稍后再试');
                        },
                        cache: false
                    });

                    return false;
                } else {
                    return false;
                }
            });
        }

        /* 消费记录 */
        XGUCENTER.bill = function () {

        }


    })();

})();