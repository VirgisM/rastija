<?php

//ClassUtils::import( 'utils.ArrayUtils' );

namespace Rastija\Service;

/**
 * Work with search webservices
 */ 
class SearchServices {
	
	/**
	 * Webservices address and port
	 */
    
	protected $ws_root_url = 'http://ncbaze.netcode.lt/rastija/';
	protected $ws_port = '8081';
	
	/*
    protected $ws_root_url = 'http://sktest.rastija.lt/rastija/';
	protected $ws_port = '8080';
	*/
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
	);
	
	/**
	 * Param URLs
	 */
	public $params_settings = array(
		'resources' => array(
			'param_uri' => 'http://www.lexinfo.net/lmf#lexicon#name'
		),
		'authors' => array(
			'param_uri' => 'http://www.lexinfo.net/lmf#lexicon#hasAuthor#name'
		),
		'publishers' => array(
			'param_uri' => 'http://www.lexinfo.net/lmf#lexicon#hasOrganization#name'
		),
		'types' => array(
			'param_uri' => 'http://www.lexinfo.net/lmf#lexicon#hasResourceType#name'
		),
		'languages' => array(
			'param_uri' => 'http://www.lexinfo.net/lmf#lexicon#hasLanguage#name'
		),
		'scopes' => array(
			'param_uri' => 'http://www.lexinfo.net/lmf#hasSense#hasContext#hasTextRepresentation#text'
		),
		'classes' => array(
			'param_uri' => '',
			'value_field' => 'searchClassUri'
		),
		'words' => array()
	);
	
	/**
	 * Dictionaries parce schema
	 */
	protected $dictionary_params = array(
		'hasAnnotation' => array(
			'local_name' => 'annotations',
			'fields_map' => array(
				'name' => 'author_name',
				'email' => 'author_email',
				'annotationType' => 'type',
				'text' => 'text'
			)
		),
		'hasAuthor' => array(
			'many' => true,
			'local_name' => 'authors'
		),
		'hasEditor' => array(
			'many' => true,
			'local_name' => 'editors'
		),
		'hasLanguage' => array(
			'many' => true,
			'local_name' => 'languages'
		),
		'hasLicence' => array(
			'local_name' => 'license',
			'fields_map' => array(
				'licenceDistributor' => 'distributor',
				'licenceOwner' => 'user',
				'licenceType' => 'type',
				'licencePrice' => 'price'
			)
		),
		'hasOrganization' => array(
			'local_name' => 'publisher',
			'fields_map' => array(
				'divisionName' => 'name_department',
				'shortName' => 'name_short',
				'url' => 'website'
			)
		),
		'hasProject' => array(
			'local_name' => 'project',
			'fields_map' => array(
				'name' => 'project_name',
				'shortName' => 'project_name_short',
				'financeSource' => 'project_funding',
				'startDate' => 'project_start',
				'endDate' => 'project_end'
			)
		),
		'hasEdition' => array(
			'local_name' => 'publishing',
			'fields_map' => array(
				'name' => 'name',
				'place' => 'place',
				'date' => 'date'
			)
		),
		'hasResourceType' => array(
			'single' => true,
			'local_name' => 'source_type'
		),
		'accessType' => array(
			'single' => true,
			'local_name' => 'access_type'
		),
		'description' => array(
			'single' => true,
			'local_name' => 'description'
		),
		'keyword' => array(
			'single' => true,
			'local_name' => 'keywords'
		),
		'name' => array(
			'single' => true,
			'local_name' => 'name'
		),
		'shortName' => array(
			'single' => true,
			'local_name' => 'acronym'
		),
		'startDate' => array(
			'single' => true,
			'local_name' => 'date'
		),
		'subtitle' => array(
			'single' => true,
			'local_name' => 'other_titles'
		),
		'url' => array(
			'single' => true,
			'local_name' => 'website'
		)
	);
	
	/**
	 * Word parse schema
	 */
	protected $word_params = array(
		'hasWordForm' => array(
			'local_name' => 'word_data',
			'many' => true,
			'fields_map' => array(
				'accentuation' => 'accent',
				'grammaticalGender' => 'genus',
				'grammaticalNumber' => 'count',
				'writtenForm' => 'form'
			)
		),
		'hasLemma' => array(
			'many' => true,
			'local_name' => 'title_words',
			'fields_map' => array(
				'writtenForm' => 'name'
			)
		),
		'hasContext' => array(
			'single' => true,
			'local_name' => 'consumption_area',
			'fields_map' => array(
				'text' => 'consumption_area'
			)
		),
		'hasDefinition' => array(
			'single' => true,
			'local_name' => 'definition',
			'fields_map' => array(
				'writtenForm' => 'definition'
			)
		),
		'hasSenseExample' => array(
			'many' => true,
			'local_name' => 'illustration',
			'fields_map' => array(
				'text' => 'name'
			)
		),
		'hasSenseRelation' => array(
			'many' => true,
			'local_name' => 'sense_relation',
			'fields_map' => array(
				'text' => 'type',
				'value' => 'writtenForm'
			)
		),
		'hasEquivalent' => array(
			'many' => true,
			'local_name' => 'equivalent',
			'fields_map' => array(
				'text' => 'name'
			)
		),
		'hasSubjectField' => array(
			'single' => true,
			'local_name' => 'term_status',
			'fields_map' => array(
				'text' => 'term_status'
			)
		),
		'rank' => array(
			'single' => true,
			'local_name' => 'rank',
			'fields_map' => array(
				'text' => 'rank'
			)
		),
	);

	/**
	* Ontology result URLs
	*/
	protected $ontology_search_param_urls = array(
		'Apibrėžtis' => 'http://www.lexinfo.net/lmf#hasSense#hasDefinition#hasTextRepresentation#writtenForm',
		'Giminė' => 'http://www.lexinfo.net/lmf#hasWordForm#grammaticalGender',
		'Kirčiuotė' => 'http://www.lexinfo.net/lmf#hasWordForm#accentuation',
		'Laikas' => 'http://www.lexinfo.net/lmf#hasWordForm#grammaticalTense',
		'Santrumpa' => 'http://www.lexinfo.net/lmf#hasLemma#abbreviation',
		'Skaičius' => 'http://www.lexinfo.net/lmf#hasWordForm#grammaticalNumber',
		'Statusas' => 'http://www.lexinfo.net/lmf#hasSense#hasSubjectField#status'
	);
	
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
		
		$ret_val = ( !$return_plain ) ? $this->objectToArray( json_decode( $result ) ) : $result;
		
		curl_close( $ch );
		
		return $ret_val;
	}
	
	/**
	 * Clear ontology tree values
	 *
	 * @param		array		$classes				Classes
	 * @param		array		$class_values			Class values
	 * @return		array		$classes				Classes
	 */
	public function clearOntologyClasses( $classes = array(), $class_values = array() ) {
		
		foreach ( $classes as $key => $value ) {
			
			if ( empty( $value['_children_'] ) ) {
				
				if ( $value['type'] == 'OBJECT_PROPERTY' || $value['type'] == 'CLASS' || ( !empty( $class_values ) && empty( $value['value'] ) ) ) {
					
					unset( $classes[ $key ] );
				}
				
				continue;
			}
			
			if ( $value['type'] == 'CLASS' ) {	
				
				$classes = $this->clearOntologyClasses( $value['_children_'], $class_values );
			} else {
				
				$classes[ $key ]['_children_'] = $this->clearOntologyClasses( $value['_children_'], $class_values );
				
				if ( empty( $classes[ $key ]['_children_'] ) ) {
					
					unset( $classes[ $key ]['_children_'] );
					
					if ( $classes[ $key ]['type'] == 'OBJECT_PROPERTY' ) {
						
						unset( $classes[ $key ] );
					}
				}
			}
		}
		
		return $classes;
	}
	
	/**
	 * Get ontology class attributes
	 * 
	 * @return		array		$ret_val		Attribute array
	 */
	public function getOntologyObjects( $ont_class = '', $search_class_uri = '' ) {
		
		$ret_val = array();
		
		$url_params = array(
			'ontology' => 'http://www.lexinfo.net/lmf',
			'ontClass' => ( !empty( $ont_class ) ) ? $ont_class : 'http://www.lexinfo.net/lmf#LexicalEntry',
			'searchClassUri' => $search_class_uri
		);
		
		$url_params = array_map( 'urlencode', array_filter( $url_params ) );
		$url_params_final = array();
		
		foreach ( $url_params as $key => $url_param ) {
			
			$url_params_final[] = $key . '=' . $url_param;
		}
		
		$addr = $this->ws_urls['classes']['addr'];
		$this->ws_urls['classes']['addr'] .= '?' . implode( '&', $url_params_final );
		$result = $this->sendRequest( 'classes' );
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' || empty( $result['details']['classes'] ) ) {
			
			return false;
		}
		
		$classes = $result['details']['classes'];
		
		foreach ( $classes as $key => $class ) {
			
			if ( $class['type'] != 'DATA_PROPERTY' ) {
				
				continue;
			}
			
			$ret_val[] = $class;
		}
		
		return $ret_val;
	}
	
	/**
	 * Get various parameters (resources, authors, publishers, types, languages, scopes)
	 * 
	 * @return		array		$ret_val		Result array
	 */
	public function getParams( $type = '' ) {
		
		$ret_val = array();
		
		if ( empty( $this->params_settings[ $type ] ) ) {
			
			return $ret_val;
		}
		
		$result = ( $type == 'authors' ) ? $this->doSearch( array( 'q' => '' ), 1, 1 ) : $this->sendRequest( $type );
		
		if ( $type == 'authors' && !empty( $result['facets']['authors']['counts'] ) ) {
			
			$authors = $this->sendRequest( $type );
			$authors = $this->restruct( $authors['details'], 'header', 'resourceUri' );
			
			foreach ( $result['facets']['authors']['counts'] as $author ) {
				
				if ( empty( $authors[ $author['value'] ] ) ) {
					
					continue;
				}
				
				$ret_val[] = array(
					'searchUri' => $this->params_settings[ $type ]['param_uri'],
					'resourceUri' => $authors[ $author['value'] ],
					'header' => $author['value']
				);
			}
		} elseif ( !empty( $result['type'] ) && $result['type'] == 'success' && !empty( $result['details'] ) ) {
			
			foreach ( $result['details'] as $entry ) {
				
				foreach ( $entry['properties'] as $property ) {
					
					if ( $property['uri'] == 'http://www.rastija.lt/isteklius#name' ) {
						
						$name = $property['dataProperty'];
						
						break;
					}
				}
				
				$ret_val[] = array(
					'searchUri' => $this->params_settings[ $type ]['param_uri'],
					'resourceUri' => $entry['resourceUri'],
					'header' => $name
				);
			}
		}
		
		return $ret_val;
	}
	
	/**
	 * Restruct detailed search parameters
	 *
	 * @param		array		$form_data					Search form data
	 * @return		array		$property_details_list		Restructed parameters array
	 */
	protected function addSearchParams( $form_data = array() ) {
		
		$property_details_list = array();
		
		foreach ( $this->params_settings as $type => $param_settings ) {
			
			if ( empty( $form_data[ $type ] ) ) {
				
				continue;
			}
			
			foreach ( $form_data[ $type ] as $item ) {
				
				$property_details_list[] = array(
					'uri' => $this->params_settings[ $type ]['param_uri'],
					'value' => $item
				);
			}
		}
		
		if ( !empty( $form_data['publishing_from'] ) && !empty( $form_data['publishing_to'] ) ) {
			
			$year_from = ( $form_data['publishing_from'] <= $form_data['publishing_to'] ) ? $form_data['publishing_from'] : $form_data['publishing_to'];
			$year_to = ( $form_data['publishing_from'] <= $form_data['publishing_to'] ) ? $form_data['publishing_to'] : $form_data['publishing_from'];
		} elseif ( !empty( $form_data['publishing_from'] ) ) {
			
			$year_from = $form_data['publishing_from'];
			$year_to = date( 'Y' );
		} elseif ( !empty( $form_data['publishing_to'] ) ) {
			
			$year_from = 1000;
			$year_to = $form_data['publishing_to'];
		}
		
		if ( !empty( $year_from ) && !empty( $year_to ) ) {
			
			$property_details_list[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#lexicon#hasEdition#date',
				'valueFrom' => $year_from,
				'valueTo' => $year_to
			);
		}
		
		if ( !empty( $form_data['synonym'] ) ) {
			
			$property_details_list[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#hasSense#hasSenseRelation#type',
				'value' => 'Sinonimas'
			);
		}
		
		return $property_details_list;
	}
	
	/**
	 * Merge arrays
	 *
	 * @param		array		$curr_value			Current array
	 * @param		array		$settings			Setting array
	 * @param		array		$value				Array to merge
	 * @return		array		$curr_value			Merged array
	 */
	protected function addToArray( $curr_value, $settings, $value ) {
		
		if ( !empty( $settings['single'] ) ) {
			
			$curr_value[ $settings['local_name'] ] = reset( $value );
		} elseif ( !empty( $settings['many'] ) ) {
			
			$curr_value[ $settings['local_name'] ][] = $value;
		} else {
			
			$curr_value[ $settings['local_name'] ] = $value;
		}
		
		return $curr_value;
	}
	
	/**
	 * Get results
	 *
	 * @param		array		$form_data			Search form data
	 * @param		integer		$items_on_page		Items on page
	 * @param		integer		$page				Page number
	 * @return		array		$ret_val			Result array
	 */
	public function doSearch( $form_data = array(), $items_on_page = 10, $page = 1 ) {
		
		$ret_val = array();
		
		if ( !isset( $form_data['q'] ) ) {
			
			return $ret_val;
		}
		
		$data = array(
			'freeTextQuery' => $form_data['q'],
			'pageSize' => $items_on_page,
			'page' => $page,
			'propertyDetailsList' => array(
				array(
					'uri' => 'class',
					'value' => 'http://www.lexinfo.net/lmf#LexicalEntry'
				)
			)
		);
		
		if ( !empty( $form_data['q'] ) ) {
			
			$data['propertyDetailsList'][] = array(
				'uri' => 'http://www.lexinfo.net/lmf#hasLemma#writtenForm',
				'value' => $form_data['q'],
				'weight' => '10'
			);
		}
		
		if ( !empty( $form_data['synonym_w'] ) ) {
			
			$data['searchForSynonyms'] = true;
		}
		
		$data['propertyDetailsList'] = array_merge( $data['propertyDetailsList'], $this->addSearchParams( $form_data ) );
		
		$result = $this->sendRequest( 'search', $data );
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' || empty( $result['details']['totalElements'] ) ) {
			
			return $ret_val;
		}
		
		if ( $items_on_page == 1 && count( $data['propertyDetailsList'] ) == 1 ) {
			
			$ret_val['words'] = array();
		} else {
			
			foreach ( $result['details']['resourceDetailsList'] as $one_result ) {
				
				$word_arr = $this->parseWordData( $one_result, false, false );
				
				$ret_val['words'][ urlencode( $one_result['resourceUri'] ) ] = $word_arr;
			}
		}
		
		$ret_val['facets'] = $this->parseFacets( $result );
		$ret_val['total'] = ( !empty( $result['details']['totalElements'] ) ) ? $result['details']['totalElements'] : 0;
		
		return $ret_val;
	}
	
	/**
	 * Parse word tree
	 *
	 * @param		array		$piece				Piece
	 * @param		string		$uri				URI piece
	 * @param		array		$ret_val			Result (recursion)
	 * @return		array		$ret_val			Result
	 */
	public function parseWordClassTree( $piece = array(), $uri = '', $ret_val = '' ) {
		
		foreach ( $piece as $key => $value ) {
			
			if ( empty( $value['objectProperty']['properties'] ) ) {
				
				$ret_val[] = array(
					'uri' => $uri . '#' . end( explode( '#', $value['uri'] ) ),
					'value' => $value['dataProperty']
				);
				
				continue;
			}
			
			$new_uri = ( !empty( $uri ) ) ? $uri . '#' . end( explode( '#', $value['uri'] ) ) : $value['uri'];
			$ret_val = $this->parseWordClassTree( $value['objectProperty']['properties'], $new_uri, $ret_val );
		}
		
		return $ret_val;
	}
	
	/**
	 * Save word class tree values
	 *
	 * @param		array		$piece				Word classes
	 * @return		array		$ret_val			Result
	 */
	public function saveWordClassTreeValues( $word_class_tree ) {
		
		if ( empty( $word_class_tree ) ) {
			
			return array();
		}
			
		$saved_values = array();
		foreach ( $word_class_tree as $key => $item ) {
			
			if ( !strpos( 'http://www.rastija.lt/isteklius#', $key ) ) {
				
				$item['value'] = ( strlen( $item['value'] ) > 50 ) ? substr( $item['value'], 0, strrpos( substr( $item['value'], 0, 50 ), ' ' ) ) . '...' : $item['value'];
				
				if ( !isset( $saved_values[ $item['uri'] ] ) ) {
					
					$saved_values[ $item['uri'] ]['value'] = $item['value'];
					$saved_values[ $item['uri'] ]['uri'] = $item['uri']; 
				} else {
					
					if ( is_array( $saved_values[ $item['uri'] ]['value'] ) ) {
						
						$saved_values[ $item['uri'] ]['value'][] = $item['value'];
					} else {
						
						$tmp = $saved_values[ $item['uri'] ]['value'];
						$saved_values[ $item['uri'] ]['value'] = array();
						$saved_values[ $item['uri'] ]['value'][] = $tmp;
						$saved_values[ $item['uri'] ]['value'][] = $item['value'];
					}
				}
			} else {
				
				$saved_values[ $item['uri'] ]['value'] = $item['value'];
				$saved_values[ $item['uri'] ]['uri'] = $item['uri']; 
			}
		}
		
		if ( !empty( $saved_values ) ) {
			
			foreach ( $saved_values  as $key => $value ) {
				
				if ( is_array( $value['value'] ) ) {
					
					$saved_values[ $key ]['value'] = array_unique( $value['value'] );
				}
			}
		}
		
		$change_uri = array(
			// 'http://www.lexinfo.net/lmf#hasWordForm#accentuation' 		=> 'http://www.lexinfo.net/lmf#hasLemma#accentuation',
			// 'http://www.lexinfo.net/lmf#hasWordForm#grammaticalGender' 	=> 'http://www.lexinfo.net/lmf#hasForm#grammaticalGender',
			// 'http://www.lexinfo.net/lmf#hasWordForm#grammaticalNumber'	=> 'http://www.lexinfo.net/lmf#hasForm#grammaticalNumber',
			// 'http://www.lexinfo.net/lmf#hasWordForm#grammaticalTense'	=> 'http://www.lexinfo.net/lmf#hasForm#grammaticalTense',
			'http://www.lexinfo.net/lmf#hasSense#hasDefinition#hasTextRepresentation#writtenForm'	=> 'http://www.lexinfo.net/lmf#hasSense#hasDefinition#definition',
		);
		
		foreach ( $change_uri as $bad_uri => $good_uri ) {
			
			if ( isset( $saved_values[ $bad_uri ] ) ) {
				
				if ( !empty( $saved_values[ $good_uri ] ) ) {
					
					if ( is_array( $saved_values[ $good_uri ]['value'] ) ) {
						
						if ( is_array( $saved_values[ $bad_uri ]['value'] ) ) {
							
							$saved_values[ $good_uri ]['value'] = array_merge( $saved_values[ $good_uri ]['value'], $saved_values[ $bad_uri ]['value'] );
						} else {
							
							$saved_values[ $good_uri ]['value'][] = $saved_values[ $bad_uri ];
						}
						
					} else {
						
						if ( is_array( $saved_values[ $bad_uri ]['value'] ) ) {
							
							$saved_values[ $bad_uri ]['value'][] = $saved_values[ $good_uri ]['value'];
							$saved_values[ $good_uri ] = $saved_values[ $bad_uri ];
						} else {
							
							$saved_values[ $bad_uri ] = $saved_values[ $good_uri ];
							$saved_values[ $good_uri ] = $saved_values[ $bad_uri ];
						}
					}
					
					unset( $saved_values[ $bad_uri ] );
				} else {
					
					$saved_values[ $good_uri ] = $saved_values[ $bad_uri ];
					unset( $saved_values[ $bad_uri ] );
				}
				
				$saved_values[ $good_uri ]['uri'] = $good_uri;
			}
		}
		
		$ret_val = ( !empty( $saved_values ) ) ? $this->restruct( $saved_values, 'uri', 'value' ) : array();
				
		return $ret_val;
	}
	
	/**
	 * Parse one word data
	 *
	 * @param		array		$one_result						Word data from WS
	 * @param		boolean		$skip_additional_parse			If TRUE - skip ontology tree parse
	 * @return		array		$word_arr						Parsed word array
	 */
	public function parseWordData( $one_result = array(), $skip_additional_parse = false, $skip_tree_parse = false ) {
		
		$word_arr = $connections_arr = array();
		
		if ( empty( $one_result ) ) {
			
			return $word_arr;
		}
		
		if ( !$skip_additional_parse && !$skip_tree_parse ) {
			
			$word_class_tree = ( !empty( $one_result['properties'] ) ) ? $this->parseWordClassTree( $one_result['properties'] ) : array();
			$word_arr['word_class_tree'] = $this->saveWordClassTreeValues( $word_class_tree );
		}
		
		foreach ( $one_result['properties'] as $property ) {
			
			if ( $property['uri'] == 'http://www.lexinfo.net/lmf#hasSense' ) {
				
				$word_meaning = array();
				
				foreach ( $property['objectProperty']['properties'] as $proptery_2 ) {
					
					$property_name = ( !empty( $proptery_2['uri'] ) ) ? end( explode( '#', $proptery_2['uri'] ) ) : '';
					
					if ( empty( $this->word_params[ $property_name ] ) ) {
						
						continue;
					}
					
					if ( $property_name == 'hasSenseRelation' ) {
						
						$parsed_data = $this->parsePiece( $proptery_2, $property_name, 'word' );
						$connection_arr_tmp = array();
						
						if ( !empty( $proptery_2['objectProperty']['properties'] ) && !empty( $parsed_data['writtenForm'] ) ) {
							
							foreach ( $proptery_2['objectProperty']['properties'] as $tmp ) {
								
								if ( empty( $tmp['objectProperty'] ) ) {
									
									continue;
								}
								
								$connection_arr_tmp = array(
									'relationType' => $parsed_data['type'],
									'synonymLexicalEntry' => ( !empty( $tmp['objectProperty']['resourceUri'] ) ) ? $tmp['objectProperty']['resourceUri'] : '',
									'lemmaText' => $parsed_data['writtenForm']
								);
								
								$connections_arr = array_merge( $connections_arr, array( $connection_arr_tmp ) );
							}
							
							if ( empty( $connection_arr_tmp ) ) {
								
								if ( stripos( $parsed_data['writtenForm'], '<a' ) !== false ) {
									
									preg_match( '/^<a.*?href=(["\'])(.*?)\1.*$/', $parsed_data['writtenForm'], $matches );
									$name = strip_tags( $parsed_data['writtenForm'] );
									$link = ( !empty( $matches[2] ) ) ? $matches[2] : '';
									
									if ( !empty( $link ) ) {
										
										$link = parse_url( $link );
										$link = ( !empty( $link['query'] ) ) ? urldecode( urldecode( end( explode( '&word=', $link['query'] ) ) ) ) : '';
									}
								} else {
									
									$name = $parsed_data['writtenForm'];
									$link = '';
								}
								
								$connection_arr = array(
									'relationType' => $parsed_data['type'],
									'synonymLexicalEntry' => $link,
									'lemmaText' => $name
								);
								
								$connections_arr = array_merge( $connections_arr, array( $connection_arr ) );
							}
						}
					} else {
						
						$parsed_data = $this->parsePiece( $proptery_2, $property_name, 'word' );
						$word_meaning = $this->addToArray( $word_meaning, $this->word_params[ $property_name ], $parsed_data );
					}
				}
				
				if ( !empty( $word_meaning['sense_relation'] ) && empty( $word_meaning['sense_relation'][0]['writtenForm'] ) ) {
					
					continue;
				}
				
				if ( !empty( $word_meaning ) ) {
					
					$word_arr['meanings'][] = $word_meaning;
				}
				
				continue;
			}
			
			if ( !$skip_additional_parse && $property['uri'] == 'http://www.rastija.lt/isteklius#lexicon' ) {
				
				$word_arr['dictionary'] = $this->parseDictionaryInfo( $property );
				
				continue;
			}
			
			$property_name = ( !empty( $property['uri'] ) ) ? end( explode( '#', $property['uri'] ) ) : '';
			
			if ( empty( $property_name ) || empty( $this->word_params[ $property_name ] ) ) {
				
				continue;
			}
			
			$parsed_data = $this->parsePiece( $property, $property_name, 'word' );
			$word_arr = $this->addToArray( $word_arr, $this->word_params[ $property_name ], $parsed_data );
		}
		
		$word_arr['resourceUri'] = $one_result['resourceUri'];
		$word_arr['connections'] = $connections_arr;
		
		return $word_arr;
	}
	
	/**
	 * Parse array piece
	 *
	 * @param		array		$piece					Array piece to parce
	 * @param		string		$property_name			Parameter name
	 * @param		string		$type					Piece to parse: word or dictionary	 
	 * @param		array		$ret_val				Recursion
	 * @return		array		$ret_val				Result
	 */
	protected function parsePiece( $piece = '', $property_name = '', $type = '', $ret_val = array() ) {
		
		$ret_val = array();
		
		if ( !empty( $piece['dataProperty'] ) ) {
			
			$ret_val = array( $piece['dataProperty'] );
			
			return $ret_val;
		} elseif ( empty( $piece['objectProperty']['properties'] ) ) {
			
			return $ret_val;
		}
		
		$params = ( $type == 'word' ) ? $this->word_params : $this->dictionary_params;
		
		foreach ( $piece['objectProperty']['properties'] as $property ) {
			
			if ( !empty( $property['objectProperty']['properties'] ) ) {
				
				$ret_val = $this->parsePiece( $property, $property_name, $type, $ret_val );
			} else {
				
				$field_name = end( explode( '#', $property['uri'] ) );
				$field_name = ( !empty( $params[ $property_name ]['fields_map'][ $field_name ] ) ) ? $params[ $property_name ]['fields_map'][ $field_name ] : $field_name;
				$ret_val[ $field_name ] = $property['dataProperty'];
			}
		}
		
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
			
			$property_name = ( !empty( $property['uri'] ) ) ? end( explode( '#', $property['uri'] ) ) : '';
			
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
	 * Parse facets
	 *
	 * @param		array		$result_arr			Result array
	 * @return		array		$ret_val			Facets array
	 */
	protected function parseFacets( $result_arr = array() ) {
		
		$ret_val = array();
		
		foreach ( $this->params_settings as $type => $param ) {
			
			if ( empty( $param['param_uri'] ) ) {
				
				continue;
			}
			
			$valid_face_urls[ $param['param_uri'] ] = $type;
		}
		
		foreach ( $result_arr['details']['facetDetailsList'] as $result ) {
			
			if ( !empty( $result['classUri'] ) && !empty( $valid_face_urls[ $result['classUri'] ] ) ) {
				
				$key = $valid_face_urls[ $result['classUri'] ];
				$ret_val[ $key ]['uri'] = $result['classUri'];
				$ret_val[ $key ]['counts'] = $result['countList'];
			}
		}
		
		
		return $ret_val;
	}
	
	/**
	 * Get word connections and objects
	 *
	 * @param		string		$uri				Word URI from WS
	 * @return		array		$ret_val			Connections and objects array
	 */
	public function getWordConnectionObjects( $uri = '' ) {
		
		$ret_val = array();
		
		$data = array(
			'propertyDetailsList' => array(
				array(
					'uri' => '?lexicalEntry',
					'value' => $uri
				)
			)
		);
		
		$ws_url_copy = $this->ws_urls['search']['addr'];
		$this->ws_urls['search']['addr'] .= '?sparqlQueryName=SenseRelation';
		$result = $this->sendRequest( 'search', $data );
		$this->ws_urls['search']['addr'] = $ws_url_copy;
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' || empty( $result['details'] ) ) {
			
			return $ret_val;
		}
		
		foreach ( $result['details'] as $item ) {
			
			if ( empty( $item['properties'] ) ) {
				
				continue;
			}
			
			$ret_val[] = ArrayUtils::restruct( $item['properties'], 'uri', 'dataProperty' ); 
		}
		
		return $ret_val;
	}
	
	/**
	 * Get search suggestions
	 *
	 * @param		string		$q					Search word part
	 * @return		array		$ret_val			Words array
	 */
	public function suggesterValues( $q = '', $limit = -1 ) {
		
		$ret_val = array();
		
		if ( empty( $q ) || strlen( $q ) < 3 ) {
			
			return $ret_val;
		}
		
		$ws_url_copy = $this->ws_urls['search']['addr'];
		$ws_url_method_copy = $this->ws_urls['search']['method'];
		$this->ws_urls['search']['addr'] .= '?suggesterField=http%3A%2F%2Fwww.lexinfo.net%2Flmf%23hasLemma%23writtenForm&searchValue=' . urlencode( $q );
		$this->ws_urls['search']['method'] = 'GET';
		
		$result = $this->sendRequest( 'search' );
		
		$this->ws_urls['search']['addr'] = $ws_url_copy;
		$this->ws_urls['search']['method'] = $ws_url_method_copy;
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' || empty( $result['details'] ) ) {
			
			return $ret_val;
		}
		
		$ret_val = $result['details'];
		
		if ( $limit != -1 ) {
			
			$ret_val = array_slice( $ret_val, 0, $limit );
		}
		
		return $ret_val;
	}
	
	/**
	 * Get word data
	 *
	 * @param		string		$uri				Word URI
	 * @return		array		$ret_val			Word data array
	 */
	public function getWordData( $uri = '' ) {
		
		$ret_val = array();
		$ws_url_copy = $this->ws_urls['resource']['addr'];
		$this->ws_urls['resource']['addr'] .= $uri;
		
		$result = $this->sendRequest( 'resource' );
		$this->ws_urls['resource']['addr'] = $ws_url_copy;
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' || empty( $result['details'] ) ) {
			
			return $ret_val;
		}
		
		$ret_val = $this->parseWordData( $result['details'] );
		
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
	 * Restruct array
	 * 	 
	 * @param		array			$array			Array to restructure
	 * @param		string			$key_key		Array key value to use for new array item key
	 * @param		string			$key_val		Array key value to use for new array item value
	 * @return		array
	 */
	static function restruct( $array, $key_key = '', $key_val = '' ) {
		
		$ret_val = array();
		
		if( is_array( $array ) && ( count( $array ) > 0 ) && is_scalar( $key_key ) && is_scalar( $key_val ) ) {
		
			if( ( strlen( $key_key ) > 0 ) && ( strlen( $key_val ) > 0 ) ) { // Full restructurization
			
				foreach( $array as $val ) {
					
					isset( $val[$key_key] ) && is_scalar( $val[$key_key] ) && isset( $val[$key_val] ) && ( $ret_val[$val[$key_key]] = $val[$key_val] );
				}
			
			} elseif( strlen( $key_key ) > 0 ) {
			
				foreach( $array as $val ) {
					isset( $val[$key_key] ) && is_scalar( $val[$key_key] ) && ( $ret_val[$val[$key_key]] = $val );
				}
			
			} elseif( strlen( $key_val ) > 0 ) {
				
				foreach( $array as $val ) {
					isset( $val[$key_val] ) && ( $ret_val[] = $val[$key_val] );
				}
			
			} else {
			
				$ret_val = array_values( $array );
			}
		}
		
		return( $ret_val );
	}
}

?>