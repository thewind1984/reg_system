<?php	namespace Helper;

	use \Model\User;

	class FormUser extends User {
		
		/**
		 * Creates html inputs for fields
		 * @param useOnlyFields array | null
		 * @param formValues array
		 *
		 * return array
		 */
		public static function build($useOnlyFields = null, $formValues = []){
			$user = new parent();
			
			$fields = $user->fields;
			if (!empty($useOnlyFields))
				$fields = array_intersect_key($fields, array_fill_keys($useOnlyFields, null));
			
			$formFields = [];
			
			foreach ($fields as $field => $fieldParams) {
				if (!empty($fieldParams['hide_form'])) {
					continue;
				}
				
				$fieldValue = !empty($formValues[$field]) ? $formValues[$field] : null;
				
				if (!empty($fieldParams['type']) && $fieldParams['type'] == 'hidden' && !$fieldValue)
					continue;
				
				$fieldType = !empty($fieldParams['type']) ? $fieldParams['type'] : 'text';
				
				switch ($fieldType) {
					case 'select':
						$fieldCode = '<select name="user[' . $field . ']" '
							. ' data-form_field="' . $field . '"'
							. (!empty($fieldParams['mandatory']) ? ' required' : '')
							. '>';
						
						foreach ($fieldParams['values'] as $fieldCodeValue) {
							$fieldCode .= '<option value="' . $fieldCodeValue . '"' . ($fieldCodeValue == $fieldValue ? ' selected' : '') . '>' . $fieldCodeValue . '</option>';
						}
						
						$fieldCode .= '</select>';
						
						break;
					
					default:
						$fieldCode = '<input type="'
							. $fieldType
							. '" name="user[' . $field . ']"'
							. ' data-form_field="' . $field . '"'
							. (!empty($fieldParams['mandatory']) ? ' required' : '')
							. (!empty($fieldParams['length']) ? ' maxlength="' . $fieldParams['length'] . '"' : '')
							. ' value="' . (string)htmlspecialchars($fieldValue) . '" />';
						
						break;
				}
				
				$formFields[] = ['label' => ucfirst(strtolower($field)), 'code' => $fieldCode];
			}
			
			return $formFields;
		}
		
	}