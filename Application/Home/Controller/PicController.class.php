<?php
namespace Home\Controller;
use Core\Image; 
/**
 * 图片控制器
 * Update time：2016-6-7 09:21:41
 */
class PicController {

    /**
     * 输出微缩图 home/pic/thumb/img/uploads/pic/2015-12-07/566523b7062dc.jpg/w/150/h/150/f/png/t/3/q/50/s/2/o/2
     * @param  string  $src      图像地址
     * @param  string $width   	 宽
     * @param  string $height    高
     * @param  string $format    输出格式 			默认jpg（貌似只有jpg支持清晰度调节）
     * @param  string $type    	 微缩方式 			默认2 缩放后填充类型
     * @param  string $q    	 清晰度调节 默认80
     * @param  string $s    	 是否缓存微缩图 	默认1缓存
     * @param  string $o    	 是否使用微缩图缓存 默认1可以使用
     * @return 图片流
     */
	public function thumb() {
		C('SHOW_PAGE_TRACE', false);

		if (!preg_match("#/img/(.*)/w/(\d+)/h/(\d+)#is", __SELF__, $matches)) return;

		preg_match("#/f/(\w+)#is", __SELF__, $a);

		preg_match("#/t/(\d+)#is", __SELF__, $b);

		preg_match("#/q/(\d+)#is", __SELF__, $c);

		preg_match("#/s/(\d+)#is", __SELF__, $d);

		preg_match("#/o/(\d+)#is", __SELF__, $e);

		$src 	= $matches[1];
		$width 	= $matches[2];
		$height = $matches[3];
		$format = isset($a[1]) ? $a[1] : 'jpg';
		$type 	= isset($b[1]) ? $b[1] : 2;
		$q 	 	= isset($c[1]) ? $c[1] : 80;
		$s 	 	= isset($d[1]) ? $d[1] : 1;
		$o 	 	= isset($e[1]) ? $e[1] : 1;

		$img = $this->getThumbFile($src, $width, $height, $format, $type, $q);

		if ($o && $this->isFile($src, $width, $height, $format, $type, $q)) {
 			$fileExt    = pathinfo($img, PATHINFO_EXTENSION);
            $mtime      = filemtime($img);
            $gmdate_mod = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';
			header('Last-Modified: ' . $gmdate_mod);
 			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (60*60*24*30)) . ' GMT');
 			header('Content-type: image/' . $fileExt);
 			header('Content-Length: ' . filesize($img));
			readfile($img);
			return;
		}

		if (!file_exists(PATH . $src)) {
			return '';
		}

		$image = new Image();
		$image->open(PATH . $src);

		$width = $width == 0 ? $image->width() : $width;
		$height = $height == 0 ? $image->height() : $height;

		if ($s) {
			$dir = dirname($img);
	        if (!is_dir($dir)) {
	            mkdir($dir, 0777, true);
	        }
	        $image->thumb($width, $height, $type, $q)->save($img, $format, $q)->ss($format, $q);
		} else {
			$image->thumb($width, $height, $type, $q)->ss($format, $q);
		}
	}


	// 获取微缩图url
	public function get_thumb_src($src, $width, $height, $format, $type, $q) {
		if (!file_exists(PATH . $src)) {
			return '';
		}
		if(!$this->isFile($src, $width, $height, $format, $type, $q)) {
			$this->createThumb($src, $width, $height, $format, $type, $q);
		}
		return str_replace(PATH, __ROOT__ . '/', $this->getThumbFile($src, $width, $height, $format, $type, $q));
	}


	// 计算微缩图文件地址
	private function getThumbFile($src, $width, $height, $format, $type, $q) {
		$fileExt = pathinfo($src, PATHINFO_EXTENSION);
		$src = str_replace('.' . $fileExt, '', $src);
		return PATH . '_thumb/' . $src . '_' . $width . '-' . $height . '-' . $format . '-' . $type . '-' . $q . '.' . $fileExt;
	}


	// 创建微缩图
	private function createThumb($src, $width, $height, $format, $type, $q) {
		$img = $this->getThumbFile($src, $width, $height, $format, $type, $q);
		$image = new Image();
		$image->open(PATH . $src);
		$dir = dirname($img);
	        if (!is_dir($dir)) {
	            mkdir($dir, 0777, true);
        }
		$image->thumb($width, $height, $type)->save($img, $format, $q);
	}


	//  检测微缩图是否存在
	private function isFile($src, $width, $height, $format, $type, $q) {
		$img = $this->getThumbFile($src, $width, $height, $format, $type, $q);
		return file_exists($img) ? true : false;
	}
}