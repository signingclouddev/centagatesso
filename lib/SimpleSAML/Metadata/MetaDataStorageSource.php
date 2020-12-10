<?php

    if ( session_status ( ) != PHP_SESSION_ACTIVE )
         session_start ( ) ;


/**
 * This abstract class defines an interface for metadata storage sources.
 *
 * It also contains the overview of the different metadata storage sources.
 * A metadata storage source can be loaded by passing the configuration of it
 * to the getSource static function.
 *
 * @author Olav Morken, UNINETT AS.
 * @author Andreas Aakre Solberg, UNINETT AS.
 * @package SimpleSAMLphp
 */

include_once ( "/var/www/html/simplesamlphp/modules/sm/lib/Auth/Source/httpful.phar" ) ;
abstract class SimpleSAML_Metadata_MetaDataStorageSource
{

    //include_once ( "httpful.phar" ) ;
    /**
     * Parse array with metadata sources.
     *
     * This function accepts an array with metadata sources, and returns an array with
     * each metadata source as an object.
     *
     * @param array $sourcesConfig Array with metadata source configuration.
     *
     * @return array  Parsed metadata configuration.
     *
     * @throws Exception If something is wrong in the configuration.
     */


    public static function parseSources($sourcesConfig)
    {
        assert('is_array($sourcesConfig)');

       

        $sources = array();

        foreach ($sourcesConfig as $sourceConfig) {
            if (!is_array($sourceConfig)) {
                throw new Exception("Found an element in metadata source configuration which wasn't an array.");
            }

             
            $sources[] = self::getSource($sourceConfig);

            //error_log(json_encode($sources));

        }

        return $sources;
    }


    /**
     * This function creates a metadata source based on the given configuration.
     * The type of source is based on the 'type' parameter in the configuration.
     * The default type is 'flatfile'.
     *
     * @param array $sourceConfig Associative array with the configuration for this metadata source.
     *
     * @return mixed An instance of a metadata source with the given configuration.
     *
     * @throws Exception If the metadata source type is invalid.
     */
    public static function getSource($sourceConfig)
    {
        assert(is_array($sourceConfig));

        if (array_key_exists('type', $sourceConfig)) {
            $type = $sourceConfig['type'];
        } else {
            $type = 'flatfile';
        }

        switch ($type) {
            case 'flatfile':
                return new SimpleSAML_Metadata_MetaDataStorageHandlerFlatFile($sourceConfig);
            case 'xml':
                return new SimpleSAML_Metadata_MetaDataStorageHandlerXML($sourceConfig);
            case 'serialize':
                return new SimpleSAML_Metadata_MetaDataStorageHandlerSerialize($sourceConfig);
            case 'mdx':
                return new SimpleSAML_Metadata_MetaDataStorageHandlerMDX($sourceConfig);
            case 'pdo':
                return new SimpleSAML_Metadata_MetaDataStorageHandlerPdo($sourceConfig);
            default:
                throw new Exception('Invalid metadata source type: "'.$type.'".');
        }
    }


    /**
     * This function attempts to generate an associative array with metadata for all entities in the
     * given set. The key of the array is the entity id.
     *
     * A subclass should override this function if it is able to easily generate this list.
     *
     * @param string $set The set we want to list metadata for.
     *
     * @return array An associative array with all entities in the given set, or an empty array if we are
     *         unable to generate this list.
     */
    public function getMetadataSet($set)
    {
        //error_log("getMetaData 4");

        return array();
    }


    /**
     * This function resolves an host/path combination to an entity id.
     *
     * This class implements this function using the getMetadataSet-function. A subclass should
     * override this function if it doesn't implement the getMetadataSet function, or if the
     * implementation of getMetadataSet is slow.
     *
     * @param string $hostPath The host/path combination we are looking up.
     * @param string $set Which set of metadata we are looking it up in.
     * @param string $type Do you want to return the metaindex or the entityID. [entityid|metaindex]
     *
     * @return string|null An entity id which matches the given host/path combination, or NULL if
     *         we are unable to locate one which matches.
     */
    public function getEntityIdFromHostPath($hostPath, $set, $type = 'entityid')
    {

        $metadataSet = $this->getMetadataSet($set);
        if ($metadataSet === null) {
            // this metadata source does not have this metadata set
            return null;
        }

        foreach ($metadataSet as $index => $entry) {
            //error_log(json_encode($metadataSet));
            if (!array_key_exists('host', $entry)) {
                continue;
            }

            if ($hostPath === $entry['host']) {
                if ($type === 'entityid') {
                    return $entry['entityid'];
                } else {
                    return $index;
                }
            }
        }

        // no entries matched, we should return null
        return null;
    }


    /**
     * This function will go through all the metadata, and check the hint.cidr
     * parameter, which defines a network space (ip range) for each remote entry.
     * This function returns the entityID for any of the entities that have an
     * IP range which the IP falls within.
     *
     * @param string $set Which set of metadata we are looking it up in.
     * @param string $ip IP address
     * @param string $type Do you want to return the metaindex or the entityID. [entityid|metaindex]
     *
     * @return string The entity id of a entity which have a CIDR hint where the provided
     *        IP address match.
     */
    public function getPreferredEntityIdFromCIDRhint($set, $ip, $type = 'entityid')
    {

        $metadataSet = $this->getMetadataSet($set);

        foreach ($metadataSet as $index => $entry) {

            if (!array_key_exists('hint.cidr', $entry)) {
                continue;
            }
            if (!is_array($entry['hint.cidr'])) {
                continue;
            }

            foreach ($entry['hint.cidr'] as $hint_entry) {
                if (SimpleSAML\Utils\Net::ipCIDRcheck($hint_entry, $ip)) {
                    if ($type === 'entityid') {
                        return $entry['entityid'];
                    } else {
                        return $index;
                    }
                }
            }
        }

        // no entries matched, we should return null
        return null;
    }


    /*
     *
     */
    private function lookupIndexFromEntityId($entityId, $set)
    {
        assert('is_string($entityId)');
        assert('isset($set)');

        $metadataSet = $this->getMetadataSet($set);

        // check for hostname
        $currenthost = \SimpleSAML\Utils\HTTP::getSelfHost(); // sp.example.org
        if (strpos($currenthost, ":") !== false) {
            $currenthostdecomposed = explode(":", $currenthost);
            $currenthost = $currenthostdecomposed[0];
        }

        foreach ($metadataSet as $index => $entry) {
            if ($index === $entityId) {
                return $index;
            }
            if ($entry['entityid'] === $entityId) {
                if ($entry['host'] === '__DEFAULT__' || $entry['host'] === $currenthost) {
                    return $index;
     
           }
            }
        }

        return null;
    }


    /**
     * This function retrieves metadata for the given entity id in the given set of metadata.
     * It will return NULL if it is unable to locate the metadata.
     *
     * This class implements this function using the getMetadataSet-function. A subclass should
     * override this function if it doesn't implement the getMetadataSet function, or if the
     * implementation of getMetadataSet is slow.
     *
     * @param string $index The entityId or metaindex we are looking up.
     * @param string $set The set we are looking for metadata in.
     *
     * @return array An associative array with metadata for the given entity, or NULL if we are unable to
     *         locate the entity.
     */


    
    public function getMetaData($index, $set)
    {

        
         assert('is_string($index)');
         assert('isset($set)');
        
         if ($set == 'saml20-sp-remote'){
          
	     $config = SimpleSAML_Configuration::getInstance();

             $url = $config->getString('ws.baseurl', 'http://localhost:8080')."/CentagateWS/webresources/app/getMetaData/";
             //error_log("__________________WS URL = ".$url);

             $actual_link = "https://".$_SERVER['HTTP_HOST'];
           
            $params = array (
                "entity"       => $index,
                "companyDomain" => $actual_link 
               ) ; //company ID


            //error_log("sp entity id = ".$index);
 
           //"K4w4T5UtNSa0"
            $json = json_encode ( $params ) ;

             $response = \Httpful\Request::post ( $url ) -> sendsJson ( ) -> body ( $json ) -> send ( ) ;
             error_log("response === ".$response);
       
             $response_body = get_object_vars ( $response -> body ) ;
             $response_object =json_decode($response_body [ "object" ]);
             $data = $response_object -> { "sp_meta_data" } ;
             //$this->sessionHandler = new SimpleSAML_SessionHandlerPHP();
             $_SESSION["IK"]=$response_object -> {"ik"};
             $_SESSION["SK"]=$response_object -> {"sk"};
             $logo_url=$response_object -> {"logo_url"};
             $body_skin = $response_object -> {"body_skin"};

             $forceAuth = $response_object -> {"forceAuth"};
             $nameID= $response_object -> {"nameID"};
             $sessionTimeout = $response_object -> {"sessionTimeout"};
             $_SESSION["forceAuth"] = $forceAuth;
             $stepupAuth = $response_object -> {"stepupAuth"};

             //error_log('________ SP DATA FROM SERVER____________ response = '.$response);
              
              
              
              //error_log('_________ END READING SP DATA FROM SERVER_________');

              if (!empty($data)) {
                \SimpleSAML\Utils\XML::checkSAMLMessage($data, 'saml-meta');
                $entities = SimpleSAML_Metadata_SAMLParser::parseDescriptorsString($data);

                // get all metadata for the entities
                foreach ($entities as &$entity) {
                     $entity = array(
                           'shib13-sp-remote'  => $entity->getMetadata1xSP(),
                           'shib13-idp-remote' => $entity->getMetadata1xIdP(),
                           'saml20-sp-remote'  => $entity->getMetadata20SP(),
                           'saml20-idp-remote' => $entity->getMetadata20IdP(),
                       );
                   }

               // transpose from $entities[entityid][type] to $output[type][entityid]
               $output = SimpleSAML\Utils\Arrays::transpose($entities);
          
               if (array_key_exists($index,$output['saml20-sp-remote']) ){
                    //error_log(json_encode($output['saml20-sp-remote']));
                    $output["saml20-sp-remote"][$index]["integrationKey"] =$response_object -> {"ik"} ;
                    $output["saml20-sp-remote"][$index]["logo_url"]=$logo_url;
                    $output["saml20-sp-remote"][$index]["body_skin"]=$body_skin;
                    $output["saml20-sp-remote"][$index]["secretKey"] =$response_object -> {"sk"} ;
                    $output["saml20-sp-remote"][$index]["forceAuth"] = $forceAuth;
                    $output["saml20-sp-remote"][$index]["nameID"] =$nameID;
                    $output["saml20-sp-remote"][$index]["sessionTimeout"] = $sessionTimeout;
                    $output["saml20-sp-remote"][$index]["stepupAuth"] = $stepupAuth;

                    error_log("_____________ nameID = ".$nameID);
 
                    $_SESSION["nameID"]=$nameID;

		    if (strpos($data,XMLSecurityKey::RSA_SHA256) === false) {
                        //SP does not support RSAwithSHA256, use RSAwithSAH1 for signature
		    	$output["saml20-sp-remote"][$index]["signature.algorithm"] = XMLSecurityKey::RSA_SHA1;
		    }
                    return $output['saml20-sp-remote'][$index];
               }else{
                    return null;
                 }

              }

              
         }else{

               $metadataSet = $this->getMetadataSet($set);
              
              if (array_key_exists($index, $metadataSet)) {
               
                   return $metadataSet[$index];
              
               }

              $indexlookup = $this->lookupIndexFromEntityId($index, $set);
              if (isset($indexlookup) && array_key_exists($indexlookup, $metadataSet)) {
                    return $metadataSet[$indexlookup];
                  }

              return null;

            }

            
    }

}
