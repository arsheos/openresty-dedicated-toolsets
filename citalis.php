<?php

	if (!empty($_PUT) or !empty($_DELETE))
	exit();
	else if (!empty($_POST)) 	{
		$headers = $_POST;
		$exAE = "method=_POST";
	}
	else	{
		$headers = $_GET;
		$exAE = "method=_GET";
	}
	
	while (list($header, $value) = each($headers)) $exAE .= "&$header=$value";
	$exAE = urlencode($exAE);
		
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.15") {
		$connection = fsockopen("localhost", 9577, $error_number, $error_description, 30); 	
		if ($connection) {
			stream_set_blocking($connection, true);
			fputs($connection,"");
			fputs($connection,"$exAE"."\r\n");
			fpassthru($connection);
		}
		else if (!empty($_POST))
		print("<p align='center'>403 server's down error on POST</p>");
		else print("<p align='center'>403 server's down error on GET</p>");
	}
	else 	{
		$connection = fsockopen("localhost", 9578, $error_number, $error_description, 30); 	 
		if ($connection) {
			stream_set_blocking($connection, true);
			fputs($connection,"");
			fputs($connection,"$exAE"."\r\n");
			fpassthru($connection);
		}
		else 	{
			$connection = fsockopen("localhost", 9579, $error_number, $error_description, 30); 	 
			if ($connection) {
				stream_set_blocking($connection, true);
				fputs($connection,"");
				fputs($connection,"$exAE"."\r\n");
				fpassthru($connection);
			}
			elseif (!empty($_POST))
			print("<p align='center'>403 server's down error on POST</p>");
			else print("<p align='center'>403 server's down error on GET</p>");
		}
	}

?>
	
