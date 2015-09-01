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
class EnLtDictionary extends DictionaryAbstract
{
    private $_resourceLmfName = '&j.1;zodynas.Anglų-Lietuvių_kalbų_žodynas'; //.Resource
    private $_cacheDir = 'cache/VU_EN-LT/';
    private $_ontologyFile = 'config/rastija_owl_v3_2015_07_30VM.owl';

    public function __construct() {
        $this->setResourceId('VU/10485716');
        $this->setResourceName('Anglų-Lietuvių kalbų žodynas');
    }
    
    public function generateLmfOwl() {
        $test = false;
        if ($test) {
            $filename  = $this->_cacheDir . md5($this->getResourceId()) . '_1.txt';
            $fileOfIndividuals = $this->_cacheDir . md5($this->getResourceId()) . '_individuals_1' . '.owl';
            $resourceOwlFile = $this->_cacheDir . md5($this->getResourceId()) . '_ontology_1' . '.owl';
        } else {
            $filename  = $this->_cacheDir . md5($this->getResourceId()) . '.txt';
            $fileOfIndividuals = $this->_cacheDir . md5($this->getResourceId()) . '_individuals' . '.owl';
            $resourceOwlFile = $this->_cacheDir . md5($this->getResourceId()) . '_ontology' . '.owl';
        }
        // Get resource information from the service
        //$resource = new Service\LkiisResource($this->_resourceId);
        //$resource->getRecords($filename, 100);
        
        // Build individal for LMF ontology
        $this->_buildLmfIndividuals($filename, $fileOfIndividuals);
        
        // Make owl of dictionary
        $this->_createOwl($fileOfIndividuals, $resourceOwlFile);
        
        return md5($this->_resourceId);
    }

    public function setResourceId($resourceId) {
        $this->_resourceId = $resourceId;
    }

    public function setResourceName($resourceName) {
        $this->_resourceName = $resourceName;
    }
    
    private function _buildLmfIndividuals($filename, $fileOfIndividuals)
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
                                $ins['lemma'] = $el->getAttribute('value');  
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
                                            $senseArr['partOfSpeach'] = $this->_fullAbbreviation($sense->getAttribute('value'));
                                        }

                                        // Equivalents
                                        if ($sense->getAttribute('name') == 'Atitikmuo') {
                                            $senseArr['equivalent'][] = $sense->getAttribute('value');
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
             *      - pronunciation () - TODO
             *      - wordForms        - TODO
             *      - senses
             *          - partOfSpeach
             *          - equivalent
             */
            if ($arr['metadata']['lemma']) {
                $lexicalEntries = array();


                $isFirst = TRUE;
                foreach ($arr['metadata']['senses'] as $sense) {
                    $lmfSense = new Owl\LmfSense();

                    if ($isFirst) {
                        $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                        $lexicalEntry->setSeed($arr['id']);
                        $lexicalEntry->setLemma($arr['metadata']['lemma']);

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
                            $lexicalEntry->setSeed($arr['id']);
                            $lexicalEntry->setLemma($arr['metadata']['lemma'] . '-' . (sizeof($lexicalEntries)+1));

                            $lexicalEntry->setPartOfSpeech($sense['partOfSpeach']);
                            array_push($lexicalEntries, $lexicalEntry);
                        }
                    }
                    $lmfSense->setUriBase($lexicalEntry->getUriBase());
                    $lmfSense->setLemmaWrittenForm($lexicalEntry->getLemma());

                    $equivalents = $sense['equivalent'];
                    $rank = 1;
                    foreach ($equivalents as $equivalent) {
                         $lmfEquivalent = new Owl\LmfEquivalent();
                         $lmfEquivalent->setLanguage('Anglų');
                         $lmfEquivalent->setWrittenForm($equivalent);
                         $lmfEquivalent->setUriBase($lexicalEntry->getUriBase());
                         $lmfEquivalent->setRank($rank++);

                         $lmfSense->addEquivalent($lmfEquivalent);
                     }
                     $lexicalEntry->addSense($lmfSense);
                }

                // When is more than one sense
                foreach($lexicalEntries as $lexicalEntry) {
                    fwrite($fileIndividuals, $lexicalEntry->toLmfString());
                }
            }
            echo '<br />' . $recordNr++ . '-' . $arr['id'] . '-' .  $arr['metadata']['lemma'];
        }

        fclose($fileIndividuals);
    }

    private function _fullAbbreviation($abbr)
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
    
    private function _createOwl($fileOfIndividuals, $resourceOwlFile)
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
            <<owl:NamedIndividual rdf:about=\"{$this->_resourceLmfName}.Resource\">
                <rdfs:label>{$this->_resourceName}</rdfs:label>
                <&j.1;hasEdition rdf:resource=\"{$this->_resourceLmfName}.Edition\" />;
                <rdf:type rdf:resource=\"&j.1;lexicon\"/>
            </<owl:NamedIndividual>
            
            <<owl:NamedIndividual rdf:about=\"{$this->_resourceLmfName}.Edition\" >
                <rdfs:label>{$this->_resourceName}-Edition</rdfs:label>
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
