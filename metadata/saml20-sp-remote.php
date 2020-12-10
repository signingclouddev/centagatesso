<?php
/**
 * SAML 2.0 remote SP metadata for SimpleSAMLphp.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-sp-remote
 */

    $files = glob ( "/var/www/html/simplesamlphp/metadata/sps/*.php" ) ;

    foreach ( $files as $filename )
        include_once ( $filename ) ;
