<?php	namespace Model;

	use \Exception;
	use \Helper\Notifications;
	use \Helper\Utils;

	class CustomException extends Exception {
		
		/**
		 *
		 */
		public function __construct($message, $code = 0, Exception $previous = null){
			parent::__construct($message, $code, $previous);
		}
		
		/**
		 *
		 */
		public function addNotificationAndReload($url = null){
			Notifications::set('error', $this->getCode() . ': ' . $this->getMessage(), Notifications::MESSAGE_ERROR);
			return Utils::redirect('/');
		}
		
	}
	
?>