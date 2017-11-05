<?php	namespace Model;

	use \Model\Db;
	use \Helper\Session;

	class User extends Base {
		
		const
			TABLE_NAME = 'user',
			
			USER_STATUS_ACTIVE = 'ACTIVE',
			USER_STATUS_SUSPENDED = 'SUSPEND',
			USER_STATUS_DELETED = 'DELETED';
		
		private static $userStatuses = [
			self::USER_STATUS_ACTIVE,
			self::USER_STATUS_SUSPENDED,
			self::USER_STATUS_DELETED
		];
		
		/**
		 * Model fields with validation params
		 * Possible params: type, auto, empty, mandatory, length, min_length, default, validate, validate_args, values, regexp
		 * @var fields array
		 */
		protected $fields = [
			'id' => ['type' => 'hidden', 'auto' => true],
			'username' => ['length' => 20, 'mandatory' => true, 'min_length' => 3, 'regexp' => '/^[a-z0-9]{3,20}$/i'],
			'password' => ['type' => 'password', 'length' => 100, 'mandatory' => true, 'min_length' => 6, 'regexp' => '/^.{6,100}$/'],
			'email' => ['type' => 'email', 'length' => 255, 'mandatory' => true, 'validate' => 'filter_var', 'validate_args' => [FILTER_VALIDATE_EMAIL]],
			'name' => ['length' => 40, 'empty' => true, 'default' => null, 'regexp' => '/^[a-z]{0,40}$/i'],
			'surname' => ['length' => 40, 'empty' => true, 'default' => null, 'regexp' => '/^[a-z]{0,40}$/i'],
			'phone' => ['type' => 'tel', 'validate' => 'is_numeric', 'empty' => true, 'default' => null, 'regexp' => '/^[0-9]{5,30}$/i'],
			'birthday' => ['type' => 'date', 'validate' => 'checkBirthday', 'empty' => true, 'default' => null],
			'status' => ['type' => 'select', 'values' => [], 'hide_form' => true],
		];
		
		private $user;
		
		private $sault = '72%}|i';
		
		private $isLogged = false;
		
		private $defaultUserSettings = [];
		
		/**
		 * Constructor
		 */
		public function __construct($id = null){
			if (!empty($id))
				$this->initById($id);
			
			$this->fields['status']['values'] = self::$userStatuses;
			$this->defaultUserSettings['status'] = self::USER_STATUS_ACTIVE;
		}
		
		/**
		 * Finds user by its id
		 * @param id integer
		 *
		 * return array
		 */
		public function getById($id){
			return $this->getByField('id', $id);
		}
		
		/**
		 * Returns current user data
		 *
		 * return array
		 */
		public function getCurrent(){
			return $this->user;
		}
		
		/**
		 * Overloads default settings of users (i.e. status=ACTIVE)
		 * @param settings array
		 */
		public function overloadDefaultSettings($settings = []){
			foreach (array_intersect_key($settings, $this->defaultUserSettings) as $field => $fieldValue)
				$this->defaultUserSettings[$field] = $fieldValue;
			
			$this->defaultUserSettings = array_filter($this->defaultUserSettings, function($v){ return !empty($v); });
		}
		
		/**
		 * Finds user by pair field => value
		 * @param fieldName string
		 * @param fieldValue string
		 *
		 * return array
		 */
		public function getByField($fieldName, $fieldValue){
			return Db::getRow(self::TABLE_NAME, array_merge(
				[$fieldName => $fieldValue],
				$this->defaultUserSettings
			));
		}
		
		/**
		 * Finds user by its id and stores it into internal variable
		 * @param id integer
		 */
		public function initById($id){
			$row = $this->getById($id);
			$this->pushData($row);
			
			return;
		}
		
		/**
		 * Stores array data into internal scope
		 * @param data array
		 * @param cleanCurrentData boolean
		 */
		public function pushData($data, $cleanCurrentData = false){
			$this->user = array_merge($cleanCurrentData ? [] : (array)$this->user, $this->processData($data));
			
			return;
		}
		
		/**
		 * Returns any field from current user
		 * @param fieldName string
		 *
		 * return mixed;
		 */
		public function getField($fieldName){
			return isset($this->user[$fieldName]) ? $this->user[$fieldName] : null;
		}
		
		/**
		 * Saves internal user data into DB
		 *
		 * return boolean | integer
		 */
		public function save(){
			if (gettype($this->user) != 'array') {
				parent::setError('User is not pushed');
				return false;
			}
			
			if (!$this->validate()) {
				return false;
			}
			
			$savingResult = null;
			
			if (empty($this->user['id'])) {
				$savingResult = Db::insertRow(self::TABLE_NAME, array_merge($this->user, [
					'id' => null,
					'password' => $this->getPasswordHash($this->user['password']),
				]));
				
				$this->user['id'] = $savingResult;
			} else {
				$savingResult = Db::updateRow(self::TABLE_NAME, $this->user, ['id' => $this->user['id']]);
			}
			
			return $savingResult;
		}
		
		/**
		 * Returns if plain password is equaled to current user's password
		 * @param plainPassword string
		 *
		 * return boolean
		 */
		public function checkPassword($plainPassword){
			return !empty($this->user['id']) && !empty($this->user['password']) && $this->user['password'] === $this->getPasswordHash($plainPassword);
		}
		
		/**
		 * Generates secret hash of current user
		 *
		 * return string
		 */
		public function generateHash(){
			if (empty($this->user['id']))
				return '';
			
			return base64_encode(md5($this->user['id'] . '_' . $this->user['username'] . '_' . $this->sault));
		}
		
		/**
		 * Tries to authorize user from session
		 */
		public function loginFromSession(){
			$userSessionId = Session::get('user_logged_id');
			$userSessionHash = Session::get('user_logged_hash');
			
			if (empty($userSessionId) || empty($userSessionHash))
				return;
			
			$user_data = $this->getById($userSessionId);
			$this->pushData($user_data);
			
			if ($userSessionHash === $this->generateHash()) {
				$this->isLogged = true;
			} else {
				$this->user = null;
			}
			
			return;
		}
		
		/**
		 * Stores current user encoded data into session
		 */
		public function storeSessionLoginData(){
			Session::set('user_logged_id', $this->getField('id'));
			Session::set('user_logged_hash', $this->generateHash());
			
			return;
		}
		
		/**
		 * Returns if user is logged
		 *
		 * return boolean
		 */
		public function isUserLogged(){
			return $this->isLogged;
		}
		
		/**
		 * Returns user last error
		 *
		 * return mixed
		 */
		public function getLastError(){
			return parent::getError();
		}
		
		/**
		 * Removes session user data
		 */
		public function logout(){
			Session::delete('user_logged_id');
			Session::delete('user_logged_hash');
			
			return;
		}
		
		/**
		 * Returns internal fields
		 *
		 * return array
		 */
		public function getFields(){
			//return $this->fields;
		}
		
		/**
		 * Returns either entered password is equaled to current user's password or not
		 *
		 * return boolean
		 */
		public function passwordPassed($plainPassword){
			return $this->user['password'] === $this->getPasswordHash($plainPassword);
		}
		
		/**
		 * Generates hash from plain password and sets it as password
		 */
		public function setPassword($plainPassword){
			$this->user['password'] = !empty($this->user['id']) ? $this->getPasswordHash($plainPassword) : $plainPassword;
			
			return;
		}
		
		/**
		 * Sets to current user deleted flags
		 */
		public function setDeleted(){
			$this->user['status'] = self::USER_STATUS_DELETED;
			$this->user['date_deleted'] = date('Y-m-d H:i:s');
		}
		
		/**
		 * Sets to current user suspend flag
		 */
		public function setSuspended(){
			$this->user['status'] = self::USER_STATUS_SUSPENDED;
		}
		
		/**
		 * Sets to current user active flag
		 */
		public function setActive(){
			$this->user['status'] = self::USER_STATUS_ACTIVE;
		}
		
		/**
		 * Finds not DELETED users by params
		 * @param data array
		 * @param exceptId integer
		 *
		 * return array
		 */
		public function findActiveUsersByData($data, $exceptId = null){
			$dataQuery = !empty($data) ? implode(' OR ', array_map(function($v){ return "`" . $v . "`=?"; }, array_keys($data))) : '';
			
			$list = Db::runQuery(
				"SELECT `id` FROM `" . self::TABLE_NAME . "` WHERE `status`!=?" . ($exceptId ? " AND `id`!=?" : "") . ($dataQuery ? " AND (" . $dataQuery . ")" : ""),
				array_merge(
					[self::USER_STATUS_DELETED],
					$exceptId ? [$exceptId] : [],
					$dataQuery ? array_values($data) : []
				),
				true
			);
			
			return $list;
		}
		
		/**
		 * Returns hash of password, generated from plain text
		 * @param plainPassword string
		 *
		 * return string
		 */
		private function getPasswordHash($plainPassword){
			return md5($plainPassword . '_' . $this->sault);
		}
		
		/**
		 * Compares data array with internal fields by keys
		 * @param data array
		 *
		 * return array
		 */
		private function processData($data){
			return array_intersect_key((array)$data, $this->fields);
		}
		
		/**
		 * Validates current user's fields
		 *
		 * return boolean
		 */
		private function validate(){
			$wrongFields = [];
			
			$mandatoryFields = array_filter($this->fields, function($v){ return !empty($v['mandatory']); });
			
			if ($this->isLogged)
				unset($mandatoryFields['password']);
			
			if (sizeOf(array_intersect_key($this->user, $mandatoryFields)) != sizeOf($mandatoryFields)) {
				parent::setError('Fill in all mandatory fields (' . implode(',', array_keys(array_diff_key($mandatoryFields, $this->user))) . ')');
				return false;
			}
			
			foreach ($this->user as $field => $value) {
				if (!array_key_exists($field, $this->fields))
					continue;
				
				$fieldParams = $this->fields[$field];
				
				$isValueEmpty = empty($value);
				
				if ($isValueEmpty && !empty($fieldParams['empty'])) {
					$this->user[$field] = isset($fieldParams['default']) ? $fieldParams['default'] : null;
					continue;
				}
				
				if (
					(!empty($fieldParams['mandatory']) || (isset($fieldParams['empty']) && !$fieldParams['empty']))
					&&
					$isValueEmpty
				){
					$wrongFields[$field] = 'empty field';
					continue;
				}
				
				if (isset($fieldParams['length']) && strlen($value) > $fieldParams['length']) {
					$wrongFields[$field] = 'length is more than ' . $fieldParams['length'];
					continue;
				}
				
				if (isset($fieldParams['min_length']) && strlen($value) < $fieldParams['min_length']) {
					$wrongFields[$field] = 'length is less than ' . $fieldParams['min_lenght'];
					continue;
				}
				
				if (!empty($fieldParams['regexp']) && !preg_match($fieldParams['regexp'], $value)) {
					$wrongFields[$field] = 'does not match set of allowed symbols';
					continue;
				}
				
				if (!empty($fieldParams['values']) && !in_array($value, $fieldParams['values'])) {
					$wrongFields[$field] = 'wrong value';
					continue;
				}
				
				if (!empty($fieldParams['validate'])) {
					$fieldValidationResult = false;
					$fieldValidationArguments = array_merge([$value], isset($fieldParams['validate_args']) ? $fieldParams['validate_args'] : []);
					
					if (method_exists($this, $fieldParams['validate']))
						$fieldValidationResult = call_user_func_array([$this, $fieldParams['validate']], $fieldValidationArguments);
					else if (function_exists($fieldParams['validate']))
						$fieldValidationResult = call_user_func_array($fieldParams['validate'], $fieldValidationArguments);
					
					if (!$fieldValidationResult) {
						$wrongFields[$field] = 'validation error';
						continue;
					}
				}
			}
			
			if (!empty($wrongFields)) {
				parent::setError(
					'Following fields contain errors:<br />'
					. implode('<br />', array_map(
						function($k, $v){ return $k . ': ' .$v; },
						array_keys($wrongFields),
						array_values($wrongFields)
					))
				);
			}
			
			return empty($wrongFields);
		}
		
		/**
		 * Checks if specified date is valid (less than now - 18 years)
		 * @param inputData string
		 *
		 * return boolean
		 */
		private function checkBirthday($inputData){
			$timestamp = strtotime($inputData);
			
			if (!$timestamp)
				return false;
			
			return $timestamp <= strtotime('-18 years');
		}
		
	}
	
?>