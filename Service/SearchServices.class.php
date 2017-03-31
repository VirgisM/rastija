<?php

ClassUtils::import( 'utils.ArrayUtils' );
ClassUtils::import( 'utils.VarUtils' );

/**
 * Darbas su paieskos webservisais
 */ 
class SearchServices {
	
	/**
	 * Tai debuginam ar ne
	 */
	public $debug = false;
	
	/**
	 * Jeigu reikia grazinti visa atsakyma, o ne tik reikalingus duomenis
	 */
	public $output_full_result = false;
	
	/**
	 * Webservisu ROOT adresas
	 */
	//protected $ws_root_url = 'http://ncbaze.netcode.lt/rastija/';
	//protected $ws_port = '8081';
	protected $ws_root_url = 'https://sk.xn--ratija-ckb.lt/rastija/';
	protected $ws_port = '443';
	
	/**
	 * WS'u urlai
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
		'word_delete' => array(
			'addr' => 'semisearch/resources?uri=',
			'method' => 'DELETE'
		),
		'ontology_export' => array(
			'addr' => 'semisearch/ontologies?ontology=http%3A%2F%2Fwww.rastija.lt%2Fisteklius',
			'method' => 'POST'
		),
		'discitonary_export' => array(
			'addr' => 'semisearch/ontologies?ontology=http%3A%2F%2Fwww.lexinfo.net%2Flmf',
			'method' => 'POST'
		),
		'ontology_import' => array(
			'addr' => 'semisearch/ontologies/content?ontology=http%3A%2F%2Fwww.lexinfo.net%2Flmf&ontClass=http%3A%2F%2Fwww.lexinfo.net%2Flmf%23LexicalEntry&doIndex=true&prefix=',
			'method' => 'POST',
			'content_type' => 'multipart/form-data'
		),
		'ontology_import_word' => array(
			'addr' => 'semisearch/ontologies/content?ontology=http%3A%2F%2Fwww.lexinfo.net%2Flmf&importTypeEnum=ONTOLOGY&prefix=',
			'method' => 'POST',
			'content_type' => 'multipart/form-data'
		),
		'data_properties' => array(
			'addr' => 'semisearch/ontology/property/range?ontology=http%3A%2F%2Fwww.lexinfo.net%2Flmf&property=http%3A%2F%2Fwww.lexinfo.net%2Flmf%23',
			'method' => 'GET',
		)
	);
	
	/**
	 * Lentos, kur keshuosim reikalus
	 */
	public $params_settings = array(
		'resources' => array(
			'datasource' => 'search_resources',
			'param_uri' => 'http://www.lexinfo.net/lmf#lexicon#name'
		),
		'authors' => array(
			'datasource' => 'search_authors',
			'param_uri' => 'http://www.lexinfo.net/lmf#lexicon#hasAuthor#name'
		),
		'publishers' => array(
			'datasource' => 'search_publishers',
			'param_uri' => 'http://www.lexinfo.net/lmf#lexicon#hasOrganization#name'
		),
		'types' => array(
			'datasource' => 'search_types',
			'param_uri' => 'http://www.lexinfo.net/lmf#lexicon#hasResourceType#name'
		),
		'languages' => array(
			'datasource' => 'search_languages',
			'param_uri' => 'http://www.lexinfo.net/lmf#lexicon#hasLanguage#name'
		),
		'scopes' => array(
			'datasource' => 'search_scopes',
			'param_uri' => 'http://www.lexinfo.net/lmf#hasSense#hasContext#hasTextRepresentation#text'
		),
		'classes' => array(
			'datasource' => 'search_classes',
			'param_uri' => '',
			'value_field' => 'searchClassUri'
		),
		'words' => array(
			'datasource' => 'search_words'
		)
	);
	
	/**
	 * Zodynu informacijos parsinimo schema
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
	 * Zodzio informacijos parsinimo schema
	 */
	protected $word_params = array(
		'partOfSpeech' => array(
			'local_name' => 'speech_part',
			'many' => false
		),
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
				'writtenForm' => 'name',
				'partOfSpeech' => 'speech_part'
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
	*	Paieškos ontologijos clasių reikšmių adresai
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
	 * Konvertuojam objekta i masyva
	 * 
	 * @param		array		$object			Objektas
	 * @return		array						Masyvas
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
	 * Patikrinam ar WS'ai veikia
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
	 * Siunciam uzklausa i WS ir gaunam atsakyma
	 * 
	 * @param		string		$ws_url				I kur siusim nuoroda, adresas imamas is $this->ws_urls
	 * @param		array		$data				Duomenu masyvas, kuri siusim i WS'us
	 * @param		boolean		$return_plain		Ar grazinti ne dekodinta atsakyma
	 * @return		array		$ret_val			Gautu duomenu masyvas
	 */
	public function sendRequest( $ws_url = '', $data = array(), $return_plain = false ) {
		
		$ws_url_endpoint = ( !empty( $this->ws_urls[ $ws_url ]['addr'] ) ) ? $this->ws_root_url . $this->ws_urls[ $ws_url ]['addr'] : '';
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
		
		$timeout = ( !empty( $this->timeout ) && is_numeric( $this->timeout ) ) ? (int)$this->timeout : 10;
		
		// ontologijos importui reikia daug laiko
		$timeout = !( $ws_url == 'ontology_import' ) ? $timeout : 0;
		
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt( $ch, CURLOPT_PORT, $this->ws_port );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $this->ws_urls[ $ws_url ]['method'] );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $httpheaders );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, $this->debug );
		curl_setopt( $ch, CURLINFO_HEADER_OUT, true );
		
		$result = curl_exec( $ch );
		
		if ( $this->debug ) {
			
			$result_tmp = $result;
			
			$info = curl_getinfo( $ch );
			$header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
			$header = substr( $result, 0, $header_size );
			$response = substr( $result, $header_size );
			$body = $this->objectToArray( json_decode( $response ) );
			
			$debug_contents = "REQUEST:\r\n" . $info['request_header'] . "\r\n\r\n" . print_r( $data, true ) . "\r\n\r\n-----\r\n\r\nRESPONSE:\r\n" . $header . "\r\n" . $response . "\r\n\r\n" . print_r( $body, true );
			
			file_put_contents( CACHE_DIR . 'tmp/' . time() . '_' . uniqid() . '.txt', $debug_contents );
			
			$result = $result_tmp;
		}
		
		$ret_val = ( !$return_plain ) ? $this->objectToArray( json_decode( $result ) ) : $result;
		
		curl_close( $ch );
		
		return $ret_val;
	}
	
	/**
	 * Pakesuojam elementus arba grazinam uzkesuotus
	 * 
	 * @return		array		$ret_val		Kalbu masyvas
	 */
	protected function cacheItems( $type, $result ) {
		
		$datasource = $this->params_settings[ $type ]['datasource'];
		$cached_items = $this->dataLayer->select( $datasource );
		$ret_val = array();
		
		if ( !empty( $result['type'] ) && $result['type'] == 'success' && !empty( $result['details'] ) && is_array( $result['details'] ) ) {
			
			$cached_items_restructed = ArrayUtils::restruct( $cached_items, 'resourceUri', 'id' );
			
			foreach ( $result['details'] as $key => $item ) {
				
				if ( empty( $item['properties'] ) ) {
					
					continue;
				}
				
				foreach ( $item['properties'] as $property ) {
					
					if ( $type == 'scopes' && !empty( $property['uri'] ) && $property['uri'] == 'http://www.lexinfo.net/lmf#hasTextRepresentation' && !empty( $property['objectProperty']['properties'] ) ) {
						
						foreach ( $property['objectProperty']['properties'] as $inner_property ) {
							
							if ( !empty( $inner_property['uri'] ) && $inner_property['uri'] == 'http://www.lexinfo.net/lmf#text' && !empty( $inner_property['dataProperty'] ) ) {
								
								$header = $inner_property['dataProperty'];
								
								break 2;
							}
						}
					}
					
					if ( $type != 'scopes' && !empty( $property['uri'] ) && $property['uri'] == 'http://www.rastija.lt/isteklius#name' && !empty( $property['dataProperty'] ) ) {
						
						$header = $property['dataProperty'];
						
						break;
					}
				}
				
				if ( !empty( $header ) ) {
					
					if ( empty( $cached_items_restructed[ $item['resourceUri'] ] ) ) {
						
						$item_id = $this->dataLayer->insert( $datasource, array( 'resourceUri' => $item['resourceUri'], 'header' => $header ) );
					} else {
						
						$item_id = $cached_items_restructed[ $item['resourceUri'] ];						
						$this->dataLayer->update( $datasource, array( 'resourceUri' => $item['resourceUri'], 'header' => $header ), array( 'id' => $cached_items_restructed[ $item['resourceUri'] ] ) );						
					}
					
					$item = reset( $this->dataLayer->select( $datasource, array( 'id' => $item_id ) ) );
					$ret_val[] = $item;
				}
			}
		}
		
		return $ret_val;
	}
	
	/**
	 * Imam ontologijos klases is DB
	 *
	 * @param		integer		$parent_id				Tevinio elemento ID
	 * @param		array		$class_values			Cia is zodzio ateinancios reikmes, gali ir nebut
	 * @param		integer		$level					Einamasis lygis
	 * @param		array		$parent_item			Tevinis elementas
	 * @return		array		$ret_val				Klasiu masyvas
	 */
	public function getOntologyClasses( $parent_id = 0, $class_values = array(), $level = 0, $parent_item = array() ) {
		
		$filter = array(
			'parent_id' => $parent_id
		);
		
		if ( $level == 0 ) {
			
			$filter['in|uri'] = array(
				'http://www.lexinfo.net/lmf#hasLemma',
				'http://www.lexinfo.net/lmf#hasWordForm',
				'http://www.lexinfo.net/lmf#hasSense'
			);
		} elseif ( $level == 2 ) {
			
			if ( !empty( $parent_item ) && $parent_item['searchClassUri'] == 'http://www.lexinfo.net/lmf#hasWordForm' ) {
				
				$filter['in|searchClassUri'] = array(
					'http://www.lexinfo.net/lmf#hasWordForm#grammaticalGender',
					'http://www.lexinfo.net/lmf#hasWordForm#grammaticalNumber',
					'http://www.lexinfo.net/lmf#hasWordForm#declension',
					'http://www.lexinfo.net/lmf#hasWordForm#accentuation'
				);
			} elseif ( !empty( $parent_item ) && $parent_item['searchClassUri'] == 'http://www.lexinfo.net/lmf#hasLemma' ) {
				
				// Cia unsetinam, nes reikia paimti 'http://www.lexinfo.net/lmf#lexicon#hasLexicalEntry#abbreviation' is kito lygio
				unset( $filter['parent_id'] );
				
				$filter['in|searchClassUri'] = array(
					'http://www.lexinfo.net/lmf#hasLemma#comment',
					'http://www.lexinfo.net/lmf#lexicon#hasLexicalEntry#abbreviation',
					'http://www.lexinfo.net/lmf#hasLemma#origin',
					'http://www.lexinfo.net/lmf#hasLemma#writtenForm',
					'http://www.lexinfo.net/lmf#hasLemma#accentuation'
				);
			} elseif ( !empty( $parent_item ) && $parent_item['searchClassUri'] == 'http://www.lexinfo.net/lmf#hasSense' ) {
				
				$filter['in|searchClassUri'] = array(
					'http://www.lexinfo.net/lmf#hasSense#hasEquivalent',
					'http://www.lexinfo.net/lmf#hasSense#hasSubjectField',
					'http://www.lexinfo.net/lmf#hasSense#hasDefinition',
					'http://www.lexinfo.net/lmf#hasSense#hasSenseRelation'
				);
			}
		} elseif ( $level == 4 ) {
			
			$filter['in|searchClassUri'] = array(
				'http://www.lexinfo.net/lmf#hasSense#hasEquivalent#language',
				'http://www.lexinfo.net/lmf#hasSense#hasSubjectField#status',
				'http://www.lexinfo.net/lmf#hasSense#hasDefinition#definition',
				'http://www.lexinfo.net/lmf#hasSense#hasSenseRelation#type'
			);
		}
		
		$ret_val = $this->dataLayer->select( $this->params_settings['classes']['datasource'], $filter );
		
		foreach ( $ret_val as $key => $value ) {
			
			$children = $this->getOntologyClasses( $value['id'], $class_values, $value['level'], $value );
			
			if ( !empty( $children ) ) {
				
				foreach ( $children as $key2 => $child ) {
					
					// Cia hackas, nes elementas yra is ne ten is kur priklauso
					if ( $child['searchClassUri'] == 'http://www.lexinfo.net/lmf#lexicon#hasLexicalEntry#abbreviation' ) {
						
						$children[ $key2 ]['level'] = 3;
					}
					
					$children[ $key2 ]['value'] = ( !empty( $child['searchClassUri'] ) && !empty( $class_values[ $child['searchClassUri'] ] ) ) ? $class_values[ $child['searchClassUri'] ] : '';
				}
				
				$ret_val[ $key ]['_children_'] = $children;
			}
		}
		
		if ( empty( $parent_id ) ) {
			
			$ret_val = $this->clearOntologyClasses( $ret_val, $class_values );
		}
		
		return $ret_val;
	}
	
	/**
	 * Pravalom klasiu medi, ismetam besidubliuojanciu pavadinimu elementus (CLASS).
	 *
	 * @param		array		$classes				Klases
	 * @param		array		$class_values			Klasiu reiksmes	 
	 * @return		array		$classes				Klases
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
	 * Kesuojam ontologijos klases
	 *
	 * @param		string		$ont_class				Ontologijos klase
	 * @param		string		$search_class_uri		Dar kazkokia klase
	 * @param		integer		$level					Prie masyvo priseginesim lygi, kad po to HTML'e butu paprasciau isvest
	 * @param		integer		$parent_id				Tevinio elemento ID musu duombazej
	 * @return		boolean								TRUE/FALSE
	 */
	public function cacheOntologyClasses( $ont_class = '', $search_class_uri = '', $level = 1, $parent_id = 0 ) {
		
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
			
			$class['level'] = $level;
			$class['parent_id'] = $parent_id;
			$check_item = reset( $this->dataLayer->select( $this->params_settings['classes']['datasource'], $class ) );
			
			$id = ( empty( $check_item ) ) ? $this->dataLayer->insert( $this->params_settings['classes']['datasource'], $class ) : $check_item['id'];
			
			if ( !in_array( $class['type'], array( 'OBJECT_PROPERTY', 'CLASS' ) ) ) {
				
				continue;
			}
			
			$this->ws_urls['classes']['addr'] = $addr;
			
			if ( $level < 5 ) {
				
				$this->cacheOntologyClasses( $class['uri'], $class['searchClassUri'], ( $level + 1 ), $id );
			}
		}
		
		return true;
	}
	
	/**
	 * Imam ontologijos klases atributus
	 * 
	 * @return		array		$ret_val		Atributu masyvas
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
	 * Imam ivairius parametrus ( isteklius/zodynus, autorius, leidejus, tipus, kalbas, sritis)
	 * (resources, authors, publishers, types, languages, scopes)
	 * 
	 * @return		array		$ret_val		Kalbu masyvas
	 */
	public function getParams( $type = '' ) {
		
		$ret_val = array();
		
		if ( empty( $this->params_settings[ $type ] ) ) {
			
			return $ret_val;
		}
		
		// Autorius imam is facetu, LOL.
		if ( $type == 'authors' ) {
			
			$result_authors = $this->doSearch( array( 'q' => '' ), 1, 1 );
		}
		
		$result = $this->sendRequest( $type );
		
		if ( $this->output_full_result ) {
			
			return $result;
		}
		
		$ret_val = $this->cacheItems( $type, $result );
		
		if ( $type == 'authors' ) {
			
			if ( !empty( $result_authors['facets']['authors']['counts'] ) ) {
				
				$ret_val = array();
				
				foreach ( $result_authors['facets']['authors']['counts'] as $author ) {
					
					$ret_val[] = array(
						'id' => $author['id'],
						'resourceUri' => $author['resourceUri'],
						'header' => $author['value']
					);
				}
			}
		}
		
		if ( $type == 'publishers' ) {
			
			foreach ( $ret_val as $key => $value ) {
				
				if ( $value['resourceUri'] == 'http://www.rastija.lt/isteklius#Organization.Lietuvių_kalbos_institutas' ) {
					
					unset( $ret_val[ $key ] );
				}
			}
		}
		
		return $ret_val;
	}
	
	/**
	 * Susidesim detalio paieskos parametrus
	 *
	 * @param		array		$form_data					Paieskos formos duomenys
	 * @return		array		$property_details_list		Suformuotas parametru masyvas
	 */
	public function addSearchParams( $form_data = array() ) {
		
		$property_details_list = array();
			
		// if ( empty( $form_data['resources'] ) ) {
			
		// 	$dictionaries = ArrayUtils::restruct( $this->getDictionaries(), '', 'resource_uri' );
		// 	$not_public_dictionaries = array_filter( ArrayUtils::restruct( $this->oDictionaries->getItems( array( 'is_public' => 0 ) ), 'id', 'resource_uri' ) );
		// 	$search_dictionaries = $this->dataLayer->select( $this->params_settings['resources']['datasource'] );
			
		// 	foreach ( $search_dictionaries as $key => $dictionary ) {
				
		// 		if ( in_array( $dictionary['resourceUri'], $not_public_dictionaries ) || !in_array( $dictionary['resourceUri'], $dictionaries ) ) {
					
		// 			unset( $search_dictionaries[ $key ] );
		// 		}
		// 	}
			
		// 	$form_data['resources'] = ArrayUtils::restruct( $search_dictionaries, '', 'id' );
		// }
		
		foreach ( $this->params_settings as $type => $param_settings ) {
			
			if ( empty( $form_data[ $type ] ) ) {
				
				continue;
			}
			
			$param_ids = $form_data[ $type ];
			
			if ( $type == 'classes' ) {
				
				$items = ArrayUtils::restruct( $this->dataLayer->select( $this->params_settings[ $type ]['datasource'], array( 'id' => $param_ids ) ), 'id' );
			} else {
				
				$items = ArrayUtils::restruct( $this->dataLayer->select( $this->params_settings[ $type ]['datasource'], array( 'id' => $param_ids ) ), 'id', 'header' );
			}
			
			$param_uri = $this->params_settings[ $type ]['param_uri'];
			
			foreach ( $items as $item ) {
				
				$value = ( $type == 'classes' ) ? $form_data['q'] : $item;
				
				if ( $type == 'classes' && !empty( $form_data['classes_value'][ $item['id'] ] ) ) {
					
					$value = $form_data['classes_value'][ $item['id'] ];
				}
				
				$uri = ( $type == 'classes' ) ? $item['searchClassUri'] : $param_uri;
				
				// perrasau uri gauta is db su aprasyta prie klases parametru, situos uri dave klientas. ziauru...
				if ( $type == 'classes' && !empty( $this->ontology_search_param_urls[ $item['label'] ] ) ) {
						
					$uri = $this->ontology_search_param_urls[ $item['label'] ];
				}
				
				$property_details_list[] = array(
					'uri' => $uri,
					'value' => $value
				);
			}
		}
		
		// Nu cia truputi burtas su datomis, kad jeigu kiaurai ivede, tai apverciam ir pan.
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
	 * Suliejam pagal nustatymus masyvus
	 *
	 * @param		array		$curr_value			Dabartinis masyvas
	 * @param		array		$settings			Nustatymu masyvas
	 * @param		array		$value				Masyvas, kuri prijunginesim
	 * @return		array		$curr_value			Sujungtas masyvas
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
	 * Imam rezultatus
	 *
	 * @param		array		$form_data			Paieskos formos duomenys
	 * @param		integer		$items_on_page		Kiek elementu puslapyje
	 * @param		integer		$page				Kelintas puslapis
	 * @return		array		$ret_val			Kalbu masyvas
	 */
	public function doSearch( $form_data = array(), $items_on_page = 10, $page = 1, $real_search = false ) {
		
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
		
		// Cia guli visi paieskos rezultatai
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
		
		if ( $real_search ) {
			
			$last_search_data = array(
				'page' => $page,
				'f' => $form_data
			);
			
			$this->objEnv->setSData( 'last_search_data', $last_search_data );
		}
		
		return $ret_val;
	}
	
	/**
	 * Imam rezultatus ir grazinam neisparsintus
	 *
	 * @param		array		$form_data			Paieskos formos duomenys
	 * @param		integer		$items_on_page		Kiek elementu puslapyje
	 * @param		integer		$page				Kelintas puslapis
	 * @return		array		$ret_val			Kalbu masyvas
	 */
	public function doSearch2( $data ) {
		
		$ret_val = array();

		$result = $this->sendRequest( 'search', $data );
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' || empty( $result['details']['totalElements'] ) ) {
			
			return $ret_val;
		}
		
		return $result;
	}
	
	/**
	 * Parsinam zodzio medi.
	 *
	 * @param		array		$piece				Dalis
	 * @param		string		$uri				Uri dalis
	 * @param		array		$ret_val			Rezultatas (rekursija)
	 * @return		array		$ret_val			Rezultatas
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
	 * Issisaugom zodzio klasiu medzio reiksmes, o ne perrasom ant virsaus
	 *
	 * @param		array		$piece				Zodzio klases
	 * @return		array		$ret_val			Rezultatas
	 */
	public function saveWordClassTreeValues( $word_class_tree ) {
		
		// susidedam i value visas reiksmes, o ne paskutine kaip buvo :)
		if ( empty( $word_class_tree ) ) {
			
			return array();
		}
			
		$saved_values = array();
		foreach ( $word_class_tree as $key => $item ) {
			
			if ( !strpos( 'http://www.rastija.lt/isteklius#', $key ) ) {
				
				$item['value'] = ( strlen( strip_tags( $item['value'] ) ) > 50 ) ? substr( strip_tags( $item['value'] ), 0, strrpos( substr( strip_tags( $item['value'] ), 0, 50 ), ' ' ) ) . '...' : $item['value'];
				
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
		
		// panaikinam dubliuotas reiksmes
		if ( !empty( $saved_values ) ) {
			
			foreach ( $saved_values  as $key => $value ) {
				
				if ( is_array( $value['value'] ) ) {
					
					$saved_values[ $key ]['value'] = array_unique( $value['value'] );
				}
			}
		}
		
		// dar vienas hakas, pakeiciam adresus, pvz reiksmiu apibreztis laikom pagrindinio zodzio apibreztimis
		$change_uri = array(
			// 'http://www.lexinfo.net/lmf#hasWordForm#accentuation' 		=> 'http://www.lexinfo.net/lmf#hasLemma#accentuation', // gal prireiks
			// 'http://www.lexinfo.net/lmf#hasWordForm#grammaticalGender' 	=> 'http://www.lexinfo.net/lmf#hasForm#grammaticalGender', // gal prireiks
			// 'http://www.lexinfo.net/lmf#hasWordForm#grammaticalNumber'	=> 'http://www.lexinfo.net/lmf#hasForm#grammaticalNumber', // gal prireiks
			// 'http://www.lexinfo.net/lmf#hasWordForm#grammaticalTense'	=> 'http://www.lexinfo.net/lmf#hasForm#grammaticalTense', // gal prireiks
			'http://www.lexinfo.net/lmf#hasSense#hasDefinition#hasTextRepresentation#writtenForm'	=> 'http://www.lexinfo.net/lmf#hasSense#hasDefinition#definition',
		);
		
		// sujungiam reiksmes, vienisas paliekam stringais, jei ne - dedam i masyva 
		foreach ( $change_uri as $bad_uri => $good_uri ) {
			
			// yra url kuri reik pakeist
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
		
		$ret_val = ( !empty( $saved_values ) ) ? ArrayUtils::restruct( $saved_values, 'uri', 'value' ) : array();
				
		return $ret_val;
	}
	
	/**
	 * Parsinam vieno zodzio duomenu masyva
	 *
	 * @param		array		$one_result						Zodzio masyvas is WS'u
	 * @param		boolean		$skip_additional_parse			Jeigu TRUE, tai skipinsim ontologijos medzio ir zodyno informacijos parsinima
	 * @return		array		$word_arr						Isparsintas zodzio masyvas
	 */
	public function parseWordData( $one_result = array(), $skip_additional_parse = false, $skip_tree_parse = false ) {
		
		$word_arr = $connections_arr = array();
		
		if ( empty( $one_result ) ) {
			
			return $word_arr;
		}
		
		if ( !$skip_additional_parse && !$skip_tree_parse ) {
			
			// new version
			$word_class_tree = ( !empty( $one_result['properties'] ) ) ? $this->parseWordClassTree( $one_result['properties'] ) : array();
			$word_arr['word_class_tree'] = $this->saveWordClassTreeValues( $word_class_tree );
			
			// old version
			// $word_arr['word_class_tree'] = ( !empty( $one_result['properties'] ) ) ? ArrayUtils::restruct( $this->parseWordClassTree( $one_result['properties'] ), 'uri', 'value' ) : array();
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
									$name = $parsed_data['writtenForm'];
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
			
			// Zodynas
			if ( !$skip_additional_parse && $property['uri'] == 'http://www.rastija.lt/isteklius#lexicon' ) {
				
				$dictionary = $this->parseDictionaryInfo( $property );
				$word_arr['dictionary'] = ( !empty( $dictionary['dictionary_id'] ) ) ? $dictionary : array();
				
				continue;
			}
			
			$property_name = ( !empty( $property['uri'] ) ) ? end( explode( '#', $property['uri'] ) ) : '';
			
			if ( empty( $property_name ) || empty( $this->word_params[ $property_name ] ) ) {
				
				continue;
			}
			
			$parsed_data = $this->parsePiece( $property, $property_name, 'word' );
			$word_arr = $this->addToArray( $word_arr, $this->word_params[ $property_name ], $parsed_data );
		}
		
		if ( !empty( $word_arr['meanings'] ) && !empty( $word_arr['meanings'][0]['rank'] ) ) {
			
			ArrayUtils::sort( $word_arr['meanings'], 'rank', SO_ASC );
		}
		
		$insert_arr = array(
			//'word_data' => serialize( $word_arr ),
			'uri' => $one_result['header'],
			'resourceUri' => $one_result['resourceUri']
		);
		
		$check_item = reset( $this->dataLayer->select( $this->params_settings['words']['datasource'], array( 'eq|uri' => $one_result['header'], 'eq|resourceUri' => $one_result['resourceUri'] ) ) );
		$word_id = ( !empty( $check_item['id'] ) ) ? $check_item['id'] : 0;
		
		if ( empty( $check_item ) ) {
			
			$word_id = $this->dataLayer->insert( $this->params_settings['words']['datasource'], $insert_arr );
		}
		/*elseif ( $check_item['word_data'] != $insert_arr['word_data'] ) {
			
			$word_id = $check_item['id'];
			$this->dataLayer->update( $this->params_settings['words']['datasource'], $insert_arr, array( 'id' => $check_item['id'] ) );
		}*/
		
		$word_arr['resourceUri'] = $one_result['resourceUri'];
		$word_arr['word_id'] = $word_id;
		$word_arr['connections'] = $connections_arr;
		
		return $word_arr;
	}
	
	/**
	 * Parsinam masyvo dali
	 *
	 * @param		array		$piece					Masyvo dalis parsinimui
	 * @param		string		$property_name			Parametro pavadinimas
	 * @param		string		$type					Kieno dali parsinam: zodzio ar zodyno (word, dictionary)	 
	 * @param		array		$ret_val				Rekursija
	 * @return		array		$ret_val				Rezultatas
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
				$ret_val[ $field_name ] = str_replace( '</strong>', '</b>', $property['dataProperty'] );
			}
		}
		
		if ( !empty( $piece['objectProperty']['classUri'] ) && $piece['objectProperty']['classUri'] == 'http://www.lexinfo.net/lmf#Equivalent' ) {
			
			$ret_val['uri'] = !empty( $piece['objectProperty']['resourceUri'] ) ? $piece['objectProperty']['resourceUri'] : '';
		}
		
		return $ret_val;
	}
	
	/**
	 * Is atsakymo parsinames zodynu informacija
	 *
	 * @param		array		$result_arr			Rezultatu masyvas
	 * @return		array		$ret_val			Zodyno informacijos masyvas
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
		
		$dictionary_item = reset( $this->dataLayer->select( $this->params_settings['resources']['datasource'], array( 'eq|resourceUri' => $result_arr['objectProperty']['resourceUri'] ) ) );
		$ret_val['dictionary_id'] = ( !empty( $dictionary_item ) ) ? $dictionary_item['id'] : 0;
		$ret_val['resource_uri'] = ( !empty( $result_arr['objectProperty']['resourceUri'] ) ) ? $result_arr['objectProperty']['resourceUri'] : '';
		
		return $ret_val;
	}
	
	/**
	 * Is rezultatu parsinam facetus, kurie reikalingi paieskos tikslinimui, siaurinimui
	 *
	 * @param		array		$result_arr			Rezultatu masyvas
	 * @return		array		$ret_val			Facetu masyvas
	 */
	protected function parseFacets( $result_arr = array() ) {
		
		$ret_val = array();
		
		foreach ( $this->params_settings as $type => $param ) {
			
			if ( empty( $param['param_uri'] ) ) {
				
				continue;
			}
			
			$valid_face_urls[ $param['param_uri'] ] = $type;
		}
		
		$not_public_dictionaries = array_filter( ArrayUtils::restruct( $this->oDictionaries->getItems( array( 'is_public' => 0 ) ), 'id', 'name' ) );
		
		// hako hakas, jei yra du zodynai vienodu pavadinimu o kazkuris yra viesas, kazkuris ne, laikom viesu
		$public_dictionaries = array_filter( ArrayUtils::restruct( $this->oDictionaries->getItems( array( 'is_public' => 1 ) ), 'id', 'name' ) );
		
		foreach ( $not_public_dictionaries as $key => $not_public ) {
			
			if ( in_array( $not_public, $public_dictionaries ) ) {
				
				unset( $not_public_dictionaries[ $key ] );
			}
		}
		
		foreach ( $result_arr['details']['facetDetailsList'] as $result ) {
			
			if ( !empty( $result['classUri'] ) && !empty( $valid_face_urls[ $result['classUri'] ] ) ) {
				
				$key = $valid_face_urls[ $result['classUri'] ];
				$ret_val[ $key ]['uri'] = $result['classUri'];
				$ret_val[ $key ]['counts'] = $result['countList'];
				$datasource = $this->params_settings[ $key ]['datasource'];
				
				foreach ( $ret_val[ $key ]['counts'] as $count_key => $item ) {
					
					if ( $key == 'resources' && in_array( $item['valueUri'], $not_public_dictionaries ) ) {
						
						unset( $ret_val[ $key ]['counts'][ $count_key ] );
						
						continue;
					}
					
					$check_item = reset( $this->dataLayer->select( $datasource, array( 'eq|header' => $item['value'] ), array( 'id' => 'DESC' ) ) );
					$ret_val[ $key ]['counts'][ $count_key ]['id'] = ( empty( $check_item ) ) ? 0 : $check_item['id'];
					$ret_val[ $key ]['counts'][ $count_key ]['resourceUri'] = ( empty( $check_item ) ) ? '' : $check_item['resourceUri'];
				}
			}
		}
		
		return $ret_val;
	}
	
	/**
	 * Importuojam ontologija
	 *
	 * @param		string		$acronym				Zodyno akronimas
	 * @param		araay		$file_data				Importo failo duomenys
	 * @return		boolean								TRUE/FALSE
	 */
	public function importOntology( $acronym = '', $file_data = array() ) {
		
		if ( empty( $acronym ) || empty( $file_data ) ) {
			
			return false;
		}
		
		$data = array(
			'file' => '@' . $file_data['tmp_name'],
			'filename' => $file_data['name'],
			'filesize' => $file_data['size']
		);
		
		$addr = $this->ws_urls['ontology_import']['addr'];
		$this->ws_urls['ontology_import']['addr'] .= urlencode( $acronym );
		$result = $this->sendRequest( 'ontology_import', $data );
		$this->ws_urls['ontology_import']['addr'] = $addr;
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' ) {
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Importuojam žodžio ontologija
	 *
	 * @param		araay		$file_data				Importo failo duomenys
	 * @return		boolean								TRUE/FALSE
	 */
	public function importOntologyWord( $file_data = array() ) {
		
		if ( empty( $file_data ) ) {
			
			return false;
		}
		
		$data = array(
			'file' => '@' . $file_data['tmp_name'],
			'filename' => $file_data['name'],
			'filesize' => $file_data['size']
		);
		
		$addr = $this->ws_urls['ontology_import_word']['addr'];
		$this->ws_urls['ontology_import_word']['addr'] .= urlencode( 'mat_term' );
		$result = $this->sendRequest( 'ontology_import_word', $data );
		$this->ws_urls['ontology_import_word']['addr'] = $addr;
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' ) {
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Exportuojam zodyno zodzius
	 *
	 * @param		integer		$dictionary_id			Zodyno ID
	 * @return		string		$ret_val				Eksporto failo turinys
	 */
	public function exportDictionaryWords( $dictionary_id = 0 ) {
		
		$dictionary_item = ( !empty( $dictionary_id ) ) ? $this->oDictionaries->getItem( array( 'id' => $dictionary_id ) ) : array();
		$dictionaries = ( !empty( $dictionary_item ) ) ? ArrayUtils::restruct( $this->getDictionaries(), 'resource_uri', 'name' ) : array();
		
		if ( !empty( $dictionary_item['resource_uri'] ) && !empty( $dictionaries[ $dictionary_item['resource_uri'] ] ) ) {
			
			$data = array(
				'freeTextQuery' => '',
				'propertyDetailsList' => array(
					array(
						'uri' => 'class',
						'value' => 'http://www.lexinfo.net/lmf#LexicalEntry'
					),
					array(
						'uri' => 'http://www.lexinfo.net/lmf#lexicon#name',
						'value' => $dictionaries[ $dictionary_item['resource_uri'] ]
					)
				)
			);
		}
		
		if ( empty( $data ) ) {
			
			return false;
		}
		
		$result = $this->sendRequest( 'discitonary_export', $data, true );
		
		return $result;
	}
	
	/**
	 * Exportuojam ontologija
	 *
	 * @param		integer		$dictionary_id			Zodyno ID
	 * @return		string		$ret_val				Eksporto failo turinys
	 */
	public function exportOntology( $dictionary_id = 0 ) {
		
		if ( !empty( $dictionary_id ) ) {
			
			$dictionaries = ArrayUtils::restruct( $this->getDictionaries(), 'dictionary_id', 'name' );
		}
		
		$data = array(
			'freeTextQuery' => '',
			'propertyDetailsList' => array(
				array(
					'uri' => 'class',
					'value' => 'http://www.rastija.lt/isteklius#Resource'
				)
			)
		);
		
		if ( !empty( $dictionaries[ $dictionary_id ] ) ) {
			
			$data['propertyDetailsList'][] = array( 
				'uri' => 'http://www.rastija.lt/isteklius#name',
				'value' => $dictionaries[ $dictionary_id ]
			);
		}
		
		$result = $this->sendRequest( 'ontology_export', $data, true );
		
		return $result;
	}
	
	/**
	 * Eksportavimas ontologijos be įrašų
	 *
	 * @return		string		$ret_val				Eksporto failo turinys
	 */
	public function exportOntologyPlain() {
		
		$result = $this->sendRequest( 'discitonary_export', '{}', true );
		
		return $result;
	}
	
	/**
	 * Parsipuciam zodzio eksporto failo turini ir ji grazinam
	 *
	 * @param		integer		$word_id			Zodyno ID musu DB
	 * @return		string		$ret_val			Eksporto failo turinys
	 */
	public function getWordLmf( $word_id = 0 ) {
		
		$ret_val = '';
		$word_item = ( !empty( $word_id ) ) ? reset( $this->dataLayer->select( $this->params_settings['words']['datasource'], array( 'id' => $word_id ) ) ) : array();
		
		if ( empty( $word_item ) ) {
			
			return $ret_val;
		}
		
		$addr = $this->ws_urls['ontologies']['addr'];
		$this->ws_urls['ontologies']['addr'] .= '&resourceUri=' . urlencode( $word_item['resourceUri'] );
		$this->timeout = 120;
		$result = $this->sendRequest( 'ontologies', '{}', true );
		$this->timeout = 10;
		$this->ws_urls['ontologies']['addr'] = $addr;
		
		return $result;
	}
	
	/**
	 * Parsipuciam zodyno eksporto failo turini ir ji atiduodam atgal
	 *
	 * @param		integer		$dictionary_id			Zodyno ID musu DB
	 * @return		string		$ret_val				Eksporto failo turinys
	 */
	public function getDictionaryLmf( $dictionary_id = 0 ) {
		
		$ret_val = '';
		$dictionary_item = ( !empty( $dictionary_id ) ) ? reset( $this->dataLayer->select( $this->params_settings['resources']['datasource'], array( 'id' => $dictionary_id ) ) ) : array();
		
		if ( empty( $dictionary_item ) ) {
			
			return $ret_val;
		}
		
		$data = array(
			'freeTextQuery' => '',
			'propertyDetailsList' => array(
				array(
					'uri' => 'class',
					'value' => 'http://www.lexinfo.net/lmf#LexicalEntry'
				),
				array(
					'uri' => 'http://www.lexinfo.net/lmf#lexicon#name',
					'value' => $dictionary_item['header']
				)
			)
		);
		
		$this->timeout = 300;
		$result = $this->sendRequest( 'ontologies', $data, true );
		$this->timeout = 10;
		
		return $result;
	}
	
	/**
	 * Updeitinam arba kuriam nauja zodyna WS'uose. Kursim tada, kai pas mus DB nebus prie zodyno 'resourceUri'
	 *
	 * @param		integer		$dictionary_id			Zodyno ID musu DB
	 * @return		boolean								TRUE/FALSE
	 */
	public function modifyDictionary( $dictionary_id = 0 ) {
		
		$filter = array(
			'id' => $dictionary_id,
			'_with_annotator_' => 1,
			'_with_author_' => 1,
			'_with_editor_' => 1,
			'_with_languages_' => 1,
			'_with_publisher_' => 1,
			'_with_source_type_' => 1
		);
		
		$dictionary_item = ( !empty( $dictionary_id ) ) ? $this->oDictionaries->getItem( $filter ) : array();
		
		$data = array(
			'classUri' => 'http://www.rastija.lt/isteklius#Resource',
			'header' => $dictionary_item['name'], // Zodyno pavadinimas
			'ontologyUri' => 'http://www.rastija.lt/isteklius'
		);
		
		if ( !empty( $dictionary_item['resource_uri'] ) ) {
			
			$data['resourceUri'] = $dictionary_item['resource_uri'];
		}
		
		$data['properties'] = array();
		
		// Anotacija
		if (
			!empty( $dictionary_item['_annotator_']['name'] )
			|| !empty( $dictionary_item['_annotator_']['email'] )
			|| !empty( $dictionary_item['annotation_type'] )
			|| !empty( $dictionary_item['annotation_text'] )
		) {
			
			 $annotation_arr = array(
				'uri' => 'http://www.rastija.lt/isteklius#hasAnnotation',
				'objectProperty' => array(
					'classUri' => 'http://www.rastija.lt/isteklius#Annotation',
					'header' => ( !empty( $dictionary_item['annotation_text'] ) ) ? $dictionary_item['annotation_text'] : '', // Pavadinimas
					'properties' => array(
						array(
							'uri' => 'http://www.rastija.lt/isteklius#hasAnnotator',
							'objectProperty' => array(
								'classUri' => 'http://www.rastija.lt/isteklius#Person',
								'header' => ( !empty( $dictionary_item['_annotator_']['name'] ) ) ? $dictionary_item['_annotator_']['name'] : '', // Vardas, pavarde
								'properties' => array(
									array(
										'uri' => 'http://www.rastija.lt/isteklius#email',
										'dataProperty' => ( !empty( $dictionary_item['_annotator_']['email'] ) ) ? $dictionary_item['_annotator_']['email'] : '' // El. pasto adresas
									),
									array(
										'uri' => 'http://www.rastija.lt/isteklius#name',
										'dataProperty' => ( !empty( $dictionary_item['_annotator_']['name'] ) ) ? $dictionary_item['_annotator_']['name'] : '' // Vardas, pavarde
									)
								)
							)
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#annotationType',
							'dataProperty' => ( !empty( $dictionary_item['annotation_type'] ) ) ? $dictionary_item['annotation_type'] : '' // Anotacijos tipas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#text',
							'dataProperty' => ( !empty( $dictionary_item['annotation_text'] ) ) ? $dictionary_item['annotation_text'] : '' // Anotacijos pavadinimas
						)
					)
				)
			);
			
			if ( !empty( $dictionary_item['annotation_resource_uri'] ) ) {
				
				$annotation_arr['objectProperty']['resourceUri'] = $dictionary_item['annotation_resource_uri'];
				$annotation_arr['objectProperty']['ontologyUri'] = 'http://www.rastija.lt/isteklius';
			}
			
			$data['properties'][] = $annotation_arr;
		}
		
		// Autorius
		if ( !empty( $dictionary_item['_authors_'] ) ) {
			
			foreach ( $dictionary_item['_authors_'] as $author ) {
				
				if ( empty( $author['name'] ) && empty( $author['email'] ) ) { 
					
					continue;
				}
				
				$author_arr =  array(
					'uri' => 'http://www.rastija.lt/isteklius#hasAuthor',
					'objectProperty' => array(
						'classUri' => 'http://www.rastija.lt/isteklius#Person',
						'header' => ( !empty( $author['name'] ) ) ? $author['name'] : '', // Autoriaus vardas
						'properties' => array(
							array(
								'uri' => 'http://www.rastija.lt/isteklius#email',
								'dataProperty' => ( !empty( $author['email'] ) ) ? $author['email'] : '' // El. pasto adresas
							),
							array(
								'uri' => 'http://www.rastija.lt/isteklius#name',
								'dataProperty' => ( !empty( $author['name'] ) ) ? $author['name'] : '' // Vardas, pavarde
							)
						)
					)
				);
				
				if ( !empty( $author['resource_uri'] ) ) {
					
					$author_arr['objectProperty']['resourceUri'] = $author['resource_uri'];
					$author_arr['objectProperty']['ontologyUri'] = 'http://www.rastija.lt/isteklius';
				}
				
				$data['properties'][] = $author_arr;
			}
		}
		
		// Redaktorius
		if ( !empty( $dictionary_item['_editors_'] ) ) {
			
			foreach ( $dictionary_item['_editors_'] as $editor ) {
				
				if ( empty( $editor['name'] ) && empty( $editor['email'] ) ) { 
					
					continue;
				}
				
				$editor_arr = array(
					'uri' => 'http://www.rastija.lt/isteklius#hasEditor',
					'objectProperty' => array(
						'classUri' => 'http://www.rastija.lt/isteklius#Person',
						'header' => ( !empty( $editor['name'] ) ) ? $editor['name'] : '', // Autoriaus vardas
						'properties' => array(
							array(
								'uri' => 'http://www.rastija.lt/isteklius#email',
								'dataProperty' => ( !empty( $editor['email'] ) ) ? $editor['email'] : '' // El. pasto adresas
							),
							array(
								'uri' => 'http://www.rastija.lt/isteklius#name',
								'dataProperty' => ( !empty( $editor['name'] ) ) ? $editor['name'] : '' // Vardas, pavarde
							)
						)
					)
				);
				
				if ( !empty( $editor['resource_uri'] ) ) {
					
					$editor_arr['objectProperty']['resourceUri'] = $editor['resource_uri'];
					$editor_arr['objectProperty']['ontologyUri'] = 'http://www.rastija.lt/isteklius';
				}
				
				$data['properties'][] = $editor_arr;
			}
		}
		
		// Kalbos
		if ( !empty( $dictionary_item['_languages_'] ) ) {
			
			foreach ( $dictionary_item['_languages_'] as $language ) {
				
				if ( empty( $language['name_lt'] ) ) {
					
					continue;
				}
				
				$language_arr = array(
					'uri' => 'http://www.rastija.lt/isteklius#hasLanguage',
					'objectProperty' => array(
						'classUri' => 'http://www.rastija.lt/isteklius#Language',
						'header' => $language['name_lt'], // Pavadinimas
						'properties' => array(
							array(
								'uri' => 'http://www.rastija.lt/isteklius#name',
								'dataProperty' => $language['name_lt'] // Pavadinimas
							)
						)
					)
				);
				
				if ( !empty( $language['resource_uri'] ) ) {
					
					$language_arr['objectProperty']['resourceUri'] = $language['resource_uri'];
					$language_arr['objectProperty']['ontologyUri'] = 'http://www.rastija.lt/isteklius';
				}
				
				$data['properties'][] = $language_arr;
			}
		}
		
		// Licensija
		if (
			!empty( $dictionary_item['license_name'] )
			|| !empty( $dictionary_item['license_type'] )
			|| !empty( $dictionary_item['license_distributor'] )
			|| !empty( $dictionary_item['license_user'] )
			|| !empty( $dictionary_item['license_price'] )
		) {
			
			$license_arr = array(
				'uri' => 'http://www.rastija.lt/isteklius#hasLicence',
				'objectProperty' => array(
					'classUri' => 'http://www.rastija.lt/isteklius#Licence',
					'header' => ( !empty( $dictionary_item['license_name'] ) ) ? $dictionary_item['license_name'] : '', // Pavadinimas
					'properties' => array(
						array(
							'uri' => 'http://www.rastija.lt/isteklius#licenceDistributor',
							'dataProperty' => ( !empty( $dictionary_item['license_distributor'] ) ) ? $dictionary_item['license_distributor'] : '' // Platintojas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#licenceOwner',
							'dataProperty' => ( !empty( $dictionary_item['license_user'] ) ) ? $dictionary_item['license_user'] : '' // Naudotojas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#licenceType',
							'dataProperty' => ( !empty( $dictionary_item['license_type'] ) ) ? $dictionary_item['license_type'] : '' // Tipas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#name',
							'dataProperty' => ( !empty( $dictionary_item['license_name'] ) ) ? $dictionary_item['license_name'] : '' // Pavadinimas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#licencePrice',
							'dataProperty' => ( !empty( $dictionary_item['license_price'] ) ) ? $dictionary_item['license_price'] : '' // Kaina
						)
					)
				)
			);
			
			if ( !empty( $dictionary_item['license_resource_uri'] ) ) {
				
				$license_arr['objectProperty']['resourceUri'] = $dictionary_item['license_resource_uri'];
				$license_arr['objectProperty']['ontologyUri'] = 'http://www.rastija.lt/isteklius';
			}
			
			$data['properties'][] = $license_arr;
		}
		
		// Leidejas
		if (
			!empty( $dictionary_item['_publisher_']['name'] )
			|| isset( $dictionary_item['_publisher_']['name_short'] )
			|| isset( $dictionary_item['_publisher_']['name_department'] )
			|| isset( $dictionary_item['_publisher_']['email'] )
			|| isset( $dictionary_item['_publisher_']['website'] )
		) {
			
			$publisher_arr = array(
				'uri' => 'http://www.rastija.lt/isteklius#hasOrganization',
				'objectProperty' => array(
					'classUri' => 'http://www.rastija.lt/isteklius#Organization',
					'header' => ( !empty( $dictionary_item['_publisher_']['name'] ) ) ? $dictionary_item['_publisher_']['name'] : '', // Pavadinimas
					'properties' => array(
						array(
							'uri' => 'http://www.rastija.lt/isteklius#divisionName',
							'dataProperty' => ( !empty( $dictionary_item['_publisher_']['name_department'] ) ) ? $dictionary_item['_publisher_']['name_department'] : '' // Padalinio pavadinimas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#email',
							'dataProperty' => ( !empty( $dictionary_item['_publisher_']['email'] ) ) ? $dictionary_item['_publisher_']['email'] : '' // El. pasto adreas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#name',
							'dataProperty' => ( !empty( $dictionary_item['_publisher_']['name'] ) ) ? $dictionary_item['_publisher_']['name'] : '' // Pavadinimas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#shortName',
							'dataProperty' => ( !empty( $dictionary_item['_publisher_']['name_short'] ) ) ? $dictionary_item['_publisher_']['name_short'] : '' // Trumpas pavadinimas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#url',
							'dataProperty' => ( !empty( $dictionary_item['_publisher_']['website'] ) ) ? $dictionary_item['_publisher_']['website'] : '' // URL adresas
						)
					)
				)
			);
			
			if ( !empty( $dictionary_item['_publisher_']['resource_uri'] ) ) {
				
				$publisher_arr['objectProperty']['resourceUri'] = $dictionary_item['_publisher_']['resource_uri'];
				$publisher_arr['objectProperty']['ontologyUri'] = 'http://www.rastija.lt/isteklius';
			}
			
			$data['properties'][] = $publisher_arr;
		}
		
		// Projektas
		if (
			!empty( $dictionary_item['project_name'] )
			|| !empty( $dictionary_item['project_name_short'] )
			|| !empty( $dictionary_item['project_funding'] )
			|| !empty( $dictionary_item['project_start'] )
			|| !empty( $dictionary_item['project_end'] )
		) {
			
			$project_arr = array(
				'uri' => 'http://www.rastija.lt/isteklius#hasProject',
				'objectProperty' => array(
					'classUri' => 'http://www.rastija.lt/isteklius#Project',
					'header' => ( !empty( $dictionary_item['project_name'] ) ) ? $dictionary_item['project_name'] : '', // Pavadinimas
					'properties' => array(
						array(
							'uri' => 'http://www.rastija.lt/isteklius#endDate',
							'dataProperty' => ( !empty( $dictionary_item['project_end'] ) ) ? $dictionary_item['project_end'] : '' // Pabaigos data
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#financeSource',
							'dataProperty' => ( !empty( $dictionary_item['project_funding'] ) ) ? $dictionary_item['project_funding'] : '' // Finansavimo saltinis
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#name',
							'dataProperty' => ( !empty( $dictionary_item['project_name'] ) ) ? $dictionary_item['project_name'] : '' // Pavadinimas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#shortName',
							'dataProperty' => ( !empty( $dictionary_item['project_name_short'] ) ) ? $dictionary_item['project_name_short'] : '' // Trumpas pavadinimas
						),
						array(
							'uri' => 'http://www.rastija.lt/isteklius#startDate',
							'dataProperty' => ( !empty( $dictionary_item['project_start'] ) ) ? $dictionary_item['project_start'] : '' // Pradzios data
						)
					)
				)
			);
			
			if ( !empty( $dictionary_item['project_resource_uri'] ) ) {
				
				$project_arr['objectProperty']['resourceUri'] = $dictionary_item['project_resource_uri'];
				$project_arr['objectProperty']['ontologyUri'] = 'http://www.rastija.lt/isteklius';
			}
			
			$data['properties'][] = $project_arr;
		}
		
		// Isteklius
		if ( !empty( $dictionary_item['_source_type_']['name_lt'] ) ) {
			
			$resource_arr = array(
				'uri' => 'http://www.rastija.lt/isteklius#hasResourceType',
				'objectProperty' => array(
					'classUri' => 'http://www.rastija.lt/isteklius#ResourceType',
					'header' => $dictionary_item['_source_type_']['name_lt'], // Pavadinimas
					'properties' => array(
						array(
							'uri' => 'http://www.rastija.lt/isteklius#name',
							'dataProperty' => $dictionary_item['_source_type_']['name_lt'] // Pavadinimas
						)
					)
				)
			);
			
			if ( !empty( $dictionary_item['_source_type_']['resource_uri'] ) ) {
				
				$resource_arr['objectProperty']['resourceUri'] = $dictionary_item['_source_type_']['resource_uri'];
				$resource_arr['objectProperty']['ontologyUri'] = 'http://www.rastija.lt/isteklius';
			}
			
			$data['properties'][] = $resource_arr;
		}
		
		$tmp_dictionary_item = reset( $GLOBALS['cm']->DbUtils->select( 'search_resources', array( 'eq|resourceUri' => $dictionary_item['resource_uri'] ) ) );
		
		if ( !empty( $tmp_dictionary_item ) ) {
			
			$ratings_count = $GLOBALS['cm']->DictionaryRatingsEntity->getItems( array( 'dictionary_id' => $tmp_dictionary_item['id'] ), array(), 'count' );
			$ratings_sum = reset( $GLOBALS['cm']->DbUtils->getRow( 'SELECT SUM( `rating` ) FROM `' . $GLOBALS['cm']->DictionaryRatingsEntity->datasource . '` WHERE `dictionary_id` = "' . $tmp_dictionary_item['id'] . '"' ) );
		}
		
		$curr_stars = ( !empty( $ratings_count ) ) ? round( $ratings_sum / $ratings_count ) : 0;
		
		// Reitingas
		$data['properties'][] = array(
			'uri' => 'http://www.rastija.lt/isteklius#rating',
			'dataProperty' => $curr_stars
		);
		
		// Prieinamumas
		$data['properties'][] = array(
			'uri' => 'http://www.rastija.lt/isteklius#accessType',
			'dataProperty' => ( !empty( $dictionary_item['is_public'] ) ) ? 'Viešas' : 'Privatus'
		);
		
		// Aprasymas
		$data['properties'][] = array(
			'uri' => 'http://www.rastija.lt/isteklius#description',
			'dataProperty' => $dictionary_item['description']
		);
		
		// Raktazodziai
		$data['properties'][] = array(
			'uri' => 'http://www.rastija.lt/isteklius#keyword',
			'dataProperty' => $dictionary_item['keywords']
		);
		
		// Pavadinimas
		$data['properties'][] = array(
			'uri' => 'http://www.rastija.lt/isteklius#name',
			'dataProperty' => $dictionary_item['name']
		);
		
		// Trumpas pavadinimas
		$data['properties'][] = array(
			'uri' => 'http://www.rastija.lt/isteklius#shortName',
			'dataProperty' => $dictionary_item['acronym']
		);
		
		// Leidimo metai
		$data['properties'][] = array(
			'uri' => 'http://www.rastija.lt/isteklius#startDate',
			'dataProperty' => $dictionary_item['date']
		);
		
		// Paantraštė
		$data['properties'][] = array(
			'uri' => 'http://www.rastija.lt/isteklius#subtitle',
			'dataProperty' => $dictionary_item['other_titles']
		);
		
		// Adresas
		$data['properties'][] = array(
			'uri' => 'http://www.rastija.lt/isteklius#url',
			'dataProperty' => ( !empty( $dictionary_item['url'] ) ) ? ROOT_URL . 'žodynas/' . $dictionary_item['url'] : ''
		);
		
		// Publikavimo duomenys
		$publishing_arr = array(
			'uri' => 'http://www.rastija.lt/isteklius#hasEdition',
			'objectProperty' => array(
				'classUri' => 'http://www.rastija.lt/isteklius#Edition',
				'header' => ( !empty( $dictionary_item['publishing_edition'] ) ) ? $dictionary_item['name'] . ' ' . $dictionary_item['publishing_edition'] : $dictionary_item['name'],
				'properties' => array(
					array(
						'uri' => 'http://www.rastija.lt/isteklius#name',
						'dataProperty' => ( !empty( $dictionary_item['publishing_edition'] ) ) ? $dictionary_item['publishing_edition'] : ''
					),
					array(
						'uri' => 'http://www.rastija.lt/isteklius#place',
						'dataProperty' => ( !empty( $dictionary_item['publishing_place'] ) ) ? $dictionary_item['publishing_place'] : ''
					),
					array(
						'uri' => 'http://www.rastija.lt/isteklius#date',
						'dataProperty' => ( !empty( $dictionary_item['publishing_date'] ) && $dictionary_item['publishing_date'] != '0000-00-00' ) ? date( 'Y', strtotime( $dictionary_item['publishing_date'] ) ) : ''
					)
				)
			)
		);
		
		if ( !empty( $dictionary_item['publishing_resource_uri'] ) ) {
			
			$publishing_arr['objectProperty']['resourceUri'] = $dictionary_item['publishing_resource_uri'];
		}
		
		$data['properties'][] = $publishing_arr;
		
		$this->ws_urls['dictionaries']['addr'] .= 'zodynas&appendGuid=false';
		$this->timeout = 0;
		$result = $this->sendRequest( 'dictionaries', $data );
		$this->timeout = 10;
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' || empty( $result['details']['resourceUri'] ) ) {
			
			return false;
		}
		
		$dictionary_update_arr = array();
		$dictionary_update_arr['resource_uri'] = $result['details']['resourceUri'];
		
		// Is gauto atsakymo susidedame 'resourceUri' i savo DB
		foreach ( $result['details']['properties'] as $property ) {
			
			if ( $property['uri'] == 'http://www.rastija.lt/isteklius#hasAnnotation' ) {
				
				if ( !empty( $property['objectProperty']['resourceUri'] ) ) {
					
					$dictionary_update_arr['annotation_resource_uri'] = $property['objectProperty']['resourceUri'];
				}
				
				foreach ( $property['objectProperty']['properties'] as $property_2 ) {
					
					if ( $property_2['uri'] == 'http://www.rastija.lt/isteklius#hasAnnotator' && !empty( $property_2['objectProperty']['resourceUri'] ) ) {
						
						$this->oDictionaries->oAnnotators->update( array( 'resource_uri' => $property_2['objectProperty']['resourceUri'] ), array( 'id' => $dictionary_item['annotation_author'] ) );
						
						break;
					}
				}
				
				continue;
			}
			
			if ( in_array( $property['uri'], array( 'http://www.rastija.lt/isteklius#hasAuthor', 'http://www.rastija.lt/isteklius#hasEditor' ) ) ) {
				
				if ( !empty( $property['objectProperty']['properties'] ) ) {
					
					$filter = array();
					
					foreach ( $property['objectProperty']['properties'] as $property_2 ) {
						
						$filter_name = str_replace( 'http://www.rastija.lt/isteklius#', '', $property_2['uri'] );
						$filter[ 'eq|' . $filter_name ] = $property_2['dataProperty'];
					}
					
					$entity = ( $property['uri'] == 'http://www.rastija.lt/isteklius#hasAuthor' ) ? 'oAuthors' : 'oEditors';
					
					$item = $this->oDictionaries->$entity->getItem( $filter );
					
					if ( !empty( $item ) ) {
						
						$this->oDictionaries->$entity->update( array( 'resource_uri' => $property['objectProperty']['resourceUri'] ), array( 'id' => $item['id'] ) );
					}
				}
				
				continue;
			}
			
			if ( $property['uri'] == 'http://www.rastija.lt/isteklius#hasLanguage' ) {
				
				if ( !empty( $property['objectProperty']['properties'][0]['dataProperty'] ) ) {
					
					$item = $this->oDictionaries->oLanguages->getItem( array( 'eq|name_lt' => $property['objectProperty']['properties'][0]['dataProperty'] ) );
					
					if ( !empty( $item ) ) {
						
						$this->oDictionaries->oLanguages->update( array( 'resource_uri' => $property['objectProperty']['resourceUri'] ), array( 'id' => $item['id'] ) );
					}
				}
				
				continue;
			}
			
			if ( $property['uri'] == 'http://www.rastija.lt/isteklius#hasLicence' ) {
				
				if ( !empty( $property['objectProperty']['resourceUri'] ) ) {
					
					$dictionary_update_arr['license_resource_uri'] = $property['objectProperty']['resourceUri'];
				}
				
				continue;
			}
			
			if ( $property['uri'] == 'http://www.rastija.lt/isteklius#hasOrganization' ) {
				
				if ( !empty( $property['objectProperty']['resourceUri'] ) ) {
					
					$this->oDictionaries->oPublishers->update( array( 'resource_uri' => $property['objectProperty']['resourceUri'] ), array( 'id' => $dictionary_item['publisher'] ) );
				}
				
				continue;
			}
			
			if ( $property['uri'] == 'http://www.rastija.lt/isteklius#hasProject' ) {
				
				if ( !empty( $property['objectProperty']['resourceUri'] ) ) {
					
					$dictionary_update_arr['project_resource_uri'] = $property['objectProperty']['resourceUri'];
				}
				
				continue;
			}
			
			if ( $property['uri'] == 'http://www.rastija.lt/isteklius#hasEdition' ) {
				
				if ( !empty( $property['objectProperty']['resourceUri'] ) ) {
					
					$dictionary_update_arr['publishing_resource_uri'] = $property['objectProperty']['resourceUri'];
				}
				
				continue;
			}
			
			if ( $property['uri'] == 'http://www.rastija.lt/isteklius#hasResourceType' ) {
				
				if ( !empty( $property['objectProperty']['resourceUri'] ) ) {
					
					$this->oDictionaries->oSourceTypes->update( array( 'resource_uri' => $property['objectProperty']['resourceUri'] ), array( 'id' => $dictionary_item['source_type'] ) );
				}
				
				continue;
			}
		}
		
		$this->oDictionaries->update( $dictionary_update_arr, array( 'id' => $dictionary_item['id'] ) );
		
		return true;
	}
	
	/**
	 * Imam zodzio rysius ir objektus
	 *
	 * @param		string		$uri				Zodzio URI WS'uose
	 * @return		array		$ret_val			Rysiu ir objektu masyvas
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
	 * Imam paieskos suggestionus
	 *
	 * @param		string		$q					Paieskos zodzio fragmentas
	 * @return		array		$ret_val			Zodziu masyvas
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
		
		if ( !empty( $ret_val ) ) {
			
			function lengthSort( $a, $b ){
				
				return strlen( $a ) - strlen( $b );
			}
			
			usort( $ret_val, 'lengthSort' );
		}
		
		foreach ( $ret_val as $key => $word ) {
			
			$ret_val[ $key ] = json_decode( str_replace( '\u0307', '', json_encode( $word ) ) );
		}
		
		return $ret_val;
	}
	
	/**
	 * Imam vieno zodzio duomenis is WS'u
	 *
	 * @param		string		$uri				Zodzio URI
	 * @return		array		$ret_val			Zodzio informacijos masyvas
	 */
	public function getWordData( $uri = '', $plain = false ) {
		
		$ret_val = array();
		$ws_url_copy = $this->ws_urls['resource']['addr'];
		$this->ws_urls['resource']['addr'] .= $uri;
		
		$result = $this->sendRequest( 'resource' );
		$this->ws_urls['resource']['addr'] = $ws_url_copy;
		
		if ( empty( $result['type'] ) || $result['type'] != 'success' || empty( $result['details'] ) ) {
			
			return $ret_val;
		}
		
		if ( $plain ) {
			
			return $result['details'];
		}
		
		$ret_val = $this->parseWordData( $result['details'] );
		
		return $ret_val;
	}
	
	/**
	 * Imam visus zodynus
	 *
	 * @return		array		$ret_val			Zodynu masyvas
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
	 * Saugom zodzio informacija i WS'us
	 * 	 
	 * @param		integer					Zodzio ID
	 * @return		boolean					TRUE/FALSE
	 */
	public function modifyWord( $word_id = 0 ) {
		
		$word_item = ( !empty( $word_id ) ) ? $this->oWords->getItem( array( 'id' => $word_id ) ) : array();
		$dictionary_item = ( !empty( $word_item ) ) ? $this->oDictionaries->getItem( array( 'id' => $word_item['control_parent_id'] ) ) : array();
		
		if ( empty( $word_item ) || empty( $dictionary_item ) || empty( $dictionary_item['acronym'] ) ) {
			
			return false;
		}
		
		// zodzio formos guli homonimu lentoj
		$word_forms = $this->oWords->oHomonyms->getItems( array( 'control_parent_id' => $word_item['id'] ), array( 'item_sort' => 'ASC' ) );
		
		$data = array(
			'classUri' => 'http://www.lexinfo.net/lmf#LexicalEntry',
			'ontologyUri' => 'http://www.lexinfo.net/lmf',
			'header' => $word_item['name'],
			'properties' => array()
		);
		
		if ( !empty( $word_item['resource_uri'] ) ) {
			
			// paimam WS info, apjungsim su info is formos
			$word_data = $this->getWordData( urlencode( $word_item['resource_uri'] ), true );
			
			$data['resourceUri'] = $word_item['resource_uri'];
		}
		
		// Antrastinis zodis
		$title_word_properties = array(
			array(
				'uri' => 'http://www.lexinfo.net/lmf#writtenForm',
				'dataProperty' => $word_item['title_word']
			),
			array(
				'uri' => 'http://www.lexinfo.net/lmf#accentuation',
				'dataProperty' => $word_item['accent']
			)
		);
		
		if ( !empty( $word_item['notes'] ) ) {
			
			$title_word_properties[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#comment',
				'dataProperty' => $word_item['notes']
			);
		}
		
		if ( !empty( $word_item['origin'] ) ) {
			
			$title_word_properties[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#origin',
				'dataProperty' => $word_item['origin']
			);
		}
		
		if ( !empty( $word_item['sound'] ) ) {
			
			$title_word_properties[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#sound',
				'dataProperty' => $word_item['sound']
			);
		}
		
		if ( !empty( $word_item['image'] ) ) {
			
			$title_word_properties[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#image',
				'dataProperty' => $word_item['image']
			);
		}
		
		if ( !empty( $word_item['part_of_speech'] ) ) {
			
			$title_word_properties[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#partOfSpeech',
				'dataProperty' => $word_item['part_of_speech']
			);
		}
		
		if ( !empty( $word_item['part_of_speech'] ) ) {
			
			$title_word_properties[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#partOfSpeech',
				'dataProperty' => $word_item['part_of_speech']
			);
		}
		
		if ( !empty( $word_item['word_count'] ) ) {
			
			$title_word_properties[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#grammaticalNumber',
				'dataProperty' => $this->objLang->getLangPart( 'cms.sheets.dictionaries_homonyms_list.word_count_' . $word_item['word_count'] ),
				'objectProperty' => null
			);
		}
		
		if ( !empty( $word_item['word_genus'] ) ) {
			
			$title_word_properties[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#grammaticalGender',
				'dataProperty' => $this->objLa
				ng->getLangPart( 'cms.sheets.dictionaries_homonyms_list.word_genus_' .  $word_item['word_genus'] ),
				'objectProperty' => null
			);
		}
		
		if ( !empty( $word_item['word_time'] ) ) {
			
			$title_word_properties[] = array(
				'uri' => 'http://www.lexinfo.net/lmf#grammaticalTense',
				'dataProperty' => $this->objLang->getLangPart( 'cms.sheets.dictionaries_homonyms_list.word_time_' . $word_item['word_time'] ),
				'objectProperty' => null
			);
		}
		
		$data['properties'][] = array(
			'uri' => 'http://www.lexinfo.net/lmf#hasLemma',
			'objectProperty' => array(
				'classUri' => 'http://www.lexinfo.net/lmf#Lemma',
				'header' => $word_item['name'],
				'properties' => $title_word_properties
			)
		);
		
		foreach ( $word_forms as $word_form ) {
			
			$word_form_data = array(
				'uri' => 'http://www.lexinfo.net/lmf#hasWordForm',
				'objectProperty' => array(
					'classUri' => 'http://www.lexinfo.net/lmf#WordForm',
					'header' => $word_form['word_form'],
					'properties' => array(
						array(
							'uri' => 'http://www.lexinfo.net/lmf#writtenForm',
							'dataProperty' => $word_form['word_form']
						)
					)
				)
			);
			
			if ( !empty( $word_form['accent'] ) ) {
				
				$word_form_data['objectProperty']['properties'][] = array(
					'uri' => 'http://www.lexinfo.net/lmf#accentuation',
					'dataProperty' => $word_form['accent']
				);
			}
			
			if ( !empty( $word_form['part_of_speech'] ) ) {
				
				$word_form_data['objectProperty']['properties'][] = array(
					'uri' => 'http://www.lexinfo.net/lmf#partOfSpeech',
					'dataProperty' => $word_form['part_of_speech'],
					'objectProperty' => null
				);
			}
			
			if ( !empty( $word_form['origin'] ) ) {
				
				$word_form_data['objectProperty']['properties'][] = array(
					'uri' => 'http://www.lexinfo.net/lmf#origin',
					'dataProperty' => $word_form['origin'],
					'objectProperty' => null
				);
			}
			
			if ( !empty( $word_form['word_genus'] ) ) {
				
				$word_form_data['objectProperty']['properties'][] = array(
					'uri' => 'http://www.lexinfo.net/lmf#grammaticalGender',
					'dataProperty' => $this->objLang->getLangPart( 'cms.sheets.dictionaries_homonyms_list.word_genus_' . $word_form['word_genus'] )
				);
			}
			
			if ( !empty( $word_form['word_count'] ) ) {
				
				$word_form_data['objectProperty']['properties'][] = array(
					'uri' => 'http://www.lexinfo.net/lmf#grammaticalNumber',
					'dataProperty' => $this->objLang->getLangPart( 'cms.sheets.dictionaries_homonyms_list.word_count_' . $word_form['word_count'] )
				);
			}
			
			if ( !empty( $word_form['word_time'] ) ) {
				
				$word_form_data['objectProperty']['properties'][] = array(
					'uri' => 'http://www.lexinfo.net/lmf#grammaticalTense',
					'dataProperty' => $this->objLang->getLangPart( 'cms.sheets.dictionaries_homonyms_list.word_time_' . $word_form['word_time'] )
				);
			}
			
			if ( !empty( $word_form['sound'] ) ) {
				
				$word_form_data['objectProperty']['properties'][] = array(
					'uri' => 'http://www.lexinfo.net/lmf#sound',
					'dataProperty' => $word_form['sound']
				);
			}
			
			$data['properties'][] = $word_form_data;
		}
		
		$meanings = $this->oWords->oMeanings->getItems( array( 'control_parent_id' => $word_item['id'] ) );
		
		foreach ( $meanings as $meaning ) {
			
			$meaning_connections = $this->oDictionariesConnections->getItems( array( 'control_parent_id' => $meaning['id'] ) );
			// $meaning_connections = ( !empty( $meaning_connections ) ) ? ArrayUtils::group( $meaning_connections, 'connection_type' ) : array();
			$meaning_equivalents = $GLOBALS['cm']->DictionariesEquivalentsEntity->getItems( array( 'control_parent_id' => $meaning['id'] ) );
			
			$properties_arr = array();
			
			$properties_arr = array(
				array(
					'uri' => 'http://www.lexinfo.net/lmf#hasDefinition',
					'objectProperty' => array(
						'classUri' => 'http://www.lexinfo.net/lmf#Definition',
						'header' => $word_item['name'],
						'properties' => array(
							array(
								'uri' => 'http://www.lexinfo.net/lmf#hasTextRepresentation',
								'objectProperty' => array(
									'classUri' => 'http://www.lexinfo.net/lmf#TextRepresentation',
									'header' => $word_item['name'],
									'properties' => array(
										array(
											'uri' => 'http://www.lexinfo.net/lmf#writtenForm',
											'dataProperty' => $meaning['definition']
										)
									)
								)
							)
						)
					)
				)
			);
			
			$illustrations = $GLOBALS['cm']->DictionariesIllustrationsEntity->getItems( array( 'control_parent_id' => $meaning['id'] ) );
			
			if ( !empty( $illustrations ) ) {
				
				foreach ( $illustrations as $key => $illustration ) {
					
					$properties_arr[] = array(
						'uri' => 'http://www.lexinfo.net/lmf#hasSenseExample',
						'objectProperty' => array(
							'classUri' => 'http://www.lexinfo.net/lmf#SenseExample',
							'header' => $word_item['name'],
							'properties' => array(
								array(
									'uri' => 'http://www.lexinfo.net/lmf#text',
									'dataProperty' => nl2br( $illustration['name'] )
								)
							)
						)
					);
				}
			}
			
			foreach ( $meaning_connections as $key => $meaning_connection ) {
				
				$properties_arr_tmp = array(
					'uri' => 'http://www.lexinfo.net/lmf#hasSenseRelation',
					'dataProperty' => null,
					'objectProperty' => array(
						'classUri' => 'http://www.lexinfo.net/lmf#SenseRelation',
						'header' => $word_item['name'] . ' ' . strtolower( $this->objLang->getLangPart( 'site.dictionaries.form_labels.connection_' . $meaning_connection['connection_type'] ) ),
						'properties' => array(
							array(
								'uri' => 'http://www.lexinfo.net/lmf#type',
								'dataProperty' => $this->objLang->getLangPart( 'site.dictionaries.form_labels.connection_' . $meaning_connection['connection_type'] )
							)
						)
					)
				);
				
				if ( !empty( $meaning_connection['connection_lexical_entry'] ) ) {
					
					$word_item_tmp = $this->oWords->getItem(
						array(
							'eq|resource_uri' => $meaning_connection['connection_lexical_entry'],
							'control_parent_id' => $dictionary_item['id']
						)
					);
				} else {
					
					$word_item_tmp = $this->oWords->getItem(
						array(
							'custom|title_word' => ' = A.title_word AND ( title_word = \'' . $meaning_connection['connection_name'] . '\' OR name = \'' . $meaning_connection['connection_name'] . '\' )',
							'control_parent_id' => $dictionary_item['id']
						)
					);
				}
				
				
				// if ( empty( $word_item_tmp['resource_uri'] ) ) {
					
				// 	$title_word_item_tmp = $this->oWords->oTitleWords->getItems( array( 'eq|name' => $meaning_connection['connection_name'] ) );
				// 	$title_word_item_tmp_ids = ArrayUtils::restruct( $title_word_item_tmp, '', 'control_parent_id' );
				// 	$word_item_tmp = $this->oWords->getItem( array( 'id' => $title_word_item_tmp_ids, 'control_parent_id' => $dictionary_item['id'] ) );
				// }
				
				// if ( empty( $word_item_tmp['resource_uri'] ) ) {
					
				// 	continue;
				// }
				
				if ( !empty( $word_item_tmp['resource_uri'] ) ) {
					
					$properties_arr_tmp['objectProperty']['properties'][] = array(
						'uri' => 'http://www.lexinfo.net/lmf#senseRelatedTo',
						'objectProperty' => array(
							'resourceUri' => !empty( $word_item_tmp['resource_uri'] ) ? $word_item_tmp['resource_uri'] : '',
							'classUri' => 'http://www.lexinfo.net/lmf#LexicalEntry',
							'header' => !empty( $word_item_tmp['title_word'] ) ? $word_item_tmp['title_word'] : $meaning_connection['connection_name']
						)
					);
				}
				
				$properties_arr_tmp['objectProperty']['properties'][] = array(
					'uri' => 'http://www.lexinfo.net/lmf#writtenForm',
					'dataProperty' => !empty( $word_item_tmp['title_word'] ) ? $word_item_tmp['title_word'] : $meaning_connection['connection_name']
				);
				
				$properties_arr[] = $properties_arr_tmp;
			}
			
			if ( !empty( $meaning['translations'] ) ) {
				
				$properties_arr[] = array(
					'uri' => 'http://www.lexinfo.net/lmf#hasSenseRelation',
					'dataProperty' => null,
					'objectProperty' => array(
						'classUri' => 'http://www.lexinfo.net/lmf#SenseRelation',
						'header' => $meaning['translations'],
						'properties' => array(
							array(
								'uri' => 'http://www.lexinfo.net/lmf#type',
								'dataProperty' => 'Vertimo pavyzdžiai'
							),
							array(
								'uri' => 'http://www.lexinfo.net/lmf#writtenForm',
								'dataProperty' => $meaning['translations']
							)
						)
					)
				);
			}
			
			if ( !empty( $meaning['term_status'] ) ) {
				
				$properties_arr[] = array( 
					'uri' => 'http://www.lexinfo.net/lmf#hasSubjectField',
					'objectProperty' => array(
						'classUri' => 'http://www.lexinfo.net/lmf#SubjectField',
						'header' => $this->objLang->getLangPart( 'cms.sheets.dictionaries_meanings_list.term_status_' . $meaning['term_status'] ),
						'properties' => array(
							array(
								'uri' => 'http://www.lexinfo.net/lmf#status',
								'dataProperty' => $this->objLang->getLangPart( 'cms.sheets.dictionaries_meanings_list.term_status_' . $meaning['term_status'] )
							)
						)
					)
				);
			}
			
			$consumption_area_ids = ArrayUtils::restruct( $GLOBALS['cm']->ConsumptionAreasEntity->getRelation( 'meaning', array( 'meaning_id' => $meaning['id'] ) ), '', 'consumption_id' );
			
			if ( !empty( $consumption_area_ids ) ) {
				
				foreach ( $consumption_area_ids as $consumption_area_id ) {
					
					$consumption_area = $GLOBALS['cm']->ConsumptionAreasEntity->getItem( array( 'id' => $consumption_area_id ) );
					
					if ( empty( $consumption_area ) ) {
						
						continue;
					}
					
					$properties_arr[] = array(
						'uri' => 'http://www.lexinfo.net/lmf#hasContext',
						'objectProperty' => array(
							'classUri' => 'http://www.lexinfo.net/lmf#Context',
							'header' => $consumption_area['name'],
							'properties' => array(
								array(
									'uri' => 'http://www.lexinfo.net/lmf#hasTextRepresentation',
									'objectProperty' => array(
										'classUri' => 'http://www.lexinfo.net/lmf#TextRepresentation',
										'header' => $consumption_area['name'],
										'properties' => array(
											array(
												'uri' => 'http://www.lexinfo.net/lmf#text',
												'dataProperty' => $consumption_area['name']
											)
										)
									)
								)
							)
						)
					);
				}
			}
			
			if ( !empty( $meaning_equivalents ) ) {
				
				foreach ( $meaning_equivalents as $key => $equivalent ) {
					
					$properties_arr[] = array(
						'uri' => 'http://www.lexinfo.net/lmf#hasEquivalent',
						'objectProperty' => array(
							'classUri' => 'http://www.lexinfo.net/lmf#Equivalent',
							'header' => $equivalent['name'],
							'properties' => array(
								array(
									'uri' => 'http://www.lexinfo.net/lmf#language',
									'dataProperty' => $equivalent['language']
								),
								array(
									'uri' => 'http://www.lexinfo.net/lmf#writtenForm',
									'dataProperty' => $equivalent['name']
								)
							)
						)
					);
				}
			}
			
			$data['properties'][] = array(
				'uri' => 'http://www.lexinfo.net/lmf#hasSense',
				'objectProperty' => array(
					'classUri' => 'http://www.lexinfo.net/lmf#Sense',
					'header' => $word_item['name'],
					'properties' => array_values( $properties_arr )
				)
			);
		}
		
		// Nurodom zodyno resource URI
		$data['properties'][] = array(
			'uri' => 'http://www.rastija.lt/isteklius#lexicon',
			'objectProperty' => array(
				'resourceUri' => $dictionary_item['resource_uri']
			)
		);
		
		if ( !empty( $word_data ) ) {
			
			$data = $this->mergeDatas( $data, $word_data );
		}
		
		$ws_url = $this->ws_urls['dictionaries']['addr'];
		$this->ws_urls['dictionaries']['addr'] .= urlencode( $dictionary_item['acronym'] );
		$result = $this->sendRequest( 'dictionaries', $data );
		$this->ws_urls['dictionaries']['addr'] = $ws_url;
		
		if ( !empty( $result['type'] ) && $result['type'] == 'success' && !empty( $result['details'] ) && is_array( $result['details'] ) ) {
			
			$this->oWords->update( array( 'resource_uri' => $result['details']['resourceUri'] ), array( 'id' => $word_item['id'] ) );
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Apjungiam vietine zodio info su gauta is WS
	 * 	 
	 * @param		array					Zodzio duomenys is musu db
	 * @param		array					Zodzio duomenys is WSu
	 * @return		array					Apjungti duomenys
	 */
	public function mergeDatas( $data, $word_data ) {
		
		$word_data['classUri'] = $data['classUri'];
		$word_data['ontologyUri'] = $data['ontologyUri'];
		$word_data['header'] = $data['header'];
		
		$data = ArrayUtils::group( $data['properties'], 'uri' );
		// $data = VarUtils::preformatArr( $data['properties'], 'uri' );
		
		foreach ( $word_data['properties'] as $key => $property ) {
			
			// zodyno info, paduodam trumpai
			if ( $property['uri'] == 'http://www.rastija.lt/isteklius#lexicon' ) {
				
				$word_data['properties'][ $key ] = reset( $data[ $property['uri'] ] );
				unset( $data[ $property['uri'] ] );
			}
			
			// title word, tik prikabinam
			if ( $property['uri'] == 'http://www.lexinfo.net/lmf#hasLemma' ) {
				
				$tmp = !empty( $data[ $property['uri'] ] ) ? reset( $data[ $property['uri'] ] ) : array();
				
				if ( !empty( $tmp['objectProperty']['properties'] ) ) {
					
					// gautas is WS ir suformuotas pas mus unsetinam, po to dedam musiskes
					foreach ( $data[ $property['uri'] ] as $key2 => $property2 ) {
						
						foreach ( $property2['objectProperty']['properties'] as $key3 => $property3 ) {
							
							foreach ( $property['objectProperty']['properties'] as $key4 => $property4 ) {
								
								if ( $property3['uri'] == $property4['uri'] ) {
									
									unset( $word_data['properties'][ $key ]['objectProperty']['properties'][ $key4 ] );
								}
							}
						}
					}
					
					$word_data['properties'][ $key ]['objectProperty']['properties'] = array_merge(
						$word_data['properties'][ $key ]['objectProperty']['properties'],
						$tmp['objectProperty']['properties'] 
					);
				}
				
				if ( !empty( $data[ $property['uri'] ] ) ) {
					
					unset( $data[ $property['uri'] ] );
				}
			}
			
			// word form
			if ( $property['uri'] == 'http://www.lexinfo.net/lmf#hasWordForm' ) {
				
				$tmp = !empty( $data[ $property['uri'] ] ) ? reset( $data[ $property['uri'] ] ) : array();
				
				if ( !empty( $tmp['objectProperty'] ) ) {
					
					$word_data['properties'][ $key ]['objectProperty'] = $tmp['objectProperty'];
					array_shift( $data[ $property['uri'] ] );
				} else {
					
					unset( $word_data['properties'][ $key ] );
				}
			}
			
			// meanings, jei atejo is ws, o lokaliai neturim ne vieno, trinam
			if ( $property['uri'] == 'http://www.lexinfo.net/lmf#hasSense' ) {
				
				$tmp = !empty( $data[ $property['uri'] ] ) ? reset( $data[ $property['uri'] ] ) : array();
				
				if ( !empty( $tmp['objectProperty'] ) ) {
					
					$word_data['properties'][ $key ]['objectProperty'] = $tmp['objectProperty'];
					array_shift( $data[ $property['uri'] ] );
				} else {
					
					unset( $word_data['properties'][ $key ] );
				}
			}
		}
		
		// prikabinam kas liko
		if ( !empty( $data ) ) {
			
			foreach ( $data as $key => $properties ) {
				
				foreach ( $properties as $key2 => $property ) {
					
					$word_data['properties'][] = $property;
				}
			}
		}
		
		return $word_data;
	}
	
	/**
	 * Saugom zodzio informacija i WS'us
	 * 	 
	 * @param		integer					Zodzio ID
	 * @return		boolean					TRUE/FALSE
	 */
	public function deleteWord( $word_id = 0 ) {
		
		$word_item = ( !empty( $word_id ) ) ? $this->oWords->getItem( array( 'id' => $word_id ) ) : array();
		
		if ( empty( $word_item ) ) {
			
			return false;
		}
		
		$ws_url = $this->ws_urls['word_delete']['addr'];
		$this->ws_urls['word_delete']['addr'] .= urlencode( $word_item['resource_uri'] );
		$result = $this->sendRequest( 'word_delete' );
		$this->ws_urls['word_delete']['addr'] = $ws_url;
		
		if ( !empty( $result['type'] ) && $result['type'] == 'success' ) {
			
			$this->oWords->update( array( 'resource_uri' => '' ), array( 'id' => $word_item['id'] ) );
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Trinam zodyno zodzius PO VIENA...
	 * 	 
	 * @param		integer					Zodyno id is cache lentos
	 * @return		boolean					TRUE/FALSE
	 */
	public function deleteDictionaryWords( $dictionary_id = 0 ) {
		
		if ( empty( $dictionary_id ) ) {
			
			return false;
		}
		
		$form_data = array();
		$form_data['q'] = '';
		$form_data['resources'][] = $dictionary_id;
		
		$result = $this->doSearch( $form_data, 1, 1 );
		
		if ( !empty( $result['total'] ) ) {
			
			$ret_val = array();
			
			$searchParams = $this->addSearchParams( $form_data );
			
			$page_count = ceil( $result['total'] / 100 );
			
			for ( $i = 1; $i <= $page_count; $i++ ) {
				
				$data = array(
					'freeTextQuery' => $form_data['q'],
					'pageSize' => 100,
					'page' => 1,
					'propertyDetailsList' => array(
						array(
							'uri' => 'class',
							'value' => 'http://www.lexinfo.net/lmf#LexicalEntry'
						)
					)
				);
				
				$data['propertyDetailsList'] = array_merge( $data['propertyDetailsList'], $searchParams );
				
				$result = $this->doSearch2( $data );
				
				if ( !empty( $result['details']['resourceDetailsList'] ) ) {
				
					foreach ( $result['details']['resourceDetailsList'] as $word ) {
						
						$this->deleteWordByUri( $word['resourceUri'] );
					}
				}
			}
		}
	}
	
	/**
	 * Trinam zodi pagal uri
	 * 	 
	 * @param		integer					Zodzio uri
	 * @return		boolean					TRUE/FALSE
	 */
	public function deleteWordByUri( $uri = '' ) {
		
		if ( empty( $uri ) ) {
			
			return false;
		}
		
		$ws_url = $this->ws_urls['word_delete']['addr'];
		$this->ws_urls['word_delete']['addr'] .= urlencode( $uri );
		$result = $this->sendRequest( 'word_delete' );
		$this->ws_urls['word_delete']['addr'] = $ws_url;
		
		if ( !empty( $result['type'] ) && $result['type'] == 'success' ) {
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Trinam zodyna
	 *
	 * @param		integer					Zodzio ID
	 * @return		boolean					TRUE/FALSE
	 */
	public function deleteDictionary( $uri = '' ) {
		
		if ( empty( $uri ) ) {
			
			return false;
		}
		
		$ws_url = $this->ws_urls['word_delete']['addr'];
		$this->ws_urls['word_delete']['addr'] .= urlencode( $uri );
		$result = $this->sendRequest( 'word_delete' );
		$this->ws_urls['word_delete']['addr'] = $ws_url;
		
		if ( !empty( $result['type'] ) && $result['type'] == 'success' ) {
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Pereinam per ontologijos klases ir surenkam DATA_PROPERTY'ciu papildomus parametrus
	 *
	 * @param		array		$classes			Ontologijos klases
	 * @return		string		$ret_val			Papildytos ontologijos klases
	 */
	public function getClassDataProperties( &$classes ) {
		
		foreach ( $classes as &$class ) {
			
			if ( $class['type'] == 'DATA_PROPERTY' && !empty( $class['uri'] ) ) {
				
				$uri_parts = explode( '#', $class['uri'] );
				
				if ( count( $uri_parts ) > 1 ) {
					
					$type = array_pop( $uri_parts );
					
					if ( !empty( $type ) ) {
						
						// ismetam situos nes jie grazina debesis rezultatu ir viskas nuluzta :)
						if ( !in_array( $type, array( 'writtenForm', 'abbreviation', 'comment', 'definition', 'origin', 'text', 'language' ) ) ) {
							
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
	 * Parsipuciam paieskos ontologijos papildomus parametrus
	 *
	 * @param		string		$type				Papildomo parametro tipas
	 * @return		string		$result				Parametru array
	 */
	public function getDataProperties( $type ) {
		
		$ws_url = $this->ws_urls['data_properties']['addr'];
		$this->ws_urls['data_properties']['addr'] .= $type;
		$result = $this->sendRequest( 'data_properties' );
		$this->ws_urls['data_properties']['addr'] = $ws_url;
		
		return $result;
	}
}

?>