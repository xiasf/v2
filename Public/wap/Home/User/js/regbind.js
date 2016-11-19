/**
 * 注册绑定js功能
 * update：2016-6-19 16:01:31
 */

$(function(){

	function s() {
		var i = 60;
		$(".mesg-code").addClass('disabled');
		$('.mesg-code').attr('disabled', true);

		var s = setInterval(function(){
			if (i <= 0) {
				$(".mesg-code").removeClass('disabled');
				$('.mesg-code').attr('disabled', false);
				$('.mesg-code').html('获取短信验证码');
				clearInterval(s);
				return ;
			}
			var str = i-- + 's后可重新发送';
			$('.mesg-code').html(str);
		}, 1000);
	}


	$('.mesg-code').click(function(){
	var mobile = $('input[name=mobile]').val();
	var reg=/^1[3|4|5|7|8][0-9]\d{8}$/;
	if (!reg.test(mobile)) {
		layer.mis('请填写正确的手机号码！');
		return;
	}
	$.post(sms_code_url, {mobile:mobile}, function(result, textStatus) {
		if (result.status) {
			s();
			layer.open({
			    content: '短信发送成功',
			    time: 1,
			});
		} else {
			layer.mis(result.info, {time:1000});
		}
	}, 'json');
	});


	$('#regBtn').on('click', function(){
		var mobile = $('input[name=mobile]').val();
		var password = $('input[name=password]').val();
		var smsCode = $('input[name=smsCode]').val();

	var reg=/^1[3|4|5|7|8][0-9]\d{8}$/;
	if (!reg.test(mobile)) {
		layer.mis('请填写正确的手机号码！');
		return;
	}

	if (!password.trim()) {
		layer.mis('请输入登录密码');
		return;
	}

	if (!smsCode.trim()) {
		layer.mis('请输入短信验证码');
		return;
	}

	$.post(regbind_url, {mobile:mobile, password:password, smsCode:smsCode}, function(result, textStatus) {
		if (result.status) {
			layer.open({
			    content: '注册成功',
			    time: 1,
			    success: function(){
			    	window.location.href = result.url;
			    },
			});
		} else {
			layer.mis(result.info, {time:1000});
		}
	}, 'json');
	});
});