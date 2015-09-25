<?php
	define('APP_PATH','./App/');
	define('__APP__', '');
	define('APP_DEBUG', 1);
	define('BIND_MODULE','Home');
	chdir(realpath(dirname(__FILE__)));

	if(!function_exists('slog')){
		require  './SocketLog.class.php';
		$slog_config=array(
		   'host'=>'i.kuaijianli.com',
		   'port'=>1229,
		   'error_handler'=>true,
		   'optimize'=>true,
		   'allow_client_ids'=>array('yangweijie_jay'),
		   'show_included_files'=>false
		);
		if(isset($_GET['slog_force_client_id'])){
			$slog_config['force_client_id'] = $_GET['slog_force_client_id'];
		}
		slog($slog_config,'set_config');
	}

	require './ThinkPHP/ThinkPHP.php';
