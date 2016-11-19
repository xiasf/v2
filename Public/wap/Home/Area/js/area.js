/**
 * 选择地址js功能
 * update：2016-4-15 02:24:15
 */

var city_code = 271;	// 默认城市
var city = '黄冈市';
var address = '';
var lng = '';
var lat = '';

// 清除历史地址
function history_clean() {
	layer.open({
		content: '确定要清除历史地址吗？',
		btn: ['确定', '不清除'],
		yes: function(index){
			set_cookie('history_address', null);
			$('#history_address_list').empty().html('<p id="get_location_btn" onclick="get_location()"><i class="fa fa-crosshairs"></i>定位</p>');
			layer.open({content: '<i class="fa fa-check-circle"></i>&nbsp;清除成功', time:1});
		},
	});
}

// 定位
function get_location() {
	layer.open({content: '<i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;正在定位……'});
	getLocation(function(position){
		showPosition(position, function(result){
			if (result.status == 0) {
				layer.open({
					content: '猜你在：<font color=red>'+result.result.formatted_address+'</font>',
					btn: ['嗯嗯，是的', '错了，笨蛋'],
					yes: function(index){
						ck(result.result.location.lng, result.result.location.lat, result.result.formatted_address, '');
					},
				    no: function(index){
				    	layer_no();
				    },
				});
			} else {
				layer.open({content: '<i class="fa fa-exclamation-triangle"></i>&nbsp;哎呀妈，暂无法获取您的位置。'});
			}
		});
	});
};

var layer_no = function(){
		layer.open({
			content: '<i class="fa fa-meh-o"></i>&nbsp;我错了，我哄你还不行吗，别生气了，嗯？',
			btn: ['再试一次', '我讨厌你'],
			yes: function(index){
				get_location();
			},
		    no: function(index){
		    	layer.open({
		    		content: '<i class="fa fa-meh-o"></i>&nbsp;别生气了，我真的错了，我错在不乖，连给你定个位都不准，要我有什么用呢，我给你讲个笑话听好不好，不要生气了',
					btn: ['嗯', '不听'],
					yes: function(index){
						layer.open({
							content: '“小时候去动物园看老虎，看着特威风可爱，暗暗发誓长大以后一定也要养一个。20年后，这个梦想终于实现了。不说了，该给媳妇做饭去了……”',
							btn: ['好吧，原谅你了', '一点都不好笑'],
							yes: function(index){
								layer.open({
						    		content: '嘻嘻，那不要生气了哦，让我再试一次好吧？',
						    		btn: ['嗯嗯', '不用了，你歇一会'],
						    		yes: function(index){
										get_location();
									},
									no: function(index){
										layer.open({content: '那你不要生气了啊，生气对身体不好。', time:2});
									},
						    	});
							},
						    no: function(index){
						    	layer.open({content: '<i class="fa fa-frown-o"></i>&nbsp;对不起，我错了，我面壁去了。',time:2});
						    },
						});
					},
					no: function(index){
						layer.open({content: '<i class="fa fa-frown-o"></i>&nbsp;大哭', time:2});
					},
		    	});
		    },
		});
	};


function ck(lng, lat, name, address) {
	set_cookie('lng', lng);
	set_cookie('lat', lat);
	set_cookie('address', name);

	// 设置历史地址
	var json = {'lng':String(lng), 'lat':String(lat), 'name':name, 'address':address};
	var json_ = window.JSON.stringify(json);
	if (!$.cookie('history_address') || ($.cookie('history_address').indexOf(json_) == -1)) {
		var arr = $.parseJSON($.cookie('history_address') ? $.cookie('history_address') : '[]');
		arr.unshift(json);
		if(arr.length > 5) {
			arr.pop();
		}
		set_cookie('history_address', window.JSON.stringify(arr));
	} else {	// 移动位置（移动到第一个 ————当前位置）
		var arr = $.parseJSON($.cookie('history_address'));
		$(arr).each(function(i, v){
			if (json_ == window.JSON.stringify(v)) {
				arr.splice(i, 1);
				arr.unshift(v);
			}
		});
		set_cookie('history_address', window.JSON.stringify(arr));
	}
	window.location.href = check_url + '?' + Math.random();
}

$(function() {
	set_cookie('city_code', city_code);
	set_cookie('city', city);

	// 定位服务
	if (!$.cookie('lng'))
		layer.open({
			content: '待会可能会有人问你是否允许我访问你的位置信息，你可要点允许哦',
			btn: ['开始定位', '好的，知道了'],
			yes: function(index){
				get_location();
			},
		});


	// 历史地址
	if ($.cookie('history_address')) {
		var html = template('tpl_history_address_list', {'list':$.parseJSON($.cookie('history_address'))});
		$('#history_address_list').html(html);
	}

	// 地址搜索联想
	$('input[name="word"]').bind('keyup', function(){
		return;
		$('#area_list').hide();
		var word = $.trim($('input[name="word"]').val());
		if(word.length > 0) {
			suggestion_poi(word, function(result){
				if(result.status == 0) {
					$.each(result.result, function(i, v){
						result.result[i].address = v.city + v.district;
					});
					var html = template('tpl_area_list', {'list':result.result});
					$('#area_list').html(html);
					$('#area_list').show();
				}
			});
		}
	});

	// 搜索服务
	$('.geo-search-btn').click(function() {
		var that = $(this);
		var word = $(this).prev().val().trim();
		if (!word) return false;
		that.html('搜索中.');
		var wait;
		var wait_i = 0;
		wait = setInterval(function() {  
            wait_i++;
            that.html(that.html() + '.');
            if (wait_i > 4) {
            	wait_i = 0;
            	that.html(that.html().substring(0, 4));
            }
        },200);
		that.attr("disabled", true);
		that.addClass("geo-search-btn-on");
		search_poi(word, function(result) {
			clearInterval(wait);
			that.html('搜索');
			that.removeAttr("disabled");
			that.removeClass("geo-search-btn-on");
			if (result.status == 0) {
				if (result.results.length > 0) {
					var html = template('tpl_area_list', {'list':result.results});
					$('#area_list').html(html);
				} else {
					$('#area_list').html('<div class="geo-search-empty ng-scope" ng-if="searchaddrlen === 0">啊！竟然找不到“'+word+'”这个地址</div>');
				}
			} else {
				$('#area_list').html('<div class="geo-search-empty ng-scope" ng-if="searchaddrlen === 0">对不起服务忙，我好遭捏啊，等下再试试吧。</div>');
			}

		});
		return false;
	});
});