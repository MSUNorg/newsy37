// xigu  lwx
(function() {

    var XGUC = window.XGUC = window.XGUC || {};

    (function() {

        XGUC.init = function() {

            XGUC.is_login();

        };

        XGUC.is_login = function() {

            $.ajax({

                type: 'POST',

                async: false,

                dataType: 'json',

                url: UURL + 'isLogin',

                data: '',

                success: function(data) {

                    switch (parseInt(data['status'])) {

                    case 1:
						$('.withoutLogon-link').addClass('hidden');
						$('logon-link').removeClass('hidden').find('#topShowName').text('您好，'+data.username);
                        
                        return;

                        break;

                    default:
						$('.withoutLogon-link').removeClass('hidden');
						$('logon-link').addClass('hidden').find('#topShowName').empty();
                        
                        return false;

                        break;

                    }

                },

                error: function() {
					$('.withoutLogon-link').removeClass('hidden');
					$('logon-link').addClass('hidden').find('#topShowName').empty();
                },

                cache: false

            });
			
			$('.top-login-link').on('click',function() {
				XGUC.login('loginform');				
			});
			
			$('.top-register-link').on('click',function() {
				XGUC.reg('registerform');				
			});

            $('#xglogin .link-close,.m-mask').click(function() {

                XGUC.login_close();

            });

            $('#xguc_reg .reg_close,#xguc_mask').click(function() {

                XGUC.reg_close();

            });
        };

        XGUC.login_out = function() {

            $.ajax({

                type: 'POST',

                async: false,

                dataType: 'json',

                url: UURL + 'logout',

                data: 'reurl=' + location.href,

                success: function(data) {

                    if (data.reurl) {

                        location.href = data.reurl;

                    } else {

                        location.reload();

                    }

                },

                cache: false

            });

        };

        XGUC.login = function(form_id) {

            XGUC.reg_close();

            if (!form_id) {

                form_id = 'loginform';

            }

            //$('#' + form_id)[0].reset();

            if (form_id == 'loginform') {
				$('#xglogin').addClass('active');
                /* $('#xguc_mask').fadeIn(500);

                $('#xguc_login').fadeIn(500); */

            }

            $('#' + form_id + ' #loginSubmit').val('登录').attr('disabled', false);

            $('#' + form_id).unbind('submit').bind('submit',
            function(event) {

                event.preventDefault();

                var username = $('#' + form_id + ' #loginInputUname').val();

                if (username == '') {

                    alert('手机/邮箱/用户名不能为空！');

                    $('#' + form_id + ' #loginInputUname').focus();

                    return false;

                }

                var password = $('#' + form_id + ' #loginPassword').val();

                if (password == '') {

                    alert('密码不能为空！');

                    $('#' + form_id + ' #loginPassword').focus();

                    return false;

                }

                $.ajax({

                    type: 'POST',

                    async: true,

                    dataType: 'json',

                    url: UURL + 'login',

                    data: $('#' + form_id + '').serialize(),

                    beforeSend: function() {

                        $('#' + form_id + ' #loginSubmit').val('登录中').attr('disabled', true);

                    },

                    success: function(data) {

                        switch (parseInt(data['status'])) {

                        case 1:

                            var reurl = $('#' + form_id).data('reurl');

                            if (reurl) {

                                location.href = reurl;

                            } else {

                                location.reload();

                            }

                            break;

                        default:

                            alert(data['info']);

                            $('#' + form_id + ' #loginSubmit').val('登录').attr('disabled', false);

                            break;

                        }

                        return false;

                    },

                    error: function() {

                        alert('服务器故障，稍后再试')

                        $('#' + form_id + ' #loginSubmit').val('登录').attr('disabled', false);

                    },

                    cache: false

                });

                return false;

            });

        };

        XGUC.login_close = function() {
			$('#xglogin').removeClass('active');
            /* $('#xguc_mask').hide();

            $('#xguc_login').hide(); */

        };

        XGUC.reg = function(form_id) {

            XGUC.login_close();

            if (!form_id) {

                form_id = 'xguc_reg_form';

            }

            //$('#' + form_id)[0].reset();

            if (form_id == 'xguc_reg_form') {
				$('#xgregister').addClass('active');
                /* $('#xguc_mask').fadeIn(500);

                $('#xguc_reg').fadeIn(500); */

            }

            $('#' + form_id + ' #sub').val('注册').attr('disabled', false);

            $('#' + form_id).unbind('submit').bind('submit',
            function(event) {

                event.preventDefault();

                var username = $('#' + form_id + ' #username').val();

                if (username == '') {

                    alert('手机/邮箱/用户名不能为空！');

                    $('#' + form_id + ' #username').focus();

                    return false;

                }

                var password = $('#' + form_id + ' #password').val();

                if (password == '') {

                    alert('密码不能为空！');

                    $('#' + form_id + ' #password').focus();

                    return false;

                }

                var verifycode = $('#' + form_id + ' #verifycode').val();

                if (verifycode == '') {

                    alert('验证码密码不能为空！');

                    $('#' + form_id + ' #verifycode').focus();

                    return false;

                }

                if (!$('#' + form_id + ' #reg_is_ok').attr('checked')) {

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

                        $('#' + form_id + ' #sub').val('注册中').attr('disabled', true);

                    },

                    success: function(data) {

                        switch (parseInt(data.status)) {

                        case 1:

                            var reurl = $('#' + form_id).data('reurl');

                            if (reurl) {

                                location.href = reurl;

                            } else {

                                location.reload();

                            }

                            break;

                        default:

                            alert(data.info);

                            $('#' + form_id + ' #sub').val('注册').attr('disabled', false);

                            break;

                        }

                    },

                    error: function() {

                        alert('服务器故障，稍后再试')

                        $('#' + form_id + ' #sub').val('注册').attr('disabled', false);

                    },

                    cache: false

                });

                return false;

            });

        };

        XGUC.reg_close = function() {
			$('#xgregister').removeClass('active');
            /* $('#xguc_mask').hide();

            $('#xguc_reg').hide(); */

        };

        XGUC.get_game_gift = function(giftid, giftname) {

            if (!giftid) {

                alert('礼包不存在');
                return;

            }

            $.ajax({

                type: 'POST',

                async: true,

                dataType: 'json',

                url: UURL + 'getGameGift',
                data: {
                    'giftid': giftid,
                    'giftname': giftname
                },
                beforeSend: function() {

},

                success: function(data) {

                    switch (parseInt(data.status)) {

                    case 1:
                        var h = '<div class="wx_gift_ok">';
                        h += '<div class="mask_layer"></div>';
                        h += '<div class="box">';
                        h += '<div class="close">×</div>';
                        h += ' <div class="cons">';
                        if (data.info == 'ok') {
                            h += ' <h5>您领取的【' + giftname + '】礼包码为：</h5>';
                            h += ' <div><p>' + data.data + '</p><div class="btns" id="copy_code" data-url="' + data.data + '">复制</div></div>';
                            h += ' <span>礼包领取成功 (:</span>';
                        } else if (data.info == 'noc') {
                            h += ' <h5>您领取的【' + giftname + '】礼包码为：</h5>';
                            h += ' <span>当前礼包未发放激活码 (:</span>';
                        } else {
                            h += ' <h5>您领取的【' + giftname + '】礼包码为：</h5>';
                            h += ' <div><p>' + data.data + '</p><div class="btns" id="copy_code" data-url="' + data.data + '">复制</div></div>';
                            h += ' <span>当前礼包您已经领取过了 (:</span>';
                        }
                        h += ' </div>';
                        h += ' </div>';
                        h += '</div>';
                        $('body').append(h);

                        $('.mask_layer').css({
                            opacity: 0.8
                        });

                        $('.wx_gift_ok').find('.close').bind('click',
                        function() {

                            $('.wx_gift_ok').remove();

                        });

                        $("#copy_code").zclip({

                            path: "/Public/Defaults/js/ZeroClipboard.swf",

                            copy: function() {

                                return $(this).data('url');

                            },

                            beforeCopy: function() {

                                $(this).css("color", "orange");

                            },

                            afterCopy: function() {

                                $(this).text('已经复制');

                            }

                        });
                        break;
                    default:
                        if (data.info == 'no_login') {
                            XGUC.login();
                            return;

                        } else {
                            alert(data.info);

                        }

                        break;

                    }

                },

                error: function() {

                    alert('服务器故障，稍后再试')

                },

                cache: false

            });

        };

        XGUC.uc_content_avatar = function() {

            var $pick = $('#uc-avatar-bnt'),
            uploader;

            uploader = WebUploader.create({

                auto: true,

                pick: {

                    id: $pick,

                    innerHTML: '<div class="btnok">点击选择截图</div>',

                    multiple: false

                },

                formData: {

                    'type': 'avatar'

                },

                swf: RES_BASE_DIR + 'js/Uploader.swf',

                chunked: false,

                chunkSize: 512 * 1024,

                server: SERVICE_URL + 'uploadAvatar',

                accept: {

                    title: 'Images',

                    extensions: 'gif,jpg,jpeg,bmp,png',

                    mimeTypes: 'image/*'

                },

                compress: {

                    width: 1024,

                    height: 1024,

                    // 图片质量，只有type为`image/jpeg`的时候才有效。
                    quality: 90,

                    // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                    allowMagnify: false,

                    // 是否允许裁剪。
                    crop: true,

                    // 是否保留头部meta信息。
                    preserveHeaders: true,

                    // 如果发现压缩后文件大小比原来还大，则使用原来图片
                    // 此属性可能会影响图片自动纠正功能
                    noCompressIfLarger: false,

                    // 单位字节，如果图片大小小于此值，不会采用压缩。
                    compressSize: 2 * 1024 * 1024

                },

                disableGlobalDnd: true,

                fileNumLimit: 1,
                // 最多文件个数
                fileSizeLimit: 2 * 1024 * 1024,
                // 2 M
                fileSingleSizeLimit: 2 * 1024 * 1024 // 2 M
            });

            // 文件上传成功
            uploader.on('uploadSuccess',
            function(file, ret) {

                console.log(file);

                if (ret.state == 'SUCCESS') {
                    alert('上传成功');

                    $('#thumb').val(ret.url);

                    $('#users-avatar-src').attr('src', ret.url + '?' + Math.random());

                    uploader.removeFile(file);

                } else {

                    alert(ret.state);

                }

            });

        };

    })();

})();

window.onload = function() {

    XGUC.init();

}	