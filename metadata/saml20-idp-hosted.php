<?php
/**
 * SAML 2.0 IdP configuration for SimpleSAMLphp.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-hosted
 */


$metadata [ '__DYNAMIC:1__' ] = array 
(
    'host' => '__DEFAULT__',
    'auth' => 'sm-userpass',
    'name' => 'CENTAGATE Identity Provider',
    'description' => 'CENTAGATE Identity Provider',
    'privatekey' => 'googleappsidp.pem',
    'certificate' => 'googleappsidp.crt',
    'redirect.sign' => true,
    'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',	
    //'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:email',
    #'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
    'AttributeNameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri',
    'redirect.validate' => false,
    'saml20.sign.assertion' => true,
    'assertion.encryption' => true,
    #'userid.attribute' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
    'simplesaml.nameidattribute' => 'userId',
    'authproc' => array (
    //    60 => array (
    //        'class' => 'core:TargetedID',
    //        'nameId' => TRUE
    //    ),
        90 => array (
            'class' => 'core:AttributeMap' , 'sm'
        )
    ),
    //'attributeencodings' => array (
    //            'urn:oid:1.3.6.1.4.1.5923.1.1.1.10' => 'raw'
    //),
);

?>
