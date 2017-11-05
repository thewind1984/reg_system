<?php	namespace Helper;

	class Config {
		
		private static $cached;
		
		/**
		 * Return any config data from 'config' file
		 * @param config string
		 * @param path string		delimiter = '/';
		 *
		 * return array
		 */
		public static function get($config, $path = null){
			$innerPath = md5($config . ($path ? $path : ''));
			
			if (isset(self::$cached[$innerPath]))
				return self::$cached[$innerPath];
			
			$file = ROOT_DIR . '/config/' . $config . '.php';
			
			if (!file_exists($file))
				return [];
			
			$data = include($file);
			
			if (!$path)
				return $data;
			
			$path = explode('/', trim($path));
			
			while (sizeOf($path)) {
				$element = array_shift($path);
				if (isset($data[$element]))
					$data = $data[$element];
				else
					return false;
			}
			
			self::$cached[$innerPath] = $data;
			
			return $data;
		}
		
	}
	
?>