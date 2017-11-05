<?php	namespace Controller;

	use \Helper\Notifications;
	use \Helper\FormUser;
	use \Model\Db;
	use \Helper\Mailer;

	class Account extends Base {
		
		/**
		 * Main action of controller
		 */
		public function index(){
			if (!$this->user->isUserLogged())
				return $this->redirect('/');
			
			$user = $this->user->getCurrent();
			
			$this->assign('user', $user);
			
			$dataFormFields = FormUser::build(['email', 'name', 'surname', 'phone', 'birthday'], $this->user->getCurrent());
			$this->assign('data_form_fields', $dataFormFields);
			
			$passwordFormFields = FormUser::build(['password']);
			$this->assign('password_form_fields', $passwordFormFields);
		}
		
		/**
		 * Logs out of the user
		 */
		public function logout(){
			if (!$this->user->isUserLogged())
				return $this->redirect('/');
			
			$this->user->logout();
			return $this->reload();
		}
		
		/**
		 * Registers new user
		 */
		private function register(){
			$data = $this->getRequestData();
			
			if (!empty($data['user']['username']) && !empty($data['user']['email'])) {
				$existUsers = $this->user->findActiveUsersByData( array_intersect_key($data['user'], ['username' => '', 'email' => '']) );
				
				if (!empty($existUsers) && sizeOf($existUsers)) {
					Notifications::set('error', 'User already exists', Notifications::MESSAGE_ERROR);
					return $this->reload();
				}
			}
			
			$this->setUserData('/');
			
			/*$mailResult = Mailer::send($this->user->getField('email'), 'Account created', $this->view->fetch([
				'welcome' => 'Your account has been created!',
				'user' => $this->user->getCurrent(),
				'extra_data' => [
					'password' => $data['user']['password']
				],
			], 'Email/account.tpl'));*/
			
			return $this->redirect('/account');
		}
		
		/**
		 * Updates account of current user
		 */
		private function data(){
			$this->setUserData('/account');
			
			$mailResult = Mailer::send($this->user->getField('email'), 'Account updated', $this->view->fetch([
				'welcome' => 'Your account data has been updated!',
				'user' => $this->user->getCurrent(),
			], 'Email/account.tpl'));
			
			return $this->reload();
		}
		
		/**
		 *
		 */
		private function setUserData($failedUrlRedirect){
			$data = $this->getRequestData();
			
			$this->user->pushData($data['user']);
			
			$userSaveResult = $this->user->save();
			
			if ($userSaveResult === false) {
				Notifications::set('error', $this->user->getLastError(), Notifications::MESSAGE_ERROR);
				return $this->redirect($failedUrlRedirect);
			}
			
			// affected rows count could be 0 (if no one field was updated)
			// than do not show success message
			if ($userSaveResult)
				Notifications::set('success', 'Account has been successfully created!', Notifications::MESSAGE_SUCCESS);
			
			$this->user->storeSessionLoginData();
		}
		
		/**
		 * Updates password of current user
		 */
		private function password(){
			$data = $this->getRequestData();
			
			if (empty($data['current_password']) || !$this->user->passwordPassed($data['current_password'])) {
				Notifications::set('error', 'Current password is wrong', Notifications::MESSAGE_ERROR);
				return $this->reload();
			}
			
			$this->user->setPassword($data['user']['password']);
			
			$userSaveResult = $this->user->save();
			
			if ($userSaveResult === false) {
				Notifications::set('error', $this->user->getLastError(), Notifications::MESSAGE_ERROR);
				return $this->redirect('/account');
			}
			
			Notifications::set('success', 'Password has been successfully updated!', Notifications::MESSAGE_SUCCESS);
			
			$mailResult = Mailer::send($this->user->getField('email'), 'Password updated', $this->view->fetch([
				'welcome' => 'Your password has been updated!',
				'extra_data' => [
					'password' => $data['user']['password']
				],
			], 'Email/account.tpl'));
				
			return $this->redirect('/account');
		}
		
		/**
		 * Deleted current user's account
		 */
		private function delete(){
			$this->user->setDeleted();
			
			$userSaveResult = $this->user->save();
			
			if ($userSaveResult === false) {
				Notifications::set('error', $this->user->getLastError(), Notifications::MESSAGE_ERROR);
				return $this->redirect('/account');
			}
			
			$this->user->logout();
			
			Notifications::set('success', 'Account was deleted.', Notifications::MESSAGE_SUCCESS);
			
			return $this->redirect('/');
		}
		
		/**
		 * Magic processer for POST methods (register, data, password, delete)
		 * Breaks either method does not exist or POST request is absent
		 */
		public function __call($method, $args){
			if (method_exists($this, $method) && $this->isPost) {
				if (in_array($method, ['data', 'password', 'delete']) && !$this->user->isUserLogged())
					exit;
				
				return call_user_func_array([$this, $method], $args);
			} else {
				$this->redirect('/account');
			}
			
			exit;
		}
		
	}
	
?>