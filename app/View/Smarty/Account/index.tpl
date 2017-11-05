
	<section class="account wrapper flex-column ">
	
		<div class="flex-row flex-center">
			<a href="/account/logout" class="btn">Log out</a>
		</div>
	
		<div class="flex-row flex-center">
			<form method="post" action="/account/data">
				<input type="hidden" name="csrf_token" value="{$csrf_token}" />
				
				<h3 class="text-center">Edit your account data</h3>
				
				<label>
					Username
					<div><b>{$user.username}</b></div>
				</label>
				
				{foreach from=$data_form_fields item="form_field"}
					<label>
						{$form_field.label}
						{$form_field.code}
					</label>
				{/foreach}
				
				<div class="text-center">
					<button type="submit" class="btn">Update</button>
				</div>
			</form>
		</div>
		
		<div class="flex-row flex-center">
			<form method="post" action="/account/password">
				<input type="hidden" name="csrf_token" value="{$csrf_token}" />
				
				<h3 class="text-center">Edit your password</h3>
				
				<label>
					Current password
					<input type="password" name="current_password" required />
				</label>
				
				{foreach from=$password_form_fields item="form_field"}
					<label>
						{$form_field.label}
						{$form_field.code}
					</label>
				{/foreach}
				
				<div class="text-center">
					<button type="submit" class="btn">Update</button>
				</div>
			</form>
		</div>
		
		<div class="flex-row flex-center">
			<form method="post" action="/account/delete" id="form_account_delete" data-prevent_validation>
				<input type="hidden" name="csrf_token" value="{$csrf_token}" />
				
				<h3 class="text-center">Delete your account</h3>
				
				<div class="text-center">
					<button type="submit" class="btn">Delete</button>
				</div>
			</form>
		</div>
		
	</section>