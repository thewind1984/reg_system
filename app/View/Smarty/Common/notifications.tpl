
	{if !empty($notifications)}
		<div class="notifications">
			{foreach from=$notifications item="notification"}
				<div class="alert alert-{$notification.type}">{$notification.value}</div>
			{/foreach}
		</div>
		<br />
	{/if}