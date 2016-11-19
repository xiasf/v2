/**
 * 支付页面js
 * update：2016-4-10 01:00:07
 */

// 倒计时
function GetRTime() {
   var t = end_time - now_time++;
   if (t < 1) {
   		clearInterval(s);
		$("#pay_button").onclick = function() {return false;};
		$("#pay_button").addClass('pay_button_off');
		$("#pay_button").attr('disabled', true);
   		$("#tt").parent().html('订单已超时，请重新下单');
   		return;
   }
   var d=Math.floor(t/60/60/24);
   var h=Math.floor(t/60/60%24);
   var m=Math.floor(t/60%60);
   var s=Math.floor(t%60);
   d_ = (d != 0) ? (d + "天") : '';
   h_ = (h != 0) ? (h + "时") : '';
   m_ = (m != 0) ? (m + "分") : '';
   s_ = (s != 0) ? (s + "秒") : '';
   var str = d_ + h_ + m_ + s_;
   $("#tt").html(str);
}

// 切换支付方式
function pay_type(t, i) {
	if (i == 'ALI_WAP') {
		$('form[name=payForm] input[name=channel]').val(i);
		t.className = 'ali_on';
		$(t).prev().attr('class', 'wx');
		$(t).next().attr('class', 'un');
	} else if (i == 'wx') {
		$('form[name=payForm] input[name=channel]').val(i);
		t.className = 'wx_on';
		$(t).next().attr('class', 'ali');
		$(t).next().next().attr('class', 'un');
	} else {
		t.className = 'un_on';
		$(t).prev().attr('class', 'ali');
		$(t).prev().prev().attr('class', 'wx');
		layer.open({
		    content: '抱歉，银行卡支付将晚些时候支持哈^_^',
		    end: function(){
		    	t.className = 'un';
				if ($('form[name=payForm] input[name=channel]').val() == 'wx')
					$(t).prev().prev().attr('class', 'wx_on');
				else
					$(t).prev().attr('class', 'ali_on');
		    }
		});
	}
}

// 支付
function pay() {
	$('#toast').show();
	$('#toast_mask').show();
	if ($('form[name=payForm] input[name=channel]').val() == 'wx') {
		callpay();
		$('#toast').hide();
		$('#toast_mask').hide();
		return;
	}
    $.ajax({
	  	type: "POST",
	  	dataType: "json",
	  	url: pay_url + '?' + Math.random(),
	  	data: $('form[name=payForm]').serialize(),
	  	success: function (result) {
	       if (result.status == 1) {
	       		callback(result.info);
	      	} else {
	      		$('#toast').hide();
				$('#toast_mask').hide();
	          	layer.open({
				    content: result.info,
				    btn: ['啊！你个大坏蛋！'],
				});
	      	}
	  	},
	});
}


// 提交回调函数
function callback(url) {
	$('#toast').hide();
	$('#toast_mask').hide();
	window.location.href = url;
}

//调用微信JS api 支付
function jsApiCall() {
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        jsApiParam,
        function(res){
            WeixinJSBridge.log(res.err_msg);
            // alert(res.err_code+res.err_desc+res.err_msg);
        }
    );
}
function callpay() {
	if (!openid) {
		layer.open({
		    content: '请在微信客户端里进行支付哦',
		});
		return;
	}

    if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    }else{
        jsApiCall();
    }
}