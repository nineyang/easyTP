<?php
	namespace framework\tool;
	class REQUEST{
		//用户可以设置路径
		private $url = '';
		private $return = 'true';
		private $header = 'false';
		private $useragent = 'hh';
		public function setuseragent($useragent){
			$this->useragent = $useragent;
		}
		public function seturl($url){
			$this->url = $url;
		}
		public function setreturn($return){
			$this->return = $return;
		}
		public function setheader($header){
			$this->header = $header;
		}
		public function getRequest(){
			$curl = curl_init();
			curl_setopt($curl , CURLOPT_URL , $this->url);
			curl_setopt($curl , CURLOPT_RETURNTRANSFER , $this->return);
			curl_setopt($curl , CURLOPT_HEADER , $this->header);
			curl_setopt($curl , CURLOPT_USERAGENT , $this->useragent);
			$res = curl_exec($curl);
			curl_close($curl);
			return $res;
		}
		
	}