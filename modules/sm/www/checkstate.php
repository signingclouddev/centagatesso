<?php
    include_once ( "/var/www/html/simplesamlphp/modules/sm/lib/Auth/Source/httpful.phar" ) ;
    
    session_start ( ) ;
    
	function determineAuthMethods ( $auth_methods_array )
	{
		$_SESSION [ "otp_enabled" ] = false ;
		$_SESSION [ "pki_enabled" ] = false ;
		$_SESSION [ "sms_otp_enabled" ] = false ;
		$_SESSION [ "cr_otp_enabled" ] = false ;
		$_SESSION [ "advanced_otp_enabled" ] = false ;
		$_SESSION [ "mobile_softcert_enabled" ] = false ;
		$_SESSION [ "mobile_audiopass_enabled" ] = false ;
		$_SESSION [ "qrcode_enabled" ] = false ;
		$_SESSION [ "singpass_enabled" ] = false ;
		$_SESSION [ "num_of_2fa" ] = 0 ;
		
		foreach ( $auth_methods_array as $auth_method )
		{
			switch ( $auth_method )
			{
				case "OTP":
					$_SESSION [ "otp_enabled" ] = true ;
					$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
					
					break ;
				
				case "PKI":
					$_SESSION [ "pki_enabled" ] = true ;
					$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
					
					break ;
				
				case "SMS":
					$_SESSION [ "sms_otp_enabled" ] = true ;
					$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
					
					break ;
				
				case "CROTP":
					$_SESSION [ "cr_otp_enabled" ] = true ;
					$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
					
					break ;
				
				case "AOTP":
					$_SESSION [ "advanced_otp_enabled" ] = true ;
					$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
					
					break ;
				
				case "MSOFTCERT":
					$_SESSION [ "mobile_softcert_enabled" ] = true ;
					$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
					
					break ;
				
				case "MAUDIOPASS":
					$_SESSION [ "mobile_audiopass_enabled" ] = true ;
					$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
					
					break ;
				
				case "QRCODE":
					$_SESSION [ "qrcode_enabled" ] = true ;
					$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
					
					break ;
					
				case "SINGPASS":
					$_SESSION [ "singpass_enabled" ] = true ;
					$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
					break;
			}
		}
	}

	$config = SimpleSAML_Configuration::getInstance ( ) ;

    $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/CentagateWS/webresources/session/statecheck/" ;
    $integration_key = $_SESSION["IK"];//$config->getString('ws.integration.key','');
    //error_log("____________IK = ".$integration_key);    
    $email = $_GET [ "email" ] ;
    $unix_timestamp = time ( ) ;
    $auth_token = str_replace(' ','+',$_GET [ "authToken" ]) ;
    $auth_method = $_GET [ "auth_method" ] ;

	$params = array (
		"username"       => $email,
		"authToken"      => $auth_token,
		"authMethod"     => $auth_method,
		"integrationKey" => $integration_key,
		"unixTimestamp"  => strval ( $unix_timestamp ),
		"ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
		"userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
		"hmac"           => hash_hmac ( "sha256" , $email . $auth_method . $integration_key . $unix_timestamp . $auth_token . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $_SESSION["SK"]  )
	) ;
	
	$json = json_encode ( $params ) ;

        
	try
	{
		$response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
		$response_body = get_object_vars ( $response -> body ) ;
		$response_object = json_decode ( $response_body [ "object" ] ) ;

		//error_log(" _______________CHECK STATE ,  SEND DATA ".$json."___________ , RESPONSE = ".$response);
	        
		if ( $response -> code == 200 )
		{
			if ( $response -> body -> code === "0" )
			{
				$_SESSION [ "response_object" ] = $response_object ;
				
				$new_auth_token = $response_object -> { "authToken" } ;
				$new_secret_code = $response_object -> { "secretCode" } ;
				
				$_SESSION [ "auth_secret" ] = $new_secret_code ;
				
				//$auth_methods = $response_object -> { "authMethods" } ;
				//$auth_methods_array = split ( "," , $auth_methods ) ;

				//determineAuthMethods ( $auth_methods_array ) ;                            
							
				if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true ){
					$_SESSION [ "reset_login_form" ] = true ;
				}

				error_log("_______check stat , auth statc = ".$_SESSION [ "auth_static" ]);

				if ( $_SESSION [ "auth_static" ] && isset ( $response_object -> { "multiStepAuth" } ) && $response_object -> { "multiStepAuth" } === "true" )
				{
					$_SESSION [ "multi_step_auth" ] = true ;
					unset ( $new_secret_code ) ;
				}
				else{
					$_SESSION [ "multi_step_auth" ] = false ;
				}
					
				$_SESSION [ "authToken" ] = $new_auth_token ;
				
				if ( ! isset ( $new_secret_code ) && $_SESSION [ "multi_step_auth" ] == false ){
					echo "-1|Invalid credentials" ;
				}else if ( ! isset ( $new_secret_code ) && $_SESSION [ "multi_step_auth" ] == true ){
					echo "-2" ;
				}else{

					//error_log("SUCCESS_______________");
					$_SESSION["2FA_STATUS"]=true;
					error_log("SUCCESS_______________ 2fa status =".$_SESSION["2FA_STATUS"]);
					echo "1" ;
				}
			}
			else if ( $response -> body -> code === "23007" )
			{
				echo "2" ;
			}
			else
			{
				echo "0|" . $response -> body -> message ;
			}
		}
		else
		{
			echo "-1|Invalid credentials" ;
		}
	} 
	catch ( Httpful\Exception\ConnectionErrorException $ex )
	{
		echo "-1|Invalid credentials" ;
	}
?>
