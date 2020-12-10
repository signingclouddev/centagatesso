<?php
	session_start ( ) ;
	
	include_once ( "/var/www/html/simplesamlphp/modules/sm/lib/Auth/Source/httpful.phar" ) ;
	include_once ('/var/www/html/simplesamlphp/modules/sm/lib/Auth/Source/Config.php');

	if ( isset ( $_POST [ "email" ] ) || isset($_GET["username"])  )
	{	   		
		$config = SimpleSAML_Configuration::getInstance ( ) ;		
		//$rest_url = "http://centagate:8080/".sspmod_sm_Auth_Source_Config::webContext."/webresources/security/getUserImage/" . $_POST [ "email" ] ;

		try
		{
			$email = $_POST["email"];
			if (isset ($_POST["email"])){
				$email = $_POST["email"];
			}else {
				$email = $_GET["username"];
			}
			$isBelowIE8 = preg_match('/msie [2-7]/i',$_SERVER['HTTP_USER_AGENT']);
			
			//$response = \Httpful\Request::get ( $rest_url ) -> send ( ) ;			
			$response = sspmod_sm_Auth_Source_UserPass::getSecurityImage ( $email );	
			if ( $response -> code == 200 )
			{
				if ( $response -> body -> code === "0" )
				{
					//two mode. if IE6 and below, show it as image
					if ( $isBelowIE8 == 1)
					{
						header("content-type: image/jpeg");
						echo base64_decode ( $response -> body -> object );
					}
					else //if otherwise, display the base64 string
					{
						$_SESSION [ "base64_img" ] = $response -> body -> object ;
						echo $response -> body -> object ;
					}										
				}
				else
				{
					/* Problem happened. Consider as wrong login. */
					$_SESSION [ "error_message" ] = $response -> body -> message ;    
					echo $response -> body -> message;					
				}
			}
			else
			{
				/* Problem happened. Consider as wrong login. */
				$_SESSION [ "error_message" ] = "Internal server error. Please contact administrator" ; 
				echo 	$response -> code 			;
			}
		} 
		catch ( Httpful\Exception\ConnectionErrorException $ex )
		{
			/* Connection error. Just say invalid login */
			$_SESSION [ "error_message" ] = "Unable to connect to CENTAGATE" ;   
			echo $ex;
		}
	}	
	else
	{
		$_SESSION [ "base64_img" ] = "NO IMAGE" ;
		
		echo '{"code":"10002"}' ;
	}
 ?>
