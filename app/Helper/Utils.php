<?php	namespace Helper;

	class Utils {
		
		/**
		 * Redirects to another URL
		 * Works without any additional headers like HTTP_RESPONSE_CODE
		 * @param url string
		 */
		public static function redirect($url){
			header("Location: " . $url);
			exit;
		}
		
		/**
		 * Reloads currently opened location
		 */
		public static function reload(){
			self::redirect(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/');
			exit;
		}
		
	}
	
?>