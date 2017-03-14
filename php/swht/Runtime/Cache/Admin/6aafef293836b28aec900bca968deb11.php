<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>Vlcms溪谷软件游戏运营管理平台2.0</title>
    <link href="/newswht/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/newswht/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/newswht/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/newswht/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/newswht/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/newswht/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/newswht/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/newswht/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/newswht/Public/Admin/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
    
</head>
<body>
    <!-- 头部 -->
    <div class="header">
        <!-- Logo -->
        <span class="logo"></span>
        <!-- /Logo -->

        <!-- 主导航 -->
        <ul class="main-nav">
            <?php if(is_array($__MENU__["main"])): $key = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($key % 2 );++$key;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (U($menu["url"])); ?>"><i class="menu_<?php echo ($key); ?>"></i><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <!-- /主导航 -->

        <!-- 用户栏 -->
        <div class="user-bar">
            <span style="display:block;float:left;margin:0 10px;color:#fff;">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></span>
            <a href="javascript:;" style="float:left;" class="user-entrance"><i class="icon-user"></i></a>
            <ul class="nav-list user-menu hidden">
                <li><i  class="man_modify"></i><a href="/media.php" target="_blank">网站首页</a></li>
                <li><i  class="man_modify"></i><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
                <li><i  class="man_quit"></i><a href="<?php echo U('Public/logout');?>">退出</a></li>
            </ul>   
        </div>
    </div>
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar">
        <div class="user_nav">
           <span><img src="/newswht/Public/Admin/images/tx.jpg"></span>
           <p><?php echo session('user_auth.username');?></p>
           <p style="margin-top:0px;">管理员</p>
        </div>
        <div  class="fgx">功能菜单</div>
        <!-- 子导航 -->
        
            <div id="subnav" class="subnav">
                <?php if(!empty($_extra_menu)): ?>
                    <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
                <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
                    <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
                        <ul class="side-sub-menu">
                            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                                    <a class="item" href="<?php echo (U($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul><?php endif; ?>
                    <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        
        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
            <!-- nav -->
            

            
    <!-- 标题栏 -->
    <div class="main-title">
        <h2>[<?php echo ($model['title']); ?>] 列表</h2>
    </div>
	<div class="cf">
		<!-- 高级搜索 -->
		<div class="search-form fr cf">
            <?php echo W('Search/game_list');?>
            <?php echo W('Search/promote_list');?>
            <div class="sleft">
                <div class="drop-down drop-down1" style="width:100px">
                    <span id="sch-sort-txt" class="sort-txt" data="{I('register_way')}" style="width:65px">
                        <?php if(I('register_way') == ''): ?>注册方式
                        <?php elseif(I('register_way') == 0): ?>WEB
                        <?php elseif(I('register_way') == 1): ?>SDK
                        <?php else: ?>APP<?php endif; ?>
                    </span>
                    <i class="arrow arrow-down"></i>
                    <ul id="sub-sch-menu" class="nav-list hidden">
                        <li><a href="javascript:;" value="" style="width:80px">注册方式</a></li>
                        <li><a href="javascript:;" value="0" style="width:80px">WEB</a></li>
                        <li><a href="javascript:;" value="1" style="width:80px">SDK</a></li>
                        <li><a href="javascript:;" value="2" style="width:80px">APP</a></li>
                    </ul>
                </div>
                <input type='hidden' id='register_way' name="register_way" value="<?php echo I('register_way');?>"></input>
            </div>
        <div class='sleft'>
                <input type="text" id="time-start" name="time-start" class="text input-2x" value="<?php echo I('time-start');?>" placeholder="注册起始时间" /> -                     
                <div class="input-append date" id="datetimepicker"  style="display:inline-block">
                    <input type="text" id="time-end" name="time-end" class="text input-2x" value="<?php echo I('time-end');?>" placeholder="注册结束时间" />
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
        </div>
			<div class="sleft">
				<input type="text" name="account" class="search-input" value="<?php echo I('account');?>" placeholder="请输入账号">
				<a class="sch-btn" href="javascript:;" id="search" url="<?php echo U('Member/user_info','model='.$model['name'],false);?>">
                    <i class="btn-search"></i>
                </a>
			</div>
		</div>
	</div>


    <!-- 数据列表 -->
    <div class="data-table">
        <div class="data-table table-striped">
            <table>
                <!-- 表头 -->
                <thead>
                    <tr>
                        <th class="row-selected row-selected">
                            <input class="check-all" type="checkbox">
                        </th>
                        <th style="text-align:center" >用户id</th>
                        <th style="text-align:center" >用户账号</th>
                        <th style="text-align:center" >用户昵称</th>
                        <th style="text-align:center" >平台币余额</th>
                        <th style="text-align:center" >用户状态</th>
                        <th style="text-align:center" >最后登陆时间</th>
                        <th style="text-align:center" >最后登陆IP</th>
                        <th style="text-align:center" >所属渠道</th>
                        <th style="text-align:center" >注册时间</th>
                        <th style="text-align:center" >注册IP</th>
                        <th style="text-align:center" >操作</th>
                    </tr>
                </thead>
                <!-- 列表 -->
                <tbody>
                    <?php if(is_array($list_data)): $i = 0; $__LIST__ = $list_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><tr>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><input class="ids" type="checkbox" value="<?php echo ($data['id']); ?>" name="ids[]"></td>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><?php echo ($data["id"]); ?></td>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><?php echo ($data["account"]); ?></td>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><?php echo ($data["nickname"]); ?></td>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><?php echo ($data["balance"]); ?></td>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><?php echo get_status_text(1,$data['lock_status'],4);?></td>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><?php echo (date('Y-m-d H:i:s',$data["login_time"])); ?></td>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><?php echo ($data["login_ip"]); ?></td>
                           <td style="border-right:1px solid #DDDDDD;text-align:center"> <?php if(get_parent_promote(get_promote_name($data['promote_id'])) != '' ): echo get_promote_name($data['promote_id']);?>[<?php echo get_parent_promote(get_promote_name($data['promote_id']));?>]<?php else: echo get_promote_name($data['promote_id']); endif; ?></td>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><?php echo (date('Y-m-d H:i:s',$data["register_time"])); ?></td>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><?php echo ($data["register_ip"]); ?></td>
                            <td style="border-right:1px solid #DDDDDD;text-align:center"><a href="<?php echo U('Member/edit?id='.$data['id']);?>">查看</a></td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="page">
        <?php echo ((isset($_page) && ($_page !== ""))?($_page):''); ?>
    </div>

        </div>
        <div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用<a href="http://www.vlcms.com">Vlcms溪谷软件</a>游戏运营平台V2.0</div>
                <div class="fr">V2.0.1.0604</div>
            </div>
        </div>
    </div>
    <!-- /内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "/newswht", //当前网站地址
            "APP"    : "/newswht/admin.php?s=", //当前项目地址
            "PUBLIC" : "/newswht/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/newswht/Public/static/think.js"></script>
    <script type="text/javascript" src="/newswht/Public/Admin/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /*初始化导航菜单*/
            $subnav.find(".icon").addClass("icon-fold");
            $subnav.find("ul").siblings(".side-sub-menu").hide();
            
            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
            $subnav.find("a[href='" + url + "']").parent().addClass("current");
            //显示选中的菜单
            $subnav.find("a[href='" + url + "']").parent().parent().prev("h3").find("i").removeClass("icon-fold");
            $subnav.find("a[href='" + url + "']").parent().parent().show();

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
<link href="/newswht/Public/static/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
<?php if(C('COLOR_STYLE')=='blue_color') echo '<link href="/newswht/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">'; ?>
<link href="/newswht/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/newswht/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="/newswht/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
<script type="text/javascript">
//导航高亮
highlight_subnav('<?php echo U('Member/user_info');?>');
$(function(){
	//搜索功能
	$("#search").click(function(){
		var url = $(this).attr('url');
        var query  = $('.search-form').find('input').serialize();
        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
        query = query.replace(/^&/g,'');
        if( url.indexOf('?')>0 ){
            url += '&' + query;
        }else{
            url += '?' + query;
        }
		window.location.href = url;
	});

    //回车自动提交
    $('.search-form').find('input').keyup(function(event){
        if(event.keyCode===13){
            $("#search").click();
        }
    });
    $('#time-start').datetimepicker({
        format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });

    $('#datetimepicker').datetimepicker({
       format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        autoclose:true,
        pickerPosition:'bottom-left'
    })
})

/* 状态搜索子菜单 */
    $(".search-form").find(".drop-down1").hover(function(){
        $("#sub-sch-menu").removeClass("hidden");
    },function(){
        $("#sub-sch-menu").addClass("hidden");
    });

    $("#sub-sch-menu li").find("a").each(function(){
        $(this).click(function(){
            var text = $(this).text();
            $("#sch-sort-txt").text(text).attr("data",$(this).attr("value"));
            $("#sub-sch-menu").addClass("hidden");
            $('#register_way').val($(this).attr('value'));
        })
    });
</script>

</body>
</html>