
	<section class="wrapper">

		<form method="get" action="" onsubmit="$(this).find('input[name=page]').val(1);">
		
			<input type="hidden" name="page" value="{$page}" />
		
			<label>
				Registered from
				<input type="date" name="filter[from_date]" value="{$filter.from_date}" />
				to
				<input type="date" name="filter[to_date]" value="{$filter.to_date}" />
			</label>
			
			<div class="flex-row flex-full">
				<div>
					<button type="submit" class="btn">Filter</button>
					<button type="reset" class="btn" onclick="location.href='/admin';">Reset</button>
				</div>
				<div>
					<button type="button" class="btn" onclick="location.href='/admin/user';">Create user</button>
				</div>
			</div>
		
		</form>
		
		<div class="pagination">
			Results: <b>{$count}</b>
		</div>
		
		{if empty($list)}
			
			<p>Users not found.</p>
			
		{else}
		
			<table>
				<thead>
					<tr>
						<th>ID</th>
						<th>Username</th>
						<th>Email</th>
						<th>Name</th>
						<th>Surname</th>
						<th>Phone</th>
						<th>Birthday</th>
						<th>Registered</th>
						<th>Status</th>
						<th>Deleted</th>
						<th />
					</tr>
				</thead>
				<tbody>
					{foreach from=$list item="user"}
						<tr>
							<td>{$user.id}</td>
							<td>{$user.username}</td>
							<td>{$user.email}</td>
							<td>{$user.name}</td>
							<td>{$user.surname}</td>
							<td>{$user.phone}</td>
							<td>{$user.birthday}</td>
							<td>{$user.date_registered}</td>
							<td>{$user.status}</td>
							<td>{$user.date_deleted}</td>
							<td>
								{if $user.status != 'DELETED'}
									<a href="/admin/user/{$user.id}">Edit</a>
								{/if}
								{if $user.status == 'ACTIVE'}
									<a href="/admin/user/{$user.id}/delete">Delete</a>
									<a href="/admin/user/{$user.id}/suspend">Suspend</a>
								{else if $user.status == 'SUSPEND'}
									<a href="/admin/user/{$user.id}/active">Activate</a>
								{/if}
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
			
			<div class="pagination">
				Pages:
				
				{foreach from=$pagination item="page_item"}
					{if $page_item.link && empty($page_item.active)}
						<a href="javascript:void(0);" data-filter_page="{$page_item.num}">{$page_item.num}</a>
					{else}
						{$page_item.num}
					{/if}
				{/foreach}
			</div>
		
		{/if}
	
	</section>