
	<section class="wrapper">
	
		<div class="pagination">
			<a href="/admin">&laquo; Users list</a>
		</div>
		
		<form method="post" action="">
			<input type="hidden" name="csrf_token" value="{$csrf_token}" />
			
			<h3 class="text-center">{if !empty($user_data.id)}Edit user account{else}Create user account{/if}</h3>
			
			{foreach from=$form_fields item="form_field"}
				<label>
					{$form_field.label}
					{$form_field.code}
				</label>
			{/foreach}
			
			<div class="text-center">
				<button type="submit" class="btn">Save</button>
			</div>
		</form>
		
	</section>