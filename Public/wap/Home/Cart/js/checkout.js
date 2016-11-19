/**
 * 确认下单页面js
 * update：2016-4-25 04:30:20
 */

layer.mis = function (mis) {
	layer.open({
	    content: mis,
	    style: 'background-color:#92192E; color:#fff; border:none;',
	    time: 1
	});
};

// 提交订单
function submit_order() {
	if (!$('input[name=lng]').val().trim()) {
		layer.mis('请选择地址 [1]');
		return;
	}
	if (!$('input[name=lat]').val().trim()) {
		layer.mis('请选择地址 [2]');
		return;
	}
	if (!$('input[name=location]').val().trim()) {
		layer.mis('请选择地址 [3]');
		return;
	}
	if (!$('input[name=address]').val().trim()) {
		layer.mis('请填写详细地址！');
		return;
	}
	var mobile = $('input[name=mobile]').val();
	var reg=/^1[3|4|5|7|8][0-9]\d{8}$/;
	if (!reg.test(mobile)) {
		layer.mis('请填写正确的手机号码！');
		return;
	}
	if (mobile == '15171010250' || mobile == '13212742422') {
		layer.open({
		    content: '亲爱的baby，你就不用下单了，想吃什么，现在就叫我过来做给你吃',
		    style: 'background-color:#26CC96; color:#fff; border:none;',
		    time: 1
		});
		return;
	}

	$('#submit_button').val('提交中……');
	var submit_s = layer.open({
	    content: '提交中……',
	    shadeClose: false,
	});
    $.post(cart_checkout_post_url + '?' + Math.random(), $('#cart_form').serialize(), function(result){
       if (result.status == 1) {
          	layer.open({
			    content: '<i id="ok_ico" class="fa fa-check-circle"></i>下单成功，嘻嘻',
			    shadeClose: false,
			    success: function(){
			    	if ($('input[name=pay_type]').val() == 2) {
			    		window.location.href = pay_url + '?order_id=' + result.info + '&' + Math.random();
			    	} else {
			    		window.location.href = remindorder_url + '?order_id=' + result.info + '&' + Math.random();
			    	}
			    }
			});
      	} else {
          	layer.open({
			    content: result.info,
			    btn: ['啊！'],
			});
      	}
      	$('#submit_button').val('提交订单');
  	});
}


// 切换支付方式
function pay(t, i) {
	$('input[name=pay_type]').val(i);
	$('#pay_type .fa-circle-o,.fa-check-circle').addClass("fa-circle-o").removeClass("fa-check-circle");
	$(t).find('i').addClass("fa-check-circle").removeClass("fa-circle-o");
	$('#pay_type .pay').removeClass("on");
	$(t).addClass("on");
}


$(function() {

	var lng = $.cookie('lng'),
		lat = $.cookie('lat'),
		address = $.cookie('address'),
		cart_address = $.cookie('cart_address'),
		mobile = $.cookie('cart_mobile'),
		user_name = $.cookie('cart_user_name');

	if (!lng || !lat || !address) {
		window.location.href = area_url;
	}

	$('input[name=location]').click(function(){
		layer.open({
		    content: '您需要切换当前地址吗？',
		    btn: ['不切换', '是的'],
		    no: function(index){
		    	window.location.href = area_url;
		    },
		});
	});

	setTimeout(function() {
		$('input[name=lng]').val(lng);
		$('input[name=lat]').val(lat);
		$('input[name=location]').val(address);
		$('input[name=address]').val(cart_address);
		$('input[name=mobile]').val(mobile);
		$('input[name=user_name]').val(user_name);

		$('#submit_button').css('backgroundColor', '#FF2E51');

		layer.close(area_init_load);	// 地址初始化完成
	}, 260);
});