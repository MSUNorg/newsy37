$(function() {
    function i(i) {
        i.zclip({
            path: "http://image.37wan.cn/platform/js/lib/zclip/ZeroClipboard.swf",
            copy: function() {
                return $(this).siblings("#copyText").text()
            },
            beforeCopy: function() {
                $(this).addClass("copy-active")
            },
            afterCopy: function() {
                var i = $("<div class='copy-tips'><div class='copy-tips-wrap'>☺ 复制成功</div></div>");
                $("body").find(".copy-tips").remove().end().append(i),
                $(".copy-tips").fadeOut(3e3)
            }
        })
    }
	
	$('.copy').each(function() {
		i($(this));
	});
});
