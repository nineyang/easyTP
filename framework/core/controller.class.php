<?php
	namespace framework\core;
	class Controller{
		protected $view;
		protected $error_info = [];
		public function __construct(){
			$this->initView();
			$this->initHead();
		}
		public function getErrorInfo($sep = null){
			if(is_null($sep)){
				return $this->error_info;
			}else{
				$str = '';
				foreach($this->error_info as $k=>$v){
					$str .= $k . ':' . $v . $sep;
				}
				return $str;
			}
		}
		private function initView(){
			$this->view = new \Smarty;
			$this->view->template_dir = MODULE_PATH . 'view/';
			$this->view->compile_dir = MODULE_PATH . 'runtime/';
		}
		private function initHead(){
			header('content-type:text/html');
		}
		protected function jumpNow($url){
			header('location:' . $url);
			die;
		}
		protected function jumpLater($url , $echo , $time=2){
			header("Refresh:$time;url=$url");
			echo $echo;
			die;
		}
		//分页的方法
		public function paging($page , $pagemax , $num){
			$page_arr = [];
			$left = max(1 , $page - ($num-1)/2);
			$right = min($pagemax , $left+($num-1));
			$left = max(1 , $right-($num -1));
			for($i = $left ; $i <= $right ; ++$i){
				$page_arr[$i] = $i;
			}
			return $page_arr;
		}
	}
