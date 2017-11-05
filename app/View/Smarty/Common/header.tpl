<!DOCTYPE html>
<html>
<head>
	<title>Reg System</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
	<link rel="stylesheet" type="text/css" href="/assets/css/main.css" />
</head>
<body class="{$controller_name}">

	<header>
		<div class="wrapper flex-row flex-full flex-vcenter">
			<div>
				<a href="/" class="logo">
					<span class="flex-vcenter">Unleash your <strong>Super</strong> Trader</span>
				</a>
			</div>
			<div>
				{if $logged_user}
					<a href="/account" class="btn">My profile</a>
				{else}
					<a href="/login" class="btn">Log in</a>
				{/if}
			</div>
		</div>
	</header>
	
	<main>
		
		{if $controller_name != 'index'}
			{include file="Common/notifications.tpl"}
		{/if}