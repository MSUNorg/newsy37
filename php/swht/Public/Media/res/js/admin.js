/**
 * 后台公共模块
 *
 * @package		www.anfeng.cn
 * @author		xiaoFei(daipengfei@qq.com)
 * @copyright	Copyright (c) 2008 - 2014, xiaoFei.
 * @since		14-11-17 13:27
 */

(function () {

    var admin = window.admin = window.admin || {};
    var admin_input_error;

    (function () {

        /**
         * input 错误提示
         * @param input
         * @param msg
         */
        admin.input_error = function (input, msg) {

            if (!$('#input_error_msg').length > 0) {
                $("body").append('<div id="input_error_msg"></div>');
            }

            if (admin_input_error) {
                clearTimeout(admin_input_error);
            }

            var num = 0;
            var fn = function () {
                admin_input_error = setTimeout(function () {

                    var p = $(input).offset();
                    $('#input_error_msg').css({position : 'absolute', left : p.left + 'px', top : (p.top + $(input).height() + 2) + 'px'}).width($(input).width() + 'px');
                    $('#input_error_msg').html(msg).attr('className', 'input_error_msg');

                    $(input).attr('className', $(input).attr('className') === '' ? 'input_error ' : '');
                    if (num === 8) {
                        $(input).attr('className', '');
                        $("#input_error_msg").hide();
                    } else {
                        $("#input_error_msg").show();
						num++
                        fn();
                    }
                    ;

                }, 150);
            };
            fn();
            $(input).focus();
        };

        /**
         * input 成功提示
         * @param input
         * @param msg
         */
        admin.input_ok = function (input, msg) {

            if (!$('#input_ok_msg').length > 0) {
                $("body").append('<div id="input_ok_msg"></div>');
            }

            if (admin_input_error) {
                clearTimeout(admin_input_error);
            }

            var num = 0;
            var fn = function () {
                admin_input_error = setTimeout(function () {

                    var p = $(input).offset();
                    $('#input_ok_msg').css({position : 'absolute', left : p.left + 'px', top : (p.top + $(input).height() + 2) + 'px'}).width($(input).width() + 'px');
                    $('#input_ok_msg').html(msg).attr('className', 'input_ok_msg');

                    $(input).attr('className', $(input).attr('className') === '' ? 'input_error ' : '');
                    if (num === 8) {
                        $(input).attr('className', '');
                        $("#input_ok_msg").hide();
                    } else {
                        $("#input_ok_msg").show();
                        fn(num++);
                    }
                    ;

                }, 150);
            };
            fn();
        };

        /**
         * 全选
         * @param input
         * @param bool
         */
        admin.checkde_all = function (input, bool) {

            $('.table_list :input[type=checkbox][id=\'' + input + '\']').each(function () {

                this.checked = bool;
                var trobj = $(this).parent().parent();
                if (bool) {
                    trobj.addClass('checked').data('checked','checked');
                } else {
                    trobj.removeClass('checked').data('checked','');
                }
            });

        };

        /**
         * 获得全选值
         * @param input
         * @returns {{val: string, len: (Number|number|u.tb.length|*|p.length|b.length)}}
         */
        admin.checkde_all_values = function (input) {

            var ids = Array();
            var i = 0;

            $(':input[type=checkbox][id=\'' + input + '\']').each(function () {

                if (this.checked) {
                    ids[i] = $(this).val();
                    i++;
                }

            });

            return {'val' : ids.join('|'), 'len' : ids.length}
        };

        /**
         * 绑定列表数据列效果
         * @param obj
         */
        admin.table_list_bind_style = function (obj) {

            $(".table_list tr", obj).hover(function () { $(this).addClass('mouseover'); }, function () { $(this).removeClass('mouseover'); } );

            $(".table_list tr td", obj).each(function(){

                $(this).children(":checkbox,:radio").each(function(){

                    var trobj = $(this).parent().parent();
                    if ($(this).attr('checked')) {
                        trobj.addClass('checked');
                    }

                    $(this).click(function(){
                        var trobj = $(this).parent().parent();

                        if ($(this).attr('type') == 'radio') {

                            $(".table_list tr", obj).removeClass('checked');

                            trobj.addClass('checked');
                        } else if ($(this).attr('type') == 'checkbox') {

                            if (trobj.data('checked')=='checked') {
                                trobj.removeClass('checked').data('checked','');
                            } else {
                                trobj.addClass('checked').data('checked','checked');
                            }
                        }


                    });
                });
            });

        };

        /**
         * 绑定表单页面效果
         * @param obj
         */
        admin.table_form_bind_style = function (obj) {

            $(".option_group label", obj).each(function(){

                var input = $(this).find("input");
                if ($(input).attr('checked')) {
                    $(this).css({background:'#99d3fb'}).data('checked','checked');
                }

                $(input).click(function(){

                    $(this).parent().parent().find("input[type='radio']").each(function(){
                        $(this).parent().css({background:''});
                    });

                    if ($(this).attr('type') == 'radio') {
                        $(this).parent().css({background:'#99d3fb'});
                    } else if ($(this).attr('type') == 'checkbox') {
                        if ($(this).parent().data('checked')=='checked') {
                            $(this).parent().css({background:''}).data('checked','');
                        } else {
                            $(this).parent().css({background:'#99d3fb'}).data('checked','checked');
                        }
                    }
                });
            });
        }

        /**
         * 警告提示
         * @param msg
         * @param callback
         */
        admin.msg_alert = function (msg, callback) {

            art.dialog({
                id           : 'admin_msg',
                title        : '提示',
                content      : '<table><tr><td width="50"><div style="background-position: center center;background-repeat: no-repeat; height: 48px; margin: 10px 0 10px 10px; width: 48px;background: url(' + RES_BASE_DIR + '/res/lib/artDialog/skins/icons/warning.png) repeat scroll 0% 0% transparent; display:block;"></div></td><td>' + msg + '</td></tr></table>',
                beforeunload : callback,
                fixed        : true, lock : true, ok : true, time : 3000, padding : '0px 10px'
            });

        };

        /**
         * 失败提示
         * @param msg
         * @param callback
         */
        admin.msg_error = function (msg, callback) {

            art.dialog({
                id           : 'admin_msg_error',
                title        : '失败',
                content      : '<table><tr><td width="50"><div style="background-position: center center;background-repeat: no-repeat; height: 48px; margin: 10px 0 10px 10px; width: 48px;background: url(' + RES_BASE_DIR + '/res/lib/artDialog/skins/icons/error.png) repeat scroll 0% 0% transparent; display:block;"></div></td><td>' + msg + '</td></tr></table>',
                beforeunload : callback,
                fixed        : true, lock : true, ok : true, time : 3000, padding : '0px 10px'
            });

        };

        /**
         * 成功提示
         * @param msg
         * @param callback
         */
        admin.msg_ok = function (msg, callback) {

            art.dialog({
                id           : 'admin_msg_ok',
                title        : '成功',
                content      : '<table><tr><td width="50"><div style="background-position: center center;background-repeat: no-repeat; height: 48px; margin: 10px 0 10px 10px; width: 48px;background: url(' + RES_BASE_DIR + '/res/lib/artDialog/skins/icons/succeed.png) repeat scroll 0% 0% transparent; display:block;"></div></td><td>' + msg + '</td></tr></table>',
                beforeunload : callback,
                fixed        : true, lock : true, ok : true, time : 3000, padding : '0px 10px'
            });

        };

        /**
         * 询问
         * @param msg
         * @param callback
         * @param id
         */
        admin.msg_question = function (msg, callback, id) {

            art.dialog({
                id      : 'admin_msg_question_' + id,
                title   : '询问',
                content : '<table><tr><td width="50"><div style="background-position: center center;background-repeat: no-repeat; height: 48px; margin: 10px 0 10px 10px; width: 48px;background: url(' + RES_BASE_DIR + '/res/lib/artDialog/skins/icons/question.png) repeat scroll 0% 0% transparent; display:block;"></div></td><td>' + msg + '</td></tr></table>',
                button  : [
                    {
                        value    : '确定',
                        callback : callback,
                        focus    : true
                    },
                    {
                        value : '取消'
                    }
                ],
                fixed   : true, lock : true, padding : '0px 10px'
            });

        };

        /**
         * 数据操作
         */

        /**
         * 单个删除数据
         * @param options
         * @param msg
         */
        admin.del_data = function (options, msg) {

            if (typeof options === 'string') {
                options = { text : options }
            }

            var opt = $.extend({
                d  : ADMIN_APP_URL_DIRECTORY,
                c  : '',
                m  : 'delete',
                id : ''
            }, options || {});

            if (!opt.id) {
                admin.msg_error('不知道删除哪个！');
                return;
            }

            admin.msg_question('确认要删除《' + msg + '》的数据么？', function () {

                $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url      : ADMIN_APP_URL + '?d=' + opt.d + '&c=' + opt.c + '&m=' + opt.m,
                    data     : 'id=' + opt.id,
                    success  : function (request) {

                        switch (parseInt(request.status)) {
                            case 1:

                                admin.msg_ok("删除成功！", function () {
                                    $('#tr_' + opt.id).remove();
                                });

                                break;

                            default:

                                admin.msg_error(request.info);

                                break;

                        }

                        return false;
                    },
                    cache    : false
                });
            });

        };

        /**
         * 批量删除数据
         * @param options
         */
        admin.del_datas = function (options) {

            if (typeof options === 'string') {
                options = { text : options }
            }

            var opt = $.extend({
                d     : ADMIN_APP_URL_DIRECTORY,
                c     : '',
                m     : 'delete',
                input : ''
            }, options || {});

            if (!opt.input) {
                admin.msg_error('参数错误！');
                return;
            }

            var data = admin.checkde_all_values(opt.input);

            if (data.len == 0) {

                admin.msg_error('哥，你错了，你还没有选择！');
                return;
            }

            admin.msg_question('确认要删除选择的《' + data.len + '》条数据么？', function () {
                $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url      : ADMIN_APP_URL + '?d=' + opt.d + '&c=' + opt.c + '&m=' + opt.m,
                    data     : 'id=' + data.val,
                    success  : function (request) {

                        switch (parseInt(request.status)) {
                            case 1:

                                admin.msg_ok("删除成功！", function () {
                                    location.reload();
                                });

                                break;

                            default:

                                admin.msg_error(request.info);

                                break;

                        }

                        return false;
                    },
                    cache    : false
                });
            });
        };

        /**
         * 获得input id对应的value
         * @param input
         * @returns {{id: string, sort: string, len: (Number|number|u.tb.length|*|p.length|b.length)}}
         * @private
         */
        admin._get_input_sort = function (input) {
            var ids = Array()
            var sorts = Array();
            var i = 0;
            $(':input[type=text][id=\'' + input + '\']').each(function () {

                // 判断是否修改过
                if ($(this).val() != $(this).attr('sort')) {
                    ids[i] = $(this).attr('sort_id');
                    sorts[i] = $(this).val();
                    i++;
                }
            });

            return {'id' : ids.join('|'), 'sort' : sorts.join('|'), 'len' : ids.length}
        }

        /**
         * 更新排序
         * @param options
         */
        admin.update_sort = function (options) {

            if (typeof options === 'string') {
                options = { text : options }
            }

            var opt = $.extend({
                d     : ADMIN_APP_URL_DIRECTORY,
                c     : '',
                m     : 'sort',
                input : ''
            }, options || {});

            if (!opt.input) {
                admin.msg_error('参数错误！');
                return;
            }

            var data = admin._get_input_sort(opt.input);

            if (data.len == 0) {
                admin.msg_error('哥，你错了，可能你没有修改过！');
                return;
            }

            admin.msg_question('确认要更新当前数据的排序么？', function () {

                $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url      : ADMIN_APP_URL + '?d=' + opt.d + '&c=' + opt.c + '&m=' + opt.m,
                    data     : 'id=' + data.id + '&sort=' + data.sort,
                    success  : function (request) {

                        switch (parseInt(request.status)) {
                            case 1:

                                admin.msg_ok("更新成功！");

                                break;

                            default:

                                admin.msg_error(request.info);

                                break;

                        }

                        return false;
                    },
                    cache    : false
                });
            });
        };

        /**
         * 更新状态2个状态选择,屏蔽/显示
         * @param options
         * @param ico_obj
         */
        admin.update_status = function (options, ico_obj) {

            if (typeof options === 'string') {
                options = { text : options }
            }

            var opt = $.extend({
                d  : ADMIN_APP_URL_DIRECTORY,
                c  : '',
                m  : 'status',
                id : ''
            }, options || {});

            if (!opt.id) {
                admin.msg_error('参数错误！');
                return;
            }

            if ($(ico_obj).attr('class') == 'status_normal') {
                var status = 'hidden';
            }
            else if ($(ico_obj).attr('class') == 'status_hidden') {
                var status = 'normal';
            }

            admin.msg_question('确认要更新当前数据的状态为【' + (status == 'hidden' ? '屏蔽' : '正常') + '】？', function () {

                $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url      : ADMIN_APP_URL + '?d=' + opt.d + '&c=' + opt.c + '&m=' + opt.m,
                    data     : 'id=' + opt.id + '&status=' + status,
                    success  : function (request) {

                        switch (parseInt(request.status)) {
                            case 1:

                                if (status == 'hidden') {
                                    $(ico_obj).removeClass('status_normal').addClass('status_hidden');
                                }
                                else if (status == 'normal') {
                                    $(ico_obj).removeClass('status_hidden').addClass('status_normal');
                                }

                                break;

                            default:

                                admin.msg_error(request.info);

                                break;

                        }

                        return false;
                    },
                    cache    : false
                });
            });
        };

        /**
         * 修改数据的发布状态
         * @param options
         * @param ico_obj
         */
        admin.update_publish_status = function (options, ico_obj) {

            if (typeof options === 'string') {
                options = { text : options }
            }

            var opt = $.extend({
                d : ADMIN_APP_URL_DIRECTORY,
                c : '',
                m : 'publish_status',

                id      : '',
                title   : '',
                pubtime : '',
                status  : ''
            }, options || {});

            if (!opt.id) {
                admin.msg_error('参数错误！');
                return;
            }

            var dialog = art.dialog({id : 'admin_update_publish_status', fixed : true, title : '加载中...', width : 360, height : 60, zIndex : 50});

            var html = '';

            html += "<form id=\"dialog_form\">";
            html += "<table cellpadding=\"0\" cellspacing=\"1\">";
            html += "    <tr>";
            html += "      <th> <strong>选择的数据</strong> </th>";
            html += "      <td>";
            html += "      " + opt.id + ":" + opt.title + " ";
            html += "      </td>";
            html += "    </tr>";
            html += "    <tr>";
            html += "      <th> <strong>发布时间</strong> </th>";
            html += "      <td>";
            html += "      	<input type=\"text\" name=\"publish_time\" id=\"update_status_publish_time\" size=\"20\" value=\"" + opt.pubtime + "\" autocomplete=\"off\" />";
            html += "      </td>";
            html += "    </tr>";
			html += "    <tr>";
            html += "      <th> <strong>状态</strong> </th>";
            html += "      <td>";
            html += "        <label><input type=\"radio\" name=\"status\" id=\"status\" value=\"normal\" checked=\"checked\"/>显示</label>";
            html += "        <label><input type=\"radio\" name=\"status\" id=\"status\" value=\"hidden\"/>屏蔽</label>";
            html += "        <label><input type=\"radio\" name=\"status\" id=\"status\" value=\"pendding\"/>待发布</label>";
            html += "        <label><input type=\"radio\" name=\"status\" id=\"status\" value=\"deleted\"/>回收站</label>";
            html += "        <input type=\"hidden\" name=\"submit\" value=\"submit\" /><input type=\"hidden\" name=\"id\" value=\"" + opt.id + "\" />";
            html += "      </td>";
            html += "    </tr>";
            html += "</table>";
            html += "</form>";

            dialog.content(html);
            dialog.title('修改数据发布状态');
            dialog.button({
                value    : '保存',
                callback : function () {

                    if ($('#dialog_form [name=publish_time]').val() == '') {

                        admin.input_error('#dialog_form [name=publish_time]', '时间不能为空！');

                        return false;
                    }

                    $.ajax({
                        type     : "POST",
                        dataType : "json",
                        url      : ADMIN_APP_URL + '?d=' + opt.d + '&c=' + opt.c + '&m=' + opt.m,
                        data     : $('#dialog_form').serializeArray(),
                        success  : function (request) {

                            switch (parseInt(request.status)) {
                                case 1:

                                    var dialog = art.dialog.get('admin_update_publish_status');

                                    dialog.close();

                                    admin.msg_ok("修改成功！", function () {
                                        location.reload();
                                    });

                                    break;

                                default:

                                    admin.msg_error(request.info);

                                    break;

                            }

                            return false;
                        },
                        cache    : false
                    });

                    return false;

                }});
            dialog.button({value : '取消'});

           	Calendar.setup({
             inputField     :    "update_status_publish_time",
             ifFormat       :    "%Y-%m-%d %H:%M:%S",
             showsTime      :    true,
             timeFormat     :    "24"
             });

            $('#dialog_form :input[type=radio][id=status][value=' + opt.status + ']').each(function () {

                this.checked = true;

            });
        };

        /**
         * 批量修改状态
         * @param options
         */
        admin.update_state = function (options) {

            if (typeof options === 'string') {
                options = { text : options }
            }
            var opt = $.extend({
                d     : ADMIN_APP_URL_DIRECTORY,
                c     : '',
                m     : '',
                input : '',
                status : ''
            }, options || {});

            if (!opt.input) {
                admin.msg_error('参数错误！');
                return;
            }

            var data = admin.checkde_all_values(opt.input);

            if (data.len == 0) {

                admin.msg_error('哥，你错了，你还没有选择！');
                return;
            }

            admin.msg_question('确认要修改选择的《' + data.len + '》条数据么？', function () {

                $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url      : ADMIN_APP_URL + '?d=' + opt.d + '&c=' + opt.c + '&m=' + opt.m,
                    data     : {'id':data.val,'status':opt.status},
                    success  : function (request) {

                        switch (parseInt(request.status)) {
                            case 1:

                                admin.msg_ok("修改状态成功！", function () {
                                    location.reload();
                                });

                                break;

                            default:

                                admin.msg_error(request.info);

                                break;

                        }

                        return false;
                    },
                    cache    : false
                });
            });
        };

        /**
         * 询问是否执行操作
         * @param options
         */
        admin.confirm_action = function (options) {

            if (typeof options === 'string') {
                options = { text : options }
            }

            var opt = $.extend({
                d     : ADMIN_APP_URL_DIRECTORY,
                c     : '',
                m     : '',
                t     : '是否执行当前操作？',
				p	  : ''
            }, options || {});

            if (!opt.m || !opt.m) {
                admin.msg_error('参数错误！');
                return;
            }

            admin.msg_question(opt.t, function () {
                $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url      : ADMIN_APP_URL + '?d=' + opt.d + '&c=' + opt.c + '&m=' + opt.m,
					data	 : opt.p,
                    success  : function (request) {

                        switch (parseInt(request.status)) {
                            case 1:

                                admin.msg_ok(request.info, function () {
                                    location.reload();
                                });

                                break;

                            default:

                                admin.msg_error(request.info);

                                break;

                        }

                        return false;
                    },
                    cache    : false
                });
            });
        };

        /**
         * 常用组件
         */

        /**
         * 联动菜单
         * @param pid
         * @param options
         */
        admin.select_change = function (pid, options) {
			
			if (!pid) pid = 0;
			
            if (typeof options === 'string') {
                options = { text : options }
            }
            var opt = $.extend({
                d     : ADMIN_APP_URL_DIRECTORY,
                c     : '',
                m     : '',
				obj : '',
                id : ''                
            }, options || {});

            if (!opt.obj) {
                admin.msg_error('参数错误！');
                return;
            }

            $.ajax({
				type     : "POST",
				dataType : "json",
				url      : ADMIN_APP_URL + '?d=' + opt.d + '&c=' + opt.c + '&m=' + opt.m,
				data     : {'pid':pid},
				success  : function (request) {

					switch (parseInt(request.status)) {
						case 1:
							
							$(opt.obj).html('');  
							
							$(opt.obj).append('<option '+(opt.id==0?' selected':'')+'>请选择</option>'); 
							
							$.each(request.data,function(key,vo){
								$(opt.obj).append('<option value="'+vo['id']+'"'+(opt.id==vo['id']?' selected':'')+'>'+vo['category']+'</option>'); 
							});
							
							break;

						default:

							admin.msg_error(request.info);

							break;

					}

					return false;
				},
				cache    : false
			});
        };

        /**
         * 单文件上传弹窗
         * @param options
         */
        admin.file_ajax_upload = function (options) {
		
            if ( typeof options === 'string' ) {
				options = { text: options }
			}
			
			var opt = $.extend({
				id : 'script_ajax_upload_file', // 操作区域
				input_name : '', // 保存的控件名称
				input_value : '', // 控件默认值
				path : 'images', // 上传子目录
				resize_size : '', // 重置图片大小100x200
				resize_size_water : '', // 对主图打水印
				cut_size : '', // 需要生成缩略图大小,多个100x200,300x400
				cut_size_water : '' // 对副图打水印
            }, options || {});
			
			var html = '';
				html += '<a href="'+ (opt.input_value?opt.input_value:'/res/admin/images/image.png') +'" target="_blank"><img width="60" height="50" id="'+ opt.id +'_pic" src="'+ (opt.input_value?opt.input_value:'/res/admin/images/image.png') +'" /></a>';
				html += '<input type="hidden" name="'+ opt.input_name +'" id="'+ opt.id +'_input" value="'+ opt.input_value +'" />';
				
			if (opt.resize_size) {
				html += '('+ opt.resize_size +')';	
			}				
				html += '<input type="button" class="button_style" id="'+ opt.id +'_input_bnt" value="上传图片" />';
			
			$('#'+opt.id).html(html);
			
			// 按钮
			$('#'+ opt.id +'_input_bnt').click(function(){
														
				// 弹窗
				var upload_dialog = art.dialog({
					title        : '上传图片',
					content      : '<form id="script-form_upload_file"><input type="file" id="script-form_upload_file-input_file" name="file" />&nbsp;<input class="button_style" type="submit" value=" 上传 " /></form>',
					
					lock         : true,
					padding      : '15px',
					
					cancel       : function () {  },
					cancelValue  : '取消',
					
					initialize   : function () {
					
						var this_dialog = this;
						
						//$('#script-form_upload_file-input_file').trigger("click");
						
						// 绑定上传按钮
						$('#script-form_upload_file').submit(function (event) {
							
							event.preventDefault();
							
							$.ajaxFileUpload(
								{
									url           : ADMIN_APP_URL +'?d='+ ADMIN_APP_URL_DIRECTORY +'&c=upload&m=save&ext=image',
									secureuri     : false,
									fileElementId : 'script-form_upload_file-input_file',
									dataType      : 'json',
									data          : { path : opt.path, resize_size : opt.resize_size, resize_size_water : opt.resize_size_water, cut_size : opt.cut_size,cut_size_water : opt.cut_size_water },
									success       : function (data, status) {
										
										if(data.state == 'success') {
											
											$('#'+ opt.id +'_input').val(data.url);
											$('#'+ opt.id +'_pic').attr('src',data.url);
											
											/* 关闭弹窗 */
											this_dialog.close();
											
										} else {
											admin.msg_error('上传错误：' + data.msg);
										}
									},
									error        : function (data, status, e) {
										admin.msg_error(e);
									}
								}
							);
							
						});
						
					},
					beforeunload : function () {
						
					}
				});									
			});
			
		};

        /**
         * Ajax批量上传插件
         * @param options
         */
        admin.files_ajax_upload = function (options) {
		
            if ( typeof options === 'string' ) {
				options = { text: options }
			}
			
			var opt = $.extend({
				id : 'script_ajax_upload_file', // 操作区域
				input_name : '', // 保存的控件名称
				input_value : '', // 控件默认值
				path : 'images', // 上传子目录
				resize_size : '', // 重置图片大小100x200
				resize_size_water : '', // 对主图打水印
				cut_size : '', // 需要生成缩略图大小,多个100x200,300x400
				cut_size_water : '' // 对副图打水印
            }, options || {});
			
			var html = '';
				
            var images_list = opt.input_value.split(',');

            html += '<div class="ajax_upload_images_list" id="'+opt.id+'_images_list">';

            if (opt.input_value!='' && images_list.length>0)
            {
                for (var i=0;i<images_list.length;i++)
                {
                    html += '<li id="'+opt.id+'_list_'+i+'">';
                    html += '<a href="'+images_list[i]+'" target="_blank"><img width="60" height="50" src="'+images_list[i]+'" /></a>';
                    html += '<a href="javascript:" onclick="javascript:$(\'#'+opt.id+'_list_'+i+'\').remove();">删除</a>';
                    html += '<input name="'+opt.input_name+'['+i+']" type="hidden" value="'+images_list[i]+'" />';
                    html += '</li>';
                }
            }
            html += '</div>';

            if (opt.resize_size) {
                html += '('+ opt.resize_size +')';
            }

            html += '<input type="button" class="button_style" id="'+ opt.id +'_input_bnt" value="上传图片" />';
			
			$('#'+opt.id).html(html);
			
			// 按钮
			$('#'+ opt.id +'_input_bnt').click(function(){
														
				// 弹窗
				var upload_dialog = art.dialog({
					title        : '上传图片',
					content      : '<form id="script-form_upload_file"><input type="file" id="script-form_upload_file-input_file" name="file" />&nbsp;<input class="button_style" type="submit" value=" 上传 " /></form>',
					
					lock         : true,
					padding      : '15px',
					
					cancel       : function () {  },
					cancelValue  : '取消',
					
					initialize   : function () {
					
						var this_dialog = this;
						
						$('#script-form_upload_file-input_file').trigger("click");
						
						// 绑定上传按钮
						$('#script-form_upload_file').submit(function (event) {
							
							event.preventDefault();
							
							$.ajaxFileUpload(
								{
									url           : ADMIN_APP_URL +'?d='+ ADMIN_APP_URL_DIRECTORY +'&c=upload&m=save&ext=image',
									secureuri     : false,
									fileElementId : 'script-form_upload_file-input_file',
									dataType      : 'json',
									data          : { path : opt.path, resize_size : opt.resize_size, resize_size_water : opt.resize_size_water, cut_size : opt.cut_size, cut_size_water : opt.cut_size_water  },
									success       : function (data, status) {
										
										if(data.state == 'success') {
											
											var i_max=$('#'+opt.id+'_images_list li').length;
						
											var html = '<li id="'+opt.id+'_list_'+i_max+'">';
												html += '<a href="'+data.url+'" target="_blank"><img width="60" height="50" src="'+data.url+'" /></a>';
												html += '<a href="javascript:" onclick="javascript:$(\'#'+opt.id+'_list_'+i_max+'\').remove();">删除</a>';
												html += '<input name="'+opt.input_name+'['+i_max+']" type="hidden" value="'+data.url+'" />';
												html += '</li>';
											
											$('#'+opt.id+'_images_list').append(html);
											
											/* 关闭弹窗 */
											this_dialog.close();
											
										} else {
											admin.msg_error('上传错误：' + data.msg);
										}
									},
									error        : function (data, status, e) {
										admin.msg_error(e);
									}
								}
							);
							
						});
						
					},
					beforeunload : function () {
						
					}
				});									

			});
			
		};

  admin.css_flash_upload = function (options) {

            if ( typeof options === 'string' ) {
                options = { text: options }
            }

            var opt = $.extend({
                id : 'script_ajax_upload_file', // 操作区域
                multi :false, // 是否多个
                limit : 1 , // 最大上传个数
                input_name : 'css', // 保存的控件名称
                input_value : '', // 控件默认值
                path : 'css', // 上传子目录
                resize_size : '', // 重置图片大小100x200
                resize_size_water : '', // 对主图打水印
                cut_size : '', // 需要生成缩略图大小,多个100x200,300x400
                cut_size_water : '' // 对副图打水印
            }, options || {});

            var images_list = opt.input_value.split(',');

            var html = '<div class="flash_upload">';

            html += '<div class="flash_upload_list">';

            if (opt.input_value!='' && images_list.length>0){

                for (var i=0;i<images_list.length;i++)
                {
                    html += '<input type="text" name="'+ opt.input_name +'" value="'+ opt.input_value +'" readonly="readonly" />';

                }
            } else if(!opt.multi && opt.input_value=='') {
                    html += '<input type="text" name="'+ opt.input_name +'" value="'+ opt.input_value +'" readonly="readonly" />';
            }

            html += '</div>';

            html += '<div class="flash_upload_tool">';


            html += '<div class="flash_upload_input">';
            html +=     '<input type="file" id="'+ opt.id +'_uplaod_bnt"/>';
            html += '</div>';

            html += '</div>';

            html += '</div>';

            $('#'+opt.id).html(html);

            var uplaod_bnt_id = '#'+ opt.id +'_uplaod_bnt';

            //$(uplaod_bnt_id).css({"background":"url(/res/lib/uploadify/uploadify-bnt.jpg)","background-repeat":"no-repeat","background-position":"center top"});

            $(uplaod_bnt_id).uploadify({
                'formData'        : { path : opt.path, resize_size : opt.resize_size, resize_size_water : opt.resize_size_water, cut_size : opt.cut_size,cut_size_water : opt.cut_size_water },
                'swf'             : "/res/lib/uploadify/uploadify.swf",
                'fileObjName'     : 'file',
                'buttonText'      : '点击上传样式表',
                'uploader'        : ADMIN_APP_URL +'?d='+ ADMIN_APP_URL_DIRECTORY +'&c=upload&m=save&ext=css',
                'simUploadLimit'  : opt.limit,
                //'queueSizeLimit'  : opt.limit,
                'multi'           : opt.multi,
                'auto'            : true,
                'removeTimeout'	  : 1,
                'fileDesc' 		  : '支持格式:css',
                'fileTypeExts'	  : '*.css;',
                'onSelect'     : function(){

                },
                'onUploadSuccess' : function(file, request){
                    var request = eval('(' + request + ')');
                    if(request.state =='success'){
                        //写入进对应的input
                        in_name = "input[name='"+ opt.input_name +"']";
                        $(in_name).val(request.url);
                    } else {
                        admin.msg_error(request.msg);
                    }
                },
                'onUploadError':function(){

                    admin.msg_error('上传失败!');
                },
                'onFallback' : function() {

                    admin.msg_error('未检测到兼容版本的Flash.');
                }
            });

        };




        /**
         * Flash文件上传
         * @param options
         */
        admin.file_flash_upload = function (options) {

            if ( typeof options === 'string' ) {
                options = { text: options }
            }

            var opt = $.extend({
                id : 'script_ajax_upload_file', // 操作区域
                multi :false, // 是否多个
                limit : 1 , // 最大上传个数
                input_name : 'images', // 保存的控件名称
                input_value : '', // 控件默认值
                path : 'images', // 上传子目录
                resize_size : '', // 重置图片大小100x200
                resize_size_water : '', // 对主图打水印
                cut_size : '', // 需要生成缩略图大小,多个100x200,300x400
                cut_size_water : '' // 对副图打水印
            }, options || {});

            var images_list = opt.input_value.split(',');

            var html = '<div class="flash_upload">';

                html += '<div class="flash_upload_list">';
                html += '<ul>';

            if (opt.input_value!='' && images_list.length>0)
            {
                for (var i=0;i<images_list.length;i++)
                {
                    html += '<li id="'+opt.id+'_list_'+i+'">';
                    html += '<a class="flash_upload_list_img" href="'+images_list[i]+'" target="_blank"><img src="'+images_list[i]+'" /></a>';
                    html += '<a class="flash_upload_list_del" href="javascript:" onclick="javascript:$(this).parent().remove();"></a>';
                    html += '<input name="'+(opt.multi?(opt.input_name+'['+i+']'):opt.input_name)+'" type="hidden" value="'+images_list[i]+'" />';
                    html += '<input name="'+(opt.multi?(opt.input_name+'_old['+i+']'):opt.input_name+'_old')+'" type="hidden" value="'+images_list[i]+'" />';
                    html += '</li>';
                }
            }

                html += '</ul>';
                html += '</div>';
            
            html += '<div class="flash_upload_tool">';
            
                html += '<div class="flash_upload_info">';
                if (opt.resize_size) {
                    html += '&nbsp;(分辨率：'+ opt.resize_size +')';
                } else {
                    html += '&nbsp;(分辨率：原始分辨率)';
                }

                if (opt.cut_size) {
                    html += '&nbsp;(缩略图分辨率：'+ opt.resize_size +')';
                }

                if (opt.multi) {
                    html += '&nbsp;(最多上传：'+ opt.limit +'张)';
                }

                html += '</div>';
                
                html += '<div class="flash_upload_input">';
                html +=     '<input type="file" id="'+ opt.id +'_uplaod_bnt"/>';
                html += '</div>';

                html += '</div>';
                
            html += '</div>';

            $('#'+opt.id).html(html);

            var uplaod_bnt_id = '#'+ opt.id +'_uplaod_bnt';

            $(uplaod_bnt_id).css({"background":"url(/res/lib/uploadify/uploadify-bnt.jpg)","background-repeat":"no-repeat","background-position":"center top"});


            $(uplaod_bnt_id).uploadify({
                'formData'        : { path : opt.path, resize_size : opt.resize_size, resize_size_water : opt.resize_size_water, cut_size : opt.cut_size,cut_size_water : opt.cut_size_water },
                'swf'             : "/res/lib/uploadify/uploadify.swf",
                'fileObjName'     : 'file',
                'buttonText'      : '点击选择图片',
                'uploader'        : ADMIN_APP_URL +'?d='+ ADMIN_APP_URL_DIRECTORY +'&c=upload&m=save&ext=image',
                'width'           : 120,
                'height'          : 44,
                'simUploadLimit'  : opt.limit,
                //'queueSizeLimit'  : opt.limit,
                'multi'           : opt.multi,
                'auto'            : true,
                'removeTimeout'	  : 1,
                //'fileSizeLimit'   : '10M',
                'fileDesc' 		  : '支持格式:jpg/gif/jpeg/png/bmp.',
                'fileTypeExts'	  : '*.jpg;*.gif;*.jpeg;*.png;*.bmp',
                'onSelect'     : function(){


                    /*
                     var ul_obj = $(uplaod_bnt_id).parent().parent().find('.flash_upload_list ul');
                     var li_lenght = $(ul_obj).find('li').length;

                     if (li_lenght>opt.simUploadLimit) {
                     alert('1')
                     }
                    */

                },
                'onUploadSuccess' : function(file, request){

                    var request = eval('(' + request + ')');

                    if(request.state =='success'){

                        var pic_val = request.url; // 数据存储
                        var ul_obj = $(uplaod_bnt_id).parent().parent().parent().find('.flash_upload_list ul');
                        var li_lenght = $(ul_obj).find('li').length;

                        /*
                        if (li_lenght>opt.limit) {
                         alert('1')
                         }
                        */

                        if (!opt.multi) {
                            $(ul_obj).find('li').remove();
                        }

                        var _html = '<li><a class="flash_upload_list_img" href="'+ pic_val +'" target="_blank"><img src="'+ pic_val +'"></a><a class="flash_upload_list_del" href="javascript:" onclick="javascript:$(this).parent().remove();"></a><input type="hidden" name="'+(opt.multi?(opt.input_name+'['+(li_lenght+1)+']'):opt.input_name)+'" value="'+ pic_val +'"></li>';
                        if ((li_lenght+1)>opt.limit) {
                            admin.msg_error('最多上传'+opt.limit+'张');
                            return;
                        }else{
                            ul_obj.append(_html);
                        }

                    } else {
                        admin.msg_error(request.msg);
                    }
                },
                'onUploadError':function(){

                    admin.msg_error('上传失败!');
                },
                'onFallback' : function() {

                    admin.msg_error('未检测到兼容版本的Flash.');
                }
            });

        };

        /**
         * 页面初始化
          */
        admin.init = function () {

            $("input[type='text']").addClass('input_blur');
            $("input[type='password']").addClass('input_blur');
            $("input[type='submit']").addClass('button_style');
            $("input[type='reset']").addClass('button_style');
            $("input[type='button']").addClass('button_style');
            $("input[type='radio']").addClass('radio_style');
            $("input[type='checkbox']").addClass('checkbox_style');
            $("input[type='textarea']").addClass('textarea_style');
            $("input[type='file']").addClass('file_style');
            $("input[type='file']").blur(function () { $(this).removeClass('input_focus').addClass('input_blur'); } );
            $("input[type='file']").focus(function () { $(this).removeClass('input_blur').addClass('input_focus'); } );
            $("input[type='password']").blur(function () { $(this).removeClass('input_focus').addClass('input_blur'); } );
            $("input[type='password']").focus(function () { $(this).removeClass('input_blur').addClass('input_focus'); } );
            $("input[type='text']").blur(function () { $(this).removeClass('input_focus').addClass('input_blur'); } );
            $("input[type='text']").focus(function () { $(this).removeClass('input_blur').addClass('input_focus'); } );
            $("textarea").blur(function () { $(this).removeClass('textarea_focus').addClass('textarea_style'); } );
            $("textarea").focus(function () { $(this).removeClass('textarea_style').addClass('textarea_focus'); } );
            $("#title").focus(function () { $(this).removeClass('inputtitle').addClass('inputtitle'); } );
            $("#title").blur(function () { $(this).removeClass('inputtitle').addClass('inputtitle'); } );

            admin.table_list_bind_style(document);
			
			admin.table_form_bind_style(document);
        }

    })();

})();

admin.init();	