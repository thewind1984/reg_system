<?php	namespace Helper;

	use \Helper\Session as Session;
	
	class Notifications {
		
		const
			LIFECOUNT_INFINITE = -1,
			LIFECOUNT_SINGLE = 1;
		
		const
			MESSAGE_INFO = 'info',
			MESSAGE_ERROR = 'error',
			MESSAGE_SUCCESS = 'success';
		
		const
			PAGE_ALL = '~';
		
		private static $list = [];
		private static $sessionKey = 'notifications_list';
		
		/**
		 * Inits notifications list
		 *
		 * return array
		 */
		public static function init(){
			$set = Session::get(static::$sessionKey);
			
			if (empty($set) || gettype($set) != 'array')
				$set = [];
			
			$currentUrlPage = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';
			
			foreach ($set as $k => $item) {
				//echo 'key: ' . $k . '; count: ' . $item['count'] . '; lifecount: ' . $item['lifecount'] . '<br>';
				
				if ($item['count'] < $item['lifecount']) {
					
					if ($item['page'] == self::PAGE_ALL || $item['page'] == $currentUrlPage) {
						
						static::$list[$item['name']] = [
							'value' => $item['value'],
							'type' => $item['type'],
						];
						
					}
					
				} else {
					unset($set[$k]);
				}
			}
			
			static::save($set);
			
			return $set;
		}
		
		/**
		 * Sets pair key => value into notifications list
		 */
		public static function set($name, $value, $type = self::MESSAGE_INFO, $lifecount = self::LIFECOUNT_SINGLE, $page = self::PAGE_ALL){
			$set = Session::get(static::$sessionKey);
			
			$set[$name] = [
				'name' => $name,
				'value' => $value,
				'type' => $type,
				'page' => $page,
				'lifecount' => $lifecount,
				'count' => 0,
			];
			
			static::$list = $set;
			
			static::save($set);
			
			return;
		}
		
		/**
		 * Returns value from notifications list by its name
		 * @param name string
		 *
		 * return mixed
		 */
		public static function get($name = null){
			$isExist = $name ? isset(static::$list[$name]) : true;
			$data = $isExist && $name ? static::$list[$name] : static::$list;
			
			if ($isExist) {
				if ($name)
					static::lifecount($name);
				else {
					foreach (array_keys($data) as $name)
						static::lifecount($name);
				}
			}
			
			return $data;
		}
		
		/**
		 * Increases count of requests to specific key from notifications list
		 * @param name string
		 * @param add integer
		 */
		private static function lifecount($name, $add = 1){
			$set = Session::get(static::$sessionKey);
			
			if (empty($set) || !isset($set[$name]))
				return;
			
			$set[$name]['count'] += $add;
			
			static::save($set);
			
			return;
		}
		
		/**
		 * Saves notifications list into session
		 * @param set array
		 */
		private static function save($set){
			if (empty($set))
				return;
			
			Session::set(static::$sessionKey, $set);
			//Session::finish();
		}
		
	}
	
?>