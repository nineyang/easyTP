<?php
	namespace framework\dao;
	interface I_DAO{
		public static function getsing($arr);
		public function query($sql);
		public function exec($sql);
		public function fetchall($sql);
		public function fetchrow($sql);
		public function fetchone($sql);
		public function fetchcolumn($sql);
		public function affectedrows();
		public function resultrows();
		public function lastinsertid();
		public function escape($data);
	}