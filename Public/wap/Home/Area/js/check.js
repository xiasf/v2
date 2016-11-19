/**
 * 检测分店js功能
 * update：2016-4-4 11:34:14
 */
var wait;

$(function() {

	$('.bnrs img').css('width', parseInt($(window).width()) + 'px');
	$(window).resize(function(){
		$('.bnrs img').css('width', parseInt($(window).width()) + 'px');
	});

	var wait_i = 0;
	wait = setInterval(function() {
        wait_i++;
        $('#wait').html($('#wait').html() + '.');
        if (wait_i > 4) {
        	wait_i = 0;
        	$('#wait').html($('#wait').html().substring(0, 9));
        }
    },200);
});

so_lbs(lng + ',' + lat, function(result) {
	if (result.status == 0) {
		if (result.contents.length > 0) {

			clearInterval(wait);
			$('#branch_load #wait').html('恭喜，附近有分店');
			$('.note').html('<span class="area">' + address + '</span>附近有<span class="num">'+result.total+'</span>家分店可配送哦');
			$('.main_warp').show();
			$('#branch_load').remove();

			var swiper = new Swiper('#swiper-container', {pagination: '.swiper-pagination', paginationClickable: true, autoplay:3000});

			var html = template('tpl_branch_list', {'list':result.contents});
			$('#branch_list').html(html);

			$(".geo-search-result").lazyFade({
		        everse: false,
		        duration: 250,
		        delay: 50,
		        opacity: {
		            start: 0.01,
		            end: 1
		        }
	        });

			$("img.lazy").lazyload({
				placeholder: '',
				threshold : 20,
				// event : "click",
				// effect : "fadeIn",
				failure_limit : 10,
				container: $(".main_warp"),
			});

			// $('.geo-search-result').each(function(){
			// 	$(this).click(function(){
			// 		layer.open({
			// 		    content: '您无需选择分店哦，直接进入点餐就可以啦，我们会整合资源就近分配您的订单',
			// 		    btn: ['知道了', '查看分店信息']
			// 		});
			// 	});
			// });
			
		} else {
			set_cookie('lng', null);
			set_cookie('lat', null);
			set_cookie('address', null);
			layer.open({
			    content: '抱歉，' + address + '附近没有找到可以配送的分店，五味真火将加快覆盖速度，尽早为您服务，谢谢！',
			    btn: ['啊！哦，那好吧'],
			    end: function(){
			        window.location.href = area_url;
			    }
			});
		}
	} else {
		set_cookie('lng', null);
		set_cookie('lat', null);
		set_cookie('address', null);
		layer.open({
			title: '提示',
		    content: '抱歉，' + address + '附近没有找到可以配送的分店，五味真火将加快覆盖速度，尽早为您服务，谢谢！',
		    btn: ['啊！哦，那好吧'],
		    end: function(){
		        window.location.href = area_url;
		    }
		});
	}
});