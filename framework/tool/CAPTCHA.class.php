<?php
	namespace framework\tool;
	class CAPTCHA{
		//用户可以设置雪花的数量,条纹的数量,验证码的个数
		//设置验证码生成的高宽
		//控制哪种字体
		private $snow_num = 30;
		private $line_num = 5;
		private $code_num = 4;
		private $str = "QAZWSXEDCRFVTGBYHNUJMIKOLPqazwsxedcrfvtgbyhnujmikolp1234567890";
		private $code = '';//用来接收设置的验证码
		private $font_style = WEB_PATH . MODULE . '/fonts/SNAP.ttf';
		public function getCode(){
			return $this->code;
		}
		public function setsnow_num($num){
			if($num>0){
				$this->snow_num = $num;
			}
		}
		public function setline_num($num){
			if($num>0){
				$this->line_num = $num;
			}
		}
		public function setcode_num($num){
			if($num>0){
				$this->code_num = $num;
			}
		}
		public function setfont_style($font){
			if(file_exists($font)){
				$this->font_style = $font;
			}
		}
		public function getCaptcha($w , $h){
			//先画图
			$image = imagecreatetruecolor($w , $h);
			$bg_color = imagecolorallocate($image , mt_rand(150,250),mt_rand(150,250),mt_rand(150,250));
			imagefill($image , 0 , 0 , $bg_color);
			//画雪花
			for($i = 1 ; $i <= $this->snow_num ; ++ $i){
				$snow_color = imagecolorallocate($image , mt_rand(150,250) , mt_rand(150,250) , mt_rand(150,250));
				imagestring($image , mt_rand(1 , 3) , mt_rand(0,$w) , mt_rand(0 , $h) , "*" , $snow_color );
			}
			//画横线
			for($i = 1 ; $i <= $this->line_num ; ++ $i){
				$line_color = imagecolorallocate($image , mt_rand(150,250) , mt_rand(150,250) , mt_rand(150,250));
				imageline($image , mt_rand(0 , $w) , mt_rand(0, $h) , mt_rand(0 , $w) , mt_rand(0, $h) , $line_color);
			}
			//填写验证码
			for($i = 0 ; $i < $this->code_num ; ++ $i){
				$code = substr(str_shuffle($this->str) , 0 , 1);
				$code_color = imagecolorallocate($image , mt_rand(50 , 150) , mt_rand(50 , 150) , mt_rand(50 , 150));
				$font_size = $w/$this->code_num - 5;
				$font_x = $i*$font_size + 5;
				$font_y = ($h + $font_size)/2 + mt_rand(-3 , 3);
				imagettftext($image , $font_size , mt_rand(-30 , 30) , $font_x , $font_y , $code_color , $this->font_style , $code);
				$this->code .= $code;
			}
			//存入session
			@session_start();
			$_SESSION['code'] = $this->code;
			//画图
			header('content-type:image/png');
			
			imagepng($image);
			imagedestroy($image);
		}
	}