<?php	namespace Helper;

	use \Helper\Config;

	class Session {
		
		/**
		 * Internal connection pointer
		 * @var id object
		 */
		static private $id;
		
		/**
		 * Start new session
		 * @param id string		session's id
		 */
		private static function start($id = null){
			$sessionDir = Config::get('env', 'session_dir');
			if (empty($sessionDir) || !is_dir($sessionDir))
				$sessionDir = ROOT_DIR . '/cache/session';
			
			ini_set('session.save_path', $sessionDir);
			ini_set('session.name', 'REG_SITE');
			
			if ($id !== null)
				session_id($id);
			
			session_start();
			self::$id = session_id();
		}
		
		/**
		 * Returns id of currently opened session
		 */
		public static function getID(){
			if (!self::$id) {
				//self::start($id);
				return null;
			}
			
			return self::$id;
		}
		
		/**
		 * Close current session and start new by its id
		 * @param id string			session's id
		 */
		public static function restart($id){
			self::finish();
			self::start($id);
		}
		
		/**
		 * Returns variable from session by its key
		 * @param a mixed			key of variable
		 * @param id string			session_id | null for current
		 */
		public static function get($a = null, $id = null) {
			if (!self::$id) {
				self::start($id);
				//return null;
			} else if ($id) {
				$current_id = self::getID();
				self::restart($id);
			}
			
			$value = $a === null ? $_SESSION : (!empty($_SESSION[$a]) ? $_SESSION[$a] : null);
			
			if (!empty($current_id))
				self::restart($current_id);
			
			return $value;
		}
		
		/**
		 * Sets variable into session by its key
		 * @param a mixed			key of variable
		 * @param v mixed			value of variable
		 * @param id string			session_id | null for current
		 */
		public static function set($a, $v, $id = null) {
			if (!self::$id) {
				self::start($id);
			} else if ($id) {
				$current_id = self::getID();
				self::restart($id);
			}
			
			$_SESSION[$a] = $v;
			
			if (!empty($current_id))
				self::restart($current_id);
		}
		
		/**
		 * Removes variable from session by its key
		 * @param a mixed			key of variable
		 * @param id string			session_id | null for current
		 */
		public static function delete($a, $id = null){
			if (!self::$id) {
				self::start($id);
				//return null;
			} else if ($id) {
				$current_id = self::getID();
				self::restart($id);
			}
			
			if (isset($_SESSION[$a]))
				unset($_SESSION[$a]);
			
			if (!empty($current_id))
				self::restart($current_id);
		}
		
		/**
		 * Save and close currently opened session
		 */
		public static function finish(){
			if (!self::$id)
				return;
			
			session_write_close();
		}
		
	}
	
?>