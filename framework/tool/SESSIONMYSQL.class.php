<?php
	//SESSION工具类
	namespace framework\tool;
	use framework\dao\PDOMYSQL;
	class SESSIONMYSQL{
		public $_dao;
		public function __construct(){
			ini_set('session.save_handler' , 'user');
			session_set_save_handler(
				array($this , 'sessionstart'),	
				array($this , 'sessionend'),	
				array($this , 'sessionread'),	
				array($this , 'sessionwrite'),	
				array($this , 'sessiondelete'),	
				array($this , 'sessiongc')	
			);
			session_start();
		}
		public function sessionstart(){
			$arr = array(
				'host' => 'localhost',
				'user' => 'root',
				'psd' => 'root',
				'dbname' => 'ywbd',
				'port' => 3306,
				'charset' => 'utf8'
			);
			$this->_dao = PDOMYSQL::getsing($arr);
		}
		public function sessionend(){
			return true;
		}
		public function sessionread($session_id){
			$sql = "select * from session where session_id = '$session_id'";
			$res = $this->_dao->fetchrow($sql);
			return $res ? $res['session_id'] : '';
		}
		public function sessionwrite($session_id , $session_content){
			$sql = "replace into session values('$session_id' , '$session_content')";
			$this->_dao->query($sql);
		}
		public function sessiondelete($session_id){
			$sql = "delete from session where session_id = '$session_id'";
			$this->_dao->query($sql);
		}
		public function sessiongc($max){
			$sql = "delete from session where last_time < unix_timestamp() - '$max'";
			$this->_dao->query($sql);
		}
	}