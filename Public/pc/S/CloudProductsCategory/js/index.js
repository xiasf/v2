/**
 * 云餐品 分类 管理 js功能
 * update：2016-4-19 14:48:27
 */

$(function() {
	// 保存
	$('#save_btn').click(function() {
		save();
	});
	// 添加
	$('#add_btn').click(function() {
		add();
	});

	// 展开所有
	$('.expand-all').click(function() {
		$(this).attr('disabled', true);
		$(".collapse-all").removeAttr('disabled');
		$(".dd").nestable("expandAll");
	});

	// 收起所有
	$('.collapse-all').click(function() {
		$(this).attr('disabled', true);
		$(".expand-all").removeAttr('disabled');
		$(".dd").nestable("collapseAll");
	});


    $("#shop").change(function(){
		init_cat_list($(this).val());
	});

	// 初始化
	init_cat_list();
});

// 初始化
function init_cat_list() {
	$(".dd").prev().show();
	// 初始化
	topOL.empty();
	
	init_nestable();
	// 获取分类数据
	get_json();
}

// 初始化nestable及绑定事件
function init_nestable() {
	$("#nestable_wrapper").empty().append(topOL).nestable({group:1,maxDepth:1}).on("change", updateOutput);
	updateOutput($("#nestable_wrapper"));
}

// 获取数据
function get_json() {
	$.getJSON(get_category_call_json_url + '?' + Math.random(), {shop_type:$("#shop option:selected").val()}, function(data, status) {
		if (status)
			callback(data);
		else
			top.layer.alert('获取分类数据失败！');
	});
}

// 一级标准模板
var topOL = $("<ol class='dd-list'></ol>");

// 生成树结构DOM
function create_cat_item(item, parent) {
	var _item = $("<li class='dd-item' data-id='"+item.id+"' data-name='"+item.name+"'></li>").appendTo(parent);
	$("<div class='dd-handle text-danger'><i class='fa fa-arrows'></i>&nbsp;&nbsp;<span class='dd-name'>"+item.name+"</span></div><div class='dd-btn'><a href='javascript:void();' onClick='up_cat_name(this)' class='btn btn-primary btn-sm'>修改</a>&nbsp;&nbsp;<a href='javascript:void();' onClick='del(this)' class='btn btn-default btn-sm'>删除</a><div>").appendTo(_item);
	return _item;
}

// 树事件
var updateOutput = function(e) {
	var nestable_wrapper = e.length ? e : $(e.target),
	allcat = $("#allcat");
	if (window.JSON) {
		var json = nestable_wrapper.nestable("serialize");
		allcat.val(window.JSON.stringify(json));
		for (var item in json) {
			if (json[item].children != undefined) {
				$(".expand-all").attr('disabled', true);
				$(".collapse-all").removeAttr('disabled');
				break;
			} else {
				$(".expand-all").attr('disabled', true);
				$(".collapse-all").attr('disabled', true);
			}
		}
	} else {
		allcat.val("浏览器不支持");
	}
};

// 获取数据后的回调
function callback(data) {
	$(".dd").prev().hide();
	if (data.length == 0) {
		$("#save_btn").attr("disabled", true);
		$("#nestable_wrapper").empty().append('<div class="alert alert-danger">悲催，暂时没有数据</div>');
		updateOutput($("#nestable_wrapper"));
		return;
	};
	show_cat_list(data);
	$("#save_btn").removeAttr('disabled');
}

// 解析树结构
function parse_cat_json(list, parent) {
	for (var item in list) {
		var _item = create_cat_item(list[item], parent);
		if (list[item].children != undefined) {
			var _parent = $("<ol class='dd-list'></ol>").appendTo(_item);
			parse_cat_json(list[item].children, _parent);
		}
	}
}

// 显示树
function show_cat_list(catList) {
	parse_cat_json(catList, topOL);
	// 绑定事件与初始化，注意如果绕过这里其它地方要记得初始化绑定事件
	init_nestable();
};

// 添加树
function add() {
	var catName = $("input[name='catName']").val().trim();
	if (catName) {
		if ($('.dd-list').length == 0) {
			// 首次添加生成树，注意这里要初始化，并绑定事件哦，否则会出现严重问题！！！
			init_nestable();
		}
		create_cat_item({name:catName,id:0}, topOL);
		updateOutput($("#nestable_wrapper"));
		top.layer.msg("添加分类成功，记得点击保存生效哦", {time:1000});
		$("#save_btn").removeAttr('disabled');
	} else {
		top.layer.alert('请填写分类名称',{icon: 2,btn: '哦',moveType: 1});
	}
}

// 保存树
function save() {
	var data_base = $("#allcat").val().trim();
	if (!$.parseJSON(data_base).length) {
		top.layer.alert('数据非法！',{icon: 2,btn: '哦',moveType: 1});
		return false;
	}
	var index = top.layer.msg("正在保存……",{time:0,shade: [0.8, '#393D49']});
	$.post(category_edit_url, {str:data_base, shop_type:$("#shop option:selected").val()}, function(result, textStatus) {
		top.layer.close(index);
		if (result.status) {
			top.layer.msg("分类保存成功",{time:1000});
			init_cat_list();	// 初始化
		} else {
			top.layer.msg(result.info,{time:1000});
		}
	});
}

// 删除树
function del(_t){
	var id = $(_t).parent().parent().data("id");
	if (id==0) {
		$(_t).parent().parent().remove();
		updateOutput($("#nestable_wrapper"));
		var data_base = $("#allcat").val().trim();
		if (!$.parseJSON(data_base).length) {
			$("#save_btn").attr('disabled', true);
		}
		return false;
	};
	top.layer.confirm("你确定要删除分类吗？<br>注意，该分类下面全部子分类也将删除，且操作不可还原，连带所有分类下的商品将自动归为未分类", function(){
		$.post(del_url, {id:id}, function(result) {
			if (result.status) {
			    top.layer.msg("删除分类成功");
			    $(_t).parent().parent().remove();
				updateOutput($("#nestable_wrapper"));
				var data_base = $("#allcat").val().trim();
				if (!$.parseJSON(data_base).length) {
					$("#save_btn").attr('disabled', true);
				}
			} else {
				top.layer.msg(result.info);
			}
		});
	});
}

// 修改分类名称
function up_cat_name(_t){
	var txt = $(_t).parent().parent().find(".dd-name:first").html();
	var id = $(_t).parent().parent().attr('data-id');
	top.layer.prompt({
    	title: txt + ' - 输入新分类名称，并确认',
    	value: txt,
    	formType: 0
	}, function(text, index) {
		if (id == 0) {
			$(_t).parent().parent().find(".dd-name:first").html(text);
	    	$(_t).parent().parent().data("name", text);
	    	updateOutput($("#nestable_wrapper"));
			top.layer.close(index);
			return;
		}
		top.layer.title('正在更新……',index);
		$.post(update_name_url, {id:id,name:text}, function(result){
		    if (result.status) {
		    	$(_t).parent().parent().find(".dd-name:first").html(text);
		    	$(_t).parent().parent().data("name", text);
				updateOutput($("#nestable_wrapper"));
				top.layer.close(index);
				top.layer.msg("更新成功！",{time:1000});
		    } else {
		    	top.layer.alert(result.info);
		    }
		});
	});
}