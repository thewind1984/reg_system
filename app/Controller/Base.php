<?php	namespace Controller;

	use \Helper\Session;
	use \Helper\Config;
	use \View\Base as View;
	use \Helper\Notifications;
	use \Model\User;
	use \Helper\Utils;

	class Base {
		
		/**
		 * Current controller name
		 * @var controller string
		 */
		protected $controller;
		
		/**
		 * Current action name
		 * @var action string
		 */
		protected $action;
		
		/**
		 * Extra data from URL after action
		 * @var input array
		 */
		protected $input = [];
		
		/**
		 * Customized template for output
		 * Controller can set it to avoid rendering of default template (identical to 'action')
		 * @var template string
		 */
		protected $template;
		
		/**
		 * Flag, which shows is controller top-leveled or invoked by another controller
		 * @var routed boolean [default = false]
		 */
		protected $routed = false;
		
		/**
		 * Flag, which shows controller has to render after it's action
		 * @var rendering boolean [default = true]
		 */
		protected $rendering = true;
		
		/**
		 * Scope of variables for rendered in future template
		 * @var templateVariablesScope array
		 */
		protected $templateVariablesScope = [];
		
		/**
		 * Instance of VIEW component of MVC system
		 * @var view View
		 */
		protected $view;
		
		/**
		 * Flag, which shows request was send through POST
		 * @var isPost boolean [default = false]
		 */
		protected $isPost = false;
		
		/**
		 * CSRF token of loaded page
		 * @var csrfToken string
		 */
		private $csrfToken = null;
		
		/**
		 * Private sault
		 * @var csrfSault string
		 */
		private $csrfSault = 'iWEO~{';
		
		
		/**
		 * Constructor
		 * @param controller string
		 * @param action string
		 */
		public function __construct($controller, $action, $input = []){
			$this->assign('static_version', Config::get('env', 'static_version'));
			
			$this->controller = $controller;
			$this->action = $action;
			$this->input = $input;
			
			$this->isPost = isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST';
			
			// if POST request is here, than check csrf token
			if ($this->isPost) {
				$lastCsrfToken = Session::get('last_token');
				$postData = $this->getRequestData();
				
				if (
					empty($lastCsrfToken) || !isset($postData['csrf_token']) || $lastCsrfToken != $postData['csrf_token']					// CSRF attack
					|| empty($_SERVER['HTTP_REFERER']) || parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != $_SERVER['SERVER_NAME']		// empty REFERER
				) {
					$this->isPost = false;
				}
			}
			
			// generate new csrf token for any form around the site
			$this->csrfToken = base64_encode(md5($this->csrfSault . '.' . time()) . '_' . crc32(uniqid()));
			$this->assign('csrf_token', $this->csrfToken);
			Session::set('last_token', $this->csrfToken);
			
			// init user
			$this->user = new User;
			$this->user->loginFromSession();
			$this->assign('logged_user', $this->user->isUserLogged());
			
			// flash notifications
			Notifications::init();
			
			$this->view = View::getInstance();
		}
		
		/**
		 * Main application method to run
		 * Automatically renders template after all controllers actions
		 */
		public function run(){
			call_user_func_array([$this, $this->action], []);
			
			if (!$this->routed && $this->rendering) {
				$this->assign('notifications', Notifications::get());
				$this->render();
			}
		}
		
		/**
		 * Routing to another router's path
		 * @param class string		additional class
		 * @param action string		action to execute
		 */
		protected function route($class, $action = 'index', $input = []){
			$controller = $this->controller . '\\' . implode('\\', array_map(function($v){ return ucfirst(strtolower($v)); }, explode('/', $class)));
			
			$this->routed = true;
			
			$class = new $controller($controller, $action, $input);
			$class->run();
		}
		
		public function __call($method, $args){
			if (method_exists($this, 'index'))
				call_user_func_array([$this, 'index'], []);
		}
		
		/**
		 * Put some variable into inner scope for output
		 * @param k string
		 * @param v mixed
		 */
		protected function assign($k, $v){
			$this->templateVariablesScope[$k] = $v;
		}
		
		/**
		 * Render output template with inner scope of data
		 */
		protected function render(){
			$controllerName = explode('\\', trim($this->controller, '\\'));
			
			$this->templateVariablesScope['controller_name'] = strtolower(implode('_', array_slice($controllerName, 1)));
			
			$this->view->render(
				$this->templateVariablesScope,
				end($controllerName) . '/' . ($this->template ? $this->template : $this->action) . '.tpl'
			);
		}
		
		/**
		 * Redirects to another URL
		 * Works without any additional headers like HTTP_RESPONSE_CODE
		 * @param url string
		 */
		protected function redirect($url){
			return Utils::redirect($url);
		}
		
		/**
		 * Reloads currently opened location
		 */
		protected function reload(){
			return Utils::reload();
		}
		
		/**
		 * Returns data of request by its type
		 * @param methodType string		null | GET | POST
		 *
		 * return array
		 */
		protected function getRequestData($methodType = null){
			$methodTypeData = $methodType !== null ? $_GET : ($this->isPost ? $_POST : $_GET);
			
			return $methodTypeData;
		}
		
	}
	
?>