
	<section class="wrapper">
	
		<form method="post" action="">
			<input type="hidden" name="csrf_token" value="{$csrf_token}" />
			
			<h3 class="text-center">Update account status</h3>
			
			<p class="text-center">Make account status: <b>{$action}</b>?</p>
			
			<div class="text-center">
				<button type="submit" class="btn">Confirm</button>
				<button type="reset" class="btn" onclick="window.history.back();">Cancel</button>
			</div>
		</form>
		
	</section>