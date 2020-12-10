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

$file = '/tmp/idpsync.log';
$current = file_get_contents($file);
$current .= 'started'."\n";				
file_put_contents($file, $current);			
 
if ( session_status ( ) != PHP_SESSION_ACTIVE )
    session_start ( ) ;

$authStateId = $_REQUEST [ 'AuthState' ] ;

if ( isset ( $_GET [ "username" ] ) )
{
    
	$file = '/tmp/idpsync.log';
	$current = file_get_contents($file);
	$current .= '1'."\n";				
	file_put_contents($file, $current);			
	
    try
    {
		$file = '/tmp/idpsync.log';
		$current = file_get_contents($file);
		$current .= '2'."\n";				
		file_put_contents($file, $current);			
        sspmod_sm_Auth_Source_UserPass::syncPid ( $authStateId, $_GET [ "username" ] ) ;
		
		
		
    }
    catch ( SimpleSAML_Error_Error $e )
    {
        /* Login failed. Extract error code and parameters, to display the error. */
        $errorCode = $e -> getErrorCode ( ) ;
        $errorParams = $e -> getParameters ( ) ;

		$file = '/tmp/idpsync.log';
		$current = file_get_contents($file);
		$current .= 'die..'.$e."\n";				
		file_put_contents($file, $current);			
		
		if ( isset ( $_SESSION [ "error_message" ] ) )
		{
			$error = $_SESSION [ "error_message" ] ;		
		}
		else
			$_SESSION [ "ignore_error" ] = true ;
		
		
    }
}

$globalConfig = SimpleSAML_Configuration::getInstance ( ) ;


$t = new SimpleSAML_XHTML_Template ( $globalConfig , 'sm:sync.php' ) ;
$t -> show ( ) ;
exit ( ) ;
