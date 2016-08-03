<?php
	namespace framework\core;
	class Framework {
		public function __construct(){
			//初始化
			$this->initPath();
			//初始化路由
			$this->initPathInfo();
			$GLOBALS['config'] = $this->getFrameConfig();
			$GLOBALS['config'] = array_merge($GLOBALS['config'] , $this->getCommonConfig());
			//引入配置
			$this->initModule();
			$GLOBALS['config'] = array_merge($GLOBALS['config'] , $this->getModuleConfig());
			//自动加载
			$this->initAutoLoad();
			//new \framework\tool\SESSIONMYSQL;
			$this->initCA();
			//路由分发
			$this->dispath();
		}
		private function initPath(){
			define('ROOT_PATH' , getcwd() . '/');
			defined('FRAMEWORK_PATH') || define('FRAMEWORK_PATH' , ROOT_PATH . 'framework/');
			defined('APPLICATION_PATH') || define('APPLICATION_PATH' , ROOT_PATH . 'application/');
			defined('UPLOAD_PATH') || define('UPLOAD_PATH' , ROOT_PATH . 'upload/');
			defined('WEB_PATH') || define('WEB_PATH' , ROOT_PATH . 'web/');
			$path = str_replace(basename($_SERVER['SCRIPT_NAME']) , '' , $_SERVER['SCRIPT_NAME']);
			define('ROOT' , $path);
		}
		private function initPathInfo(){
			$pathinfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '' ;
			if($pathinfo == ''){
				return;
			}
			//去掉后缀
			$pathinfo = explode( '/' , substr(str_replace(strrchr($pathinfo , '.') , '' , $pathinfo) , 1));
			$leng = count($pathinfo);
			if($leng == 1){
				$_GET['m'] = $pathinfo[0];
			}elseif($leng == 2){
				$_GET['m'] = $pathinfo[0];
				$_GET['c'] = $pathinfo[1];
			}elseif($leng == 3){
				$_GET['m'] = $pathinfo[0];
				$_GET['c'] = $pathinfo[1];
				$_GET['a'] = $pathinfo[2];
			}else{
				$_GET['m'] = $pathinfo[0];
				$_GET['c'] = $pathinfo[1];
				$_GET['a'] = $pathinfo[2];
				for($i=3 ; $i < $leng ; $i+=2){
					$_GET[$pathinfo[$i]] = $pathinfo[$i+1];
				}
			}
		}
		private function getFrameConfig(){
			return require FRAMEWORK_PATH . 'config/config.php';
		}
		private function getCommonConfig(){
			$file = APPLICATION_PATH . 'common/config/config.php';
			if(file_exists($file)){
				return require $file;
			}else{
				return [];
			}
		}
		private function initModule(){
			$default_module = $GLOBALS['config']['default_module'];
			define('MODULE' , isset($_GET['m']) ? $_GET['m'] : $default_module);
			define('MODULE_PATH' , APPLICATION_PATH . MODULE . '/');
		}
		private function getModuleConfig(){
			$file = MODULE_PATH . 'config/config.php';
			if(file_exists($file)){
				return require $file;
			}else{
				return [];
			}
		}
		private function initAutoLoad(){
			spl_autoload_register(array($this , 'userAutoLoad'));
		}
		public function userAutoLoad($name){
			$list['Smarty'] = FRAMEWORK_PATH . 'ventor/smarty/libs/Smarty.class.php';
			if(isset($list[$name])){
				return require $list[$name];
			}
			$split = explode('\\' , $name);
			if('framework' == $split[0]){
				$f_path = FRAMEWORK_PATH;
			}else{
				$f_path = MODULE_PATH;
			}
			unset($split[0]);
			$s_path = implode('/' , $split);
			$base = basename($name);
			if('I_' == substr($base , 0 ,2)){
				$l_path = '.interface.php';
			}else{
				$l_path = '.class.php';
			}
			$path = $f_path . $s_path . $l_path;
			//echo $path . '<br>';
			if(file_exists($path)){
				require $path;
			}
		}
		private function initCA(){
			$default_controller = $GLOBALS['config']['default_controller'];
			define('CONTROLLER' , isset($_GET['c']) ? $_GET['c'] : $default_controller);
			$default_action = $GLOBALS['config']['default_action'];
			define('ACTION' , isset($_GET['a']) ? $_GET['a'] : $default_action);
		}
		private function dispath(){
			$controller_name = CONTROLLER . (('controller' == substr(CONTROLLER , -10)) ? '' : 'controller' );
			$controller_name = '\\' . MODULE . '\\controller\\' . $controller_name;
			$action = ACTION . (('action' == substr(ACTION , -6)) ? '' : 'action' );
			$controller = new $controller_name;
			$controller -> $action();
		}
	}