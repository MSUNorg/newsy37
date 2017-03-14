/* 上传图片预览弹出层 */
if (typeof isloaded == 'undefined') {
	$(function(){
	    $(window).resize(function(){
	        var winW = $(window).width();
	        var winH = $(window).height();
	        $(document).delegate('.upload-pre-item img', 'click', function() {
	            //如果没有图片则不显示
	            if($(this).attr('src') === undefined){
	                return false;
	            }
	            // 创建弹出框以及获取弹出图片
	            var imgPopup = "<div class=\"uploadback\"><div id=\"uploadPop\" class=\"upload-img-popup\"></div></div>"
	            var imgItem = $(this).parent().html();

	            //如果弹出层存在，则不能再弹出
	            var popupLen = $(".uploadback").length;
	            if( popupLen < 1 ) {
	                $(imgPopup).appendTo("body");
	                $(".upload-img-popup").append($(this).parent().clone());
	                var $_a = $("<a class=\"close-pop\" href=\"javascript:;\" title=\"关闭\"></a>");
	                $(".upload-img-popup").append($_a);
	            }

	            // 弹出层定位
	            var uploadImg = $("#uploadPop").find("img");
	            var popW = uploadImg.width();
	            var popH = uploadImg.height();
	            $(".upload-img-popup").css({
	                // "max-width" : winW * 0.9,
	                "left": '50%',
	                "top": '50%',
	                "margin-left": -popW/2-10,
	                "margin-top": -popH/2-10,
	            });
	        });

	        // 关闭弹出层
	        $("body").on("click", "#uploadPop .upload-pre-item  , .uploadback", function(){
	            $(".uploadback").remove();
	        });
	    }).resize();
	})
};
var isloaded = true;