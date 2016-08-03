<?php
	namespace framework\dao;
	use framework\dao\I_DAO;
	use \PDO;
	class PDOMYSQL implements I_DAO{
		private $host;
		private $user;
		private $psd;
		private $dbname;
		private $port;
		private $charset;
		private static $instance;
		private $result;//存储结果集
		private $num;//存储增删改的数量
		private $pdo;
		public function __construct($arr){
			$this->initconfig($arr);
			$this->initpdo();
		}
		private function initconfig($arr){
			$this->host = $arr['host'];
			$this->user = $arr['user'];
			$this->psd = $arr['psd'];
			$this->dbname = $arr['dbname'];
			$this->port = $arr['port'];
			$this->charset = $arr['charset'];
		}
		private function initpdo(){
			$dsn = "mysql:dbname=$this->dbname;port=$this->port;charset=$this->charset";
			$this->pdo = new PDO($dsn , $this->user , $this->psd);
		}
		public static function getsing($arr){
			if(!self::$instance instanceof self){
				self::$instance = new self($arr);
			}
			return self::$instance;
		}
		public function query($sql){
			if('false' === ($res = $this->pdo->query($sql))){
				return false;
			}else{
				$this->result = $res;
				return $res;
			}
		}
		public function exec($sql){
			if('false' === ($res = $this->pdo->exec($sql))){
				return false;
			}else{
				$this->num = $res;
				return $res;
			}
		}
		public function fetchall($sql){
			$res = $this->query($sql);
			$row = $res->fetchAll(PDO::FETCH_ASSOC);
			$res->closeCursor();
			return $row;
		}
		public function fetchrow($sql){
			$res = $this->query($sql);
			$row = $res->fetch(PDO::FETCH_ASSOC);
			$res->closeCursor();
			return $row;
		}
		public function fetchone($sql){
			$res = $this->query($sql);
			$row = $res->fetchColumn();
			$res->closeCursor();
			return $row;
		}
		public function fetchcolumn($sql){
			$res = $this->query($sql);
			while($row = $res->fetchall()){
				$rows[] = $row;
			}
			$res->closeCursor();
			return $rows;
		}
		public function affectedrows(){
			$res = $this->num;
			$this->num = null;
			return $res;
		}
		public function resultrows(){
			return $this->result->rowCount();
		}
		public function lastinsertid(){
			return $this->pdo->lastInsertId();
		}
		public function escape($data){
			return $this->pdo->quote($data);
		}
	}