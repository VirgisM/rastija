<?php

//ClassUtils::import( 'utils.ArrayUtils' );

namespace Rastija\Service;

/**
 * Work with search webservices
 */ 
class SkService {
	
	/**
	 * Webservices address and port
	 */
    
	//protected $ws_root_url = 'http://ncbaze.netcode.lt/rastija/';
    //protected $ws_port = '8081';
	
	
    protected $ws_root_url = 'http://sktest.rastija.lt/rastija/';
	protected $ws_port = '8080';
	
	/**
	 * Webservice URLs
	 */
	protected $ws_urls = array(
		'resources' => array(
			'addr' => 'semisearch/resources?ontology=http%3A%2F%2Fwww.rastija.lt%2Fisteklius&ontClass=http%3A%2F%2Fwww.rastija.lt%2Fisteklius%23Resource',
			'method' => 'GET'
		),
		'resource' => array(
			'addr' => 'semisearch/resources?uri=',
			'method' => 'GET'
		),
		'authors' => array(
			'addr' => 'semisearch/resources?ontology=http%3A%2F%2Fwww.rastija.lt%2Fisteklius&ontClass=http%3A%2F%2Fwww.rastija.lt%2Fisteklius%23Person',
			'method' => 'GET'
		),
		'publishers' => array(
			'addr' => 'semisearch/resources?ontology=http%3A%2F%2Fwww.rastija.lt%2Fisteklius&ontClass=http%3A%2F%2Fwww.rastija.lt%2Fisteklius%23Organization',
			'method' => 'GET'
		),
		'types' => array(
			'addr' => 'semisearch/resources?ontology=http%3A%2F%2Fwww.rastija.lt%2Fisteklius&ontClass=http%3A%2F%2Fwww.rastija.lt%2Fisteklius%23ResourceType',
			'method' => 'GET'
		),
		'languages' => array(
			'addr' => 'semisearch/resources?ontology=http%3A%2F%2Fwww.rastija.lt%2Fisteklius&ontClass=http%3A%2F%2Fwww.rastija.lt%2Fisteklius%23Language',
			'method' => 'GET'
		),
		'scopes' => array(
			'addr' => 'semisearch/resources?ontology=http%3A%2F%2Fwww.lexinfo.net%2Flmf&ontClass=http%3A%2F%2Fwww.lexinfo.net%2Flmf%23Context',
			'method' => 'GET'
		),
		'search' => array(
			'addr' => 'semisearch/search',
			'method' => 'POST'
		),
		'classes' => array(
			'addr' => 'semisearch/ontology/classes',
			'method' => 'GET'
		),
		'ontologies' => array(
			'addr' => 'semisearch/ontologies?ontology=http%3A%2F%2Fwww.lexinfo.net%2Flmf',
			'method' => 'POST'
		),
		'dictionaries' => array(
			'addr' => 'semisearch/resources?prefix=',
			'method' => 'POST'
		),
		'data_properties' => array(
			'addr' => 'semisearch/ontology/property/range?ontology=http%3A%2F%2Fwww.lexinfo.net%2Flmf&property=http%3A%2F%2Fwww.lexinfo.net%2Flmf%23',
			'method' => 'GET',
		)
        /// Mano
        ,
        'ontologies_list' => array(
			'addr' => 'semisearch/ontologies?ontology=http%3A%2F%2Fwww.lexinfo.net%2Flmf',
			'method' => 'GET'
		),        
	);
	
	/**
	 * Check if webservices are online
	 * 
	 * @return		boolean		$ret_val		TRUE/FALSE
	 */
	public function isWsOnline() {
		
		$data = array(
			'freeTextQuery' => '',
			'propertyDetailsList' => array()
		);
		
		$result = $this->sendRequest( 'search', $data );
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' ) {
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Send request
	 * 
	 * @param		string		$ws_url				Endpoint address
	 * @param		array		$data				Data array
	 * @param		boolean		$return_plain		Return plain or not
	 * @return		array		$ret_val			Result array
	 */
	public function sendRequest( $ws_url = '', $data = array(), $return_plain = false ) {
		
echo		$ws_url_endpoint = ( !empty( $this->ws_urls[ $ws_url ]['addr'] ) ) ? $this->ws_root_url . $this->ws_urls[ $ws_url ]['addr'] : '';
		$ret_val = array();
		
		if ( empty( $ws_url_endpoint ) ) {
			
			return $ret_val;
		}
		
		$content_type = ( !empty( $this->ws_urls[ $ws_url ]['content_type'] ) ) ? $this->ws_urls[ $ws_url ]['content_type'] : 'application/json';
		
		$data = ( is_array( $data ) && $content_type == 'application/json' ) ? json_encode( $data ) : $data;
		$ch = curl_init( $ws_url_endpoint );
		
		$httpheaders = array(
			'Content-Type: ' . $content_type, 
		);
		
		if ( $content_type == 'application/json' ) {
			
			$httpheaders[] = 'Content-Length: ' . strlen( $data );
		} else {
			
			curl_setopt( $ch, CURLOPT_INFILESIZE, $data['filesize'] );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			unset( $data['filesize'] );
		}
		
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt( $ch, CURLOPT_PORT, $this->ws_port );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $this->ws_urls[ $ws_url ]['method'] );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $httpheaders );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLINFO_HEADER_OUT, true );
        
print_r ($this->ws_urls[ $ws_url ]['method']);
var_dump($data);		
var_dump($httpheaders);
		$result = curl_exec( $ch );
var_dump($result);		
		$ret_val = ( !$return_plain ) ? $this->objectToArray( json_decode( $result ) ) : $result;
		
		curl_close( $ch );
		
		return $ret_val;
	}
	
	
	/**
	 * Get dictionary data from result
	 *
	 * @param		array		$result_arr			Result array
	 * @return		array		$ret_val			Dictionary data array
	 */
	protected function parseDictionaryInfo( $result_arr = array() ) {
		
		$ret_val = array();
		
		if ( empty( $result_arr['objectProperty']['properties'] ) ) {
			
			return $ret_val;
		}
		
		foreach ( $result_arr['objectProperty']['properties'] as $property ) {
			//Mano pakeitimas
            $tmp = explode( '#', $property['uri'] );
                    
			$property_name = ( !empty( $property['uri'] ) ) ? end( $tmp ) : '';
			
			if ( empty( $property_name ) || empty( $this->dictionary_params[ $property_name ] ) ) {
				
				continue;
			}
			
			$parsed_data = $this->parsePiece( $property, $property_name, 'dictionary', array() );
			
			if ( !empty( $this->dictionary_params[ $property_name ]['single'] ) ) {
				
				$ret_val[ $this->dictionary_params[ $property_name ]['local_name'] ] = reset( $parsed_data );
			} elseif ( !empty( $this->dictionary_params[ $property_name ]['many'] ) ) {
				
				$ret_val[ $this->dictionary_params[ $property_name ]['local_name'] ][] = $parsed_data;
			} else {
				
				$ret_val[ $this->dictionary_params[ $property_name ]['local_name'] ] = $parsed_data;
			}
		}
		
		$ret_val['resource_uri'] = ( !empty( $result_arr['objectProperty']['resourceUri'] ) ) ? $result_arr['objectProperty']['resourceUri'] : '';
		
		return $ret_val;
	}

	/**
	 * Get all dictionaries
	 *
	 * @return		array		$ret_val			Dictionaries array
	 */
	public function getDictionaries() {
		
		$ret_val = array();
		
		$data = array(
			'freeTextQuery' => '',
			'pageSize' => '100',
			'propertyDetailsList' => array(
				array(
					'uri' => 'class',
					'value' => 'http://www.rastija.lt/isteklius#Resource'
				)
			)
		);
		
		$result = $this->sendRequest( 'search', $data );
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' || empty( $result['details'] ) ) {
			
			return $ret_val;
		}
		
		foreach ( $result['details']['resourceDetailsList'] as $one_result ) {
			
			$ret_val[] = $this->parseDictionaryInfo( array( 'objectProperty' => $one_result ) );
		}
		
		return $ret_val;
	}
	
	/**
	 * Walk through ontology classes and get additional params
	 *
	 * @param		array		$classes			Ontology classes
	 * @return		string		$ret_val			Filled ontology classes
	 */
	public function getClassDataProperties( &$classes ) {
		
		foreach ( $classes as &$class ) {
			
			if ( $class['type'] == 'DATA_PROPERTY' && !empty( $class['uri'] ) ) {
				
				$uri_parts = explode( '#', $class['uri'] );
				
				if ( count( $uri_parts ) > 1 ) {
					
					$type = array_pop( $uri_parts );
					
					if ( !empty( $type ) ) {
						
						if ( !in_array( $type, array( 'writtenForm', 'abbreviation', 'comment', 'definition', 'origin', 'text' ) ) ) {
							
							$class['extra_data_property'] = $this->getDataProperties( $type );
						}
					}
				}
			}
			
			if ( !empty( $class['_children_'] ) ) {
				
				$this->getClassDataProperties( $class['_children_'] );
			}
		}
		
		return $classes;
	}
	
	/**
	 * Get additional search ontology params
	 *
	 * @param		string		$type				Additional param type
	 * @return		string		$result				Parameters array
	 */
	public function getDataProperties( $type ) {
		
		$ws_url = $this->ws_urls['data_properties']['addr'];
		$this->ws_urls['data_properties']['addr'] .= $type;
		$result = $this->sendRequest( 'data_properties' );
		$this->ws_urls['data_properties']['addr'] = $ws_url;
		
		return $result;
	}

	/**
	 * Convert data to array
	 * 
	 * @param		array		$object			Object
	 * @return		array						Array
	 */
	protected function objectToArray( $object ) {
		
		if( !is_object( $object ) && !is_array( $object ) ) {
			
			return $object;
		}
		
		if( is_object( $object ) ) {
			
			$object = get_object_vars( $object );
		}
		
		return array_map( array( $this, 'objectToArray' ), $object );
	}    
    
    /*----------------------My functions -----------------------------*/
    
    /**
     * 
     */
    public function getOntologyList() {        
        $result = $this->sendRequest( 'ontologies_list' );
        return $result;
    }
    
    /**
     * Gets ontology (Eqvivalent to exportOntology
     * 
     * @param type $ontology posible values ['lmf', 'zodynas']
     * @return RDF
     */
    public function getMainOntology($ontology = 'lmf') {
        if ($ontology == 'zodynas') {
            // Zodynas
            $ws_url = $this->ws_urls['ontologies']['addr'];
            $pos = strpos($ws_url, '=');
            if ($pos) {
                $new_ws_url = substr($ws_url, 0, $pos) . '=http://www.rastija.lt/isteklius';
                $this->ws_urls['ontologies']['addr'] = $new_ws_url;
            }
            
            $value = "http://www.rastija.lt/isteklius";
            // Represents Resourse class elements of isteklius ontology individuals
            //$value = "http://www.rastija.lt/isteklius#Resource";
        } else {
            $value = "";
            // Default uri is LMF  
        }
        
        
        $request = json_decode('
            {
              "freeTextQuery": "",
              "propertyDetailsList": [
                {
                  "uri": "class",
                  "value": "$value"
                }
              ]
            }            
        ', true);
        var_dump($request);
        $result = $this->sendRequest( 'ontologies', $request, true );
        return $result;
    }
    

}

?>