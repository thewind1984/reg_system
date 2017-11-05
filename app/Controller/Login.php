<?php	namespace Controller;

	use \Helper\Notifications;
	use \Helper\FormUser;

	class Login extends Base {
		
		/**
		 * Main action of controller
		 */
		public function index(){
			if ($this->user->isUserLogged())
				return $this->redirect('/account');
			
			if ($this->isPost) {
				$data = $this->getRequestData();
				
				$user_data = $this->user->getByField('username', $data['user']['username']);
				
				if (empty($user_data)) {
					Notifications::set('empty_user_data', 'User not found', Notifications::MESSAGE_ERROR);
					return $this->reload();
				}
				
				$this->user->pushData($user_data);
				
				if (!$this->user->checkPassword($data['user']['password'])) {
					Notifications::set('wrong_user_password', 'Wrong user password', Notifications::MESSAGE_ERROR);
					return $this->reload();
				}
				
				$this->user->storeSessionLoginData();
				
				return $this->redirect('/account');
			}
			
			$formFields = FormUser::build(['username', 'password']);
			$this->assign('form_fields', $formFields);
		}
		
	}
	
?>