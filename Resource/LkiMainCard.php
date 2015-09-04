<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Resource;
use Rastija\Owl;
use Rsstija\Owl\Uri;

/**
 * Description of LkiMainCard
 *
 * @author Virginijus
 */
class LkiMainCard extends AbstractDictionary
{   
    private $_cacheDir = 'cache/LKI_MAIN_CARD/';
    private $_ontologyFile = 'config/rastija_owl_v3_2015_07_30VM.owl';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->setResourceId('LKIKartoteka/1');
        $this->setResourceName('Pagrindinė kartoteka');
        
        /** @var Uri\AbstractUri $uriFactory */
        $uriFactory = new Owl\Uri\UriFactory();
        $uriFactory->setUriBase('&lmf;kartoteka.Pagrindinė_kartoteka');
        
        $this->setUriFactory($uriFactory);
    }
    
    
    /**
     *  Main function for owl generations
     * 
     * @return string - dictionary ID encoded with MD5
     */
    public function generateLmfOwl() {
        $test = true;
        if ($test) {
            $filename  = $this->_cacheDir . md5($this->getResourceId()) . '_2.txt';
            $fileOfIndividuals = $this->_cacheDir . md5($this->getResourceId()) . '_individuals_1' . '.owl';
            $resourceOwlFile = $this->_cacheDir . md5($this->getResourceId()) . '_ontology_1' . '.owl';
        } else {
            $filename  = $this->_cacheDir . md5($this->getResourceId()) . '.txt';
            $fileOfIndividuals = $this->_cacheDir . md5($this->getResourceId()) . '_individuals' . '.owl';
            $resourceOwlFile = $this->_cacheDir . md5($this->getResourceId()) . '_ontology' . '.owl';
        }
        // Get resource information from the service
        //$resource = new Service\LkiisResource($this->_resourceId);
        //$resource->getRecords($filename, 7300);
        
        // Build individal for LMF ontology
        $this->_buildLmfIndividuals($filename, $fileOfIndividuals);
        
        
        // Make owl of dictionary
        //$this->_createOwl($fileOfIndividuals, $resourceOwlFile);
        
        return md5($this->getResourceId());
    }
    
    private function _buildLmfIndividuals($filename, $fileOfIndividuals)
    {
        $resourceName = $this->getResourceName();
                
        $file = fopen($filename, 'r');
        $xml = fread($file, filesize($filename));
        fclose($file);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml);

        //$data = array();

        $fileIndividuals = fopen($fileOfIndividuals, "w+");
        $recordNr = 1;
        // Get record ids
        $attributes = array();
        /**
         * Data structure
         * id 
         * header
         * metadata
         *  record
         *      writer (4641)                       * Metrika -> Užrašytojai
         *      imageURLs (48822)                   * Paveikslėkis 126x166
         *      sourcelink (27369)                  * Metrika -> Šaltiniai
         *      gramref_header (26)
         *      attributes (num of instances:
                    - kartoteka (48822)             - visiems rašoma "Kartoteka 1"
                    - word (48822)                  * Antraštė kirčiuotas žodis ar junginys
                    - word_header (48718)           - naudojamas LKIIS paieškoje
                    - cardno (48822)                * Kortelės numeris
                    - box (48822)                   - nenaudojamas
                    - images (48822)                - paveiklėlių numeriai
                    - sourcelocation (16140)        * Vietovė (nurodoma prie šaltinio)
                    - word_subtitle (8326)          * Paantraštė ()
                    - note (328)                    * Pastaba
                    - sourceauthor (483)            * Metrika -> Pateikėjas
                    - writedate (1580)              * Metrika -> "Užrašymo metai"
                    - wordvariant_subtitle (38)     - neatvaizduojamsa 
                    - sourcelocation_free (837)     * Metrika -> "Vietovė kortelėje"
                    - sourcelocation_geocode (2733) - Iškviečiamas interaktyvus langas
                    - wordvariant_header (459)      * Antraštė -> Žodžio variantas
                    - femineform_header (2530)      * Antraštė -> Moteriška giminė
                    - repeatable_forms3 (3009)      * Antraštė -> III forma
                    - repeatable_forms2 (3015)      * Antraštė -> II forma
                    - sourcelink_free (2647)        * Metrika -> "Šaltinis kaip kortelėje"
                    - writer_free (424)             * Metrika -> "Užrašytojas kortelėje"
                    - repeatable_forms3_subtitle (30)
                    - repeatable_forms2_subtitle (30)
                    - repeatable_forms4 (12)
                    - femineform_subtitle (49)
                    - gram (2234)                   - neatsivaizduoja reiškia kirčiuotę (pvz 3b)
                    - gram_subtitle (51)
                    - writercomment (4)
                    - sourceauthoryears (4)         * Metrika -> "Pateikėjo amžius/gimimo metai"
                    - bugacard (18)
                    - corrections (1)
                    - homonym (4)
                    - unusable (1)
                    - explanation_header (1)
                    - confidence (1)
                    - explanation_subtitle (1)
                    - content (2)
         * status
         */
        $n = array();
        foreach($dom->getElementsByTagName('return') as $domRecord) {
            /* @var $domRecord \DOMElement */
            $nodes = $domRecord->childNodes;

            $arr = array();
            foreach ($nodes as $node) {
                /* @var $node \DOMElement */
                if ($node->nodeName == 'metadata' && $node->nodeValue) {
                    $metadata = new \DOMDocument('1.0', 'UTF-8');
                    $metadata->loadXML($node->nodeValue);

                    $ins = array();
                    // Taking a record
                    /* @var $record \DOMElement */
                    $record = $metadata->getElementsByTagName('record')->item(0);
                    
                    // Get not empty attributes
                    foreach($record->attributes as $attribute_name => $attribute_node)
                    {
                      /* @var $attribute_node \DOMNode  */
                        if ($attribute_node->nodeValue) {
                            $ins[$attribute_name] = $attribute_node->nodeValue;
                            /* //To get list not empty attributes
                            if (isset($attributes[$attribute_name])) {
                                $attributes[$attribute_name] += 1;
                                 if ($attribute_name == 'gram')
                                     echo $attribute_node->nodeValue;
                                     
                            }  else {
                                $attributes[$attribute_name] = 1;
                            }*/
                        }
                    }
                    
                    foreach ($record->childNodes as $childNode) {
                        
                        /* @var $childNode \DOMElement */
                        if ($childNode->nodeValue) {
                            // Receive other metadata nodes
                            if ($childNode->nodeName == 'imageURLs') {
                                /* @var $imageUrlNode \DOMElement */
                                $imageUrlNode = $childNode->getElementsByTagName('imageURL')->item(0);
                                $arr['imageUrl'] = $imageUrlNode->getAttribute('value');
                            } else {
                                $arr[$childNode->nodeName] = $childNode->nodeValue;
                            }
                        }
                    }
                    
                    $arr[$node->nodeName] = $ins;
                } else {
                    $arr[$node->nodeName] = $node->nodeValue;
                }               
            }
            $recordNr++;

            // Concert the array to lexical entry
            /* array contains all atributes of data structure
             * - id
             * - header
             * - status
             * - metadata
             *      * all feeld of data structure with is presented upper
             */

            if ($arr['status'] != '-1' && !empty($arr['metadata']['word'])) {
                $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                $lexicalEntry->setUri($this->getUriFactory()->create('LexicalEntry', 
                                $arr['metadata']['word'] . '-' . $arr['metadata']['cardno'], 
                                $arr['id']));
                $lmfLemma = new Owl\LmfLemma();
                $lmfLemma->setWrittenForm($arr['metadata']['word']);
                $lmfLemma->setUri($this->getUriFactory()->create('Lemma', 
                                $arr['metadata']['word'] . '-' . $arr['metadata']['cardno'], 
                                $arr['id']));
                
                $lexicalEntry->setLemma($lmfLemma);
                
                //fwrite($fileIndividuals, $lmfLemma->toLmfString());
                
                /*
                $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                $lexicalEntry->setSeed($arr['id']);
                $lexicalEntry->setLemma($arr['metadata']['word']);
                */
                
                fwrite($fileIndividuals, $lexicalEntry->toLmfString());
            }
        }
        echo 'aaaaaaaa';
        fclose($fileIndividuals);
    }
    
//put your code here
}
