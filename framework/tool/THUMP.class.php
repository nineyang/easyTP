<?php
	namespace framework\tool;
	class THUMP{
		//用户可以自定义存储路径
		//用户传图片过来之后马上获取样式
		//图片映射数组
		private $path ='./';
		private $file;//用来存储文件
		private $file_type;//是一个数组
		private $pre_name = 'thum_';
		public function setpre_name($pre_name){
			$this->pre_name = $pre_name;
		}
		public function setpath($path){
			if(is_dir($path)){
				$this->path = $path;
			}
		}
		private $type2create = [
			'image/png' => 'imagecreatefrompng',
			'image/jpeg' => 'imagecreatefromjpeg',
			'image/gif' => 'imagecreatefromgif',
		];
		private $type2draw = [
			'image/png' => 'imagepng',
			'image/jpeg' => 'imagejpeg',
			'image/gif' => 'imagegif',
		];
		public function __construct($file){
			$this->file = $file;
			$this->getType($file);
		}
		private function getType($file){
			$this->file_type = getimagesize($file);
		}
		public function getThump($thum_w , $thum_h){
			//先根据需求画一个图 $w , $h
			$new_image = imagecreatetruecolor($thum_w , $thum_h);
			$bg_color = imagecolorallocate($new_image , 255 , 255 , 0);
			imagefill($new_image , 0 , 0 , $bg_color);
			//再画一个来自于图片的小图,
			$mime = $this->file_type['mime'];
			$old_image = $this->type2create[$mime]($this->file);
			list($old_w , $old_h) = $this->file_type;//获取宽高
			$rat = max($old_w/$thum_w , $old_h/$thum_h);//求得比例
			$new_w = $old_w/$rat;
			$new_h = $old_h/$rat;
			$new_x = ($thum_w - $new_w)/2;
			$new_y = ($thum_h - $new_h)/2;
			//开始合并
			imagecopyresampled($new_image , $old_image , $new_x , $new_y , 0 , 0 , $new_w , $new_h , $old_w , $old_h );
			//设置存放路径和名字
			$ext = strrchr(basename($this->file) , '.');
			$rename = uniqid($this->pre_name , true) . $thum_w . 'x' . $thum_h . $ext;
			$sub_path = date('Ymd') . '/';
			if(!is_dir($this->path . $sub_path)){
				mkdir($this->path . $sub_path , 0777 , true);
			}
			// 存放缩略图
			//var_dump($this->file_type);
			$this->type2draw[$mime]($new_image , $this->path . $sub_path . $rename);
			imagedestroy($new_image);
			imagedestroy($old_image);
			return $sub_path . $rename;
		}
	}