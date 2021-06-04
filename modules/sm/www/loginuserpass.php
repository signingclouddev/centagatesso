<?php
/**
 * This page shows a username/password login form, and passes information from it
 * to the sspmod_core_Auth_UserPassBase class, which is a generic class for
 * username/password authentication.
 *
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */

	if ( session_status ( ) != PHP_SESSION_ACTIVE )
		session_start ( ) ;

	$_SESSION [ "ignore_error" ] = false ;

	function x509_fingerprint ( $pem , $hash = 'sha1' )
	{
		$hash = in_array ( $hash , array ( 'sha1' , 'md5' , 'sha256' ) ) ? $hash : 'sha1' ;
		
		$pem = preg_replace ( '/\-+BEGIN CERTIFICATE\-+/' , '' , $pem ) ;
		$pem = preg_replace ( '/\-+END CERTIFICATE\-+/' , '' , $pem ) ;
		$pem = str_replace ( array ( "\n" , "\r" ) , '' , trim ( $pem ) ) ;
		
		return hash ( $hash , base64_decode ( $pem ) ) ;
	}

	function clear_session ( )
	{  
		unset ( $_SESSION [ "pwd" ] ) ; 
		unset ( $_SESSION [ "auth_method" ] ) ;
		//unset ( $_SESSION [ "email" ] ) ;
		unset ( $_SESSION [ "authToken" ] ) ;
		unset ( $_SESSION [ "show_2fa_input" ] ) ;
		unset ( $_SESSION [ "otp_challenge" ] ) ;
		unset ( $_SESSION [ "otp_enabled" ] ) ;
		unset ( $_SESSION [ "pki_enabled" ] ) ;
		unset ( $_SESSION [ "sms_otp_enabled" ] ) ;
		unset ( $_SESSION [ "cr_otp_enabled" ] ) ;
		unset ( $_SESSION [ "mobile_softcert_enabled" ] ) ;
		unset ( $_SESSION [ "mobile_push_enabled" ] ) ;
		unset ( $_SESSION [ "singpass_enabled" ] ) ;
		unset ( $_SESSION [ "auth_static" ] ) ;
		unset ( $_SESSION [ "multi_step_auth" ] ) ;
		unset ( $_SESSION [ "login_mode" ] ) ;
		unset ( $_SESSION [ "num_of_2fa" ] ) ;
		unset ( $_SESSION [ "ignore_error" ] ) ;
		unset ( $_SESSION [ "user_id" ] ) ;
		unset ( $_SESSION [ "auth_secret" ] ) ;
		unset ( $_SESSION [ "stepupAuth"] );
		unset ( $_SESSION [ "qr_otp_challenge" ] ) ;
		unset ( $_SESSION [ "qr_plain_text" ] ) ;
		unset ( $_SESSION [ "real_email" ] ) ;
		unset ( $_SESSION [ "password" ] ) ;
	}

	function redirect ( $error , $redirectUrl , $authStateId , $email , $cancel_login = FALSE , $clear_ssl_state = FALSE , $clear_session = FALSE )
	{
		if ( $cancel_login ){
			sspmod_sm_Auth_Source_UserPass::handleCancelLogin ( $authStateId , $email ) ;
		}
		
		$host = $_SERVER [ "SERVER_NAME" ] ;
		$uri = $_SERVER [ "REQUEST_URI" ] ;

		$uri_split = explode ( "?" , $uri , 2 ) ;
		$params = explode ( "&" , $uri_split [ 1 ] ) ;
		$newUri = "" ;
		$counter = 0 ;
		
		foreach ( $params as $param )
		{
			$keyValue = explode ( "=" , $param ) ;
			
			if ( $keyValue [ 0 ] === "AuthState" )
				$keyValue = explode ( "=" , $param , 2 ) ;
			
			if ( count ( $keyValue ) == 2 )
			{
				if ( $keyValue [ 0 ] !== "err" )
				{
					$counter ++ ;
					
					if ( $counter > 1 )
						$newUri .= "&" ;
					
					$newUri .= $param ;
				}
			}
		}
		
		if ( isset ( $redirectUrl ) && $redirectUrl !== "" )
			$uri = "?err=" . htmlspecialchars ( $error ) . "&" . $newUri ;
		else
			$uri = $uri_split [ 0 ] . "?err=" . htmlspecialchars ( $error ) . "&" . $newUri ;
			
		if ( $clear_ssl_state )
		{
			echo "<script type=\"text/javascript\">\n" ;
			echo "if ( document.execCommand )\n" ;
			echo "    document.execCommand ( \"ClearAuthenticationCache\" ) ;\n" ;
			echo "if ( window.crypto && window.crypto.logout )\n" ;
			echo "    window.crypto.logout ( ) ;\n\n" ;
		}
		else
			echo "<script>" ;
		
		if ( isset ( $redirectUrl ) && $redirectUrl !== "" ){
			echo "document.location = \"" . $redirectUrl . $uri . "\" ;\n" ;
		}else{
			echo "document.location = \"https://" . $host . str_replace ( "simplesamlsecure" , "simplesaml" , $uri ) . "\" ;\n" ;
		}
		
		echo "</script>" ;
		
		if ( $clear_session )
			clear_session ( ) ;
	}

	if ( !array_key_exists ( 'AuthState' , $_REQUEST ) )
	{
		throw new SimpleSAML_Error_BadRequest ( 'Missing AuthState parameter.' ) ;
	}

	$authStateId = $_REQUEST [ 'AuthState' ];

	/* Retrieve the SP Entity ID */
	$auth_state_array = explode ( ":" , $authStateId , 2 ) ;
	$sp_entity_id = "" ;

	if ( count ( $auth_state_array ) === 2 )
	{
		$url_split = explode ( "?" , $auth_state_array [ 1 ] ) ;
		
		if ( count ( $url_split ) === 2 )
		{
			$query_string_array = explode ( "&" , $url_split [ 1 ] ) ;
			$sp_entity_id = "" ;

			foreach ( $query_string_array as $query_string )
			{
				$param = explode ( "=" , $query_string ) ;
				
				if ( count ( $param ) === 2 && $param [ 0 ] === "spentityid" )
				{
					$sp_entity_id = urldecode ( $param [ 1 ] ) ;
					
					break ;
				}
			}
		}
	}
	// sanitize the input
	$sid = SimpleSAML_Utilities::parseStateID ( $authStateId ) ;

	if ( !is_null ( $sid [ 'url' ] ) )
	{
		SimpleSAML_Utilities::checkURLAllowed ( $sid [ 'url' ] ) ;
	}

	/* Retrieve the authentication state. */
	$error_param = "" ;

	if ( isset ( $_REQUEST [ "err" ] ) ){
		$error_param = $_REQUEST [ "err" ] ;
	}

	$state = SimpleSAML_Auth_State::loadState ( $authStateId , sspmod_sm_Auth_Source_UserPass::STAGEID , FALSE) ;
	$source = SimpleSAML_Auth_Source::getById ( $state [ sspmod_sm_Auth_Source_UserPass::AUTHID ] ) ;

	if ( $source === NULL )
	{
		throw new Exception ( 'Could not find authentication source with id ' . $state [ sspmod_sm_Auth_Source_UserPass::AUTHID ] ) ;
	}

	$errorCode = NULL ;
	$errorParams = NULL ;

	if ( array_key_exists ( 'email' , $_REQUEST ) )
	{
		$email = $_REQUEST [ 'email' ] ;
		//error_log("_______EMAIL====".$email."______________");
		$_SESSION [ "email" ] = $email ;
	}
	else if ( isset ( $_SESSION [ "email" ] ) ){
		$email = $_SESSION [ "email" ] ;
	}
	else{
		$email = '' ;
		//error_log("_______EMAIL====".$email."______________");
		/* Check for reset login operation */
	}
		
	if ( array_key_exists ( 'reset_login_button' , $_REQUEST ) )
	{
		/* User requests for reset */
		redirect ( "" , "" , $authStateId , $_SESSION [ "email" ] , true , true , true ) ;
		
		die ( ) ;
	}

	$error = "" ;
	$pkiError = false ;
	$passwordless = isset ( $_POST [ "passwordless" ] ) ? $_POST [ "passwordless" ] : "0" ;

	if ( isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true )
	{
		$request_sms_otp = isset ( $_POST [ "request_sms_otp" ] ) ? $_POST [ "request_sms_otp" ] : "0" ;
		$request_otp = isset ( $_POST [ "request_otp" ] ) ? $_POST [ "request_otp" ] : "0" ;
		$request_otp_challenge = isset ( $_POST [ "request_otp_challenge" ] ) ? $_POST [ "request_otp_challenge" ] : "0" ;
		$request_mobile_soft_cert = isset ( $_POST [ "request_mobile_soft_cert" ] ) ? $_POST [ "request_mobile_soft_cert" ] : "0" ;
		$request_mobile_push = isset ( $_POST [ "request_mobile_push" ] ) ? $_POST [ "request_mobile_push" ] : "0" ;
		$request_qr_code = isset ( $_POST [ "request_qr_code" ] ) ? $_POST [ "request_qr_code" ] : "0" ;
		$request_pki_code = isset ( $_POST [ "request_pki_code" ] ) ? $_POST [ "request_pki_code" ] : "0" ;
		$request_fido = isset ( $_POST [ "request_fido" ] ) ? $_POST [ "request_fido" ] : "0" ;
		$passwordless = isset ( $_POST [ "passwordless" ] ) ? $_POST [ "passwordless" ] : "0" ;
		$request_singpass = isset ( $_POST [ "request_singpass" ] ) ? $_POST [ "request_singpass" ] : "0" ;
	  
		$oneCount = 0 ;

		//error_log("___________________ COUNT pwd = ".$passwordless);
		
		if ( $request_otp == "1" )
		{
			$passwordless="0";
			$oneCount ++ ;
		}
		
		if ( $request_sms_otp == "1" )
		{
			$passwordless="0";	
			$oneCount ++ ;
		}

		if ( $request_otp_challenge == "1" )
		{
			$passwordless="0";
			$oneCount ++ ;
		}
		
		if ( $request_mobile_soft_cert == "1" )
		{
			$oneCount ++ ;
			$passwordless="0";
		}
		 
		if ( $request_mobile_push == "1" )
		{
			$passwordless="0";
			$oneCount ++ ;
		}
		
		if ( $request_qr_code == "1" )
		{
			$passwordless="0";
			$oneCount ++ ;
		}

		if ( $request_pki_code == "1" )
		{
			$passwordless="0";   
			$oneCount ++ ;
		}


		if ( $request_fido == "1" )
		{
		   $passwordless="0"; 
		   $oneCount ++ ;
		}
		
		if($request_singpass == "1")
		{
			$passwordless="0";   
			$oneCount ++ ;
		}

		if ( $passwordless == "1" ){
			$oneCount ++ ;
		}

		//error_log("___________________ COUNT 2 = ".$oneCount);
	}

	$headers = apache_request_headers ( ) ;

	if ( isset ( $headers [ "SSL_CLIENT_CERT" ] ) && strlen ( $headers [ "SSL_CLIENT_CERT" ] ) > 0 && isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true && ( ! isset ( $oneCount ) || $oneCount === 0 ) && ( ! isset ( $_SESSION [ "login_mode" ] ) || $_SESSION [ "login_mode" ] === 1 ) )
	{
		/* Certificate detected and user has logged-in using email/password */
		$_SESSION [ "login_mode" ] = 1 ;
		
		$cert_data = $headers [ "SSL_CLIENT_CERT" ] ;
		$fingerprint = x509_fingerprint ( $cert_data , "sha1" ) ;
		
		try
		{
			//error_log("__________Handle PKI________________");

			$data="1".$_SESSION["IK"]."PUSHSUCCESS".$_SESSION["email"];
			$sig = hash_hmac('sha256', $data, $_SESSION["SK"]);
			error_log("______________ DATA SING IN VALIDATOR = ".$sig);
			
			if ( isset($_REQUEST [ "datasign" ]) && $_REQUEST [ "datasign" ] == $sig  ){
				sspmod_sm_Auth_Source_UserPass::handlePkiLogin ( $authStateId , $_SESSION [ "email" ] ) ;
				$_SESSION [ "ignore_error" ] = true ;
			}else{
				if ( isset ( $_SESSION [ "error_message" ] ) )
					$error = $_SESSION [ "error_message" ] ;
				else
					$_SESSION [ "ignore_error" ] = true ;

				/* This is to remove the disabled */
				unset ( $_SESSION [ "login_mode" ] ) ;

				$_SESSION [ "show_2fa_input" ] = false ;

				/* Login failed. Extract error code and parameters, to display the error. */
				$errorCode = $e -> getErrorCode ( ) ;
				$errorParams = $e -> getParameters ( ) ;

			}

		}
		catch ( SimpleSAML_Error_Error $e )
		{
			/* Login failed. Extract error code and parameters, to display the error. */
			$errorCode = $e -> getErrorCode ( ) ;
			$errorParams = $e -> getParameters ( ) ;

			if ( isset ( $_SESSION [ "error_message" ] ) )
			{
				$error = $_SESSION [ "error_message" ] ;
				$pkiError = true ;
			}
			else
				$_SESSION [ "ignore_error" ] = true ;
		}
	}
	else
	{
		if ( array_key_exists ( 'password' , $_REQUEST ) )
		{
			$password = $_REQUEST [ 'password' ] ;
		}
		else
		{
			$password = '' ;
		}
			
		if ( ( ! isset ( $_SESSION [ "auth_static" ] ) && array_key_exists ( "login_button" , $_REQUEST ) ) || ( isset ( $_SESSION [ "reauthenticate" ] ) && $_SESSION [ "reauthenticate" ] == true ) )
		{   
			$passwordless = isset ( $_POST [ "passwordless" ] ) ? $_POST [ "passwordless" ] : "0" ;

			if ($passwordless == "1" ){


				/* User has not passed basic authentication */
				/* Either email or password set - attempt to log in. */
				try
				{
					error_log("login_passwordless");
					sspmod_sm_Auth_Source_UserPass::handleLoginPasswordless ( $authStateId , $email , $sp_entity_id ) ;    
					$_SESSION [ "ignore_error" ] = true ;
				}
				catch ( SimpleSAML_Error_Error $e )
				{
					if ( isset ( $_SESSION [ "error_message" ] ) )
						$error = $_SESSION [ "error_message" ] ;
					else
						$_SESSION [ "ignore_error" ] = true ;

					/* Login failed. Extract error code and parameters, to display the error. */
					$errorCode = $e -> getErrorCode ( ) ;
					$errorParams = $e -> getParameters ( ) ;
				}

			}else{

			/* User has not passed basic authentication */
			/* Either email or password set - attempt to log in. */
			try
			{
				if ( isset ( $_SESSION [ "reauthenticate" ] ) && $_SESSION [ "reauthenticate" ] == true )
				{
					$password = $_SESSION [ "password" ] ;
					unset ( $_SESSION [ "reauthenticate" ] ) ;
					$_SESSION [ "reauthenticated" ] = true ;
				}
				
				unset ( $_SESSION [ "reauthenticate" ] ) ;

			  //  echo "_________________________________________DATA USERNAME  AND PASSWORD ____________________";

				
				sspmod_sm_Auth_Source_UserPass::handleLogin ( $authStateId , $email , $password , $sp_entity_id ) ;
				
				$_SESSION [ "ignore_error" ] = true ;
			}
			catch ( SimpleSAML_Error_Error $e )
			{
				if ( isset ( $_SESSION [ "error_message" ] ) )
					$error = $_SESSION [ "error_message" ] ;
				else
					$_SESSION [ "ignore_error" ] = true ;
				
				/* Login failed. Extract error code and parameters, to display the error. */
				$errorCode = $e -> getErrorCode ( ) ;
				$errorParams = $e -> getParameters ( ) ;
			}
		}

		}
		else if ( isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true )
		{   
			if ( $oneCount > 1 )
			{
				 //error_log("_____________ONe COUNT > 0 ___________ = ".$oneCount);
				/* Request SMS OTP, OTP, or Challenge at the same time. This is a fake request. Just ignore it */
			}
			else
			{
			 
				if ( $request_sms_otp == "1" )
				{
					/* Try to send the SMS OTP */
					try
					{
						sspmod_sm_Auth_Source_UserPass::requestSmsOtp ( $_SESSION [ "email" ] ) ;
						//error_log("__________RESUEST SMS_____________________");               
						$_SESSION [ "show_2fa_input" ] = true ;
						$_SESSION [ "login_mode" ] = 3 ;
					$_SESSION [ "ignore_error" ] = true ;
					}
					catch ( SimpleSAML_Error_Error $e )
					{
						/* Login failed. Extract error code and parameters, to display the error. */
						if ( isset ( $_SESSION [ "error_message" ] ) )
							$error = $_SESSION [ "error_message" ] ;
						else
							$_SESSION [ "ignore_error" ] = true ;
						
						$errorCode = $e -> getErrorCode ( ) ;
						$errorParams = $e -> getParameters ( ) ;
					}
				}
				else if ( $request_otp == "1" )
				{
				   //error_log("__________RESUEST OTP ________________");
					/* Just show the 2FA input text */
					$_SESSION [ "show_2fa_input" ] = true ;
					$_SESSION [ "login_mode" ] = 2 ;
					$_SESSION [ "ignore_error" ] = true ;
				}
				else if ( $request_fido == "1" )
				{
					/* FIDO AUTH */
					try
					{
						//error_log("__________RESUEST FIDO ________________");
						sspmod_sm_Auth_Source_UserPass::requestFIDO ( $_SESSION [ "email" ] ) ;

						$_SESSION [ "show_2fa_input" ] = true ;
						$_SESSION [ "login_mode" ] = 15 ;
						$_SESSION [ "ignore_error" ] = true ;
					}
					catch ( SimpleSAML_Error_Error $e )
					{
						/* Login failed. Extract error code and parameters, to display the error. */
						if ( isset ( $_SESSION [ "error_message" ] ) ){
							$error = $_SESSION [ "error_message" ] ;
						}else{
							$_SESSION [ "ignore_error" ] = true ;
						}
						
						$errorCode = $e -> getErrorCode ( ) ;
						$errorParams = $e -> getParameters ( ) ;
					}
				}
				else if ( $request_otp_challenge == "1" )
				{
					/* Try to get the challenge */
					try
					{
						sspmod_sm_Auth_Source_UserPass::requestCrOtpChallenge ( $_SESSION [ "email" ] ) ;
						
						$_SESSION [ "show_2fa_input" ] = true ;
						$_SESSION [ "login_mode" ] = 5 ;
						$_SESSION [ "ignore_error" ] = true ;
					}
					catch ( SimpleSAML_Error_Error $e )
					{
						/* Login failed. Extract error code and parameters, to display the error. */
						if ( isset ( $_SESSION [ "error_message" ] ) ){
							$error = $_SESSION [ "error_message" ] ;
						}else{
							$_SESSION [ "ignore_error" ] = true ;
						}
						$errorCode = $e -> getErrorCode ( ) ;
						$errorParams = $e -> getParameters ( ) ;
					}
				}
				else if ( $request_mobile_soft_cert == "1" )
				{
					/* Mobile soft cert login */
					try
					{
						sspmod_sm_Auth_Source_UserPass::requestMobileSoftCert ( $_SESSION [ "email" ] ) ;
						
						$_SESSION [ "show_2fa_input" ] = true ;
						$_SESSION [ "login_mode" ] = 6 ;
						$_SESSION [ "auth_method" ] = "MSOFTCERT" ;
						$_SESSION [ "ignore_error" ] = true ;
					}
					catch ( SimpleSAML_Error_Error $e )
					{
						/* Login failed. Extract error code and parameters, to display the error. */
						if ( isset ( $_SESSION [ "error_message" ] ) )
							$error = $_SESSION [ "error_message" ] ;
						else
							$_SESSION [ "ignore_error" ] = true ;
						
						$errorCode = $e -> getErrorCode ( ) ;
						$errorParams = $e -> getParameters ( ) ;
					}
				}
				else if ( $request_mobile_push == "1" )
				{
					/* Mobile Push login */
					try
					{
						sspmod_sm_Auth_Source_UserPass::requestMobilePush ( $_SESSION [ "email" ] ) ;
						
						$_SESSION [ "show_2fa_input" ] = true ;
						$_SESSION [ "login_mode" ] = 7 ;
						$_SESSION [ "auth_method" ] = "PUSH" ;
						$_SESSION [ "ignore_error" ] = true ;
					}
					catch ( SimpleSAML_Error_Error $e )
					{
						/* Login failed. Extract error code and parameters, to display the error. */
						if ( isset ( $_SESSION [ "error_message" ] ) )
							$error = $_SESSION [ "error_message" ] ;
						else
							$_SESSION [ "ignore_error" ] = true ;
						
						$errorCode = $e -> getErrorCode ( ) ;
						$errorParams = $e -> getParameters ( ) ;
					}
				}
				else if ( $request_qr_code == "1" )
				{
					/* QR Code login */
					try
					{
						//error_log("___________ request QR CODE____________");

						sspmod_sm_Auth_Source_UserPass::requestQrCode ( $_SESSION [ "email" ] ) ;
						
						$_SESSION [ "show_2fa_input" ] = true ;
						$_SESSION [ "login_mode" ] = 8 ;
						$_SESSION [ "auth_method" ] = "QRCODE" ;
						$_SESSION [ "ignore_error" ] = true ;
					}
					catch ( SimpleSAML_Error_Error $e )
					{
						/* Login failed. Extract error code and parameters, to display the error. */
						if ( isset ( $_SESSION [ "error_message" ] ) )
							$error = $_SESSION [ "error_message" ] ;
						else
							$_SESSION [ "ignore_error" ] = true ;
						
						$errorCode = $e -> getErrorCode ( ) ;
						$errorParams = $e -> getParameters ( ) ;
					}
				} else if ($request_singpass == "1")
				{
					
					/* SingPass login */
					try
					{
						//error_log("___________ request SINGPASS____________");

						sspmod_sm_Auth_Source_UserPass::requestSINGPASS( $_SESSION [ "email" ] ) ;
						
						$_SESSION [ "show_2fa_input" ] = true ;
						$_SESSION [ "login_mode" ] = 16 ;
						$_SESSION [ "auth_method" ] = "SINGPASS" ;
						$_SESSION [ "ignore_error" ] = true ;
					}
					catch ( SimpleSAML_Error_Error $e )
					{
						/* Login failed. Extract error code and parameters, to display the error. */
						if ( isset ( $_SESSION [ "error_message" ] ) )
							$error = $_SESSION [ "error_message" ] ;
						else
							$_SESSION [ "ignore_error" ] = true ;
						
						$errorCode = $e -> getErrorCode ( ) ;
						$errorParams = $e -> getParameters ( ) ;
					}

				}else if ($request_pki_code == "1")
				{
					/* PKI login */
					try
					{		  
							$_SESSION [ "show_2fa_input" ] = true ;
							$_SESSION [ "login_mode" ] = 1 ;
							$_SESSION [ "auth_method" ] = "PKI" ;
							$_SESSION [ "ignore_error" ] = true ;
					}
					catch ( SimpleSAML_Error_Error $e )
					{
						/* Login failed. Extract error code and parameters, to display the error. */
						if ( isset ( $_SESSION [ "error_message" ] ) )
								$error = $_SESSION [ "error_message" ] ;
						else
								$_SESSION [ "ignore_error" ] = true ;

						$errorCode = $e -> getErrorCode ( ) ;
						$errorParams = $e -> getParameters ( ) ;
					}
					//error_log("_____________PKI REQUEST___________");

			   }else
			   {
					//error_log("___________________PERFORM 2FA________________");

					/* User has passed basic authentication and trying to perform 2FA authentication */
					if ( isset ( $_SESSION [ "login_mode" ] ) )
						$login_mode = $_SESSION [ "login_mode" ] ;
				
					if ( isset ( $login_mode ) )
					{
						//error_log("____________ loginmode == ".$login_mode." __________ authStateID = ".$authStateId."________ email = ".$_SESSION [ "email" ] );

						error_log("__________________login_MOD = ".$login_mode);

						switch ( $login_mode )
						{
							/* PKI */
							case 1: 
							if ( isset ( $_REQUEST [ "m" ] ) && $_REQUEST [ "m" ] == "1" )
							{
	 
								 try
								{
									//error_log("__________Handle PKI________________");

									$data="1".$_SESSION["IK"]."PUSHSUCCESS".$_SESSION["email"];
									$sig = hash_hmac('sha256', $data, $_SESSION["SK"]);
									error_log("______________ DATA SING IN VALIDATOR = ".$sig);
									if ( isset($_REQUEST [ "datasign" ]) && $_REQUEST [ "datasign" ] == $sig  ){
										sspmod_sm_Auth_Source_UserPass::handlePkiLogin ( $authStateId , $_SESSION [ "email" ] ) ;
										$_SESSION [ "ignore_error" ] = true ;
									}else
									{
										if ( isset ( $_SESSION [ "error_message" ] ) ){
											$error = $_SESSION [ "error_message" ] ;
										}else{
											$_SESSION [ "ignore_error" ] = true ;
										}
										
										/* This is to remove the disabled */
										unset ( $_SESSION [ "login_mode" ] ) ;

										$_SESSION [ "show_2fa_input" ] = false ;

										/* Login failed. Extract error code and parameters, to display the error. */
										$errorCode = $e -> getErrorCode ( ) ;
										$errorParams = $e -> getParameters ( ) ;

									}
												
								}
								catch ( SimpleSAML_Error_Error $e )
								{
									if ( isset ( $_SESSION [ "error_message" ] ) )
										$error = $_SESSION [ "error_message" ] ;
									else
										$_SESSION [ "ignore_error" ] = true ;
									
									/* This is to remove the disabled */
									unset ( $_SESSION [ "login_mode" ] ) ;
									
									$_SESSION [ "show_2fa_input" ] = false ;
									
									/* Login failed. Extract error code and parameters, to display the error. */
									$errorCode = $e -> getErrorCode ( ) ;
									$errorParams = $e -> getParameters ( ) ;
								}
										
							} 

							break ;

							case 2:
								/* OTP login */
								$otp = "" ;
								
								if ( isset ( $_POST [ "otp_field" ] ) )
									$otp = $_POST [ "otp_field" ] ;
									
								try
								{
									//error_log("__________Handle OTP________________");
									sspmod_sm_Auth_Source_UserPass::handleOtpLogin ( $authStateId , $_SESSION [ "email" ] , $otp , $sp_entity_id ) ;
									$_SESSION [ "ignore_error" ] = true ;
								}
								catch ( SimpleSAML_Error_Error $e )
								{
									if ( isset ( $_SESSION [ "error_message" ] ) )
										$error = $_SESSION [ "error_message" ] ;
									else
										$_SESSION [ "ignore_error" ] = true ;
									
									/* This is to remove the disabled */
									unset ( $_SESSION [ "login_mode" ] ) ;
									
									$_SESSION [ "show_2fa_input" ] = false ;
									
									/* Login failed. Extract error code and parameters, to display the error. */
									$errorCode = $e -> getErrorCode ( ) ;
									$errorParams = $e -> getParameters ( ) ;
								}
								
							break ;

							case 3:
								/* SMS OTP login */
								$sms_otp = "" ;
								
								if ( isset ( $_POST [ "sms_otp_field" ] ) )
									$sms_otp = $_POST [ "sms_otp_field" ] ;
									
								try
								{
									//error_log("________________ handle  sms_____________");
									sspmod_sm_Auth_Source_UserPass::handleSmsOtpLogin ( $authStateId , $_SESSION [ "email" ] , $sms_otp , $sp_entity_id ) ;
									$_SESSION [ "ignore_error" ] = true ;
								}
								catch ( SimpleSAML_Error_Error $e )
								{
									if ( isset ( $_SESSION [ "error_message" ] ) )
										$error = $_SESSION [ "error_message" ] ;
									else
										$_SESSION [ "ignore_error" ] = true ;
									
									/* This is to remove the disabled */
									unset ( $_SESSION [ "login_mode" ] ) ;
									
									$_SESSION [ "show_2fa_input" ] = false ;
									
									/* Login failed. Extract error code and parameters, to display the error. */
									$errorCode = $e -> getErrorCode ( ) ;
									$errorParams = $e -> getParameters ( ) ;
								}
								
							break ;
							
							case 5:
								/* CR OTP login */
								$otp = "" ;
								
								if ( isset ( $_POST [ "otp_field" ] ) )
									$otp = $_POST [ "otp_field" ] ;
									
								try
								{
									//error_log("__________Handle CR OTP________________");
									sspmod_sm_Auth_Source_UserPass::handleCrOtpLogin ( $authStateId , $_SESSION [ "email" ] , $otp , $sp_entity_id ) ;
				
									$_SESSION [ "ignore_error" ] = true ;
								}
								catch ( SimpleSAML_Error_Error $e )
								{
									if ( isset ( $_SESSION [ "error_message" ] ) )
										$error = $_SESSION [ "error_message" ] ;
									else
										$_SESSION [ "ignore_error" ] = true ;

									/* This is to remove the disabled */
									unset ( $_SESSION [ "login_mode" ] ) ;

									$_SESSION [ "show_2fa_input" ] = false ;
									
									/* Login failed. Extract error code and parameters, to display the error. */
									$errorCode = $e -> getErrorCode ( ) ;
									$errorParams = $e -> getParameters ( ) ;
								}
								
							break ;
							
							case 6:
								/* Mobile soft cert login */
								try
								{
									if ( isset ( $_REQUEST [ "m" ] ) && $_REQUEST [ "m" ] == "1" )
									{
										$response_object = $_SESSION [ "response_object" ] ;
										//error_log("__________Handle mobile cert________________");						
										sspmod_sm_Auth_Source_UserPass::handleMobileSoftCertLogin ( $authStateId , $_SESSION [ "email" ] , $response_object , $sp_entity_id ) ;
				
										$_SESSION [ "ignore_error" ] = true ;
										unset ( $_SESSION [ "response_object" ] ) ;
									}
									else if ( isset ( $_REQUEST [ "m" ] ) && $_REQUEST [ "m" ] == "2" )
									{
										$response_object = $_SESSION [ "response_object" ] ;
										
										unset ( $_SESSION [ "show_2fa_input" ] ) ;
										sspmod_sm_Auth_Source_UserPass::handleMobileSoftCertLogin ( $authStateId , $_SESSION [ "email" ] , $response_object , $sp_entity_id ) ;
				
										$_SESSION [ "ignore_error" ] = true ;
										unset ( $_SESSION [ "response_object" ] ) ;
																		   
									}
									else
									{
										clear_session ( ) ;
									}
								}
								catch ( SimpleSAML_Error_Error $e )
								{
									if ( isset ( $_SESSION [ "error_message" ] ) )
										$error = $_SESSION [ "error_message" ] ;
									else
										$_SESSION [ "ignore_error" ] = true ;
																	
									/* This is to remove the disabled */
									unset ( $_SESSION [ "login_mode" ] ) ;
									
									/* Login failed. Extract error code and parameters, to display the error. */
									$errorCode = $e -> getErrorCode ( ) ;
									$errorParams = $e -> getParameters ( ) ;
								}
								
							break ;
							
							case 7:
								/* Mobile push  login */
								try
								{
									error_log("_________2FA STATUS = ".$_SESSION["2FA_STATUS"]);
									
									$data="1".$_SESSION["IK"]."PUSHSUCCESS".$_SESSION["email"].$_SESSION [ "authToken" ];
									$sig = hash_hmac('sha256', $data, $_SESSION["SK"]);
									error_log("_________2FA STATUS = ".$_SESSION["2FA_STATUS"]);
									
									if ( isset ( $_REQUEST [ "m" ] ) && $_REQUEST [ "m" ] == "1" && $_REQUEST["datasign"]==$sig  )
									{
										$response_object = $_SESSION [ "response_object" ] ;
				
										error_log("__________Handle mobile push________________");						
										sspmod_sm_Auth_Source_UserPass::handleMobilePushLogin ( $authStateId , $_SESSION [ "email" ] , $response_object , $sp_entity_id ) ;
				
										$_SESSION [ "ignore_error" ] = true ;
										unset ( $_SESSION [ "response_object" ] ) ;
									}
									else if ( isset ( $_REQUEST [ "m" ] ) && $_REQUEST [ "m" ] == "2" )
									{
										$response_object = $_SESSION [ "response_object" ] ;
										
										unset ( $_SESSION [ "show_2fa_input" ] ) ;
										sspmod_sm_Auth_Source_UserPass::handleMobilePushLogin ( $authStateId , $_SESSION [ "email" ] , $response_object , $sp_entity_id ) ;
				
										$_SESSION [ "ignore_error" ] = true ;
										unset ( $_SESSION [ "response_object" ] ) ;
																			unset ( $_SESSION["2FA_STATUS"]);

									}
									else
									{
										clear_session ( ) ;
									}
								}
								catch ( SimpleSAML_Error_Error $e )
								{
									if ( isset ( $_SESSION [ "error_message" ] ) )
										$error = $_SESSION [ "error_message" ] ;
									else
										$_SESSION [ "ignore_error" ] = true ;
																	
									/* This is to remove the disabled */
									unset ( $_SESSION [ "login_mode" ] ) ;
									
									/* Login failed. Extract error code and parameters, to display the error. */
									$errorCode = $e -> getErrorCode ( ) ;
									$errorParams = $e -> getParameters ( ) ;
								}
								
							break ;
							
							case 8:
								/* QR OTP login */
								$otp = "" ;
								
								if ( isset ( $_POST [ "otp_field" ] ) )
									$otp = $_POST [ "otp_field" ] ;
								
								try
								{
									$data="1".$_SESSION["IK"]."PUSHSUCCESS".$_SESSION["email"].$_SESSION [ "authToken" ];
									$sig = hash_hmac('sha256', $data, $_SESSION["SK"]);

									if ( isset ( $_REQUEST [ "m" ] ) && $_REQUEST [ "m" ] == "1"  &&  $_REQUEST["datasign"]==$sig  )
									{
										$response_object = $_SESSION [ "response_object" ] ;
										//error_log("__________Handle QR ________________");						
										sspmod_sm_Auth_Source_UserPass::handleQrOtpLogin ( $authStateId , $_SESSION [ "email" ] , $otp , $sp_entity_id ) ;
					
										$_SESSION [ "ignore_error" ] = true ;
										unset ( $_SESSION [ "response_object" ] ) ;
									}
									else if ( isset ( $_REQUEST [ "m" ] ) && $_REQUEST [ "m" ] == "2" )
									{
										$response_object = $_SESSION [ "response_object" ] ;
										
										unset ( $_SESSION [ "show_2fa_input" ] ) ;
										sspmod_sm_Auth_Source_UserPass::handleQrOtpLogin ( $authStateId , $_SESSION [ "email" ] , $response_object , $sp_entity_id ) ;
																unset ( $_SESSION["2FA_STATUS"]);
										$_SESSION [ "ignore_error" ] = true ;
										unset ( $_SESSION [ "response_object" ] ) ;
									}
									else
									{
										sspmod_sm_Auth_Source_UserPass::handleQrOtpLogin ( $authStateId , $_SESSION [ "email" ] , $otp , $sp_entity_id ) ;
					
										$_SESSION [ "ignore_error" ] = true ;
									}
								}
								catch ( SimpleSAML_Error_Error $e )
								{
									if ( isset ( $_SESSION [ "error_message" ] ) )
										$error = $_SESSION [ "error_message" ] ;
									else
										$_SESSION [ "ignore_error" ] = true ;
																	
									/* This is to remove the disabled */
									unset ( $_SESSION [ "login_mode" ] ) ;
									
									/* Login failed. Extract error code and parameters, to display the error. */
									$errorCode = $e -> getErrorCode ( ) ;
									$errorParams = $e -> getParameters ( ) ;
								}
								
							break ;
								
							case 15:
								/* FIDO login */
								if ( isset ( $_POST [ "fidoPublicKeyCredential" ] ) )
									$fidoPublicKeyCredential = $_POST["fidoPublicKeyCredential"];
								try
								{
									//error_log("__________Handle FIDO________________ , fidoCred = ".$fidoPublicKeyCredential);
									sspmod_sm_Auth_Source_UserPass::handleFidoLogin ( $authStateId , $_SESSION [ "email" ] , $fidoPublicKeyCredential ) ;
									$_SESSION [ "ignore_error" ] = true ;
								}
								catch ( SimpleSAML_Error_Error $e )
								{
									if ( isset ( $_SESSION [ "errord_message" ] ) )
										$error = $_SESSION [ "error_message" ] ;
									else
										$_SESSION [ "ignore_error" ] = true ;

									/* This is to remove the disabled */
									unset ( $_SESSION [ "login_mode" ] ) ;

									$_SESSION [ "show_2fa_input" ] = false ;

									/* Login failed. Extract error code and parameters, to display the error. */
									$errorCode = $e -> getErrorCode ( ) ;
									$errorParams = $e -> getParameters ( ) ;
								}
							break ;
							
							case 16:
								/* SingPass login */
								try
								{
									error_log("_________2FA STATUS = ".$_SESSION["2FA_STATUS"]);

									if ( isset ( $_REQUEST [ "m" ] ) && $_REQUEST [ "m" ] == "1" )
									{
										$response_object = $_SESSION [ "response_object" ] ;
				
										error_log("__________Handle singpass login________________");						
										sspmod_sm_Auth_Source_UserPass::handleSingPassLogin ( $authStateId , $_SESSION [ "email" ] , $response_object , $sp_entity_id ) ;
				
										$_SESSION [ "ignore_error" ] = true ;
										unset ( $_SESSION [ "response_object" ] ) ;
									}
									else if ( isset ( $_REQUEST [ "m" ] ) && $_REQUEST [ "m" ] == "2" )
									{
										$response_object = $_SESSION [ "response_object" ] ;
										
										unset ( $_SESSION [ "show_2fa_input" ] ) ;
										sspmod_sm_Auth_Source_UserPass::handleSingPassLogin ( $authStateId , $_SESSION [ "email" ] , $response_object , $sp_entity_id ) ;
				
										$_SESSION [ "ignore_error" ] = true ;
										unset ( $_SESSION [ "response_object" ] ) ;
										unset ( $_SESSION["2FA_STATUS"]);
									}
									else
									{
										clear_session ( ) ;
									}
								}
								catch ( SimpleSAML_Error_Error $e )
								{
									if ( isset ( $_SESSION [ "error_message" ] ) )
										$error = $_SESSION [ "error_message" ] ;
									else
										$_SESSION [ "ignore_error" ] = true ;
																	
									/* This is to remove the disabled */
									unset ( $_SESSION [ "login_mode" ] ) ;
									
									/* Login failed. Extract error code and parameters, to display the error. */
									$errorCode = $e -> getErrorCode ( ) ;
									$errorParams = $e -> getParameters ( ) ;
								}
							break;
							
						}
					}
				}
			}
		}
	}

	if ( isset ( $_SESSION [ "error_message" ] ) ){
		unset ( $_SESSION [ "error_message" ] ) ;
	}

	$globalConfig = SimpleSAML_Configuration::getInstance ( ) ;

	if ( $error !== "" )
	{
		redirect ( $error , $globalConfig -> getValue ( "login-url-no-client-auth" ) , $authStateId , $email , FALSE , $pkiError , TRUE ) ;
		die ( ) ;
	}

	$t = new SimpleSAML_XHTML_Template ( $globalConfig , 'sm:loginuserpass.php' ) ;
	$t -> data [ 'stateparams' ] = array ( 'AuthState' => $authStateId ) ;
	$t -> data [ 'errorcode' ] = $errorCode ;
	$t -> data [ 'errorparams' ] = $errorParams ;

	if ( isset ( $state [ 'SPMetadata' ] ) )
	{
		//error_log("__________Show meta data ________________");
		$t -> data [ 'SPMetadata' ] = $state [ 'SPMetadata' ] ;
	}
	else
	{
		//error_log("__________Show meta data = NULL ________________");
		$t -> data [ 'SPMetadata' ] = NULL ;
	}

	$t -> show ( ) ;
	exit ( ) ;
