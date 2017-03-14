/*
	游戏模块
	
	@xiaoFei
*/

(function () {

    var game_model = window.game_model = window.game_model || {};
	
	var ADMIN_APP_URL_CONTROLLER = 'game';

    (function () {

		/* 表单搜索提示 */
        game_model.search_ajax = function (input_obj, par, type) {
			
			$(input_obj).data('par', par).data('total', 0).data('key_index', -1).attr('autocomplete', 'off');
			
			$('#so_data_'+$(input_obj).attr('id')).remove();
			$('body').append('<div id="so_data_'+$(input_obj).attr('id')+'" class="so_data"></div>');			
			
			$(document).bind("click", function(){
                $('#so_data_'+$(input_obj).attr('id')).hide();
            });

			$(input_obj).keyup(function(event,callback){
				
				var input_val=($(this).val() + '').replace(/(\s+)$/g, '').replace(/^\s+/g, '');

				if (input_val=='') return;
				
				var input_obj = $(this);
				var so_data_obj=$('#so_data_'+$(this).attr('id'));
				var offset = $(this).offset();
				var data_total = $(this).data('total');
				var key_index = $(this).data('key_index');
				var default_keyword = $(this).data('default_keyword');
				var data_total = $(this).data('total');
				
				$(so_data_obj).css({
					position: "absolute",
					top: offset.top + $(this).outerHeight() + "px",
					left: offset.left,
					width: $(this).outerWidth()-2 + "px",
					opacity: 1.0,
					zindex: 20000
				});				
				
				if (event.which == 13) {
					return false;
				}
								
				if (event.which == 38) {
					
					if (data_total==0) return;
					
					if (key_index<0) {
						
						key_index = -1;
						$(so_data_obj).find('li').css('background','');
						$(input_obj).val(default_keyword); 
					} else {
						
						if (key_index>=data_total) {
						   key_index=data_total-1;
						}
						
						$(so_data_obj).find('li').css('background','');
						$(so_data_obj).find('li').eq(key_index).css('background','#C7E7FA');
						$(input_obj).val($(so_data_obj).find('li').eq(key_index).find('span').html());

                        game_model.search_ajax_change_value($(so_data_obj).find('li').eq(key_index), type);

						key_index--;
					}
					
					$(input_obj).data('key_index', key_index);
					
					return;
				} else if (event.which == 40) {
					
					if (data_total==0) return;
					
					if (key_index>=data_total-1){
						
						key_index = -1;
						$(so_data_obj).find('ul').find('li').css('background','');
						$(input_obj).val(default_keyword); 
					} else {
						
						key_index++;
						$(so_data_obj).find('li').css('background','');
						$(so_data_obj).find('li').eq(key_index).css('background','#C7E7FA');
						$(input_obj).val($(so_data_obj).find('li').eq(key_index).find('span').html());

                        game_model.search_ajax_change_value($(so_data_obj).find('li').eq(key_index), type);
					}
					
					$(input_obj).data('key_index', key_index);
					return;					
				} else { 
					
					$.ajax({
						type: 'get',
						url : ADMIN_APP_URL +'?d='+ADMIN_APP_URL_DIRECTORY+'&c='+ADMIN_APP_URL_CONTROLLER+'&m=ajax_search',
						data: 'keyword='+encodeURIComponent(input_val)+$(input_obj).data('par'),
						success: function (request) {
							
							key_index = -1;
							default_keyword = input_val;
							
							if(request=='' || request=='null' || typeof request=='null'){
								$(so_data_obj).html('<ul><li>没有匹配数据</li></ul>').show();
								return;
							}
					
							var json = eval("("+request+")");
							data_total = json.length;
							
							$(input_obj).data('total', data_total);
							$(input_obj).data('key_index', key_index);
							$(input_obj).data('default_keyword', default_keyword);
							
							if(data_total==0){
								$(so_data_obj).html('<ul><li>没有匹配数据</li></ul>').show();
								return;
							};
							
							var i=1;html='';
							for (var item in json) {
                                html += '<li data-name="'+json[item]['name']+'" data-id="'+json[item]['id']+'" data-input_id="'+ $(input_obj).attr('id') +'"><span>'+json[item]['name']+'</span></li>';
							}
					
							$(so_data_obj).html('<ul>'+html+'</ul>').show();

                            $(so_data_obj).find('li').each(function(){
                                $(this).click(function(){
                                    game_model.search_ajax_change_value(this, type);
                                });
                            });
						},
						cache: false
					});
				}
			});			
        };

        /* 表单搜索提示操作变化值 */
        game_model.search_ajax_change_value = function (obj, type) {

            var game_id = $(obj).data('id'),game_name = $(obj).data('name'),input_id = $(obj).data('input_id');

            switch (type){
                case 'name': case 'keyword':
                        $('#'+ input_id).val(game_name);
                    break;
                case 'id':
                        $('#'+ input_id).val(game_id);
                    break;
                case 'game_data':
                        $('#game_name').val(game_name);
                        $('#game_id').val(game_id);
                    break;
                case 'recom_game_data':
                        $('#title').val(game_name);
                        $('#url').val(game_id);
                    break;
            }
        }

		/* 打开游戏搜索界面 */
        game_model.select_game= function (id_input, name_input) {
			
			var dialog = art.dialog({id: 'select_game_dialog',fixed: true,title:'加载中...',width:750,padding: 0,zIndex:22, lock:true});
			
			$.ajax({
			   type: 'GET',
				url: ADMIN_APP_URL + '?d=' + ADMIN_APP_URL_DIRECTORY + '&c=game&m=ajax_select',
			   data: '',
				success: function (html) {
					
					dialog.content(html);
					
					game_model.select_game_form_submit();
					
					dialog.title('选择游戏');
					dialog.button({
									value: '确定',
									callback: function () {
										
										var val = $("#select_game_list input[name='sle_id']:checked").val();
										
										if (!val) {
											
											admin.msg_error('请选择一个游戏');
								
											return false;	
										}
										
										var data = val.split('@');
										
										$(id_input).val(data[0]);
										$(name_input).val(data[1]);
										
									}});
					dialog.button({value: '取消'});
				},
				cache: false
			});
		};
		
		game_model.select_game_form_submit = function() {
			
			game_model.search_ajax($('#game_keyword', '#select_game_form'), '&field=name&select=id,name', 'keyword');
			
			admin.table_list_bind_style('#select_game_list');

            $('#stime','#select_game_form').focus(function(e) {
                WdatePicker({startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true})
            });

            $('#etime','#select_game_form').focus(function(e) {
                WdatePicker({startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true})
            });

            $("#select_game_list input[name='sle_id']").click(function(){

                var data = $(this).val().split('@');

                $('#game_id').val(data[0]);
                $('#game_name').val(data[1]);

                art.dialog.get('select_game_dialog').close();
            });

			$('#select_game_form').submit(function(){
							
				$.ajax({
				   type: 'GET',
					url: ADMIN_APP_URL,
				   data: $('#select_game_form').serializeArray(),
					success: function (html) {
						
						var dialog = art.dialog.get('select_game_dialog');
						
						dialog.content(html);
						
						game_model.select_game_form_submit();
						
						return false;
					},
					cache: false
					});	
										
				return false;	
			});
		};	
		
		/* 打开开服搜索界面 */
        game_model.select_server= function (id_input, name_input) {
			
			var game_id = $('#game_id').val();
			
			if (!game_id) {
				
				admin.msg_error('请选择一个游戏', function(){ $('#game_name').focus(); });
								
				return false;	
			}
			
			var dialog = art.dialog({id: 'select_server_dialog',fixed: true,title:'加载中...',width:750,padding: 0,zIndex:22, lock:true});
			
			$.ajax({
			   type: 'GET',
				url: ADMIN_APP_URL + '?d=' + ADMIN_APP_URL_DIRECTORY + '&c=game_server&m=ajax_select',
			   data: 'game_id='+game_id,
				success: function (html) {
					
					dialog.content(html);
					
					game_model.select_server_form_submit();

					dialog.title('选择游戏开服');
					dialog.button({
									value: '确定',
									callback: function () {
										
										var val = $("#select_server_list input[name='sle_id']:checked").val();
										
										if (!val) {
											
											admin.msg_error('请选择一条开服');
								
											return false;	
										}
										
										var data = val.split('@');
										
										$(id_input).val(data[0]);
										$(name_input).val(data[1]);
										
									}});
					dialog.button({value: '取消'});
				},
				cache: false
			});
		};
		
		game_model.select_server_form_submit = function() {
			
			game_model.search_ajax($('#server_keyword', '#select_server_form'), '&field=name&select=id,name', 'keyword');

            admin.table_list_bind_style('#select_server_list');

            $('#stime','#select_server_form').focus(function(e) {
                WdatePicker({startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true})
            });

            $('#etime','#select_server_form').focus(function(e) {
                WdatePicker({startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true})
            });

            $("#select_server_list input[name='sle_id']").click(function(){

                var data = $(this).val().split('@');

                $('#server_id').val(data[0]);
                $('#server_name').val(data[1]);

                art.dialog.get('select_server_dialog').close();
            });

			$('#select_server_form').submit(function(){
							
				$.ajax({
				   type: 'GET',
					url: ADMIN_APP_URL,
				   data: $('#select_server_form').serializeArray(),
					success: function (html) {
						
						var dialog = art.dialog.get('select_server_dialog');
						
						dialog.content(html);
						
						game_model.select_server_form_submit();
						
						return false;
					},
					cache: false
					});	
										
				return false;	
			});
		};
		
		/* 打开开测搜索界面 */
        game_model.select_test= function (id_input, name_input) {
			
			var game_id = $('#game_id').val();
			
			if (!game_id) {
				
				admin.msg_error('请选择一个游戏', function(){ $('#game_name').focus(); });
								
				return false;	
			}
			
			var dialog = art.dialog({id: 'select_test_dialog',fixed: true,title:'加载中...',width:750,padding: 0,zIndex:22, lock:true});
			
			$.ajax({
			   type: 'GET',
				url: ADMIN_APP_URL + '?d=' + ADMIN_APP_URL_DIRECTORY + '&c=game_test&m=ajax_select',
			   data: 'game_id='+game_id,
				success: function (html) {
					
					dialog.content(html);

					game_model.select_test_form_submit();

					dialog.title('选择游戏开测');
					dialog.button({
									value: '确定',
									callback: function () {
										
										var val = $("#select_test_list input[name='sle_id']:checked").val();
										
										if (!val) {
											
											admin.msg_error('请选择一条开测');
								
											return false;	
										}
										
										var data = val.split('@');
										
										$(id_input).val(data[0]);
										$(name_input).val(data[1]);
										
									}});
					dialog.button({value: '取消'});
				},
				cache: false
			});
		};
		
		game_model.select_test_form_submit = function() {
			
			game_model.search_ajax($('#test_keyword', '#select_test_form'), '&field=name&select=id,name', 'keyword');

            admin.table_list_bind_style('#select_test_list');

            $('#stime','#select_test_form').focus(function(e) {
                WdatePicker({startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true})
            });

            $('#etime','#select_test_form').focus(function(e) {
                WdatePicker({startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true})
            });

            $("#select_test_list input[name='sle_id']").click(function(){

                var data = $(this).val().split('@');

                $('#test_id').val(data[0]);
                $('#test_name').val(data[1]);

                art.dialog.get('select_test_dialog').close();
            });

			$('#select_test_form').submit(function(){
							
				$.ajax({
				   type: 'GET',
					url: ADMIN_APP_URL,
				   data: $('#select_test_form').serializeArray(),
					success: function (html) {
						
						var dialog = art.dialog.get('select_test_dialog');
						
						dialog.content(html);
						
						game_model.select_test_form_submit();
						
						return false;
					},
					cache: false
					});	
										
				return false;	
			});
		};	
		
		/* 打开开测搜索界面 */
        game_model.select_gift= function (id_input, name_input) {
			
			var game_id = $('#game_id').val();
			
			if (!game_id) {
				
				admin.msg_error('请选择一个游戏', function(){ $('#game_name').focus(); });
								
				return false;	
			}
			
			var dialog = art.dialog({id: 'select_gift_dialog',fixed: true,title:'加载中...',width:750,padding: 0,zIndex:22, lock:true});
			
			$.ajax({
			   type: 'GET',
				url: ADMIN_APP_URL + '?d=' + ADMIN_APP_URL_DIRECTORY + '&c=game_gift&m=ajax_select',
			   data: 'game_id='+game_id,
				success: function (html) {
					
					dialog.content(html);
					
					game_model.select_gift_form_submit();

					dialog.title('选择游戏礼包');
					dialog.button({
									value: '确定',
									callback: function () {
										
										var val = $("#select_gift_list input[name='sle_id']:checked").val();
										
										if (!val) {
											
											admin.msg_error('请选择一条礼包');
								
											return false;	
										}
										
										var data = val.split('@');
										
										$(id_input).val(data[0]);
										$(name_input).val(data[1]);
										
									}});
					dialog.button({value: '取消'});
				},
				cache: false
			});
		};
		
		game_model.select_gift_form_submit = function() {
			
			game_model.search_ajax($('#gift_keyword', '#select_gift_form'), '&field=name&select=id,name', 'keyword');

            admin.table_list_bind_style('#select_gift_list');

            $('#stime','#select_gift_form').focus(function(e) {
                WdatePicker({startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true})
            });

            $('#etime','#select_gift_form').focus(function(e) {
                WdatePicker({startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true})
            });

            $("#select_gift_list input[name='sle_id']").click(function(){

                var data = $(this).val().split('@');

                $('#gift_id').val(data[0]);
                $('#gift_name').val(data[1]);

                art.dialog.get('select_gift_dialog').close();
            });

			$('#select_gift_form').submit(function(){
							
				$.ajax({
				   type: 'GET',
					url: ADMIN_APP_URL,
				   data: $('#select_gift_form').serializeArray(),
					success: function (html) {
						
						var dialog = art.dialog.get('select_gift_dialog');
						
						dialog.content(html);
						
						game_model.select_gift_form_submit();
						
						return false;
					},
					cache: false
					});	
										
				return false;	
			});
		};	
		
		game_model.gift_export = function(gift_id, gift_name, gift_num) {
						
			if (!gift_id || gift_num<1) {
				
				admin.msg_error('没有导出的');
								
				return false;	
			}
			
			var dialog = art.dialog({id: 'gift_export_dialog',fixed: true,title:'加载中...',width:400,padding: 0,zIndex:22, lock:true});
			
			var html = '';

            html += "<form id=\"dialog_form\">";
            html += "<table cellpadding=\"0\" cellspacing=\"1\">";
            html += "    <tr>";
            html += "      <th> <strong>选择的数据</strong> </th>";
            html += "      <td>";
            html += "      " + gift_id + ":" + gift_name + " ";
            html += "      </td>";
            html += "    </tr>";
            html += "    <tr>";
            html += "      <th> <strong>导出数量</strong> </th>";
            html += "      <td>";
            html += "      	<input type=\"text\" name=\"gift_export_num\" size=\"20\" value=\"\" autocomplete=\"off\" />";
			html += "       <span class=\"c4\">还有"+ gift_num +"个可以导出</span><input type=\"hidden\" name=\"gift_export_max\" value=\""+ gift_num +"\" />";
            html += "      </td>";
            html += "    </tr>";
            html += "</table>";
            html += "</form>";
			
			dialog.content(html);
			dialog.title('导出礼包');
            dialog.button({
                value    : '导出',
                callback : function () {

                    if ($('#dialog_form [name=gift_export_num]').val() == '') {

                        admin.input_error('#dialog_form [name=gift_export_num]', '导出数量不能为空！');

                        return false;
                    }
					
					if ($('#dialog_form [name=gift_export_num]').val()*1 > $('#dialog_form [name=gift_export_max]').val()*1) {

                        admin.input_error('#dialog_form [name=gift_export_num]', '导出数量最大'+$('#dialog_form [name=gift_export_max]').val()+'个');

                        return false;
                    }
					
					var gift_export_num = $('#dialog_form [name=gift_export_num]').val();
					
                    $.ajax({
                        type     : "POST",
                        dataType : "json",
                        url      : ADMIN_APP_URL + '?d=' + ADMIN_APP_URL_DIRECTORY + '&c=game_gift&m=gift_export&a=check',
                        data     : {id:gift_id,num:gift_export_num},
                        success  : function (request) {

                            switch (parseInt(request.status)) {
                                case 1:

                                    var dialog = art.dialog.get('gift_export_dialog');
									
                                    admin.msg_ok("马上下载", function () {
                                         window.open(ADMIN_APP_URL + '?d=' + ADMIN_APP_URL_DIRECTORY + '&c=game_gift&m=gift_export&a=down&id='+gift_id+'&num='+gift_export_num+'&key='+request.info, 'new');
										 location.reload();
                                    });
									
									dialog.close();

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
			
		};

        /* 云游戏更多 */
        game_model.game_input = function (bnt_obj, list_obj) {

            $(bnt_obj).data('page', 1).data('last', 0);

            $(bnt_obj).click(function(){

                if ($(bnt_obj).data('last')==1) {

                    admin.msg_error('后面没有了哦');

                    return false;
                }

                var page = $(this).data('page');

                art.dialog({
                    id           : 'game_input_loading',
                    title        : '加载中',
                    content      : '<table><tr><td width="50"><div style="background-position: center center;background-repeat: no-repeat; height: 48px; margin: 10px 0 10px 10px; width: 48px;background: url(' + RES_BASE_DIR + '/res/lib/artDialog/skins/icons/succeed.png) repeat scroll 0% 0% transparent; display:block;"></div></td><td>正在下载数据... ...</td></tr></table>',
                    fixed        : true, lock : true, ok : false, time : false, padding : '0px 10px'
                });

                $.ajax({
                    type: 'get',
                    url : ADMIN_APP_URL +'?d='+ADMIN_APP_URL_DIRECTORY+'&c='+ADMIN_APP_URL_CONTROLLER+'&m=input',
                    data: 'page='+(page+1),
                    dataType : 'json',
                    success: function (request) {

                        switch (parseInt(request.status)) {
                            case 1:

                                if (!request.data) {

                                    art.dialog.get('game_input_loading').close();

                                    admin.msg_error('后面没有了哦');

                                    $(bnt_obj).data('last', 1);

                                    return false;
                                }

                                $(bnt_obj).data('page', (page+1));

                                $.each(request.data,function(key,vo){

                                    var _h = '<tr id="tr_'+ vo['id'] +'">';
                                    _h +=' 	<td class="align_c"><input type="checkbox" value="'+ vo['id'] +'" name="id[]" id="key" class="checkbox_style"> </td>';
                                    _h +='	<td class="align_l">'+ vo['id'] +'</td>';
                                    _h +='	<td class="align_l">'+ vo['name'] +'</td>';
                                    _h +='	<td class="align_l">'+ vo['version'] +'</td>';
                                    _h +='	<td class="align_l">'+ vo['company'] +'</td>';
                                    _h +='	<td class="align_c">'+ vo['filesize'] +' MB</td>';
                                    _h +='	<td class="align_c">'+ (vo['and_downs'][0]['url']?vo['and_downs'][0]['url']:'') +'</td>';
                                    _h +='</tr>';

                                    $(list_obj).append(_h);
                                });

                                admin.table_list_bind_style(document);

                                admin.table_form_bind_style(document);

                                art.dialog.get('game_input_loading').close();

                                break;

                            default:

                                art.dialog.get('game_input_loading').close();

                                admin.msg_error(request.info);

                                break;

                        }
                    },
                    cache: false
                });

            });
        };

        /* 云开服更多 */
        game_model.game_server_input = function (bnt_obj, list_obj) {

            $(bnt_obj).data('page', 1).data('last', 0);

            $(bnt_obj).click(function(){

                if ($(bnt_obj).data('last')==1) {

                    admin.msg_error('后面没有了哦');

                    return false;
                }

                var page = $(this).data('page');
                var ids = $(this).data('ids');

                art.dialog({
                    id           : 'game_input_loading',
                    title        : '加载中',
                    content      : '<table><tr><td width="50"><div style="background-position: center center;background-repeat: no-repeat; height: 48px; margin: 10px 0 10px 10px; width: 48px;background: url(' + RES_BASE_DIR + '/res/lib/artDialog/skins/icons/succeed.png) repeat scroll 0% 0% transparent; display:block;"></div></td><td>正在下载数据... ...</td></tr></table>',
                    fixed        : true, lock : true, ok : false, time : false, padding : '0px 10px'
                });

                $.ajax({
                    type: 'get',
                    url : ADMIN_APP_URL +'?d='+ADMIN_APP_URL_DIRECTORY+'&c=game_server&m=input&a=select_data',
                    data: 'page='+(page+1)+'&key='+ids,
                    dataType : 'json',
                    success: function (request) {

                        switch (parseInt(request.status)) {
                            case 1:

                                if (!request.data) {

                                    art.dialog.get('game_input_loading').close();

                                    admin.msg_error('后面没有了哦');

                                    $(bnt_obj).data('last', 1);

                                    return false;
                                }

                                $(bnt_obj).data('page', (page+1));

                                $.each(request.data,function(key,vo){

                                    var _h = '<tr id="tr_'+ vo['id'] +'">';
                                    _h +=' 	<td class="align_c"><input type="checkbox" value="'+ (vo['id']+','+vo['game_name']+','+vo['game_id']+','+vo['server_name']+','+vo['company']+','+vo['publish_time']) +'" name="id[]" id="key" class="checkbox_style"> </td>';
                                    _h +='	<td class="align_l">'+ vo['id'] +'</td>';
                                    _h +='	<td class="align_l">'+ vo['game_name'] +'</td>';
                                    _h +='	<td class="align_l">'+ vo['game_id'] +'</td>';
                                    _h +='	<td class="align_l">'+ vo['server_name'] +'</td>';
                                    _h +='	<td class="align_c">'+ vo['company'] +'</td>';
                                    _h +='	<td class="align_c">'+ game_model.date_format(vo['publish_time']) +'</td>';
                                    _h +='</tr>';

                                    $(list_obj).append(_h);
                                });

                                admin.table_list_bind_style(document);

                                admin.table_form_bind_style(document);

                                art.dialog.get('game_input_loading').close();

                                break;

                            default:

                                art.dialog.get('game_input_loading').close();

                                admin.msg_error(request.info);

                                break;

                        }
                    },
                    cache: false
                });

            });
        };

        /* 云礼包更多 */
        game_model.game_gift_input = function (bnt_obj, list_obj) {

            $(bnt_obj).data('page', 1).data('last', 0);

            $(bnt_obj).click(function(){

                if ($(bnt_obj).data('last')==1) {

                    admin.msg_error('后面没有了哦');

                    return false;
                }

                var page = $(this).data('page');
                var ids = $(this).data('ids');

                art.dialog({
                    id           : 'game_input_loading',
                    title        : '加载中',
                    content      : '<table><tr><td width="50"><div style="background-position: center center;background-repeat: no-repeat; height: 48px; margin: 10px 0 10px 10px; width: 48px;background: url(' + RES_BASE_DIR + '/res/lib/artDialog/skins/icons/succeed.png) repeat scroll 0% 0% transparent; display:block;"></div></td><td>正在下载数据... ...</td></tr></table>',
                    fixed        : true, lock : true, ok : false, time : false, padding : '0px 10px'
                });

                $.ajax({
                    type: 'get',
                    url : ADMIN_APP_URL +'?d='+ADMIN_APP_URL_DIRECTORY+'&c=game_gift&m=input&a=select_data',
                    data: 'page='+(page+1)+'&key='+ids,
                    dataType : 'json',
                    success: function (request) {

                        switch (parseInt(request.status)) {
                            case 1:

                                if (!request.data) {

                                    art.dialog.get('game_input_loading').close();

                                    admin.msg_error('后面没有了哦');

                                    $(bnt_obj).data('last', 1);

                                    return false;
                                }

                                $(bnt_obj).data('page', (page+1));

                                $.each(request.data,function(key,vo){

                                    var _h = '<tr id="tr_'+ vo['id'] +'">';
                                    _h +=' 	<td class="align_c"><input type="checkbox" value="'+ (vo['id']+','+vo['game_name']+','+vo['game_id']+','+vo['name']+','+vo['publish_time']+','+vo['fail_time']+','+vo['total']+','+vo['num']) +'" name="id[]" id="key" class="checkbox_style"> </td>';
                                    _h +='	<td class="align_l">'+ vo['id'] +'</td>';
                                    _h +='	<td class="align_l">'+ vo['game_name'] +'</td>';
                                    _h +='	<td class="align_l">'+ vo['game_id'] +'</td>';
                                    _h +='	<td class="align_l">'+ vo['name'] +'</td>';
                                    _h +='	<td class="align_c">'+ game_model.date_format(vo['publish_time']) +'</td>';
                                    _h +='	<td class="align_c">'+ game_model.date_format(vo['fail_time']) +'</td>';
                                    _h +='	<td class="align_c">'+ vo['total'] +'</td>';
                                    _h +='	<td class="align_c">'+ vo['num'] +'</td>';
                                    _h +='</tr>';

                                    $(list_obj).append(_h);
                                });

                                admin.table_list_bind_style(document);

                                admin.table_form_bind_style(document);

                                art.dialog.get('game_input_loading').close();

                                break;

                            default:

                                art.dialog.get('game_input_loading').close();

                                admin.msg_error(request.info);

                                break;

                        }
                    },
                    cache: false
                });

            });
        };

        /* 时间转化 */
        game_model.date_format = function (time) {

            var   now=new Date(parseInt(time) * 1000);
            var   year=now.getFullYear();
            var   month=now.getMonth()+1;
            var   date=now.getDate();
            var   hour=now.getHours();
            var   minute=now.getMinutes();
            var   second=now.getSeconds();
            return year+"-"+month+"-"+date+" "+hour+":"+minute;
        }


    })();

})();