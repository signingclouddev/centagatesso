<?php 

if ( session_status ( ) != PHP_SESSION_ACTIVE )
    session_start ( ) ;


$authStateId=$_GET["AuthState"];
$username=$_GET["username"];

echo $username;
echo $authstate;

//error_log("_____________________PKI AUTH = ".$authStateId."________________________");

try
    {
           
                
//           sspmod_sm_Auth_Source_UserPass::handlePkiLogin ( $authStateId ) ;

		
           $_SESSION [ "ignore_error" ] = true ;
 
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

?>
