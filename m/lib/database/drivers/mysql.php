<?php

namespace m\database\drivers {
	use \m as m;
	
	class mysql extends m\database\driver {

		private $dbp = null;

		public function connect() {

			$this->dbp = mysql_connect(
				$this->config->hostname,
				$this->config->username,
				$this->config->password
			);
			
			if(!$this->dbp)
			$this->throwError('unable to connect');
			
			if(!mysql_select_db($this->config->database,$this->dbp))
			$this->throwError("unable to select db {$this->database}");
		
			return true;
		}		
		
		public function disconnect() {
			if(is_resource($this->dbp)) {
				mysql_close($this->dbp);
			}
			
			$this->dbp = null;
			return;
		}
		
		public function escape($input) {
			return mysql_real_escape_string($input,$this->dbp);
		}
		
		public function query($sql) {
			$result = mysql_query($sql);
			if(!$result) return false;
			
			$query = new mysql\query($sql,$result);
			return $query;			
		}

	}
	
}

namespace m\database\drivers\mysql {
	use \m as m;
	
	class query extends m\database\query {
	
		public $sql;
		private $result;
	
		public function __construct($sql,$result) {
			if(func_num_args() != 2)
				throw new Exception('invalid parametre count');
			else list($this->sql,$this->result) = func_get_args();
									
			return;
		}
		
		public function free() {
			if(!$this->result) return false;
			
			mysql_free_result($this->result);
			$this->result = null;
			
			return true;
		}
		
		public function next() {
			if(!$this->result) return false;
			
			$object = mysql_fetch_object($this->result);
			if(!$object) $this->free();
			
			return $object;			
		}

	}
		
}

?>