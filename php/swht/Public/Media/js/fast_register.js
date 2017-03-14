
//弹出层
function $popup(arg1, arg2) {
	var $arg1 = arg1;
	var $arg2 = arg2;
	var $pLeft = ($(window).width() - $($arg1).width()) / 2 + $(window).scrollLeft();
	var $pTop = ($(window).height() - $($arg1).height()) / 2 + $(window).scrollTop();
	$pTop = $pTop > 0 ? $pTop : 40;
	if($.browser.msie && parseInt($.browser.version) == 6) {
		$("html,body").css("overflow", "hidden");
	}
	$("<div class='gray'></div>").appendTo($("body")).height($(document).height()).fadeTo("fast", 0.4);
	$($arg1).css({
		display : 'block',
		position : 'absolute',
		left : $pLeft,
		top : $pTop,
		zIndex : 10000
	});

	$($arg2 + ',' + ".gray").click(function() {
		$($arg1).hide();
		if($.browser.msie && parseInt($.browser.version) == 6) {
			$("html,body").css("overflow", "")
		};
		$(".gray").fadeOut(500, cb);
		function cb() {
			$(this).remove();
		}

		return false;
	});
	//窗口大小变化时调用
	$(window).bind('scroll resize', function(event) {
		var $pLeft = ($(window).width() - $($arg1).width()) / 2 + $(window).scrollLeft();
		var $pTop = ($(window).height() - $($arg1).height()) / 2 + $(window).scrollTop();
		$($arg1).animate({
			left : $pLeft,
			top : $pTop
		}, {
			duration : 500,
			queue : false
		})
	})
}


