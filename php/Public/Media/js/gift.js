$(function() { $.ajax({ url: "http://sy.api.m.37.com/cardapi/_getCardList?type=new_card", dataType: "jsonp", success: function(i) {
            var t = "";
            if (1 == i.state) {
                for (var s = 0; s < i.list.length; s++) t += '<li><div class="gift_icon"><a target="_blank" href="/gift/detail/?id=' + i.list[s].ID + '"><img src="' + i.list[s].IIMG + '" alt="' + i.list[s].NAME + '" title="' + i.list[s].NAME + '"></a></div><div class="gift_info"><div class="gift_info_t"><a target="_blank" href="/gift/detail/?id=' + i.list[s].ID + '">' + i.list[s].NAME + '</a></div><div class="gift_info_msg" id="m1">' + i.list[s].GNAME + '</div><div class="gift_info_msg" id="m2">剩余:<span>' + i.list[s].STOCK + "</span>/" + i.list[s].CNT + '</div><div class="gift_info_receive"><a target="_blank" href="/gift/detail/?id=' + i.list[s].ID + '"><i class="gift-icon"></i><em>领取</em></a></div>';
                $("#gift_newst").append(t) } } }) });
