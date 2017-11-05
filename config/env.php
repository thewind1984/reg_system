<?php

	return [
		// output template section
		'tpl_cache_dir' => 'cache/tpl',		// must be writeable
		'tpl_driver' => 'Smarty',
		'tpl_dir' => null,		// default '\View\{tpl_driver}
		'tpl_use_cache' => true,
		
		// database section
		'db' => [
			'host' => 'localhost',
			'port' => 3306,
			'user' => 'reg_system',
			'db' => 'reg_system',
			'password' => 'reg_system'
		],
		
		// mail section
		'mail' => [
			'from_email' => 'noreply@fake-reg.system',
			'from_name' => 'fake-reg system',
		],
		
		// extra environment data
		'debug' => false,
		'static_version' => '1.01',
	];

?>