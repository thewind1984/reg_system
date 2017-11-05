<html>
<body>

	<h1>{$welcome}</h1>
	
	{if !empty($user)}
		{foreach from=['username', 'email', 'name', 'surname', 'phone', 'birthday'] item="field"}
			{assign var="field_value" value=$user.$field}
			
			{if $field_value}
				<div>
					{$field|strtolower|ucfirst}:
					<b>{$field_value}</b>
				</div>
			{/if}
		{/foreach}
	{/if}
	
	{if !empty($extra_data)}
		
		{if !empty($user)}
			<br />
		{/if}
		
		{foreach from=$extra_data key="field" item="field_value"}
			<div>
				{$field|strtolower|ucfirst}:
				<b>{$field_value}</b>
			</div>
		{/foreach}
	{/if}

</body>
</html>