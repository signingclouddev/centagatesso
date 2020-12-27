<?php

    if ( session_status ( ) != PHP_SESSION_ACTIVE )
         session_start ( ) ;


    include_once ( "httpful.phar" ) ;

    class sspmod_sm_Auth_Source_UserPass extends SimpleSAML_Auth_Source
    {
        const STAGEID = 'sspmod_sm_Auth_UserPass.state' ;

        const AUTHID = 'sspmod_sm_Auth_UserPass.AuthId' ;

        public function __construct ( $info , &$config )
        {
            assert ( 'is_array ( $info )' ) ;
            assert ( 'is_array ( $config )' ) ;

            parent::__construct ( $info , $config ) ;
        }


        
        
        private static function build_second_string ( $seconds )
        {
            $second_string = "" ;
            
            if ( $seconds > 0 )
            {
                if ( $seconds == 1 )
                    $second_string = $seconds . " second" ;
                else
                    $second_string = $seconds . " seconds" ;
            }
            
            return $second_string ;
        }
        
        private static function build_minute_string ( $seconds )
        {
            $minute_string = "" ;
            
            if ( $seconds > 0 )
            {
                $minute = floor ( $seconds / 60 ) ;
                
                if ( $minute > 0 )
                {
                    if ( $minute == 1 )
                        $minute_string = $minute . " minute" ;
                    else
                        $minute_string = $minute . " minutes" ;
                }
                
                $second = $seconds % 60 ;
                
                if ( $second > 0 )
                    $minute_string .= " " . sspmod_sm_Auth_Source_UserPass::build_second_string ( $second ) ;
            }
            
            return $minute_string ;
        }
        
        private static function build_hour_string ( $seconds )
        {
            $hour_string = "" ;
            
            if ( $seconds > 0 )
            {
                $hour = floor ( $seconds / 3600 ) ;
                
                if ( $hour > 0 )
                {
                    if ( $hour == 1 )
                        $hour_string = $hour . " hour" ;
                    else
                        $hour_string = $hour . " hours" ;
                }
                
                $seconds = $seconds % 3600 ;
                
                if ( $seconds > 0 )
                    $hour_string .= " " . sspmod_sm_Auth_Source_UserPass::build_minute_string ( $seconds ) ;
            }
            
            return $hour_string ;
        }
        
        private static function mask_phone_number ( $phone )
        {
            $length = strlen ( $phone ) ;
            
            for ( $i = $length - 5 ; $i > 0 ; $i -- )
                $phone [ $i ] = "X" ;

            $phone = substr_replace ( $phone , " " , $length - 4 , 0 ) ;
            $phone = substr_replace ( $phone , "-" , 3 , 0 ) ;
            
            return $phone ;
        }
	
	private function clear_session ( )
{

                unset ( $_SESSION [ "pwd" ] ) ;
    		unset ( $_SESSION [ "auth_method" ] ) ;
    		unset ( $_SESSION [ "email" ] ) ;
    		unset ( $_SESSION [ "authToken" ] ) ;
    		unset ( $_SESSION [ "show_2fa_input" ] ) ;
    		unset ( $_SESSION [ "otp_challenge" ] ) ;
    		unset ( $_SESSION [ "otp_enabled" ] ) ;
    		unset ( $_SESSION [ "pki_enabled" ] ) ;
    		unset ( $_SESSION [ "sms_otp_enabled" ] ) ;
    		unset ( $_SESSION [ "cr_otp_enabled" ] ) ;
    		unset ( $_SESSION [ "mobile_softcert_enabled" ] ) ;
        	unset ( $_SESSION [ "mobile_push_enabled" ] ) ;
    		unset ( $_SESSION [ "auth_static" ] ) ;
        	unset ( $_SESSION [ "multi_step_auth" ] ) ;
        	unset ( $_SESSION [ "login_mode" ] ) ;
        	unset ( $_SESSION [ "num_of_2fa" ] ) ;
        	unset ( $_SESSION [ "ignore_error" ] ) ;
        	unset ( $_SESSION [ "user_id" ] ) ;
        	unset ( $_SESSION [ "auth_secret" ] ) ;
        	unset ( $_SESSION [ "qr_otp_challenge" ] ) ;
        	unset ( $_SESSION [ "qr_plain_text" ] ) ;
        	unset ( $_SESSION [ "real_email" ] ) ;
        	unset ( $_SESSION [ "password" ] ) ;
                unset ( $_SESSION [ "error_message" ] );
		unset ( $_SESSION [ "sessionTimeout" ] );
		unset ( $_SESSION [ "UserEmail"] );
		unset ( $_SESSION [ "UserID" ] );
                //unset ( $_SESSION [ "stepupAuth"] );
	}

	
		private static function clear_session_leave_2fa ( )
		{
			unset ( $_SESSION [ "show_2fa_input" ] ) ;
			unset ( $_SESSION [ "otp_challenge" ] ) ;
			unset ( $_SESSION [ "multi_step_auth" ] ) ;
			unset ( $_SESSION [ "login_mode" ] ) ;
			unset ( $_SESSION [ "ignore_error" ] ) ;
			unset ( $_SESSION [ "qr_otp_challenge" ] ) ;
			unset ( $_SESSION [ "qr_plain_text" ] ) ;
                        unset ( $_SESSION [ "auth_method" ] ) ;			
			if ( isset ( $_SESSION [ "reauthenticated" ] ) && $_SESSION [ "reauthenticated" ] == true )
		             unset ( $_SESSION [ "reauthenticated" ] ) ;
			else
			     $_SESSION [ "reauthenticate" ] = true ;
		}

        private function determineAuthMethods ( $auth_methods_array )
        {
            $_SESSION [ "otp_enabled" ] = false ;
            $_SESSION [ "pki_enabled" ] = false ;
            $_SESSION [ "fido_enabled" ] = false ;
            $_SESSION [ "sms_otp_enabled" ] = false ;
            $_SESSION [ "cr_otp_enabled" ] = false ;
	    $_SESSION [ "advanced_otp_enabled" ] = false ;
            $_SESSION [ "mobile_softcert_enabled" ] = false ;
	    $_SESSION [ "mobile_push_enabled" ] = false ;
	    $_SESSION [ "qrcode_enabled" ] = false ;
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
                        break;

	            case "FIDO":
                        $_SESSION [ "fido_enabled" ] = true ;
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
                    
                    case "PUSH":
                        $_SESSION [ "mobile_push_enabled" ] = true ;
						$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
						
                        break ;
				
      		    case "QRCODE":
						$_SESSION [ "qrcode_enabled" ] = true ;
						$_SESSION [ "num_of_2fa" ] = $_SESSION [ "num_of_2fa" ] + 1 ;
						
						break ;
                }
            }
        }

		public static function savePersistentId ( $username , $authToken , $id , $persistentId )
		{
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $authToken )' ) ;
            assert ( 'is_string ( $id )' ) ;
            assert ( 'is_string ( $persistentId )' ) ;

            $config = SimpleSAML_Configuration::getInstance ( ) ;

			$auth_secret = $_SESSION [ "auth_secret" ] ;
			
			if ( isset ( $auth_secret ) && strlen ( $auth_secret ) > 0 )
			{
				$token = hash_hmac ( "sha256" , $username . $authToken , $auth_secret ) ;
				
				$rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/CentagateWS/webresources/user/setPid/$username/$token" ;

				$params = array (
					"id"  => $id,
					"pid" => $persistentId,
				) ;
				
				$json = json_encode ( $params ) ;

				try
				{
					\Httpful\Request::put ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
				} 
				catch ( Httpful\Exception\ConnectionErrorException $ex )
				{
					/* Connection error. Just say invalid username and password */
					$_SESSION [ "error_message" ] = "centagateDown" ;
					
					
				}
			}
		}
		
        public function authenticate ( &$state )
        {
            assert ( 'is_array ( $state )' ) ;

            $state [ self::AUTHID ] = $this -> authId ;
            
            $id = SimpleSAML_Auth_State::saveState ( $state , self::STAGEID ) ;

             
            $_SESSION["IK"]=$state["SPMetadata"]["integrationKey"];
            $_SESSION["SK"]=$state["SPMetadata"]["secretKey"];    
            
            $_SESSION["logo_url"]=$state["SPMetadata"]["logo_url"];
            $_SESSION["body_skin"]=$state["SPMetadata"]["body_skin"];
            $color=$_SESSION["body_skin"];
            $logo_url=$_SESSION["logo_url"];

            if (empty($color)){
              $_SESSION["body_skin"]="background : radial-gradient(#eff3fc, #f2f2eb) rgba(34,34,40,0.94)";
              $_SESSION["form_color"]="#eff3fc";
              $_SESSION["logo_url"]="images/ic_launcher.png";
            }else{
               $_SESSION["body_skin"]="background : radial-gradient(#f2f2eb ,#".$color.") rgba(34,34,40,0.94)";
               if (empty($logo_url)){
                $_SESSION["logo_url"]="images/ic_launcher.png";
             }               
           }
            //$_SESSION["forceAuth"]=$state["SPMetadata"]["forceAuth"];
            $_SESSION["nameID"]=$state["SPMetadata"]["nameID"];
            $_SESSION["sessionTimeout"]=$state["SPMetadata"]["sessionTimeout"];
            $_SESSION["stepupAuth"]=$state["SPMetadata"]["stepupAuth"];

            error_log("___________________ NAME ID in auth method = ".$_SESSION["nameID"]);
            error_log("______________________ stepupAuth = ".$_SESSION["stepupAuth"]);

 
            $url = SimpleSAML_Module::getModuleURL ( 'sm/loginuserpass.php' ) ;
	    $error = "" ;
			
			if ( isset ( $state [ "err" ]  ) )
				$error = $state [ "err" ] ;
			
            $params = array ( "err" => $error , 'AuthState' => $id ) ;

            SimpleSAML_Utilities::redirect ( $url , $params ) ;
           
            assert ( 'FALSE' ) ;
        }



    private function sessionStart()
    {
        $cacheLimiter = session_cache_limiter();
        if (headers_sent()) {
            /*
             * session_start() tries to send HTTP headers depending on the configuration, according to the
             * documentation:
             *
             *      http://php.net/manual/en/function.session-start.php
             *
             * If headers have been already sent, it will then trigger an error since no more headers can be sent.
             * Being unable to send headers does not mean we cannot recover the session by calling session_start(),
             * so we still want to call it. In this case, though, we want to avoid session_start() to send any
             * headers at all so that no error is generated, so we clear the cache limiter temporarily (no headers
             * sent then) and restore it after successfully starting the session.
             */
            session_cache_limiter('');
        }
        @session_start();
        session_cache_limiter($cacheLimiter);
    }



       protected function loginPasswordless( $username , $requester_sp_id )
         {

           assert ( 'is_string ( $username )' ) ;
           

            $userId = "";
            $email = "";

            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/auth/adaptive/" ;
            $integration_key = $_SESSION["IK"]; //"e5b720fd4a7ab9008ae2e096be6ac729b1b5f5d60c3112468957603db90aee50" ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;


            $params = array (
                "username"       => $username,
                "authResult"       => "true",
                "integrationKey" => $integration_key,
                "supportFido"    => "true",
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"           => hash_hmac ( "sha256" , $username . "true" . $integration_key . $unix_timestamp ."true". $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey)
            ) ;

           //"K4w4T5UtNSa0"
            $json = json_encode ( $params ) ;

            try
            {

                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                $response_body = get_object_vars ( $response -> body ) ;
                $response_object = json_decode ( $response_body [ "object" ] ) ;
                if ( $response -> code == 200 )
                {
                    if ( $response -> body -> code === "0" )
                    {
                                                   $sp_id_list = $response_object -> { "spIdList" } ;
                                $defAccId = $response_object->{"defAccId"};
                                //error_log($defAccId);
                                $_SESSION [ "devID" ]=$defAccId;
                                $defDeviceName=$response_object->{"defDeviceName"};
                                $_SESSION["defDeviceName"]=$defDeviceName;
                                if (empty($defDeviceName)){
                                  $_SESSION["devName"]=$response_object->{"defAccId"};
                                }else{
                                  $_SESSION["devName"]=$defDeviceName;
                                }
                                $countDevList=isset($response_object->{"countDeviceList"}) ? $response_object->{"countDeviceList"} : "";

                $userId = isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : "";
                $email = isset($response_object->{"email"}) ? $response_object->{"email"} : "";
                $uniqueId = isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : "";

                $_SESSION["UserEmail"]=$email;
                $_SESSION["UserID"]=$uniqueId;


                                                $sp_ids = json_decode ( $sp_id_list ) ;
                                                $sp_id_registered = false ;

                                                foreach ( $sp_ids as $sp_id )
                                                {
                                                        if ( isset ( $sp_id ) && strlen ( $sp_id ) > 0 && isset ( $sp_id ) && strlen ( $sp_id ) > 0 && $requester_sp_id === $sp_id )
                                                        {
                                                                $sp_id_registered = true ;
                                                                break ;
                                                        }
                                                }

                                                if ( ! $sp_id_registered )
                                                {
                                                        $_SESSION [ "error_message" ] = "spNotRegistered" ;

                                                        throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                                                }

                                         $_SESSION ["pwd"]="false";
                                         $_SESSION [ "authToken" ] = $response_object -> { "authToken" } ;
                                         $_SESSION [ "user_id" ] = $response_object -> { "userId" } ;
                                         $_SESSION [ "username" ] = $username ;
                                         $_SESSION [ "real_email" ] = $response_object -> { "email" } ;

                        if ( $response_object -> { "secretCode" } === "" )
                        {
                            /* Additional authentication is required */
                            $auth_methods = $response_object -> { "authMethods" } ;
                            $auth_methods_array = explode ( "," , $auth_methods ) ;

                            $use_system_password = $response_object -> { "useSystemPassword" } ;
                            $password_expired = $response_object -> { "passwordExpired" } ;

                            $this -> determineAuthMethods ( $auth_methods_array ) ;

                                                        if ( $use_system_password === "1" )
                                                                $_SESSION [ "error_message" ] = "defaultPassword" ;
                                                        else if ( $password_expired === "1" )
                                                                $_SESSION [ "error_message" ] = "expiredPassword" ;
                                                        else
                                                                $_SESSION [ "auth_static" ] = true ;

                            throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                        }
                         else
                             $_SESSION [ "auth_secret" ] = $response_object -> { "secretCode" } ;
                       
                    } else if ( $response -> body -> code === "1"  ){
                        
                          $_SESSION["pwd"]="true";              
                     } else if  ( $response -> body -> code === "23039"  ){
                                 /* Problem happened. Consider as wrong login. */
                        if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true )
                                                        $_SESSION [ "reset_login_form" ] = true ;

                        $_SESSION [ "error_message" ] = $response -> body -> code ;

                        throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
 

                   }
                }

          }
           catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid username and password */
                $_SESSION [ "error_message" ] = "centagateDown" ;

                throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
            }
           error_log("______________auth_static = ".$_SESSION [ "auth_static" ]);

            return array ( 'email' => array ( $email ), 'userId' => array($userId) , 'nameID' => array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"]) ) ;
 
         }

        protected function login ( $username , $password , $requester_sp_id )
        {
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $password )' ) ;

	    $userId = "";
	    $email = "";

            $config = SimpleSAML_Configuration::getInstance ( ) ;
            
            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/auth/authBasic/" ;
            $integration_key = $_SESSION["IK"]; //"e5b720fd4a7ab9008ae2e096be6ac729b1b5f5d60c3112468957603db90aee50" ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;

            
            $params = array (
                "username"       => $username,
                "password"       => $password,
                "integrationKey" => $integration_key,
                "supportFido"    => "true",
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"           => hash_hmac ( "sha256" , $username . $password . $integration_key . $unix_timestamp ."true". $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey) 
            ) ;

           //"K4w4T5UtNSa0"
            $json = json_encode ( $params ) ;
            
            try
            {
                
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                $response_body = get_object_vars ( $response -> body ) ;
                $response_object = json_decode ( $response_body [ "object" ] ) ;

		$userId = isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : "";
		$email = isset($response_object->{"email"}) ? $response_object->{"email"} : "";
                $uniqueId = isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : ""; 

                $_SESSION["UserEmail"]=$email;
                $_SESSION["UserID"]=$uniqueId;
               
                unset ( $_SESSION [ "auth_method" ] ) ;
                unset ( $_SESSION [ "login_mode" ] ) ;
                
                $nameID=$_SESSION["nameID"];
                
                if ( $response -> code == 200 )
                {
                    if ( $response -> body -> code === "0" )
                    {
				$sp_id_list = $response_object -> { "spIdList" } ;
		                
                                $defAccId = $response_object->{"defAccId"};
                                //error_log($defAccId);
                                $_SESSION [ "devID" ]=$defAccId;
                                $defDeviceName=$response_object->{"defDeviceName"};
                                $_SESSION["defDeviceName"]=$defDeviceName;
                                if (empty($defDeviceName)){
                                  $_SESSION["devName"]=$response_object->{"defAccId"};
                                }else{
                                  $_SESSION["devName"]=$defDeviceName;
                                }
                                $countDevList=isset($response_object->{"countDeviceList"}) ? $response_object->{"countDeviceList"} : "";

                                				
						$sp_ids = json_decode ( $sp_id_list ) ;
						$sp_id_registered = false ;
						
						foreach ( $sp_ids as $sp_id )
						{
							if ( isset ( $sp_id ) && strlen ( $sp_id ) > 0 && isset ( $sp_id ) && strlen ( $sp_id ) > 0 && $requester_sp_id === $sp_id )
							{
								$sp_id_registered = true ;
								break ;
							}
						}
						
						if ( ! $sp_id_registered )
						{
							$_SESSION [ "error_message" ] = "spNotRegistered" ;
							
							throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
						}
						
                        $_SESSION [ "authToken" ] = $response_object -> { "authToken" } ;
						$_SESSION [ "user_id" ] = $response_object -> { "userId" } ;
						$_SESSION [ "username" ] = $username ;
						$_SESSION [ "real_email" ] = $response_object -> { "email" } ;
                        
                        if ( $response_object -> { "secretCode" } === "" )
                        {
                            /* Additional authentication is required */
                            $auth_methods = $response_object -> { "authMethods" } ;
                            $auth_methods_array = explode ( "," , $auth_methods ) ;
							
							$use_system_password = $response_object -> { "useSystemPassword" } ;
							$password_expired = $response_object -> { "passwordExpired" } ;
    
                            $this -> determineAuthMethods ( $auth_methods_array ) ;                            
                                
							if ( $use_system_password === "1" )
								$_SESSION [ "error_message" ] = "defaultPassword" ;
							else if ( $password_expired === "1" )
								$_SESSION [ "error_message" ] = "expiredPassword" ;
							else
								$_SESSION [ "auth_static" ] = true ;
                            
                            throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                        }
						else
							$_SESSION [ "auth_secret" ] = $response_object -> { "secretCode" } ;
                    }
                    else
                    {
                        /* Problem happened. Consider as wrong login. */
                        $_SESSION [ "error_message" ] = $response -> body -> code ;
                        
                        throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                    $_SESSION [ "error_message" ] = "serverError" ;
                    
                    throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                }
            }} 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid username and password */
                $_SESSION [ "error_message" ] = "centagateDown" ;
                
                throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
            }

            error_log("______________auth_static = ".$_SESSION [ "auth_static" ]);

            return array ( 'email' => array ( $email ), 'userId' => array($userId) , 'nameID' => array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"]) ) ;
        }
		
	public function getSecurityImage ( $username )
        {
            assert ( 'is_string ( $username )' ) ;            

            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/CentagateWS/webresources/security/getUserLoginImage/" ;			
            
            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;

            $params = array (
                "username"       => $username,                
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"           => hash_hmac ( "sha256" , $username . $integration_key . $unix_timestamp . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;

            $json = json_encode ( $params ) ;			
            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                $response_body = get_object_vars ( $response -> body ) ;
                $response_object = json_decode ( $response_body [ "object" ] ) ;
                //error_log("_______________________ get security image = ".$response);
 
                return $response;
            } 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                                
                throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
            }
           
        }

        protected function loginSmsOtp ( $username , $smsOtp , $requester_sp_id )
        {
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $smsOtp )' ) ;

	    $userId = "";
	    $email = "";

            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/auth/authSmsOtp/" ;
            
            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;

            $params = array (
                "username"       => $username,
                "smsOtp"         => $smsOtp,
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"           => hash_hmac ( "sha256" , $username . $smsOtp . $integration_key . $unix_timestamp . $_SESSION [ "authToken" ] . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;
			
			if ( isset ( $_SESSION [ "authToken" ] ) )
				$params [ "authToken" ] = $_SESSION [ "authToken" ] ;

            $json = json_encode ( $params ) ;


                $nameID=$_SESSION["nameID"];
/*
                $dataAttr="";
                if ($nameID === "ID"){
                  $dataAttr=$uniqueId;
                 }else{
                     if ($nameID === "Email"){
                         $dataAttr=$email;
                     }else{
                         error_log("________________NAME ID IS EMPTY _____________");
                       }
                }
*/

            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                $response_body = get_object_vars ( $response -> body ) ;
                $response_object = json_decode ( $response_body [ "object" ] ) ;
                $userId = $_SESSION["UserID"];  //isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : ""; 
		$email =  $_SESSION["UserEmail"]; //isset($response_object->{"email"}) ? $response_object->{"email"} : "";
                if ( $response -> code == 200 )
                {
                    if ( $response -> body -> code === "0" )
                    {
						$sp_id_list = $response_object -> { "spIdList" } ;
						$sp_ids = json_decode ( $sp_id_list ) ;
						$sp_id_registered = false ;
						
						foreach ( $sp_ids as $sp_id )
						{
							if ( isset ( $sp_id ) && strlen ( $sp_id ) > 0 && $requester_sp_id === $sp_id )
							{
								$sp_id_registered = true ;
								break ;
							}
						}
						
						if ( ! $sp_id_registered )
						{
							$_SESSION [ "error_message" ] = "spNotRegistered" ;
							
							throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
						}
						
                        $_SESSION [ "authToken" ] = $response_object -> { "authToken" } ;
                        
                        if ( $response_object -> { "secretCode" } === "" )
                        {
                            /* Additional authentication is required */
                            $auth_methods = $response_object -> { "authMethods" } ;
                            $auth_methods_array = explode ( "," , $auth_methods ) ;
    
                            $this -> determineAuthMethods ( $auth_methods_array ) ;
							
							if ( $_SESSION [ "auth_static" ] && $response_object -> { "multiStepAuth" } === "true" )
								$_SESSION [ "multi_step_auth" ] = true ;
							else if ( $_SESSION [ "auth_static" ] && $response_object -> { "multiStepAuth" } === "blocked" )
								$_SESSION [ "error_message" ] = "riskDetected" ;
                                
                            throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                        }
						else
							$_SESSION [ "auth_secret" ] = $response_object -> { "secretCode" } ;
                    }
                    else
                    {
                        /* Problem happened. Consider as wrong login. */
						if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true )
							$_SESSION [ "reset_login_form" ] = true ;
						
                        $_SESSION [ "error_message" ] = $response -> body -> code ;
                        
                        throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                    }
                }
                else
                {
                    /* Problem happened. Consider as wrong login. */
                    $_SESSION [ "error_message" ] = "serverError" ;
                    
                    throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                }
            } 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid login */
                $_SESSION [ "error_message" ] = "centagateDown" ;
                
                throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
            }

            return array ( 'email' => array ( $email ), 'userId' => array( $userId ),'nameID' => array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"]) ) ;
        }


       protected function loginFido ( $username , $fidoCred )
         {
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $fidoCred )' ) ;

            $userId = "";
            $email = "";

            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/auth/authFido/" ;

            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;
            $params = array (
                "username"       => $username,
                "supportFido"    => "true",
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "assertion"      => $fidoCred,
                "hmac"           => hash_hmac ( "sha256" , $username . $integration_key . $unix_timestamp . $_SESSION [ "authToken" ]."true" . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ]."" , $secretKey )
            ) ;

                        if ( isset ( $_SESSION [ "authToken" ] ) )
                                $params [ "authToken" ] = $_SESSION [ "authToken" ] ;


                $nameID=$_SESSION["nameID"];
                /*
		$dataAttr="";
                if ($nameID === "ID"){
                  $dataAttr=$uniqueId;
                 }else{
                     if ($nameID === "Email"){
                         $dataAttr=$email;
                     }else{
                         error_log("________________NAME ID IS EMPTY _____________");
                       }
                }
		*/

            $email=$_SESSION["UserEmail"];
            $userId=$_SESSION["UserID"];       
            //error_log("_______________userID ===".$userId);

            $json = json_encode ( $params ) ;
            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                $response_body = get_object_vars ( $response -> body ) ;
                $response_object = json_decode ( $response_body [ "object" ] ) ;
                //error_log("_____________FIDO RESPONSE = ".$response."_______________");

                 if ( $response -> code == 200 )
                  {
                       if ( $response -> body -> code === "0" )
                         {
				 //$email = $response_object -> { "email" } ;
                                 //$userId = $response_object -> { "userUniqueId" } ;
                         }
                        else
                         {
                                /* Problem happened. Consider as wrong login. */
                               if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true )
                                                        $_SESSION [ "reset_login_form" ] = true ;

                              $_SESSION [ "error_message" ] = $response -> body -> code ;

                             throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                         }

                  } 
                else
                 {
                    /* Problem happened. Consider as wrong login. */
                     $_SESSION [ "error_message" ] = "serverError" ;

                     throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                  }


         }
          catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid login */
                $_SESSION [ "error_message" ] = "centagateDown" ;

                throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
            }

            return array ( 'email' => array ( $email ), 'userId' => array($userId),'nameID' => array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"]) ) ;


         }

        protected function loginOtp ( $username , $otp , $requester_sp_id )
        {
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $otp )' ) ;

	    $userId = "";
	    $email = "";

            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/auth/authOtp/" ;
           
            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;


                $nameID=$_SESSION["nameID"];
                /*
		$dataAttr="";
                if ($nameID === "ID"){
                  $dataAttr=$uniqueId;
                 }else{
                     if ($nameID === "Email"){
                         $dataAttr=$email;
                     }else{
                         error_log("________________NAME ID IS EMPTY _____________");
                       }
                }
		*/

           // $json = json_encode ( $params ) ;
            $devID = $_SESSION [ "devID" ];
            $defDeviceName=$_SESSION["defDeviceName"];
            
            if (empty($defDeviceName)){   // offline , get the active one
               $tokenId="";
               $otpType="offline";
            }else{   
               $tokenId="";       // online mobile by default
               $otpType="online"; // online mobile by default
            }
            $params = array (
                "otpType"        => $otpType,
                "tokenId"        => $tokenId,
                "browserFp"      => "",
                "devAccId"       => $devID,
                "username"       => $username,
                "otp"            => $otp,
                "supportFido"    => "false",
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"           => hash_hmac ( "sha256" , $username. $devID. $otp. $otpType . $integration_key . $unix_timestamp . $_SESSION [ "authToken" ]."false" . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ]."" , $secretKey )
            ) ;
			
			if ( isset ( $_SESSION [ "authToken" ] ) )
				$params [ "authToken" ] = $_SESSION [ "authToken" ] ;

			//error_log ( "params: " . print_r ( $params , true ) ) ;
           
          

           $json = json_encode ( $params ) ;
            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                $response_body = get_object_vars ( $response -> body ) ;
                $response_object = json_decode ( $response_body [ "object" ] ) ;
		$userId = $_SESSION["UserID"]; //isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : "";
		$email = $_SESSION["UserEmail"];//isset($response_object->{"email"}) ? $response_object->{"email"} : "";
   
                if ( $response -> code == 200 )
                {
                    if ( $response -> body -> code === "0" )
                    {
						$sp_id_list = $response_object -> { "spIdList" } ;
						$sp_ids = json_decode ( $sp_id_list ) ;
						$sp_id_registered = false ;
						
						foreach ( $sp_ids as $sp_id )
						{
							if ( isset ( $sp_id ) && strlen ( $sp_id ) > 0 && $requester_sp_id === $sp_id )
							{
								$sp_id_registered = true ;
								break ;
							}
						}
						
						if ( ! $sp_id_registered )
						{
							$_SESSION [ "error_message" ] = "spNotRegistered" ;
							
							throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
						}

                        $_SESSION [ "authToken" ] = $response_object -> { "authToken" } ;
                        
                        if ( $response_object -> { "secretCode" } === "" )
                        {
                            /* Additional authentication is required */
                            $auth_methods = $response_object -> { "authMethods" } ;
                            $auth_methods_array = explode ( "," , $auth_methods ) ;
    
                            $this -> determineAuthMethods ( $auth_methods_array ) ;                            
							
							if ( $_SESSION [ "auth_static" ] && $response_object -> { "multiStepAuth" } === "true" )
								$_SESSION [ "multi_step_auth" ] = true ;
							else if ( $_SESSION [ "auth_static" ] && $response_object -> { "multiStepAuth" } === "blocked" )
								$_SESSION [ "error_message" ] = "riskDetected" ;
                            
                            throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                        }
						else
							$_SESSION [ "auth_secret" ] = $response_object -> { "secretCode" } ;
                    }
                    else
                    {
                        /* Problem happened. Consider as wrong login. */
						if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true )
							$_SESSION [ "reset_login_form" ] = true ;
						
                        $_SESSION [ "error_message" ] = $response -> body -> code ;
                        
                        throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                    }
                }
                else
                {
                    /* Problem happened. Consider as wrong login. */
                    $_SESSION [ "error_message" ] = "serverError" ;
                    
                    throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                }
            } 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid login */
                $_SESSION [ "error_message" ] = "centagateDown" ;
                
                throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
            }


            return array ( 'email' => array ( $email ), 'userId' => array($userId) , 'nameID' => array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"]) ) ;


        }

        protected function loginCrOtp ( $username , $otp , $requester_sp_id )
        {
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $otp )' ) ;

	    $userId = "";
	    $email = "";

            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/auth/authCrOtp/" ;
         
            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;
            $challenge = $_SESSION [ "otp_challenge" ] ;

            $defDeviceName=$_SESSION["defDeviceName"];

            if (empty($defDeviceName)){   // offline , get the active one
               $tokenId="";
               $otpType="offline";
            }else{
               $tokenId="";       // online mobile by default
               $otpType="online"; // online mobile by default
            }



                $nameID=$_SESSION["nameID"];
                /*
		$dataAttr="";
                if ($nameID === "ID"){
                  $dataAttr=$uniqueId;
                 }else{
                     if ($nameID === "Email"){
                         $dataAttr=$email;
                     }else{
                         error_log("________________NAME ID IS EMPTY _____________");
                       }
                }
		*/

     
            $devID = $_SESSION [ "devID" ]; 
            $params = array (
                "otpType"        => $otpType,
                "tokenId"        => $tokenId,
                "username"       => $username,
                "devAccId"       => $devID,
                "crOtp"          => $otp,
                "challenge"      => $challenge,
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "browserFp"      => "",
                "supportFido"    => "false",
                "hmac"           => hash_hmac ( "sha256" , $username.$devID .$otp . $otpType . $challenge . $integration_key . $unix_timestamp . $_SESSION [ "authToken" ]."false". $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;
			
			if ( isset ( $_SESSION [ "authToken" ] ) )
				$params [ "authToken" ] = $_SESSION [ "authToken" ] ;

            $json = json_encode ( $params ) ;

            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                $response_body = get_object_vars ( $response -> body ) ;
                $response_object = json_decode ( $response_body [ "object" ] ) ;
		$userId = $_SESSION["UserID"];//isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : "";
		$email =  $_SESSION["UserEmail"];//isset($response_object->{"email"}) ? $response_object->{"email"} : "";  
 
                if ( $response -> code == 200 )
                {
                    if ( $response -> body -> code === "0" )
                    {
						$sp_id_list = $response_object -> { "spIdList" } ;
						$sp_ids = json_decode ( $sp_id_list ) ;
						$sp_id_registered = false ;
						
						foreach ( $sp_ids as $sp_id )
						{
							if ( isset ( $sp_id ) && strlen ( $sp_id ) > 0 && $requester_sp_id === $sp_id )
							{
								$sp_id_registered = true ;
								break ;
							}
						}
						
						if ( ! $sp_id_registered )
						{
							$_SESSION [ "error_message" ] = "spNotRegistered" ;
							
							throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
						}

                        $_SESSION [ "authToken" ] = $response_object -> { "authToken" } ;
                        
                        if ( $response_object -> { "secretCode" } === "" )
                        {
                            /* Additional authentication is required */
                            $auth_methods = $response_object -> { "authMethods" } ;
                            $auth_methods_array = explode ( "," , $auth_methods ) ;
    
                            $this -> determineAuthMethods ( $auth_methods_array ) ;
							
							if ( $_SESSION [ "auth_static" ] && $response_object -> { "multiStepAuth" } === "true" )
								$_SESSION [ "multi_step_auth" ] = true ;
							else if ( $_SESSION [ "auth_static" ] && $response_object -> { "multiStepAuth" } === "blocked" )
								$_SESSION [ "error_message" ] = "riskDetected" ;
                            
                            throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                        }
						else
							$_SESSION [ "auth_secret" ] = $response_object -> { "secretCode" } ;
                    }
                    else
                    {
                        /* Problem happened. Consider as wrong login. */
						if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true )
							$_SESSION [ "reset_login_form" ] = true ;
						
                        $_SESSION [ "error_message" ] = $response -> body -> code ;
                        
                        throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                    }
                }
                else
                {
                    /* Problem happened. Consider as wrong login. */
                    $_SESSION [ "error_message" ] = "serverError" ;
                    
                    throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                }
            } 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid login */
                $_SESSION [ "error_message" ] = "centagateDown" ;
                
                throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
            }
	    return array ( 'email' => array ( $email ), 'userId' => array($userId) , 'nameID' => array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"]) ) ;
        }

        protected function loginPki ( $username , $fingerprint , $requester_sp_id )
        {
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $fingerprint )' ) ;

	    $userId = "";
	    $email = "";

            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/auth/authPki/" ;
         
            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;


                $nameID=$_SESSION["nameID"];
                /*
		$dataAttr="";
                if ($nameID === "ID"){
                  $dataAttr=$uniqueId;
                 }else{
                     if ($nameID === "Email"){
                         $dataAttr=$email;
                     }else{
                         error_log("________________NAME ID IS EMPTY _____________");
                       }
                }
		*/



            $params = array (
                "username"            => $username,
                "certFingerprintSha1" => $fingerprint,
                "integrationKey"      => $integration_key,
                "unixTimestamp"       => strval ( $unix_timestamp ),
                "ipAddress"           => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"           => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"                => hash_hmac ( "sha256" , $username . $fingerprint . $integration_key . $unix_timestamp . $_SESSION [ "authToken" ] . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;
			
			if ( isset ( $_SESSION [ "authToken" ] ) )
				$params [ "authToken" ] = $_SESSION [ "authToken" ] ;

            $json = json_encode ( $params ) ;

          
            
            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                $response_body = get_object_vars ( $response -> body ) ;
   
                $response_object = json_decode ( $response_body [ "object" ] ) ;
  		$userId = $_SESSION["UserID"];    //isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : ""; 
		$email =  $_SESSION["UserEmail"]; //isset($response_object->{"email"}) ? $response_object->{"email"} : "";
    
                if ( $response -> code == 200 )
                {
                    if ( $response -> body -> code === "0" )
                    {
						$sp_id_list = $response_object -> { "spIdList" } ;
						$sp_ids = json_decode ( $sp_id_list ) ;
						$sp_id_registered = false ;
						
						foreach ( $sp_ids as $sp_id )
						{
							if ( isset ( $sp_id ) && strlen ( $sp_id ) > 0 && $requester_sp_id === $sp_id )
							{
								$sp_id_registered = true ;
								break ;
							}
						}
						
						if ( ! $sp_id_registered )
						{
							$_SESSION [ "error_message" ] = "spNotRegistered" ;
							
							throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
						}

                        $_SESSION [ "authToken" ] = $response_object -> { "authToken" } ;
                        
                        if ( $response_object -> { "secretCode" } === "" )
                        {
                            /* Additional authentication is required */
                            $auth_methods = $response_object -> { "authMethods" } ;
                            $auth_methods_array = explode ( "," , $auth_methods ) ;
    
                            $this -> determineAuthMethods ( $auth_methods_array ) ;                            
							
							if ( $_SESSION [ "auth_static" ] && $response_object -> { "multiStepAuth" } === "true" )
								$_SESSION [ "multi_step_auth" ] = true ;
							else if ( $_SESSION [ "auth_static" ] && $response_object -> { "multiStepAuth" } === "blocked" )
								$_SESSION [ "error_message" ] = "riskDetected" ;
                            
                            throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                        }
						else
							$_SESSION [ "auth_secret" ] = $response_object -> { "secretCode" } ;
                    }
                    else
                    {
                        /* Problem happened. Consider as wrong login. */
						if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true )
							$_SESSION [ "reset_login_form" ] = true ;
						
                        $_SESSION [ "error_message" ] = $response -> body -> code ;
                            
                        throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                    }
                }
                else
                {
                    /* Problem happened. Consider as wrong login. */
					$_SESSION [ "error_message" ] = "serverError" ;
							
                    throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
                }
            } 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid login */
                $_SESSION [ "error_message" ] = "centagateDown" ;
                
                throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
            }

	    return array ( 'email' => array ( $email ), 'userId' => array($userId),'nameID'=> array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"])) ;

        }
        
        protected function loginMobileSoftCert ( $username , $response_object , $requester_sp_id )
        {
            assert ( 'is_string ( $username )' ) ;

                $userId = $_SESSION["UserID"];  //isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : "";
                $email =  $_SESSION["UserEmail"]; //isset($response_object->{"email"}) ? $response_object->{"email"} : "";

	
	

                $nameID=$_SESSION["nameID"];
                /*
		$dataAttr="";
                if ($nameID === "ID"){
                  $dataAttr=$uniqueId;
                 }else{
                     if ($nameID === "Email"){
                         $dataAttr=$email;
                     }else{
                         error_log("________________NAME ID IS EMPTY _____________");
                       }
                }
		*/

	
			$sp_id_list = $response_object -> { "spIdList" } ;
			$sp_ids = json_decode ( $sp_id_list ) ;
			$sp_id_registered = false ;
					
            return array ( 'email' => array ( $email ) , 'userId' => array($userId),'nameID'=> array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"]) ) ;
        }

        protected function loginQrOtp ( $username , $otp , $requester_sp_id )
        {
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $otp )' ) ;
	    $userId = "";
	    $email = "";		
			if ( strlen ( $otp ) > 0 )
			{
				$config = SimpleSAML_Configuration::getInstance ( ) ;

				$rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/auth/authQrCode/" ;
				
                                $integration_key = $_SESSION["IK"] ;
                                $secretKey=$_SESSION["SK"];

				$unix_timestamp = time ( ) ;
				$challenge = $_SESSION [ "qr_otp_challenge" ] ;
				$auth_token = $_SESSION [ "authToken" ] ;
				$plain_text = $_SESSION [ "qr_plain_text" ] ;
				$plain_text = base64_encode ( $plain_text ) ;



                $nameID=$_SESSION["nameID"];
                /*
		$dataAttr="";
                if ($nameID === "ID"){
                  $dataAttr=$uniqueId;
                 }else{
                     if ($nameID === "Email"){
                         $dataAttr=$email;
                     }else{
                         error_log("________________NAME ID IS EMPTY _____________");
                       }
                }
		*/

              
              

				$params = array (
					"username"       => $username,
					"otp"            => $otp,
					"challenge"      => $challenge,
					"details"        => $plain_text,
					"integrationKey" => $integration_key,
					"unixTimestamp"  => strval ( $unix_timestamp ),
					"authToken"      => $auth_token,
					"ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
					"userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
					"hmac"           => hash_hmac ( "sha256" , $username . $otp . $challenge . $plain_text . $integration_key . $unix_timestamp . $auth_token . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
				) ;
				
				$json = json_encode ( $params ) ;

				try
				{
					$response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
					$response_body = get_object_vars ( $response -> body ) ;
					$response_object = json_decode ( $response_body [ "object" ] ) ;
					$userId = $_SESSION["UserID"];//isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : "";				
					$email = $_SESSION["UserEmail"];//isset($response_object->{"email"}) ? $response_object->{"email"} : "";
					if ( $response -> code == 200 )
					{
						if ( $response -> body -> code === "0" )
						{
							$sp_id_list = $response_object -> { "spIdList" } ;
							$sp_ids = json_decode ( $sp_id_list ) ;
							$sp_id_registered = false ;
							
							foreach ( $sp_ids as $sp_id )
							{
								if ( isset ( $sp_id ) && strlen ( $sp_id ) > 0 && $requester_sp_id === $sp_id )
								{
									$sp_id_registered = true ;
									break ;
								}
							}
							
							if ( ! $sp_id_registered )
							{
								$_SESSION [ "error_message" ] = "spNotRegistered" ;
								
								throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
							}

							$_SESSION [ "authToken" ] = $response_object -> { "authToken" } ;
							
							if ( $response_object -> { "secretCode" } === "" )
							{
								/* Additional authentication is required */
								$auth_methods = $response_object -> { "authMethods" } ;
								$auth_methods_array = explode ( "," , $auth_methods ) ;
		
								$this -> determineAuthMethods ( $auth_methods_array ) ;
								
								if ( $_SESSION [ "auth_static" ] && $response_object -> { "multiStepAuth" } === "true" )
									$_SESSION [ "multi_step_auth" ] = true ;
								else if ( $_SESSION [ "auth_static" ] && $response_object -> { "multiStepAuth" } === "blocked" )
									$_SESSION [ "error_message" ] = "riskDetected" ;
								
								throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
							}
							else
								$_SESSION [ "auth_secret" ] = $response_object -> { "secretCode" } ;
						}
						else
						{
							/* Problem happened. Consider as wrong login. */
							if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true )
								$_SESSION [ "reset_login_form" ] = true ;
							
							$_SESSION [ "error_message" ] = $response -> body -> code ;
							
							throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
						}
					}
					else
					{
						/* Problem happened. Consider as wrong login. */
						$_SESSION [ "error_message" ] = "serverError" ;
						
						throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
					}
				} 
				catch ( Httpful\Exception\ConnectionErrorException $ex )
				{
					/* Connection error. Just say invalid login */
					$_SESSION [ "error_message" ] = "centagateDown" ;
					
					throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
				}
			}
			else
			{
				if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true )
					throw new SimpleSAML_Error_Error ( 'INVALIDLOGIN' ) ;
			}

            $userId = $_SESSION["UserID"];
                    
            $email = $_SESSION["UserEmail"];
            error_log ("_______________ email == ".$email."__________ uniqueid = ".$userId."___________ sessionEmil Value = ".$_SESSION["UserEmail"]);


            return array ( 'email' => array ( $email ), 'userId' => array($userId) , 'nameID' => array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"]) ) ;
        }

        public function requestMobileSoftCert ( $username )
        {
            assert ( 'is_string ( $username )' ) ;

            $config = SimpleSAML_Configuration::getInstance ( ) ;

         

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/req/requestMobileSoftCert/" ;

            $integration_key = $_SESSION["IK"] ;
            $secretKey= $_SESSION["SK"];

            $unix_timestamp = time ( ) ;
            $auth_token = $_SESSION [ "authToken" ] ;
            $devID = $_SESSION [ "devID" ];
            
            $params = array (
                "username"       => $username,
                "authToken"      => $auth_token,
                "integrationKey" => $integration_key,
                "devAccId"       => $devID,
                "details"        => "saml",
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"           => hash_hmac ( "sha256" , $username. $devID."saml" . $integration_key . $unix_timestamp . $auth_token . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;
			
            $json = json_encode ( $params ) ;

            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;

         
 
                if ( $response -> code == 200 )
                {
                    if ( $response -> body -> code === "0" )
                    {
                        $_SESSION [ "show_2fa_input" ] = true ;
                    }
                    else
                    {
                        /* Problem happened. Consider as wrong login. */
                        $_SESSION [ "error_message" ] = $response -> body -> code ;
                        
                        throw new SimpleSAML_Error_Error ( 'REQMOBILESOFTCERTFAILED' ) ;
                    }
                }
                else
                {
                    /* Problem happened. Consider as wrong login. */
                    $_SESSION [ "error_message" ] = "serverError" ;
                    
                    throw new SimpleSAML_Error_Error ( 'REQMOBILESOFTCERTFAILED' ) ;
                }
            } 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid login */
                $_SESSION [ "error_message" ] = "centagateDown" ;
                
                throw new SimpleSAML_Error_Error ( 'REQMOBILESOFTCERTFAILED' ) ;
            }
        }

        public function requestQrCode ( $username )
        {
            assert ( 'is_string ( $username )' ) ;

            $config = SimpleSAML_Configuration::getInstance ( ) ;
            $devID = $_SESSION [ "devID" ]; 
         

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/req/requestQrCode/" ;
         
            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;
            $auth_token = $_SESSION [ "authToken" ] ;
			
			$details = "Authentication from SAML: " . $username;
			$details = base64_encode ( $details ) ;

            $params = array (
                "username"       => $username,
                "devAccId"       => $devID,
    	        "details"        => $details,
                "authToken"      => $auth_token,
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"           => hash_hmac ( "sha256" , $username .$devID. $details . $integration_key . $unix_timestamp . $auth_token . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;
			
            $json = json_encode ( $params ) ;

            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;

                //error_log($response);
    
                if ( $response -> code == 200 )
                {
                    if ( $response -> body -> code === "0" )
                    {
						$response_body = get_object_vars ( $response -> body ) ;
						$response_object = json_decode ( $response_body [ "object" ] ) ;
						
                        $_SESSION [ "show_2fa_input" ] = true ;
			$_SESSION [ "qrCode" ] = $response_object -> { "qrCode" } ;
			

                        $_SESSION [ "qr_otp_challenge" ] = $response_object -> { "otpChallenge" } ;
			$_SESSION [ "qr_plain_text" ] = $response_object -> { "plainText" } ;
                    }
                    else
                    {
                        /* Problem happened. Consider as wrong login. */
                        $_SESSION [ "error_message" ] = $response -> body -> code ;
                        
                        throw new SimpleSAML_Error_Error ( 'REQQRCODEFAILED' ) ;
                    }
                }
                else
                {
                    /* Problem happened. Consider as wrong login. */
                    $_SESSION [ "error_message" ] = "serverError" ;
                    
                    throw new SimpleSAML_Error_Error ( 'REQQRCODEFAILED' ) ;
                }
            } 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid login */
                $_SESSION [ "error_message" ] = "centagateDown" ;
                
                throw new SimpleSAML_Error_Error ( 'REQQRCODEFAILED' ) ;
            }
        }
        
        protected function loginMobilePush( $username , $response_object , $requester_sp_id )
        {
           

                $nameID=$_SESSION["nameID"];
                /*
		$dataAttr="";
                if ($nameID === "ID"){
                  $dataAttr=$uniqueId;
                 }else{
                     if ($nameID === "Email"){
                         $dataAttr=$email;
                     }else{
                         error_log("________________NAME ID IS EMPTY _____________");
                       }
                }
		*/

            $userId = $_SESSION["UserID"];//isset($response_object->{"userUniqueId"}) ? $response_object->{"userUniqueId"} : "";
            $email =  $_SESSION["UserEmail"];//isset($response_object->{"email"}) ? $response_object->{"email"} : "";


   
            return array ( 'email' => array ( $email ), 'userId' => array($userId) , 'nameID' => array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"]) ) ;
        }

        public function requestMobilePush ( $username )
        {

         

            assert ( 'is_string ( $username )' ) ;

            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/req/requestMobilePushCR/" ;

            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;
            $auth_token = $_SESSION [ "authToken" ] ;
            $devID= $_SESSION [ "devID" ];

            $params = array (
                "username"       => $username,
                "devAccId"       => $devID,
                "details"        => "saml request",
                "authToken"      => $auth_token,
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "authToken"      => $auth_token,
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
		"hmac"           => hash_hmac ( "sha256" , $username.$devID."saml request" . $integration_key . $unix_timestamp . $auth_token . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;
			
            $json = json_encode ( $params ) ;

            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;

         

                if ( $response -> code == 200 )
                {
                    if ( $response -> body -> code === "0" )
                    {
                        $_SESSION [ "show_2fa_input" ] = true ;

                        error_log("____________ auth static= ".$_SESSION [ "auth_static" ]);

                    }
                    else
                    {
                        /* Problem happened. Consider as wrong login. */
                        $_SESSION [ "error_message" ] = $response -> body -> code ;
                        
                        throw new SimpleSAML_Error_Error ( 'REQMOBILEAUDIOPASSFAILED' ) ;
                    }
                }
                else
                {
                    /* Problem happened. Consider as wrong login. */
                    $_SESSION [ "error_message" ] = "serverError" ;
                    
                    throw new SimpleSAML_Error_Error ( 'REQMOBILEAUDIOPASSFAILED' ) ;
                }
            } 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid login */
                $_SESSION [ "error_message" ] = "centagateDown" ;
                
                throw new SimpleSAML_Error_Error ( 'REQMOBILEAUDIOPASSFAILED' ) ;
            }
        }
		
		public static function requestSmsOtp ( $username )
		{
            assert ( 'is_string ( $username )' ) ;

			unset ( $_SESSION [ "show_2fa_input" ] ) ;
            
            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/req/requestSmsOtp/" ;
         
            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;

            $params = array (
                "username"       => $username,
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"           => hash_hmac ( "sha256" , $username . $integration_key . $unix_timestamp . $_SESSION [ "authToken" ] . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;
			
			if ( isset ( $_SESSION [ "authToken" ] ) )
				$params [ "authToken" ] = $_SESSION [ "authToken" ] ;

            $json = json_encode ( $params ) ;

            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
          
                if ( $response -> body -> code === "0" )
                {
					/* SMS OTP sent */
					$_SESSION [ "show_2fa_input" ] = true ;
                    
                    $response_body = get_object_vars ( $response -> body ) ;
                    $response_object = json_decode ( $response_body [ "object" ] ) ;
                    
                    $_SESSION [ "phone" ] = sspmod_sm_Auth_Source_UserPass::mask_phone_number ( $response_object -> { "phone" } ) ;
                    $_SESSION [ "timeout" ] = sspmod_sm_Auth_Source_UserPass::build_hour_string ( $response_object -> { "timeout" } ) ;
                }
                else
                {
                    /* Problem happened. Consider as wrong login. */
                    $_SESSION [ "error_message" ] = $response -> body -> code ;
                    
                    if ( $response -> body -> code == "23002" )
                    {
                        $_SESSION [ "show_2fa_input" ] = true ;
                        $_SESSION [ "login_mode" ] = 3 ;
                    }
					
                    $response_body = get_object_vars ( $response -> body ) ;
                    $response_object = json_decode ( $response_body [ "object" ] ) ;
                    
                    $_SESSION [ "phone" ] = sspmod_sm_Auth_Source_UserPass::mask_phone_number ( $response_object -> { "phone" } ) ;
                    $_SESSION [ "timeout" ] = sspmod_sm_Auth_Source_UserPass::build_hour_string ( $response_object -> { "timeout" } ) ;
                    
					throw new SimpleSAML_Error_Error ( 'SENDSMSOTPFAILED' ) ;
                }
            } 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid username and password */
                $_SESSION [ "error_message" ] = "centagateDown" ;
					
				throw new SimpleSAML_Error_Error ( 'SENDSMSOTPFAILED' ) ;
            }
		}
		


               public static function requestFIDO($username){
                   assert ( 'is_string ( $username )' ) ;

                //error_log("____________________REQUEST FIDO BACKEND_________________");

                    unset ( $_SESSION [ "show_2fa_input" ] ) ;

            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/req/requestAssertionOption/" ;

            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;

            $defDeviceName=$_SESSION["defDeviceName"];

            if (empty($defDeviceName)){   // offline , get the active one
               $tokenId="";
               $otpType="offline";
            }else{
               $tokenId="";       // online mobile by default
               $otpType="online"; // online mobile by default
            }

            $devID=$_SESSION [ "devID" ];

            $params = array (
                "username"       => $username,
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "browserFp"      => "",
                "supportFido"    => "true",
                "hmac"           => hash_hmac ( "sha256" , $username . $integration_key . $unix_timestamp. $_SESSION [ "authToken" ]."true" . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;

                        if ( isset ( $_SESSION [ "authToken" ] ) )
                                $params [ "authToken" ] = $_SESSION [ "authToken" ] ;

            $json = json_encode ( $params ) ;

            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                $response_body = get_object_vars ( $response -> body ) ;
                $response_object = json_decode ( $response_body [ "object" ] ) ;

                //error_log("___________FIDO RESPONSE = ".json_encode($response_object)."___________ , DATA TO SEND = ".json_encode($params));


                if ( $response -> body -> code === "0" )
                {

                       $_SESSION["fido_challenge"] = json_encode($response_object);
               
                                        /* OTP challenge sent */
                                        $_SESSION [ "show_2fa_input" ] = true ;
                    
                }
                else
                {
                    /* Problem happened. Consider as wrong login. */
                    $_SESSION [ "error_message" ] = $response -> body -> code ;

                                        throw new SimpleSAML_Error_Error ( 'GENOTPCHALLENGEFAILED' ) ;
                }
            }

 catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid username and password */
                $_SESSION [ "error_message" ] = "centagateDown" ;

                                throw new SimpleSAML_Error_Error ( 'GENOTPCHALLENGEFAILED' ) ;
            }

                  }



		public static function requestCrOtpChallenge ( $username )
		{
            assert ( 'is_string ( $username )' ) ;

			unset ( $_SESSION [ "show_2fa_input" ] ) ;
            
            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/v2/CentagateWS/webresources/req/requestOtpChallenge/" ;
          
            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;
          
            $defDeviceName=$_SESSION["defDeviceName"];

            if (empty($defDeviceName)){   // offline , get the active one
               $tokenId="";
               $otpType="offline";
            }else{
               $tokenId="";       // online mobile by default
               $otpType="online"; // online mobile by default
            }

            $devID=$_SESSION [ "devID" ];

            $params = array (
                "otpType"        => $otpType,
                "tokenId"        => $tokenId,
                "username"       => $username,
                "devAccId"       => $devID,
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"           => hash_hmac ( "sha256" , $username .$devID. $otpType . $integration_key . $unix_timestamp . $_SESSION [ "authToken" ] . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;
			
			if ( isset ( $_SESSION [ "authToken" ] ) )
				$params [ "authToken" ] = $_SESSION [ "authToken" ] ;

            $json = json_encode ( $params ) ;
            
            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                $response_body = get_object_vars ( $response -> body ) ;
                $response_object = json_decode ( $response_body [ "object" ] ) ;
           
                //error_log("_____________CROTP RESPONSE = ".json_encode($response)."_________________");
             
                if ( $response -> body -> code === "0" )
                {
					/* OTP challenge sent */
					$_SESSION [ "show_2fa_input" ] = true ;
                    $_SESSION [ "otp_challenge" ] = $response_object -> { "otpChallenge" } ;
                }
                else
                {
                    /* Problem happened. Consider as wrong login. */
                    $_SESSION [ "error_message" ] = $response -> body -> code ;
					
					throw new SimpleSAML_Error_Error ( 'GENOTPCHALLENGEFAILED' ) ;
                }
            } 
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
                /* Connection error. Just say invalid username and password */
                $_SESSION [ "error_message" ] = "centagateDown" ;
					
				throw new SimpleSAML_Error_Error ( 'GENOTPCHALLENGEFAILED' ) ;
            }
		}

        public function cancelLogin ( $username )
        {
            assert ( 'is_string ( $username )' ) ;

            $config = SimpleSAML_Configuration::getInstance ( ) ;

            $rest_url = $config->getString('ws.baseurl', 'http://localhost:8080')."/CentagateWS/webresources/auth/request/reject/" ;

            $integration_key = $_SESSION["IK"] ;
            $secretKey=$_SESSION["SK"];

            $unix_timestamp = time ( ) ;
            $auth_token = $_SESSION [ "authToken" ] ;

            $params = array (
                "username"       => $username,
                "authToken"      => $auth_token,
                "integrationKey" => $integration_key,
                "unixTimestamp"  => strval ( $unix_timestamp ),
                "ipAddress"      => $_SERVER [ 'REMOTE_ADDR' ],
                "userAgent"      => $_SERVER [ 'HTTP_USER_AGENT' ],
                "hmac"           => hash_hmac ( "sha256" , $username . $auth_token . $integration_key . $unix_timestamp . $_SERVER [ 'REMOTE_ADDR' ] . $_SERVER [ 'HTTP_USER_AGENT' ] , $secretKey )
            ) ;
			
            $json = json_encode ( $params ) ;

            try
            {
                $response = \Httpful\Request::post ( $rest_url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
                //$response_body = get_object_vars ( $response -> body ) ;
                //$response_object = json_decode ( $response_body [ "object" ] ) ;
            }
            catch ( Httpful\Exception\ConnectionErrorException $ex )
            {
            }
        }
        
		//biau edit
        public static function syncPid ( $authStateId , $username )
        {
			$file = '/tmp/idpsync.log';
			$current = file_get_contents($file);
			$current .= 'in'."\n";				
			file_put_contents($file, $current);			
			
            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;            

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;
					
            try
            {
				$_SESSION [ "password" ] = $password ;	
                $attributes = array ( 'email' => array ( $username ) ) ;
				
            }
            catch ( SimpleSAML_Error_Error $e )
            {
                throw $e ;
            }
			
            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;
	    			
            SimpleSAML_Auth_Source::completeAuth ( $state ) ;
						
        }
		
		public static function handleLogin ( $authStateId , $username , $password , $requester_sp_id )
        {


            //error_log("__________________HANDLE LOGIN USERNAME AND PASSWORD_________________");


            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $password )' ) ;

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;
					
            try
            {
				$_SESSION [ "password" ] = $password ;	
                $attributes = $source -> login ( $username , $password , $requester_sp_id ) ;
				
            }
            catch ( SimpleSAML_Error_Error $e )
            {
                throw $e ;
            }
			
            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;
	    clear_session();
            SimpleSAML_Auth_Source::completeAuth ( $state ) ;
						
        }

       public static function handleLoginPasswordless ( $authStateId , $username , $requester_sp_id ){

    //error_log("__________________ HANDLE LOGIN PASSWORDLESS _________________");


            assert ( 'is_string ( $authStateId )' ) ;
          
        
            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

            try
            {
                
                $attributes = $source -> loginPasswordless ( $username , $requester_sp_id ) ;

            }
            catch ( SimpleSAML_Error_Error $e )
            {
                throw $e ;
            }


       }

        public static function handleSmsOtpLogin ( $authStateId , $username , $smsOtp , $requester_sp_id )
        {



            //error_log("__________________HANDLE SMS LOGIN_________________");

            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $smsOtp )' ) ;

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

            try
            {
                $attributes = $source -> loginSmsOtp ( $username , $smsOtp , $requester_sp_id ) ;
            }
            catch ( SimpleSAML_Error_Error $e )
            {
                throw $e ;
            }

            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;
            clear_session();
	    SimpleSAML_Auth_Source::completeAuth ( $state ) ;
        }

        public static function handleOtpLogin ( $authStateId , $username , $otp , $requester_sp_id )
        {



            ///error_log("__________________HANDLE OTP LOGIN_________________");

            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $otp )' ) ;

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

            try
            {
                $attributes = $source -> loginOtp ( $username , $otp , $requester_sp_id ) ;
            }
            catch ( SimpleSAML_Error_Error $e )
            {
				//error_log ( "failed" ) ;
                throw $e ;
            }

            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;
			
	    sspmod_sm_Auth_Source_UserPass::clear_session_leave_2fa ( ) ;
	    clear_session();
            SimpleSAML_Auth_Source::completeAuth ( $state ) ;
        }


        public static function handleFidoLogin ( $authStateId , $username , $fidoCred )
        {



            //error_log("__________________ HANDLE FIDO LOGIN _________________");

            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $otp )' ) ;

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

            try
            {

                $attributes = $source -> loginFido ( $username , $fidoCred ) ;

            }
            catch ( SimpleSAML_Error_Error $e )
            {
                                //error_log ( "failed" ) ;
                throw $e ;
            }

            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;

            sspmod_sm_Auth_Source_UserPass::clear_session_leave_2fa ( ) ;
	    clear_session();
            SimpleSAML_Auth_Source::completeAuth ( $state ) ;
        }



        public static function handleCrOtpLogin ( $authStateId , $username , $otp , $requester_sp_id )
        {



            //error_log("__________________HANDLE CR OTP _________________");

            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $otp )' ) ;

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

            try
            {
                $attributes = $source -> loginCrOtp ( $username , $otp , $requester_sp_id ) ;
            }
            catch ( SimpleSAML_Error_Error $e )
            {
                throw $e ;
            }

            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;
            clear_session();
            SimpleSAML_Auth_Source::completeAuth ( $state ) ;
        }


       public static function handlePkiLogin ( $authStateId , $username )
        {
            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;

            //error_log("__________________HANDLE PKI LOGIN_________________");
 

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;
       
         
            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

         

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

            try
            {
				 error_log("_____userEmail==".$email."_____ userID===".$uniqueId);
                //error_id()
                $attributes =  array ( 'email' => array ( $email ), 'userId' => array($uniqueId) , 'nameID' => array($nameID) , 'sessionTimeout' => array($_SESSION["sessionTimeout"]) ) ;

                //$attributes = 
				//array ( 'email' => array ( $username ) ) ;//$source -> loginPki ( $username , $fingerprint , $requester_sp_id ) ;
            }
            catch ( SimpleSAML_Error_Error $e )
            {
                throw $e ;
            }

            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;
            clear_session();
            SimpleSAML_Auth_Source::completeAuth ( $state ) ;
        }

/*  
        public static function handlePkiLogin ( $authStateId , $username , $fingerprint , $requester_sp_id )
        {
            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $fingerprint )' ) ;


         

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

            try
            {
                $attributes = $source -> loginPki ( $username , $fingerprint , $requester_sp_id ) ;
            }
            catch ( SimpleSAML_Error_Error $e )
            {
                throw $e ;
            }

            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;
            clear_session();
            SimpleSAML_Auth_Source::completeAuth ( $state ) ;
        }
*/

        public static function handleMobileSoftCertLogin ( $authStateId , $username , $response_object , $requester_sp_id )
        {



            //error_log("__________________HANDLE Soft Cert LOGIN_________________");

            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

            try
            {
                $attributes = $source -> loginMobileSoftCert ( $username , $response_object , $requester_sp_id ) ;
            }
            catch ( SimpleSAML_Error_Error $e )
            {
                throw $e ;
            }

            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;
            clear_session();
            SimpleSAML_Auth_Source::completeAuth ( $state ) ;
        }

        public static function handleMobilePushLogin ( $authStateId , $username , $response_object , $requester_sp_id )
        {



            //error_log("__________________HANDLE PUSH LOGIN_________________");

            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

            try
            {
                $attributes = $source -> loginMobilePush ( $username , $response_object , $requester_sp_id ) ;
            }
            catch ( SimpleSAML_Error_Error $e )
            {
                throw $e ;
            }

            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;
            clear_session();
            SimpleSAML_Auth_Source::completeAuth ( $state ) ;
        }
		
		public static function handleCancelLogin ( $authStateId , $username )
		{
            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

            try
            {
                $attributes = $source -> cancelLogin ( $username ) ;
            }
            catch ( SimpleSAML_Error_Error $e )
            {
                throw $e ;
            }

            //assert ( 'is_array ( $attributes )' ) ;
            //$state [ 'Attributes' ] = $attributes ;
		}

        public static function handleQrOtpLogin ( $authStateId , $username , $otp , $requester_sp_id )
                {
            assert ( 'is_string ( $authStateId )' ) ;
            assert ( 'is_string ( $username )' ) ;
            assert ( 'is_string ( $otp )' ) ;

            $state = SimpleSAML_Auth_State::loadState ( $authStateId , self::STAGEID ) ;

            assert ( 'array_key_exists ( self::AUTHID , $state )' ) ;

            $source = SimpleSAML_Auth_Source::getById ( $state [ self::AUTHID ] ) ;

            if ( $source == NULL )
                throw new Exception ( 'Could not find authentication source with id ' . $state [ self::AUTHID ] ) ;

                        try
                        {
                                $attributes = $source -> loginQrOtp ( $username , $otp , $requester_sp_id ) ;
                        }
                        catch ( SimpleSAML_Error_Error $e )
                        {
                                throw $e ;
                        }

            assert ( 'is_array ( $attributes )' ) ;
            $state [ 'Attributes' ] = $attributes ;
            clear_session();
            SimpleSAML_Auth_Source::completeAuth ( $state ) ;
        }
    }
?>

