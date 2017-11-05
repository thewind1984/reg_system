<?php	namespace Helper;

	use \Helper\Config;

	class Mailer {
		
		/**
		 * Sends mail to 'email' address with params
		 * @param email string
		 * @param subject string
		 * @param template string
		 * @param data array
		 *
		 * return mixed
		 */
		public static function send($email, $subject, $body){
			require_once ROOT_DIR . '/libs/phpmailer/PHPMailerAutoload.php';
			
			$mailParams = Config::get('env', 'mail');
			
			$mailer = new \PHPMailer;
			$mailer->CharSet = 'utf-8';
			$mailer->isHTML(true);
			$mailer->From = $mailParams['from_email'];
			$mailer->FromName = $mailParams['from_name'];
			//$mailer->isSMTP();
			$mailer->Subject = $subject;
			//$mailer->Body = $body;
			$mailer->msgHTML($body);
			$mailer->addAddress($email);
			//$mailer->SMTPSecure = 'ssl'; //($_secure = trim(@$extra['secure']));
			//$mailer->Host = $config['smtp']['host'];
			//$mailer->Port = $config['smtp']['port']; //$_secure=='ssl' ? 465 : 587;
			//$mailer->SMTPAuth = true;
			//$mailer->Username = $config['smtp']['login'];
			//$mailer->Password = $config['smtp']['password'];
			
			$result = $mailer->Send();
			
			if ($result === false) {
				return $mailer->ErrorInfo;
			} else {
				return true;
			}
		}
		
	}
	
?>