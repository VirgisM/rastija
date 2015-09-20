<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Resource;
use Rastija\Owl;
use Rastija\Owl\Uri;
use Rastija\Service;

/**
 * Description of LltiRiddleCard
 *
 * @author Virginijus
 */
class LltiSongCard extends AbstractDictionary
{   
    private $_cacheDir = 'cache/LLTI_SONG_CARD/';
    private $_ontologyFile = 'config/rastija_owl_v3_2015_07_30VM.owl';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->setResourceId('LLTI/daina');
        $this->setResourceName('Pokario partizanų dainų kartoteka');
        
        /** @var Uri\AbstractUri $uriFactory */
        $uriFactory = new Owl\Uri\UriFactory();
        $uriFactory->setUriBase('&lmf;kartoteka.Pokario_partizanų_dainų_kartoteka');
        
        $this->setUriFactory($uriFactory);
    }
    
    
    /**
     *  Main function for owl generations
     * 
     * @return string - dictionary ID encoded with MD5isteklius
     */
    public function generateLmfOwl() {
        $test = false;
        if ($test) {
            $filename  = $this->_cacheDir . md5($this->getResourceId()) . '_1.txt';
            $fileOfIndividuals = $this->_cacheDir . md5($this->getResourceId()) . '_individuals_1' . '.txt';
            $resourceOwlFile = $this->_cacheDir . md5($this->getResourceId()) . '_ontology_1' . '.owl';
        } else {
            $filename  = $this->_cacheDir . md5($this->getResourceId()) . '.txt';
            $fileOfIndividuals = $this->_cacheDir . md5($this->getResourceId()) . '_individuals' . '.txt';
            $resourceOwlFile = $this->_cacheDir . md5($this->getResourceId()) . '_ontology' . '.owl';
        }
        // Get resource information from the service
        //$resource = new Service\LkiisResource($this->getResourceId());       
        //$resource->getRecords($filename, 100);
      
        // Build individal for LMF ontology
        $this->buildLmfIndividuals($filename, $fileOfIndividuals);
        
        // Make owl of dictionary
        $this->createOwl($fileOfIndividuals, $resourceOwlFile);
        
        return md5($this->getResourceId());
    }
    
    protected function buildLmfIndividuals($filename, $fileOfIndividuals)
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
            [dc:identifier]             - 
            [dc:subject] => 3421        * Tipas
            [dc:description] => 3692    * Daina
            [dc:publisher] => 3349      * Fiksuotojas
            [dc:coverage] => 3095       * Vieta
            [dc:source] => 4543         * Archyvinis šaltinis
            [dc:source] => 646          * Spaudinys 
            [dc:date] => 3441           * Fiksavimo laikas
            [dc:creator] => 3174        * Pateikėjas
            [dc:source] => 4            - Signatūros nuoroda
            [dc:title] => 275           * Versija vartojame vietoj dc:subject
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
                    $num = 1;
                    // Taking a dc records
                    /* @var $record \DOMElement */
                    $record = $metadata->childNodes->item(0);
                    foreach ($record->childNodes as $childNode) {                   
                        /* @var $childNode \DOMElement */
                        if ($childNode->nodeValue) {
                            // Receive other metadata nodes, but ignore identifiers 
                            if ($childNode->nodeName != 'dc:identifier') {
                                // a lot of dublication 
                                $tmpIns = array();
                                $tmpIns[$childNode->nodeName]['label'] = $childNode->getAttribute('label');
                                $tmpIns[$childNode->nodeName]['value'] = $childNode->nodeValue;
                                $ins[] = $tmpIns;
                            }
                        }
                    }
                    
                    $arr[$node->nodeName] = $ins;
                } else {
                    $arr[$node->nodeName] = $node->nodeValue;
                }               
            }
            $recordNr++;     
            // Counting if posible attributes
            /*
            foreach ($arr['metadata'] as $nr => $keys) {
                $key = array_keys($keys)[0];
                
                $key = $key . ' - ' . $keys[$key]['label'];
                if (isset($n[$key])) {
                    $n[$key]++;
                } else {
                    $n[$key] = 1; 
                }
            }
            */
            
            // Concert the array to lexical entry
            /* array contains all atributes of data structure
             * - id
             * - header
             * - status
             * - metadata
             *      * all feeld of data structure with is presented upper
             */
            // Looking for lemma = the Song title
            $songTitle = '';
            foreach ($arr['metadata'] as $nr => $keys) {
                $key = array_keys($keys)[0];
                // BUG in data sometime song title can be in one of these tags
                if ($key == 'dc:subject' || $key == 'dc:title') {
                    $songTitle = $keys[$key]['value'];
                }
            }
            // For debuging
            if (!$songTitle) {
                print_r($arr);
            }            
            if ($arr['status'] != '-1' && $songTitle) {
                $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                $lexicalEntry->setUri($this->getUriFactory()->create('LexicalEntry', 
                                $songTitle,
                                $arr['id']));
                $lmfLemma = new Owl\LmfLemma();
                $lmfLemma->setWrittenForm($songTitle);
                $lmfLemma->setUri($this->getUriFactory()->create('Lemma', 
                                $songTitle,
                                $arr['id']));
                
                $lexicalEntry->setLemma($lmfLemma);
                
                $lmfSense = new Owl\LmfSense();
                $lmfSense->setLemmaWrittenForm($lmfLemma->getWrittenForm());

                $lmfSense->setUri($this->getUriFactory()->create('Sense', 
                                $songTitle,
                                $arr['id']));
                
                $lmfDefintion = new Owl\LmfDefinition();
                $lmfDefintion->setUri($this->getUriFactory()->create('Definition', 
                                $songTitle,
                                $arr['id']));
                
                $lmfTextRepresentation = new Owl\LmfTextRepresentation();
                $lmfTextRepresentation ->setUri($this->getUriFactory()->create('TextRepresentation', 
                                $songTitle,
                                $arr['id']));
                
                $writtenForm = "<![CDATA[";
                foreach ($arr['metadata'] as $nr => $keys) {
                    $key = array_keys($keys)[0];
                    $attr = $keys[$key];
                    if ($key != 'dc:title' && $key != 'dc:subject') {
                        if ($key == 'dc:description') {
                            $val = str_replace("\n", '<br />', $attr['value']);
                            $writtenForm .= "<div><em>{$attr['label']}:</em><br/> {$val} </div>";
                        } else {
                            $writtenForm .= "<div><em>{$attr['label']}:</em> {$attr['value']} </div>";
                        }
                        // Spacing between rows
                        $writtenForm .= "<div style=\"height: 5px;\"></div>";
                    }
                }
                $writtenForm .= "]]>";
                        
                $lmfTextRepresentation->setWrittenForm($writtenForm);
                
                $lmfDefintion->addTextRepresentation($lmfTextRepresentation);
            
                $lmfSense->setDefinition($lmfDefintion);

                $lexicalEntry->addSense($lmfSense);
                
                fwrite($fileIndividuals, $lexicalEntry->toLmfString());
            }
        }

        fclose($fileIndividuals);
        
        if (!empty($n)) {
            print_r($n);
        }
    }
    
    protected function createOwl($fileOfIndividuals, $resourceOwlFile)
    {
        // Add LMF ontology to file
        $ontologyFile = $this->_ontologyFile;

        // Read individuals
        $fileIndividuals = fopen($fileOfIndividuals, "r");
        $individuals = fread($fileIndividuals, filesize($fileOfIndividuals));
        fclose($fileIndividuals);    

        // Read ontology
        $fileLmfOntology = fopen($ontologyFile, "r");
        $ontology = fread($fileLmfOntology, filesize($ontologyFile));
        fclose($fileLmfOntology);    

        // Create resource owl
        $fileResourceOwl = fopen($resourceOwlFile, "w");
        fwrite($fileResourceOwl, $ontology);
        fwrite($fileResourceOwl, $individuals);
        $individuals = NULL;
        
        //@TODO fix anotation part
        $resourceAnnotationStr = "
        
            <!-- 
            ///////////////////////////////////////////////////////////////////////////////////////
            //
            // Annotations
            //
            ///////////////////////////////////////////////////////////////////////////////////////
             -->
<!--
            <<owl:NamedIndividual rdf:about=\"{$this->getUriFactory()->getUriBase()}.Resource\">
                <rdfs:label>{$this->getResourceName()}</rdfs:label>
                <&j.1;hasEdition rdf:resource=\"{$this->getUriFactory()->getUriBase()}}.Edition\" />;
                <rdf:type rdf:resource=\"&j.1;lexicon\"/>
            </<owl:NamedIndividual>
            
            <<owl:NamedIndividual rdf:about=\"{$this->getUriFactory()->getUriBase()}}.Edition\" >
                <rdfs:label>{$this->getResourceName()}-Edition</rdfs:label>
                <&j.1;date>2015</&j.1;date>
                <rdf:type rdf:resource=\"&j.1;Edition\"/>
            </<owl:NamedIndividual>
-->            
        </rdf:RDF>
            ";
        
        fwrite($fileResourceOwl, $resourceAnnotationStr);
        fclose($fileResourceOwl);
        
        // ontology validation
        $file = fopen($resourceOwlFile, 'r');
        $xml = fread($file, filesize($resourceOwlFile));
        fclose($file);

        try {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadXML($xml);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }         
    }
}
