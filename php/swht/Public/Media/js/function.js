// 定义常量
var COOKIE_USER='_USER',
COOKIE_REFERRER='_REFERRER',
api_url = 'http://www.vlcms.com/',
USER_URL="media.php/Member/",
SERVICE_URL = 'index.php?s=/Defaults/Service/',
RES_BASE_DIR='/Public/Defaults/';
;





$(function () {

    (function ($) {

        $.fn.Slide = function (options) {

            var defaults = {

                        item: "slide-item",

                        nav: "slide-nav",

                        nowClass: "nownav"

                    },

                    options = options || {};

            options = $.extend(defaults, options);

            var cont = $(this),

                    item = cont.find("." + options.item),

                    nav = cont.find("." + options.nav),

                    curr = options.nowClass,

                    len = item.length,

                    width = item.width(),

                    html = "",

                    index = order = 0,

                    timer = null,

                    lw = "-" + width + "px",

                    rw = width + "px",

                    newtimer;

            item.each(function (i) {

                $(this).hover(function () {

                    _stop();

                },function () {

                    auto();

                }).css({left: i === index ? 0 : (i > index ? width + 'px' : '-' + width + 'px')});

                html += '<a href="javascript:">' + (i + 1) + '</a>';

            });

            html += '<div class="nav-mask"></div>';

            nav.html(html);

            var navitem = nav.find("a");

            navitem.eq(index).addClass(curr);

            function anim(index, dir) {

                if (order === len - 1 && dir === 'next') {

                    item.eq(order).stop(true, false).animate({

                        left: lw

                    });

                    item.eq(index).css({

                        left: rw

                    }).stop(true, false).animate({

                                left: 0

                            });

                } else if (order === 0 && dir === 'prev') {

                    item.eq(0).stop(true, false).animate({

                        left: rw

                    });

                    item.eq(index).css({

                        left: lw

                    }).stop(true, false).animate({

                                left: 0

                            });



                } else {

                    item.eq(order).stop(true, false)

                            .animate({

                                left: index > order ? lw : rw

                            });

                    item.eq(index).stop(true, false)

                            .css({

                                left: index > order ? rw : lw

                            })

                            .animate({

                                left: 0

                            });

                }

                order = index;

                navitem.removeClass(curr).eq(index).addClass(curr);

            }



            function next() {

                index = index >= len - 1 ? 0 : index + 1;

                anim(index, 'next');

            }



            function prev() {

                index = index <= 0 ? len - 1 : index - 1;

                anim(index, 'prev');

            }



            function auto() {

                timer = setInterval(next, 5000);

            }



            function _stop() {

                clearInterval(timer);

            }



            return this.each(function () {

                auto();

                navitem.hover(function () {

                    _stop();

                    var i = navitem.index(this);

                    if (/nownav/.test($(this).attr('class'))) {

                        return false;

                    }

                    if (newtimer) clearTimeout(newtimer);

                    newtimer = setTimeout(function () {

                        anim(i, this)

                    }, 250);

                }, auto);

                $("#next,#prev").hover(function () {

                    _stop();

                }, auto);

                $('#next').on('click', next);

                $('#prev').on('click', prev);

            });

        }

    })(jQuery);

    jQuery("#slide").Slide();

	

	

	for(var i=0;i<jQuery('.tabs').length;i++){

		jQuery('.tabs:eq('+i+') > div').hide().filter(':first').show();//初始化选项卡

		jQuery('.tabs:eq('+i+') > ul > li > a').filter(':first').addClass("on");

	}



	jQuery(".tabs ul.tabNavigation a").hover(function(){

		var indext=jQuery(".tabs ul.tabNavigation a").index(this);

		jQuery(this).parent().parent().nextAll().hide();

		jQuery(".tabs > div").eq(indext).show();

		jQuery(this).parent().parent().find("li a").removeClass('on');

		jQuery(this).addClass("on");

		return false;

	})

	

	jQuery(".col_2 .bg").css("opacity", 0.85);

jQuery(".col_2 li").hover(function() {

	$(this).find(".img_hover").animate({

		top: "0px"

	});

	$(this).find(".bg").animate({

		top: "0px"

	});

}, function() {

	$(this).find(".img_hover").animate({

		top: "-200px"

	});

	$(this).find(".bg").animate({

		top: "-200px"

	});

});





jQuery(".mcs dl").hover(function() {

	$(this).addClass("hover");

}, function() {

	$(this).removeClass('hover');

});





jQuery('#gift').each(function() {

	var me = $(this)

	var li = me.find('li:first');

	li.addClass('cur')

})



jQuery('#gift li').mouseover(function() {

	jQuery(this).addClass("cur").siblings().removeClass('cur');



})





});



