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

session_destroy ( ) ;

$globalConfig = SimpleSAML_Configuration::getInstance ( ) ;
$t = new SimpleSAML_XHTML_Template ( $globalConfig , 'sm:logout.php' ) ;

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
