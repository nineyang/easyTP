<?php
	namespace framework\tool;
	use \FINFO;
	class UPLOAD{
		//用户可以设置存放的路径 前缀 
		private $path = './';
		private $pre_name = '';
		private $error_info;
		private $max_leng = 100000;
		private $ext2mime = array(
			'.png' => 'image/png',
			'.jpg' => 'image/jpeg',
			'.gif' => 'image/gif'
		);
		private $ext_list = array('.png' , '.jpg' , '.gif');
		public function setext_list($ext_list){
			$this->ext_list = $ext_list;
		}
		public function setmax_leng($max_leng){
			if($max_leng > 0){
				$this->max_leng = $max_leng;
			}
		}
		public function setpath($path){
			if(is_dir($path)){
				$this->path = $path;
			}
		}
		public function setpre_name($pre_name){
			$this->pre_name = $pre_name;
		}
		public function getUpload($file){
			if(0 !== $file['error']){
				$this->error_info['error'] = '上传失败';
			}elseif($this->max_leng < $file['size']){
				$this->error_info['size'] = '文件过大';
			}
			$ext = strrchr($file['name'] , '.');
			$finfo = new \FINFO(FILEINFO_MIME_TYPE);
			$mime = $finfo->file($file['tmp_name']);
			$mime_list = $this->ext2mime($this->ext_list);
			echo $ext;
			if(!in_array($ext , $this->ext_list) || !in_array($mime , $mime_list)){
				$this->error_info['mime'] = '类型错误';
			}
			if(!empty($this->error_info)){
				return $this->error_info;
			}
			//开始移动,设置名字和路径
			$rename = uniqid($this->pre_name , true) . $ext;
			$sub_path = date('Ymd') . '/';
			if(!is_dir($this->path . $sub_path)){
				mkdir($this->path . $sub_path);
			}
			if(move_uploaded_file($file['tmp_name'] , $this->path . $sub_path . $rename)){
				return $sub_path . $rename;
		}
	}
		public function ext2mime($ext_list){
			foreach($ext_list as $v){
				$mime_list[] = $this->ext2mime[$v];
			}
			return $mime_list;
		}
	}

