
var fieldErrorClassName = 'field-error',
	fieldsValidationRules = {
		username: '^[a-z0-9]{3,20}$',
		name: '^[a-z]{0,40}$',
		surname: '^[a-z]{0,40}$',
		email: '^[a-z0-9\-\_]+@[a-z]+[a-z0-9\-]{1,}[a-z0-9]+.[a-z]{2,15}$',
		phone: '^[0-9]{5,30}$',
		password: '^.{6,100}$'
	};

$(document).ready(function(){
	
	// remove temporary added 'error' class to fields
	$('body').on('change keydown click focus', '[data-form_field]', function(e){
		$(this).removeClass(fieldErrorClassName);
	});
	
	// client's validation of form fields
	$('body').on('submit', 'form', function(e){
		e.preventDefault();
		
		if (typeof $(this).data('prevent_validation') != 'undefined')
			return;
		
		$(this).find('[data-form_field]').each(function(i){
			var fieldValidationRules = fieldsValidationRules[ $(this).data('form_field') ],
				checkFieldNotEmpty = typeof $(this).attr('required') != 'undefined';
			
			if (typeof fieldValidationRules != 'undefined') {
				if (!checkFieldNotEmpty && !$.trim( $(this).val() ))
					return;
				
				var regExp = new RegExp(fieldValidationRules != 'undefined' ? fieldValidationRules : '^.{1,}$', 'gi');
				
				if ( !regExp.test( $(this).val() ) ) {
					$(this).addClass(fieldErrorClassName);
				}
			}
		});
		
		if ( !$(this).find('[data-form_field].' + fieldErrorClassName).length )
			$(this).get(0).submit();
	});
	
	// confirmation for account's deletion
	$('body').on('submit', 'form#form_account_delete', function(e){
		e.preventDefault();
		
		if (confirm('Really delete your account?'))
			$(this).get(0).submit();
	});
	
	// pagination
	$('body').on('click', '[data-filter_page]', function(e){
		var frm = $(this).parents('.pagination').eq(0).siblings('form').eq(0);
		frm.find('input[name="page"]').val( $(this).data('filter_page') );
		frm.get(0).submit();
	});
	
})