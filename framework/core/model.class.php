<?php
	namespace framework\core;
	use framework\dao\PDOMYSQL;
	class Model{
		protected $pdo;
		//实际表名
		protected $table;
		//逻辑表名
		protected $login_table;
		//统计表结构
		protected $table_line = [];
		protected $error_info = [];
		public function __construct(){
			//初始化表名和表相关结构
			$this->initPdo();
			$this->initLogin_table();
			$this->initTable();
			$this->initTable_line();
		}
		private function initPdo(){
			$conf['host'] = $GLOBALS['config']['host'];
			$conf['user'] = $GLOBALS['config']['user'];
			$conf['psd']= $GLOBALS['config']['psd'];
			$conf['dbname'] = $GLOBALS['config']['dbname'];
			$conf['charset'] = $GLOBALS['config']['charset'];
			$conf['port'] = $GLOBALS['config']['port'];
			$this->pdo = PDOMYSQL::getsing($conf);
		}
		private function initLogin_table(){
			$login = substr(basename(get_class($this)) , 0 , -5);
			$pattern = '/(?<=[a-z])(?=[A-Z])/';
			$this->login_table = strtolower(preg_replace($pattern , '_' , $login));
		}
		private function initTable(){
			$this->table = '`' . $GLOBALS['config']['pre_table'] . $this->login_table . '`';
		}
		private function initTable_line(){
			$sql = "DESC $this->table";
			$res = $this->pdo->fetchall($sql);
			foreach($res as $row){
				$this->table_line[] = $row['Field'];
				if('PRI' == $row['Key']){
					$this->table_line['pk'] = $row['Field'];
				}
			}
		}
		//获取错误信息的方法
		public function getErrorInfo($sep = null){
			if(is_null($sep)){
				return $this->error_info;
			}else{
				$str = '';
				foreach($this->error_info as $k => $v){
					$str .= $k . ':' . $v . $sep;
				}
				return $str;
			}
		}
		//增
		public function insert($data){
			//此处只做逻辑处理,不做判断
			$k_arr = array_map(function($v){
				return '`' . $v . '`';
			} , array_keys($data));
			$v_arr = array_map(function($v){
				return $this->pdo->escape($v);
			} , array_values($data));
			$k_str = implode( ',' , $k_arr);
			$v_str = implode( ',' , $v_arr);
			$sql = "INSERT INTO $this->table ( $k_str ) values ( $v_str )";
			if($this->pdo->exec($sql)){
				return $this->pdo->lastinsertid();
			}else{
				return false;
			}
		}
		//删
		public function delete($id){
			//是否还有子类
			if($this->hasChild($id)){
				$this->error_info['id'] = '还有子类,不能删除';
			}
			if(!empty($this->error_info)){
				return false;
			}
			$sql = "DELETE FROM $this->table WHERE `{$this->table_line['pk']}` = '$id'";
			return $this->pdo->exec($sql);
		}
		private function hasChild($id){
			$sql = "SELECT 1 FROM $this->table WHERE `p_id` = '$id' LIMIT 1";
			return $this->pdo->fetchrow($sql);
		}
		//改
		public function update($data , $where = null){
			//更新的条件,如果没有设置where,就看data里面是否有主键,如果没有的话就返回false
			if(!is_null($where)){
				$where_str = " WHERE $where";
			}elseif(isset($data[$this->table_line['pk']])){
				$where_str = " WHERE `{$this->table_line['pk']}` = " . $this->pdo->escape($data[$this->table_line['pk']]);
			}else{
				return false;
			}
			foreach($data as $k => $v){
				$set_arr[] = " `$k` = " . $this->pdo->escape($v);
			}
			$set_str = implode(',' , $set_arr);
			$sql = "UPDATE $this->table SET " . $set_str . $where_str;
			return $this->pdo->exec($sql);
		}
		//查
		public function selectAll($filter = []){
			
			if(!isset($filter['filed'])){
				$filter['filed'] = " *";
			}
			$str = '';
			if(isset($filter['where'])){
				$str .= " WHERE {$filter['where']}";
			}
			if(isset($filter['group'])){
				$str .= " GROUP BY {$filter['group']}";
			}
			if(isset($filter['order'])){
				$str .= " ORDER BY {$filter['order']}";
			}
			if(isset($filter['limit'])){
				$str .= " LIMIT {$filter['limit']}";
			}
			$sql = " SELECT {$filter['filed']} FROM $this->table $str";
			//echo $sql;
			return $this->pdo->fetchall($sql);
		}
		public function selectOne( $id , $filter = []){
			if(!isset($filter['filed'])){
				$filter['filed'] = " *";
			}
			$sql = "SELECT {$filter['filed']} FROM $this->table WHERE `{$this->table_line['pk']}` = " . $this->pdo->escape($id);
			return $this->pdo->fetchrow($sql);
		}
		//通过其他单个条件获取信息
		//$filed 列名 $cond条件
		public function getByOther($field , $cond){
			$sql = "SELECT * FROM $this->table WHERE `$field` = " . $this->pdo->escape($cond);
			return $this->pdo->fetchrow($sql);
		}
	}