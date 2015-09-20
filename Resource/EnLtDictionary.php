<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Resource;
use Rastija\Owl;
use Rastija\Service;

/**
 * Description of LtEnDictionary
 *
 * @author Virginijus
 */
class EnLtDictionary extends AbstractDictionary
{
    private $_cacheDir = 'cache/VU_EN-LT/';
    private $_ontologyFile = 'config/rastija_owl_v3_2015_07_30VM.owl';

    /**
     * Constructor
     */
    public function __construct() {
        $this->setResourceId('VU/10485716');
        $this->setResourceName('Anglų-Lietuvių kalbų žodynas');

        /** @var Uri\AbstractUri $uriFactory */
        $uriFactory = new Owl\Uri\UriFactory();
        $uriFactory->setUriBase('&lmf;zodynas.Anglų-Lietuvių_kalbų_žodynas');
        
        $this->setUriFactory($uriFactory);        
    }
    
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
        $partSize = 4 *1024 * 1024;        
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
            for ($i = 6 ; $i <= 6; $i++) {
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

    private function buildLmfIndividuals($filename, $fileOfIndividuals)
    {
        $resourceName = $this->getResourceName();
                
        $file = fopen($filename, 'r');
        $xml = fread($file, filesize($filename));
        fclose($file);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml);

        $fileIndividuals = fopen($fileOfIndividuals, "w+");
        $recordNr = 1;
        foreach($dom->getElementsByTagName('return') as $domRecord) {
            /* @var $domRecord \DOMElement */
            $nodes = $domRecord->childNodes;

            $arr = array();
            foreach ($nodes as $node) {
                /* @var $node \DOMElement */
                if ($node->nodeName == 'metadata') {
                    $metadata = new \DOMDocument('1.0', 'UTF-8');
                    $metadata->loadXML($node->nodeValue);

                    $ins = array();
                    // Taking a record
                    /* @var $record \DOMElement */
                    $record = $metadata->getElementsByTagName('record')->item(0);

                    foreach ($record->getElementsByTagName('el') as $el) {
                        /* @var $el \DOMElement */
                        if ($el->getAttribute('value') || $el->getAttribute('name') == 'Reiksme') {
                            // Lemma
                            if ($el->getAttribute('name') == 'AntrastinisZodis') {
                                $ins['lemma'] = htmlspecialchars($el->getAttribute('value'));  
                            }
                            // Forms
                            if ($el->getAttribute('name') == 'Forma') {
                                $ins['wordForms'][] = $el->getAttribute('value');
                            }
                            // Pronunciation
                            if ($el->getAttribute('name') == 'Tarimas') {
                                $ins['pronunciation'] = $el->getAttribute('value');
                            }

                            // Senses
                            if ($el->getAttribute('name') == 'Reiksme') {
                                $senseArr = array();

                                foreach ($el->childNodes as $sense) {

                                    // There are some DOMTExt nodes, so we will ignore them
                                    if (get_class($sense) == 'DOMElement') {
                                        /* @var $sense \DOMElement */

                                        // PartOfSpeach
                                        if ($sense->getAttribute('name') == 'KalbosDalis') {
                                            $senseArr['partOfSpeach'] = $this->fullAbbreviation($sense->getAttribute('value'));
                                        }

                                        // Equivalents
                                        if ($sense->getAttribute('name') == 'Atitikmuo') {
                                            // Dictionary can contain ilegal xml chars
                                            $senseArr['equivalent'][] = htmlspecialchars($sense->getAttribute('value'));
                                        }
                                    }
                                }
                                $ins['senses'][] = $senseArr; 
                            }
                        }
                    }
                    $arr[$node->nodeName] = $ins;
                } else {
                    $arr[$node->nodeName] = $node->nodeValue;
                }
            }
         
            // TODO pridėti tarimą ir wordFormas
            // Concert the array to lexical entry
            /* array contains
             * - id
             * - header
             * - status
             * - metadata
             *      - lemma (attr: word)
             *      - (attr: writer)
             *      - (attr: imageURL)
             *      - (attr: sourceLink)
             *      - (attr: 
             *      - pronunciation () - @TODO
             *      - wordForms        
             *      - senses
             *          - partOfSpeach
             *          - equivalent
             */
            if (isset($arr['metadata']['lemma'])) {
                $lexicalEntries = array();

                $senseNr = 1;
                $isFirst = TRUE;
                foreach ($arr['metadata']['senses'] as $sense) {
                    $lmfSense = new Owl\LmfSense();

                    if ($isFirst) {
                        $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                        $lexicalEntry->setUri($this->getUriFactory()->create('LexicalEntry', 
                                $arr['metadata']['lemma'], 
                                $arr['id']));                        
                        
                        // Set Lemma
                        $lmfLemma = new Owl\LmfLemma();
                        $lmfLemma->setWrittenForm($arr['metadata']['lemma']);
                        $lmfLemma->setUri($this->getUriFactory()->create('Lemma', 
                                        $arr['metadata']['lemma'], 
                                        $arr['id']));
                        $lexicalEntry->setLemma($lmfLemma);

                        $lexicalEntry->setPartOfSpeech($sense['partOfSpeach']);
                        array_push($lexicalEntries, $lexicalEntry);                        
                        $isFirst = FALSE;
                    } else {
                        reset($lexicalEntries);
                        $lexicalEntry = NULL;
                        // Check if lexical entry with specified part of speech exists
                        foreach($lexicalEntries as $lexEntry) {
                            /* @var $lexEntry Owl\LmfLexicalEntry */
                            if ($lexEntry->getPartOfSpeech() == $sense['partOfSpeach']) {
                                $lexicalEntry = $lexEntry;
                            }
                        }
                        // Creation of new entity of lexical entry
                        if (!$lexicalEntry) {
                            $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                            $lexicalEntry->setUri($this->getUriFactory()->create('LexicalEntry', 
                                    $arr['metadata']['lemma'] . '-' . (sizeof($lexicalEntries)+1), 
                                    $arr['id']));                             

                            // Set Lemma
                            $lmfLemma = new Owl\LmfLemma();
                            $lmfLemma->setWrittenForm($arr['metadata']['lemma']);
                            $lmfLemma->setUri($this->getUriFactory()->create('Lemma', 
                                            $arr['metadata']['lemma'] .  '-' . (sizeof($lexicalEntries)+1), 
                                            $arr['id']));
                            $lexicalEntry->setLemma($lmfLemma);
                        
                            $lexicalEntry->setPartOfSpeech($sense['partOfSpeach']);
                            array_push($lexicalEntries, $lexicalEntry);
                        }
                    }
                    $lmfSense->setUri($this->getUriFactory()->create('Sense', 
                            $lexicalEntry->getLemma()->getWrittenForm(),
                            $arr['id'] . '-' . $senseNr++));
                    $lmfSense->setLemmaWrittenForm($lexicalEntry->getLemma()->getWrittenForm());

                    $equivalents = $sense['equivalent'];
                    $rank = 1;
                    foreach ($equivalents as $equivalent) {
                        $lmfEquivalent = new Owl\LmfEquivalent();
                        // Bug "patekti į nepatogią padėtį" firs space is nor normal
                        if ($equivalent == 'patekti į nepatogią padėtį') {
                            $equivalent = 'patekti į nepatogią padėtį';
                        }
                        $lmfEquivalent->setUri($this->getUriFactory()->create('Equivalent', 
                                $equivalent, 
                                $arr['id']  .  '-' . $rank));                         
                        $lmfEquivalent->setLanguage('Lietuvių');
                        $lmfEquivalent->setWrittenForm($equivalent);
                        $lmfEquivalent->setRank($rank++);

                        $lmfSense->addEquivalent($lmfEquivalent);
                     }
                     $lexicalEntry->addSense($lmfSense);
                }
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

                // When is more than one sense
                foreach($lexicalEntries as $lexicalEntry) {
                    fwrite($fileIndividuals, $lexicalEntry->toLmfString());
                }
            }
            //echo '<br />' . $recordNr++ . '-' . $arr['id'] . '-' .  $arr['metadata']['lemma'];
        }

        fclose($fileIndividuals);
    }

    private function fullAbbreviation($abbr)
    {
        // From http://members.peak.org/~jeremy/dictionaryclassic/chapters/abbreviations.php
        $map = array (
            'a'    => 'adjective ',   // my quess by Alkonas
            'abbr' => 'abbreviation',
            'acr'  => 'acronym',
            'adj'  => 'adjective',
            'adv'  => 'adverb',
            'art'  => 'article', // my quess
            'comb' => 'prefix',   // my quess
            'conj' => 'conjunction',
            'int'  => 'interjection', // my quess by Alkonas
            'intj' => 'interjection',
            'n'    => 'noun',
            'num'  => 'numeral', // my quess by Alkonas. According to source it should be a number
            'part' => 'particle', // my quess by Alkonas
            'prep' => 'preposition',
            'pref' => 'prefix',
            'pron' => 'pronoun',
            'v'    => 'verb',
            // Free part of speech
            'num card' => 'cardinal (numeral)', //my quess by Alkonas
            'phrase'   => 'phrase',
            'prefix'   => 'prefix',
        );
        if (isset($map[$abbr])) {
            return $map[$abbr];
        } else {
            echo "Not mapped abbreviation " . $abbr;
            return $abbr;
        }
    }
    
    private function createOwl($fileOfIndividuals, $resourceOwlFile)
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
                <rdf:type rdf:resource=\"&j.1;Edition
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
