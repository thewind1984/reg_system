<?php	namespace Model;

	class Base {
		
		/**
		 * Standart error container for all children
		 *
		 * @var error mixed
		 */
		protected static $error;
		
		/**
		 * Getter and cleaner of internal error pointer
		 *
		 * return mixed
		 */
		public static function getError(){
			$error = static::$error;
			static::$error = null;
			
			return $error;
		}
		
		/**
		 * Setter of error pointer
		 *
		 * return void;
		 */
		protected static function setError($error){
			static::$error = $error;
			
			return;
		}
		
		/**
		 * Converts numeric array to assoc array with 'field' as key
		 * @param data array
		 * @param field string
		 *
		 *
		 return array
		 */
		protected static function makeAssocArray($data, $field){
			$list = [];
			foreach ($data as $v)
				$list[$v[$field]] = $v;
			
			return $list;
		}
		
	}
	
?>