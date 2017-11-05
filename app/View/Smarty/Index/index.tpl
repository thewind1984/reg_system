
	<section class="main">
	
		{include file="Common/notifications.tpl"}
	
		<div class="wrapper flex-vcenter">
		
			<div class="flex-row flex-full">
				<div class="flex-vtop">
					<h1>Traders <span>Rebate</span></h1>
				</div>
				
				{if !$logged_user}
					<form method="post" action="/account/register">
						<input type="hidden" name="csrf_token" value="{$csrf_token}" />
						
						<h3 class="text-center">Create your account!</h3>
						
						{foreach from=$form_fields item="form_field"}
							<label>
								{$form_field.label}
								{$form_field.code}
							</label>
						{/foreach}
						
						<div class="text-center">
							<button type="submit" class="btn">Create account</button>
						</div>
					</form>
				{/if}
			</div>
		
		</div><!-- end of wrapper -->
		
	</section>
	
	<section class="accounts wrapper">
		<h2>Account <span>Types</span></h2>
		
		<ul class="flex-row flex-items-3 flex-wrap">
			<li>
				<img src="/assets/images/img1.jpg" alt="" />
				<p><a href="#">Lorem ipsum dolor sit amet</a>, consectetur adipiscing elit. Praesent vitae erat nec metus pretium tempor id vel magna. Nam eu posuere purus. Etiam a nunc non erat suscipit tempus semper ac justo. Fusce et condimentum tellus. Nullam iaculis, magna vestibulum molestie fermentum, dui justo laoreet massa, non dictum neque sapien at sem. Phasellus sed nunc turpis. Vivamus venenatis vel tortor id rutrum. Nulla consequat et velit vel tincidunt.</p>
			</li>
			<li>
				<img src="/assets/images/img2.jpg" alt="" />
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vitae erat nec metus pretium tempor id vel magna. Nam eu posuere purus. Etiam a nunc non erat suscipit tempus semper ac justo. Fusce et condimentum tellus. Nullam iaculis, magna vestibulum molestie fermentum, dui justo laoreet massa, non dictum neque sapien at sem. Phasellus sed nunc turpis. Vivamus venenatis vel tortor id rutrum. Nulla consequat et velit vel tincidunt.</p>
			</li>
			<li>
				<img src="/assets/images/img3.jpg" alt="" />
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vitae erat nec metus pretium tempor id vel magna. Nam eu posuere purus. Etiam a nunc non erat suscipit tempus semper ac justo. Fusce et condimentum tellus. Nullam iaculis, magna vestibulum molestie fermentum, dui justo laoreet massa, non dictum neque sapien at sem. Phasellus sed nunc turpis. Vivamus venenatis vel tortor id rutrum. Nulla consequat et velit vel tincidunt.</p>
			</li>
		</ul>
	</section>
	