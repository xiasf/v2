/**
 * 自动登录页面js功能
 * update：2016-6-19 16:01:31
 */

$(function(){
	$('#loginBtn').on('click', function(){
		$.post(autoOauthLogin_url, null, function(result, textStatus) {
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