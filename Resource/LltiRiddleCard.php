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
class LltiRiddleCard extends AbstractDictionary
{   
    private $_cacheDir = 'cache/LLTI_RIDDLE_CARD/';
    private $_ontologyFile = 'config/rastija_owl_v3_2015_07_30VM.owl';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->setResourceId('LLTI/misle');
        $this->setResourceName('Mįslių kartoteka');
        
        /** @var Uri\AbstractUri $uriFactory */
        $uriFactory = new Owl\Uri\UriFactory();
        $uriFactory->setUriBase('&lmf;kartoteka.Mįslių_kartoteka');
        
        $this->setUriFactory($uriFactory);
    }
    
    
    /**
     *  Main function for owl generations
     * 
     * @return string - dictionary ID encoded with MD5
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

        // File will be analysed by parts
        $partSize = 25 *1024 * 1024;        
        if (filesize($filename) > $partSize) {
         
            // Splitting data file
            $file = fopen($filename, 'r');
            $content = fread($file, filesize($filename));
            fclose($file);
   
            $parts = floor(filesize($filename) / $partSize) + 1;
            $startPoss = 0;
            for ($i = 1; $i <= $parts; $i++) {
                $partFileName = $filename . '_part_' . $i . '.txt';
                $partFileOfIndividuals = $fileOfIndividuals  . '_part_' . $i . '.txt';
                
                $partText = substr($content, $startPoss, $partSize);
                $content = substr($content, $partSize);
                $length = (strlen($content) < 1024 * 1024) ? strlen($content) : 1024 * 1024;
                $tmpStr = substr($content, 0, $length);

                $endOfRecord = strpos($tmpStr, '</return>') + 9;
                
                unset($tmpStr);
                
                $partText .= substr($content, 0, $endOfRecord);
                if ($i != $parts) {
                    $partText .= '</ns2:getRecordsResponse></soap:Body></soap:Envelope>' . "\n";
                }
                // remove unecessary tags
                $partText = str_replace('</ns2:getRecordsResponse></soap:Body></soap:Envelope><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><ns2:getRecordsResponse xmlns:ns2="http://servicebus.lki/">',
                    '', $partText);
                $partFile = fopen($partFileName, 'w');
                fwrite($partFile, $partText);
                fclose($partFile);
                unset($partText);

                $content = substr($content, $endOfRecord);
                $content = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><ns2:getRecordsResponse xmlns:ns2="http://servicebus.lki/">'
                        . $content;
            } 
            
            // Building of individuals and OWL
            for ($i = 1 ; $i <= $parts; $i++) {
                $partFileName = $filename . '_part_' . $i . '.txt';
                $partFileOfIndividuals = $fileOfIndividuals  . '_part_' . $i . '.txt';
                $partResourceOwlFile = $resourceOwlFile . '_part_' . $i . '.owl';
                
                $this->buildLmfIndividuals($partFileName, $partFileOfIndividuals);
                
                $this->createOwl($partFileOfIndividuals, $partResourceOwlFile);
            }
        } else {            
            $this->buildLmfIndividuals($filename, $fileOfIndividuals);
            
            // Make owl of dictionary
            $this->createOwl($fileOfIndividuals, $resourceOwlFile);            
        }
        
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
            [dc:identifier] => 80028    - ignoring because that they dublicae each other and are not informative 
            [dc:title] => 80028         - Pavadinimas "Mįslė [tekstas]"
            [dc:description] => 79975   * Transponuotas užminimas
            [dc:description_1] => 79903 * Fiksacijos tekstas
            [dc:subject] => 79870       * Įminimas
            [dc:source] => 79898        * Signat8ra
            [tm:type] => 80020          * Tipas
            [dc:publisher] => 68196     * Fiksuotojas 
            [dc:coverage] => 34491      * Vieta
            [tm:version] => 79993       * Versija
            [dc:creator] => 40683       * Pateikėjas
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
                                if (isset($ins[$childNode->nodeName])) {
                                    $ins[$childNode->nodeName . '_' . $num]['label'] = $childNode->getAttribute('label');
                                    $ins[$childNode->nodeName . '_' . $num]['value'] = $childNode->nodeValue;
                                    $num++;
                                } else {
                                    $ins[$childNode->nodeName]['label'] = $childNode->getAttribute('label');
                                    $ins[$childNode->nodeName]['value'] = $childNode->nodeValue;
                                }
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
            foreach (array_keys($arr['metadata']) as $nr => $key) {
                $key = $key . ' - ' . $arr['metadata'][$key]['label'];
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

            if ($arr['status'] != '-1' && !empty($arr['metadata']['dc:description']['value'])) {
                $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                $lexicalEntry->setUri($this->getUriFactory()->create('LexicalEntry', 
                                $arr['metadata']['dc:description']['value'],
                                $arr['id']));
                $lmfLemma = new Owl\LmfLemma();
                $lmfLemma->setWrittenForm($arr['metadata']['dc:description']['value']);
                $lmfLemma->setUri($this->getUriFactory()->create('Lemma', 
                                $arr['metadata']['dc:description']['value'],
                                $arr['id']));
                
                $lexicalEntry->setLemma($lmfLemma);
                
                $lmfSense = new Owl\LmfSense();
                $lmfSense->setLemmaWrittenForm($lmfLemma->getWrittenForm());

                $lmfSense->setUri($this->getUriFactory()->create('Sense', 
                                $arr['metadata']['dc:description']['value'],
                                $arr['id']));
                
                $lmfDefintion = new Owl\LmfDefinition();
                $lmfDefintion->setUri($this->getUriFactory()->create('Definition', 
                                $arr['metadata']['dc:description']['value'],
                                $arr['id']));
                
                $lmfTextRepresentation = new Owl\LmfTextRepresentation();
                $lmfTextRepresentation ->setUri($this->getUriFactory()->create('TextRepresentation', 
                                $arr['metadata']['dc:description']['value'],
                                $arr['id']));
                
                $writtenForm = "<![CDATA[";
                foreach ($arr['metadata'] as $key => $attr) {
                    
                    if ($key != 'dc:title') {
                        $writtenForm .= "<div><em>{$attr['label']}:</em> {$attr['value']} </div>";
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
    }
}
