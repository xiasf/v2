/**
 * 登陆并绑定页面js功能
 * update：2016-4-3 16:03:20
 */

$(function(){
	$('#loginBtn').on('click', function(){
		var mobile = $('input[name=mobile]').val();
		var password = $('input[name=password]').val();

	var reg=/^1[3|4|5|7|8][0-9]\d{8}$/;
	if (!reg.test(mobile)) {
		layer.mis('请填写正确的手机号码！');
		return;
	}

	if (!password.trim()) {
		layer.mis('请输入登录密码');
		return;
	}

	$.post(loginbind_url, {mobile:mobile, password:password}, function(result, textStatus) {
		if (result.status) {
			layer.open({
			    content: '登录成功',
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