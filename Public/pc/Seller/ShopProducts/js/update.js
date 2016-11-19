/**
 * 商店餐品 更新餐品 js功能
 * update：2016-4-19 14:48:27
 */

 $(function(){

    new Switchery(document.querySelector(".js-switch"), {color: "#1AB394"});
    $(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green",});

    $("#filePicker").uploader({
        server:img_upload_url,
        filelist:"#fileList",
        // isone: false,
        uploaded:function(result){
            $("input[name='img']").val(result.url.slice(1));
        },
    });

    var baiduBsSuggest = $("#shop_name").bsSuggest({
        indexId: 2,
        indexKey: 1,
        keyField:'name',
        allowNoKeyword: true,
        multiWord: true,
        separator: ",",
        getDataMethod: "url",
        effectiveFieldsAlias: {
            Id: "序号",
            Keyword: "店铺名称",
            Count: "ID"
        },
        showHeader: true,
        inputWarnColor: "rgba(255,0,0,.1)",
        url: get_shop_url + '?' + Math.random() + "&name=",
        jsonp: null,
        processData: function(json) {
            var i, len, data = {
                value: []
            };
            if (!json || json.length === 0) {
                return data;
            }
            len = json.length;
            for (i = 0; i < len; i++) {
                data.value.push({
                    "Id": i + 1,
                    "Keyword": json[i]['shop_name'],
                    "Count": json[i]['id']
                });
            }
            return data;
        }
    });
    $("#shop_name").on('onSetSelectValue', function (e, Keyword) {
        $('input[name=shop_id]').attr('data-value', Keyword.id);
        $('input[name=shop_id]').val(Keyword.id);
        if ($('input[name=shop_name]').val() != '' && $('input[name=shop_id]').val() != '') {
            init_cat_list(Keyword.id);
        }
    }).on("onDataRequestSuccess", function (e, result) {
        $('input[name=shop_id]').val('');
        if (!result) {
            $('input[name=shop_id]').val('');
        } else {
            $('input[name=shop_id]').val($('input[name=shop_id]').attr('data-value'));
        }
    }).on('onUnsetSelectValue', function (e) {
        console.log("onUnsetSelectValue");
    });

	init_cat_list();

    $("form[name=products_form]").submit(function(){
        // return true;
        $.post(products_update_url, $(this).serialize(), function(result){
            if (result.status == 1) {
                top.layer.alert(result.info);
                window.location.href = result.url;
            } else {
                top.layer.alert(result.info);
            }
        }, 'json');
        return false;
    });
});

 // 初始化
function init_cat_list() {
    $('[name=shop_category_id]').empty();
    // 获取分类数据
    get_json();
}

// 获取数据
function get_json() {
    $.getJSON(get_category_call_json_url + '?' + Math.random(), {shop_id:$("input[name=shop_id]").val()}, function(data) {
        callback(data);
    });
}

// 获取数据后的回调
function callback(data) {
    if (data.length > 0) {
    	for (var i in data) {
    		$('[name=shop_category_id]').append("<option value='"+data[i]['id']+"'>"+data[i]['name']+"</option>");
    	}
    	var index_category_id = products_data.shop_category_id;
    	if ($('[name=shop_category_id] option[value='+index_category_id+']')) {
    		// $('[name=shop_category_id]).attr('value',index_category_id);
    		// $('[name=shop_category_id]).val(index_category_id);
    		$('[name=shop_category_id] option[value='+index_category_id+']').attr('selected',true);
    	}
    } else {
    	$('[name=shop_category_id]').empty();
    	$('[name=shop_category_id]').append("<option value='"+0+"'>"+'暂无分类'+"</option>");
    }
}