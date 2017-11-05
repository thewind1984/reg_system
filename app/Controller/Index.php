<?php	namespace Controller;

	use \Helper\FormUser;

	class Index extends Base {
		
		/**
		 * Main action for root path
		 */
		public function index(){
			
			$formFields = FormUser::build();
			$this->assign('form_fields', $formFields);
			
		}
		
	}

?>