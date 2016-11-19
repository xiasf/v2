document.write('<script type="text/javascript" src="'+VM.STATIC_URL+'/plugins/webuploader/dist/webuploader.js"></script>');
document.write('<link rel="stylesheet" href="/Public/js/webuploader/uploader.css">');

/**
 * WebUploader jQuery插件封装
 * Update time：2016-3-6 17:40:55
 * Doc：http://fex.baidu.com/webuploader/doc/index.html
 */

/*###code
$("#filePicker").uploader({
	uploadurl:"{url:jiaju/show_img_upload}",
	filelist:"#fileList",
	isone: false,
	uploaded:function(result){
		// result.status
		// result.url
	},
});
###code*/

jQuery(function($){
	var obj = function(options) {
		var $list = $(options.filelist),
        ratio = window.devicePixelRatio || 1,				// 优化retina, 在retina下这个值是2
        thumbnailWidth = 100 * ratio,
        thumbnailHeight = 100 * ratio,
		uploaderconfig = {
			auto: true,										// 自动上传。
			swf: '',										// BASE_URL + '/js/Uploader.swf',
			server: '',										// 文件接收服务端。
			pick: {id:this, innerHTML:'', multiple:true},	// 选择文件的按钮。可选。内部根据当前运行是创建，可能是input元素，也可能是flash.
			accept: {
				title: 'Images',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/*'
			},
			resize: false,				// 是否压缩
			compress:false,
			thumb:{
				width: 152,
			    height: 152,
			    quality: 10,			// 图片质量，只有type为`image/jpeg`的时候才有效。
			    allowMagnify: true,		// 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
			    crop: false,				// 是否允许裁剪。
			    type: 'image/jpeg', 	//为空的话则保留原有图片格式。否则强制转换成指定的类型。
			},
			fileVal:'file',
			runtimeOrder:'html5,flash',
			prepareNextFile:true,
			chunked: true,
			paste:document.body,
			// dnd:$('.uploader-list'),
			disableGlobalDnd:false,
			duplicate:true,
		};

		this.options = $.extend(uploaderconfig, options);

		// 首次创建（jQuery的length是可以用来当做判断元素是否存在的方法）
		if ($list.length == 0) $list = $('<ul class="uploader-list" id="' + options.filelist + '"></ul>').insertAfter(this);
		
		uploader = WebUploader.create(uploaderconfig);	// 初始化Web Uploader

		// 当有文件添加进来的时候
		uploader.on('fileQueued', function(file) {
			options.isone && $list.empty();
			var $li = $(
					'<div id="' + file.id + '" class="file-item thumbnail">' +
						'<img>' +
					'</div>'
					),
				$img = $li.find('img');

			// 追加
			$list.append($li);
	
			// 创建缩略图
			uploader.makeThumb(file, function(error, src) {
				if (error) {
					$img.replaceWith('<span>不能预览</span>');
					return;
				}
				$img.attr('src', src);
			}, thumbnailWidth, thumbnailHeight);
		});
	
		// 文件上传过程中创建进度条实时显示。
		uploader.on('uploadProgress', function(file, percentage) {
			var $li = $('#' + file.id ),
				$percent = $li.find('.progress span');
	
			// 避免重复创建
			if (!$percent.length) {
				$percent = $('<p class="progress"><span></span></p>').appendTo($li).find('span');
			}
			$percent.css('width', percentage * 100 + '%');
		});
	
		// 文件上传成功，给item添加成功class, 用样式标记上传成功。
		uploader.on('uploadSuccess', function(file, result) {
			var $li = $('#'+file.id);
			if (result.status == 1) {
				$li.addClass('file-item-done').children("img").attr({"src":result.url, "alt":result.url, "data-url":result.url});
				options.uploaded(result, $li);
			} else {
				$('<div class="error">错误：'+ result.info +'</div>').appendTo($li);
			}
		});
	
		// 文件上传失败，显示上传出错。
		uploader.on('uploadError', function(file, reason) {
			var $li = $('#'+file.id),
				$error = $li.find('div.error');
			// 避免重复创建
			if (!$error.length) {
				$error = $('<div class="error">上传失败:'+reason+'</div>').appendTo($li);
			}
		});

		// 完成上传完了，成功或者失败，先删除进度条。
		uploader.on('uploadComplete', function(file) {
			var $li = $('#'+file.id);
			$li.find('.progress').remove();
			if(options.uploadComplete) options.uploadComplete($li);
		});
	};

	$.fn.uploader = function(options) {
		var _options = $.extend({
				filelist:'',
				server:'',
				uploaded:function(){},
				auto:true,
				isone:true,	// 单图上传
			},
			options
		),
		i = 0;
		return this.each(function(i, d) {
			!_options.filelist && (_options.filelist = "filelist-" + i++);
			return obj.apply(this, [_options]);
		});
	};
});