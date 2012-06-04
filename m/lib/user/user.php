<?php

namespace m {
	use \m as m;

	class user extends m\object {
		static $current = null;

		static $PropertyMap = array(
			'u_id'       => 'ID',
			'u_name'     => 'Username',
			'u_email'    => 'Email',
			'u_phash'    => 'PHash',
			'u_psand'    => 'PSand',
			'u_fname'    => 'FirstName',
			'u_lname'    => 'LastName'
		);

		private $Database;

		public function __construct($raw,$opt=null) {
			$opt = new m\object($opt,array(
				'KeepHashes' => false,
				'Database'   => null
			));

			// allow the object to build itself up from the raw database data
			// provided via menagerie object construction.
			parent::__construct($raw);

			// support for custom or sharded databases.
			$this->Database = $opt->Database;

			// as a preventive security measure to help prevent a developer from
			// var_dumping something stupid, hashes are stripped from the object
			// unless you ask for them to be kept. basically, only the login
			// system really should be doing that.
			if(!$opt->KeepHashes) {
				foreach(array('PHash','PSand') as $hashkey) {
					if(property_exists($this,$hashkey))
					unset($this->{$hashkey});
				}
				unset($hashkey);
			}

			return;
		}

		public function sessionUpdate() {
			if(!property_exists($this,'PHash') || !$this->PHash)
			throw new \Exception('Unable to update session without Hash data in this object. (KeepHashes?)');

			$cdat = sprintf(
				'%d:%s',
				$this->ID,
				hash('sha512',"{$this->PHash}{$this->PSand}")
			);

			//. update the login cookie.
			setcookie('m_user',$cdat,(time() + (86400*30)),'/');
			return;
		}

		static function getFromSession($opt=null) {
			$opt = new m\object($opt,array(
				'Database' => option::get('m-user-database') or null
			));
			$opt->KeepHashes = true;

			// quit if no session data.
			$cookie = new m\request\input('cookie');
			if(!$cookie->m_user) return false;

			// quit if invalid session data format.
			if(strpos($cookie->m_user,':') === false) return false;
			list($uid,$hash) = explode(':',$cookie->m_user);

			// quit if invalid user.
			$user = self::get((int)$uid,$opt);
			if(!$user) return false;

			// quit if invalid data.
			if($hash != hash('sha512',"{$user->PHash}{$user->PSand}"))
			return false;

			// looks like a valid login.
			return $user;
		}

		static function get($what,$opt=null) {
			$opt = new m\object($opt,array(
				'KeepHashes' => false,
				'Database'   => option::get('m-user-database') or null
			));

			$where = false;
			if(strpos($what,'@')!==false) {
				// search by email address.
				$where = 'u_email LIKE "%s"';
			} else if(is_string($what)) {
				// search by username.
				$where = 'u_name LIKE "%s"';
			} else if(is_int($what)) {
				// search by unique id.
				$where = 'u_id=%d';
			}

			//. if no valid data type then quit.
			if(!$where) return false;

			//. find the record.
			//. note that i built the query here expecting you to pass probably
			//. dirty data, so dumping a username from straight post data will
			//. still end up being cleaned up by the database library to not
			//. can has injection.
			$db = new m\database($opt->Database);
			$who = $db->queryf(
				"SELECT * FROM m_users WHERE {$where} LIMIT 1;",
				$what
			)->next();

			if(!$who) return false;
			else return new static($who,array(
				'Database' => $opt->Database,
				'KeepHashes' => $opt->KeepHashes
			));
		}
	}

}

namespace {

	m\ki::queue('m-setup',function(){
		m\user::$current = m\user::getFromSession();
		return;
	});

}

?>