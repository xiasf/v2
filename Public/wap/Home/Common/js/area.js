/**
 * 地图功能
 * update；2016-4-14 17:07:05
*/


// 搜索联想 - Place Suggestion API
function suggestion_poi(query, callback) {
	$.getJSON('http://api.map.baidu.com/place/v2/suggestion?callback=?',
		{'ak':'9Go7FByT2xPEH5VcPKMiZWoY', 'region':$.cookie('city_code'), 'q':query, 'output':'json'},
		function(result) {
			callback(result);
		}
	);
}


// 城市内搜索 - Place API
function search_poi(query, callback) {
	$.getJSON('http://api.map.baidu.com/place/v2/search?callback=?',
		{'ak':'9Go7FByT2xPEH5VcPKMiZWoY', 'region':$.cookie('city_code'), 'city_limit':true, 'query':query, 'output':'json'},
		function(result) {
			callback(result);
		}
	);
}


// 获取城市API
function get_ip_city() {
	$.getJSON('http://api.map.baidu.com/location/ip?callback=?',
		{'ak':'9Go7FByT2xPEH5VcPKMiZWoY', 'ip':'117.150.168.182', 'coor':'bd09ll'},
		function(result) {
			if (result.status == 0) {
				city = result.content.address_detail.city;
				city_code = result.content.address_detail.city_code;
				// lat = result.content.point.x;
				// lng = result.content.point.y;
				set_cookie('city', city);
				set_cookie('city_code', city_code);
			}
		}
	);
}


// LBS.云 （基于LBS.云接口）
function so_lbs(location, callback) {
	$.getJSON(so_lbs_url, {'location':location}, function(result) {
		callback(result);
	});
}

// 根据 lat_lng 获取详细  by Geocoding API
function Geocoding(lng, lat, coordtype, callback) {
	var lat_lng = lat + ',' + lng;
	if (!coordtype) coordtype = 'wgs84ll';
	$.getJSON('http://api.map.baidu.com/geocoder/v2/?callback=?',
		{'ak':'9Go7FByT2xPEH5VcPKMiZWoY', 'location':lat_lng, 'coordtype':coordtype, 'output':'json', 'pois':0},
		function(result) {
			callback(result);
		}
	);
}


// 定位
function getLocation(successCallback) {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(successCallback, showError);
	} else {
		layer.open({content: '<i class="fa fa-exclamation-triangle"></i>&nbsp;悲催，浏览器不支持地理定位。'});
	}
}

function showPosition(position, callback) {
	var latlon = position.coords.latitude + ',' + position.coords.longitude;
	Geocoding(position.coords.longitude, position.coords.latitude, 'wgs84ll', callback);
}

function showError(error) {
	switch(error.code) {
		case error.PERMISSION_DENIED:
			layer.open({content: '<i class="fa fa-exclamation-triangle"></i>&nbsp;定位失败，用户残忍地拒绝了请求地理定位服务'});
			break;
		case error.POSITION_UNAVAILABLE:
			layer.open({content: '<i class="fa fa-exclamation-triangle"></i>&nbsp;定位失败，位置信息是不可用的'});
			break;
		case error.TIMEOUT:
			layer.open({content: '<i class="fa fa-exclamation-triangle"></i>&nbsp;定位失败，请求获取用户位置超时'});
			break;
		case error.UNKNOWN_ERROR:
			layer.open({content: '<i class="fa fa-exclamation-triangle"></i>&nbsp;定位失败，定位系统失效'});
			break;
    }
}