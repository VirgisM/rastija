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
 * Description of  LKI comparison dictionary
 *
 * @author Virginijus
 */
class LkiComparison extends AbstractDictionary
{   
    private $_cacheDir = 'cache/LKI_COMPARISON/';
    private $_ontologyFile = 'config/rastija_owl_v3_2015_07_30VM.owl';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->setResourceId('LKIVocabulary/1682747');
        $this->setResourceName('Palyginimų žodynas');
        
        /** @var Uri\AbstractUri $uriFactory */
        $uriFactory = new Owl\Uri\UriFactory();
        $uriFactory->setUriBase('&lmf;zodynas.Palyginimų_žodynas');
        
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
        //$resource->getRecords($filename, 0, 250);

        // File will be analysed by parts
        $partSize = 120 * 1024 * 1024;        
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
            - homonym
         *      - word  
         *      - [1..n] comp          + All child elements are transformed to one level
         *          - [1..n]examplegrp
         *              - exmpl
         *                  - exmplsrc
         *                  - exmpltag
         *   - entryfulltext            - this element is not used
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
                    $record = $metadata->getElementsByTagName('record')->item(0);
                  
                    foreach ($record->getElementsByTagName('el') as $el) {
                        /* @var $el \DOMElement */
                        if ($el->getAttribute('value') || $el->getAttribute('name') == 'word') {
                            // Lemma
                            if ($el->getAttribute('name') == 'word') {
                                if (!isset($ins['lemma'])) {
                                    // First is lemma
                                    $ins['lemma'] = htmlspecialchars($el->getAttribute('value'));  
                                } else {
                                    // Second and next are wordForms
                                    $ins['wordForms'][] = htmlspecialchars($el->getAttribute('value'));
                                }
                            }
                            
                            // Idioms
                            if ($el->getAttribute('name') == 'comp') {
                                $compsArr = array('comp' => $el->getAttribute('value'));
                                $compsArr['comps'] = array();

                                // Take all childer elements they belong to same idiom
                                foreach ($el->childNodes as $expl) {
                                    // There are some DOMTExt nodes, so we will ignore them
                                    if (get_class($expl) == 'DOMElement') {
                                        /* @var $expl \DOMElement */
                                        if ($expl->getAttribute('name') == 'exmplgrp') {
                                            $compsArr['comps'][] = array($expl->getAttribute('name') => $this->getChildNodesArray($expl));
                                        } else {
                                            $compsArr[$expl->getAttribute('name')][] = $expl->getAttribute('value');
                                        }
                                    }
                                }
                                $ins['comps'][] = $compsArr; 
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

            if ($arr['status'] != '-1' && !empty($arr['metadata']['lemma'])) {
                $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                $lexicalEntry->setUri($this->getUriFactory()->create('LexicalEntry', 
                                $arr['metadata']['lemma'],
                                $arr['id']));
                $lmfLemma = new Owl\LmfLemma();
                $lmfLemma->setWrittenForm($arr['metadata']['lemma']);
                $lmfLemma->setUri($this->getUriFactory()->create('Lemma', 
                                $arr['metadata']['lemma'],
                                $arr['id']));
                
                $lexicalEntry->setLemma($lmfLemma);
                
                $lmfSense = new Owl\LmfSense();
                $lmfSense->setLemmaWrittenForm($lmfLemma->getWrittenForm());

                $lmfSense->setUri($this->getUriFactory()->create('Sense', 
                                $arr['metadata']['lemma'],
                                $arr['id']));
                
                $lmfDefintion = new Owl\LmfDefinition();
                $lmfDefintion->setUri($this->getUriFactory()->create('Definition', 
                                $arr['metadata']['lemma'],
                                $arr['id']));
                
                $lmfTextRepresentation = new Owl\LmfTextRepresentation();
                $lmfTextRepresentation ->setUri($this->getUriFactory()->create('TextRepresentation', 
                                $arr['metadata']['lemma'],
                                $arr['id']));
                
                $writtenForm = "<![CDATA[";
                foreach ($arr['metadata']['comps'] as $key => $attr) {
                    //$writtenForm .= "<div>";
                    if (isset($attr['comp'])) {                     
                        $writtenForm .= "\n<br/><span style=\"font-weight: bold;\">{$attr['comp']}</span> ";
                        
                        // IdiomTag
                        if (isset($attr['comptag'])) {
                            $writtenForm .= implode('., ', $attr['comptag']) .  ". ";
                        }
                      
                        // Explanation
                        if (isset($attr['comps'])) {
                            $countExpl = count($attr['comps']);
                                
                            foreach ($attr['comps'] as $key => $expls ) {
                                if (isset($expls['exmplgrp'])) {                                                 
                                    if ($countExpl > 1) {
                                        $writtenForm .= "\n<br/> <em>" . ($key + 1) . ".</em><i>{$expls['exmplgrp']['value']}</i>";
                                    } else {
                                        $writtenForm .= "<i>{$expls['exmplgrp']['value']}</i>";
                                    }

                                    // Examples
                                    if (isset($expls['exmplgrp']['children'])) {
                                        foreach ($expls['exmplgrp']['children'] as $example) {                                          
                                            if (isset($example['exmpl']['value'])) {
                                                $writtenForm .= " {$example['exmpl']['value']}.";
                                            }
                                            if (isset($example['exmpl']['children'])) {
                                                foreach($example['exmpl']['children'] as $nr => $item) {
                                                    if (isset($example['exmpl']['children'][$nr]['exmplsrc']['value'])) {
                                                        $writtenForm .= " " . $example['exmpl']['children'][$nr]['exmplsrc']['value'] . ".";
                                                    }
                                                    if (isset($example['exmpl']['children'][$nr]['exmpltag']['value'])) {
                                                        $writtenForm .= " (" . $example['exmpl']['children'][$nr]['exmpltag']['value'] . ").";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }                     
                    }
                    //$writtenForm .= "</div>";
                }
                
                $writtenForm .= "]]>";
                        
                $lmfTextRepresentation->setWrittenForm($writtenForm);
                
                $lmfDefintion->addTextRepresentation($lmfTextRepresentation);
            
                $lmfSense->setDefinition($lmfDefintion);

                $lexicalEntry->addSense($lmfSense);
                
                // Word form
                if (!empty($arr['metadata']['wordForms'])) {
                    $rank = 1;
                    foreach ($arr['metadata']['wordForms']  as $wordForm) {
                        $lmfWordForm = new Owl\LmfWordForm();
                        $lmfWordForm->setUri($this->getUriFactory()->create('WordForm', 
                                $wordForm, 
                                $arr['id']  .  '-' . $rank++));                         
                        $lmfWordForm->setWrittenForm($wordForm);
                        $lexicalEntry->addWordForm($lmfWordForm);
                     }
                }
                
                fwrite($fileIndividuals, $lexicalEntry->toLmfString());
            }
        }

        fclose($fileIndividuals);
        
        if (!empty($n)) {
            print_r($n);
        }
    }
    
    /**
     * get all childen
     *  
     * @param \DOMElement $node
     * 
     * @return array
     */
    private function getChildNodesArray(\DOMElement $node) {
        if ($node->childNodes->length > 1) {
            if (get_class($node) == 'DOMElement') {
                $result = array('value' => $node->getAttribute('value'));
            }
            $result['children']= array();
            foreach ($node->childNodes as $childNode) {
                if (get_class($childNode) == 'DOMElement') {
                    $result['children'][] = array( 
                        $childNode->getAttribute('name') => $this->getChildNodesArray($childNode),
                    );   
                }
            }
            return $result;
        } else {
            return array('value' => $node->getAttribute('value'));
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
