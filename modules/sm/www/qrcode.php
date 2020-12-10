<?php

      if ( session_status ( ) != PHP_SESSION_ACTIVE )
		session_start ( ) ;

        
	
	include_once ( "/var/www/html/simplesamlphp/modules/sm/lib/phpqrcode/qrlib.php" ) ;

	$qr_code = str_replace(' ', '+', $_GET["qr"] );

        //error_log("___________QRCODE_______________".$qr_code);	
       
        //error_log("_______________________________");

	QRcode::png ( $qr_code ) ;
       

?>
