<?php	namespace Controller;

	use \Model\Db;
	use \Helper\Filter;
	use \Helper\Pagination;
	use \Model\User;
	use \Helper\FormUser;
	use \Helper\Notifications;

	class Admin extends Base {
		
		/**
		 * Main action of controller
		 */
		public function index(){
			$filter = [
				'from_date' => null,
				'to_date' => null,
			];
			$getFilter = isset($_GET['filter']) ? $_GET['filter'] : [];
			$filter = array_merge($filter, array_intersect_key($getFilter, $filter));
			
			if ($filter['from_date'] && $filter['to_date'] && strtotime($filter['from_date']) > strtotime($filter['to_date'])) {
				$filter['to_date'] = null;
			}
			
			$conditions = [];
			$dateRegisteredField = 'CAST(`date_registered` AS DATE)';
			
			if ($filter['from_date'] && $filter['to_date'])
				$conditions[$dateRegisteredField] = ['compare' => 'BETWEEN', 'value' => [$filter['from_date'], $filter['to_date']]];
			else if ($filter['from_date'])
				$conditions[$dateRegisteredField] = ['compare' => '>=', 'value' => $filter['from_date']];
			else if ($filter['to_date'])
				$conditions[$dateRegisteredField] = ['compare' => '<=', 'value' => $filter['to_date']];
			
			$usersCount = Db::getOne(User::TABLE_NAME, 'COUNT(*)', $conditions);
			
			$page = (int)Filter::get('page', 'GET', 1);
			if ($page < 1)
				$page = 1;
			
			$itemsPerPage = 10;
			$offset = ($page - 1) * $itemsPerPage;
			
			$list = Db::getList(User::TABLE_NAME, $conditions, 'AND', 'date_registered', 'DESC', $offset, $itemsPerPage);
			
			$pages = ($usersCount - ($rest = $usersCount % $itemsPerPage)) / $itemsPerPage + ($rest ? 1 : 0);
			$paginationLinks = Pagination::render($pages, $page);
			
			$this->assign('count', $usersCount);
			$this->assign('page', $page);
			$this->assign('filter', $filter);
			$this->assign('list', $list);
			$this->assign('pagination', $paginationLinks);
		}
		
		/**
		 * Edit user action
		 */
		public function user(){
			$this->user->overloadDefaultSettings(['status' => null]);
			
			$userId = isset($this->input[0]) && is_numeric($this->input[0]) && intval($this->input[0]) ? intval($this->input[0]) : null;
			$action = isset($this->input[1]) && in_array($this->input[1], ['delete', 'suspend', 'active']) ? $this->input[1] : null;
			
			// delete, suspend, activate user
			if ($action && $userId)
				return call_user_func_array([$this, 'makeUserAction'], [$userId, $action]);
			
			// create / update user account
			if ($this->isPost) {
				$data = $this->getRequestData();
				
				if (!empty($data['user']['username']) && !empty($data['user']['email'])) {
					$existUsers = $this->user->findActiveUsersByData( array_intersect_key($data['user'], ['username' => '', 'email' => '']), $userId );
					
					if (!empty($existUsers) && sizeOf($existUsers)) {
						Notifications::set('error', 'User already exists', Notifications::MESSAGE_ERROR);
						return $this->reload();
					}
				}
				
				$data['user']['id'] = $userId;
				
				$this->user->pushData($data['user'], true);
			
				$userSaveResult = $this->user->save();
				
				if ($userSaveResult === false) {
					Notifications::set('error', $this->user->getLastError(), Notifications::MESSAGE_ERROR);
					return $this->reload();
				}
				
				Notifications::set('success', 'Account (ID: ' . $this->user->getField('id') . ') has been successfully ' . ($userId ? 'updated' : 'created') . '!', Notifications::MESSAGE_SUCCESS);
				
				return $userId ? $this->reload() : $this->redirect('/admin');
			}
			
			// show user form
			
			$userData = $userId ? $this->user->getById($userId) : [];
			$this->assign('user_data', $userData);
			
			$formFields = FormUser::build(['username', 'email', 'name', 'surname', 'phone', 'birthday'], $userData);
			$this->assign('form_fields', $formFields);
		}
		
		/**
		 * Confirms and makes user action
		 * @param userId integer
		 * @param action strign
		 */
		private function makeUserAction($userId, $action){
			if ($this->isPost) {
				$userData = $this->user->getById($userId);
				$this->user->pushData($userData);
				
				switch ($action) {
					case 'delete':
						$this->user->setDeleted();
						break;
					
					case 'suspend':
						$this->user->setSuspended();
						break;
					
					case 'active':
						$this->user->setActive();
						break;
				}
				
				$userSaveResult = $this->user->save();
			
				if ($userSaveResult === false) {
					Notifications::set('error', $this->user->getLastError(), Notifications::MESSAGE_ERROR);
					return $this->redirect($_SERVER['HTTP_REFERER']);
				}
				
				Notifications::set('success', 'Account was updated.', Notifications::MESSAGE_SUCCESS);
			
				return $this->redirect('/admin');
			}
			
			$this->assign('action', $action);
			
			$this->template = 'form';
		}
		
	}
	
?>