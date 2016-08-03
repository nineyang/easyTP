<?php
	namespace framework\core;
	class FACTORY {
		public static function M($model_name){
			static $model_list = [];
			$model_name = $model_name . (('model' == substr($model_name , -5)) ? '' : 'model');
			if(!isset($model_list[$model_name])){
				$model_name = '\\' . MODULE . '\\module\\' . $model_name;
				$model_list[$model_name] = new $model_name;
			}
			return $model_list[$model_name];
		}
		//base是指mvc的三个内容 如/front/content/index
		//lim_arr是限制的参数
		public static function U($base , $lim_arr=[]){
			//开头
			$url = ROOT . $base;
			foreach($lim_arr as $k=>$v){
				$url .= '/' . $k . '/' . $v;
			}
			//结尾
			$url .= $GLOBALS['config']['static_fix'];
			return $url;
		}
		//时间自动生成多少分钟前
		public static function T($t){
			if((time()-$t) < 60 ){
				return '刚刚';
			}elseif((time()-$t) > 60 && (time()-$t) < 120){
				return '一分钟前';
			}elseif((time()-$t) > 120 && (time()-$t) < 300){
				return '十分钟内';
			}else{
				return date('Y年m月d日 H:i:s' , $t);
			}
		}
	}