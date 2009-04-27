<?php

	// LOAD LIB
	require_once('proxy.class.php');

	// CONFIGURE
	HttpProxyStream::$proxy_host = 'your_proxy';
	HttpProxyStream::$proxy_port = 8080;
	
	// OPEN NORMALY A URL
	$f = fopen('proxy://www.google.fr','r');
	while(!feof($f)) {
		echo fread($f, 4096);
	}
	fclose($f);	
	
	// OR WITH A FUNCTION
	readfile('proxy://www.google.fr');
	echo file_get_contents('proxy://www.google.fr');
	
?>