
	<section class="login wrapper flex-row flex-center">
	
		<form method="post" action="">
			<input type="hidden" name="csrf_token" value="{$csrf_token}" />
			
			<h3 class="text-center">Sign in into your account</h3>
			
			{foreach from=$form_fields item="form_field"}
				<label>
					{$form_field.label}
					{$form_field.code}
				</label>
			{/foreach}
			
			<div class="text-center">
				<button type="submit" class="btn">Log in</button>
			</div>
		</form>
		
	</section>