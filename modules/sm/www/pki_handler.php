<?php 

if ( session_status ( ) != PHP_SESSION_ACTIVE )
    session_start ( ) ;


//error_log("_____________________PKI222 AUTH = ");
$authStateId=$_GET["AuthState"];
$username=$_GET["username"];

echo $username;
echo $authStateId;

//error_log("_____________________PKI222 AUTH = ".$username);


try
    {


           sspmod_sm_Auth_Source_UserPass::handlePkiLogin ( $authStateId,$username ) ;


           $_SESSION [ "ignore_error" ] = true ;

$t = new SimpleSAML_XHTML_Template ( $globalConfig , 'sm:loginuserpass.php' ) ;
$t -> data [ 'stateparams' ] = array ( 'AuthState' => $authStateId ) ;
$t -> data [ 'errorcode' ] = $errorCode ;
$t -> data [ 'errorparams' ] = $errorParams ;

if ( isset ( $state [ 'SPMetadata' ] ) )
{
    $t -> data [ 'SPMetadata' ] = $state [ 'SPMetadata' ] ;
}
else
{
    $t -> data [ 'SPMetadata' ] = NULL ;
}

$t -> show ( ) ;
exit ( ) ;

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
