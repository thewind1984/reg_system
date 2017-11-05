<?php	namespace View;

	use \Helper\Config;
	use \Helper\Exception;

	/**
	 * pattern: Singleton
	 */
	class Base {
		
		protected static $instance;
		public $tpl;
		
		private $headerFile = 'Common/header.tpl';
		private $footerFile = 'Common/footer.tpl';
		
		/**
		 * Constructor
		 * Creates instance of template driver
		 */
		private function __construct(){
			$config = Config::get('env');
			
			$class = '\\View\\' . $config['tpl_driver'];
			$this->tpl = new $class();
			$this->tpl = $this->tpl->get();		// final View component
			
			$this->tpl->setCacheDir(ROOT_DIR . '/' . $config['tpl_cache_dir']);
			$this->tpl->setCompileDir(ROOT_DIR . '/' . $config['tpl_cache_dir']);
			$this->tpl->setConfigDir(ROOT_DIR . '/' . $config['tpl_cache_dir']);
			$this->tpl->setTemplateDir($config['tpl_dir'] !== null ? ROOT_DIR . '/' . ltrim($config['tpl_dir'], '/') : APP_DIR . '/View/' . $config['tpl_driver']);
		}
		
		// hide because Singleton used
		private function __clone(){}
		
		/**
		 * Always return single instance of self
		 */
		public static function getInstance(){
			if (self::$instance == null)
				self::$instance = new self();
			
			return self::$instance;
		}
		
		/**
		 * Render template and output it
		 * @param vars array
		 * @param tpl string
		 * @param covers boolean
		 */
		public function render($vars, $tpl, $covers = true){
			foreach ((array)$vars as $k=>$v)
				$this->tpl->assign($k, $v);
			
			if (!$this->tpl->templateExists($tpl))
				throw new Exception('Template not found (' . $tpl . ')');
			
			if ($covers && !empty($this->headerFile))
				$this->tpl->display($this->headerFile);
			
			if ($this->tpl->templateExists($tpl)) {
				$this->tpl->display($tpl);
			}
			
			if ($covers && !empty($this->footerFile))
				$this->tpl->display($this->footerFile);
		}
		
		/**
		 * Fetch template into variable without output
		 * @param vars array
		 * @param tpl string
		 */
		public function fetch($vars, $tpl){
			foreach ($vars as $k=>$v)
				$this->tpl->assign($k, $v);
			
			if (substr($tpl, 0, 7) != 'string:' && !$this->tpl->templateExists($tpl))
				throw new Exception('Template not found (' . $tpl . ')');
			
			return $this->tpl->fetch($tpl);
		}
		
	}
	
?>