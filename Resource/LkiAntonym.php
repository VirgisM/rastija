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
 * Description of  LKI classified dictionary
 *
 * @author Virginijus
 */
class LkiAntonym extends AbstractDictionary
{   
    private $_cacheDir = 'cache/LKI_ANTONYM/';
    private $_ontologyFile = 'config/rastija_owl_v3_2015_07_30VM.owl';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->setResourceId('LKIVocabulary/1682743');
        $this->setResourceName('Antonimų žodynas');
        
        /** @var Uri\AbstractUri $uriFactory */
        $uriFactory = new Owl\Uri\UriFactory();
        $uriFactory->setUriBase('&lmf;zodynas.Antonimų_žodynas');
        
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
        //$resource->getRecords($filename, 0, 500);

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
         *          - grammar
         *          - wordtag
         *      - [1..n] antonym        + All child elements are transformed to one level
         *          - antgramar
         *          - anttag
         *          - antremote
         *      - [1..n] valcontext
         *          - [1..n] example
         *              - [1..n]exampletag
         *          - expl              + word explanation
         *          - antexpl           + antonym explanation
         *   - entryfulltext            - this element is not used
         * status
         */
        $n = array();
        
        $lexEntries = array();
        // Index of all posible lexical entries ([] => 'lemma')
        $lexIndex = array();
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
                        if ($el->getAttribute('value') || $el->getAttribute('name') == 'valcontext') {
                            // Homonym
                            if ($el->getAttribute('name') == 'homonym') {
                                if ($el->getAttribute('value')) {
                                    $ins['homonym'] = $el->getAttribute('value'); 
                                }
                            }
                            
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
                           
                            // Antonyms
                            if ($el->getAttribute('name') == 'antonym') {
                                $ant = array('antonym' => $el->getAttribute('value'));

                                foreach ($el->childNodes as $param) {
                                    // There are some DOMTExt nodes, so we will ignore them
                                    if (get_class($param) == 'DOMElement') {
                                        /* @var $param \DOMElement */
                                        $ant[$param->getAttribute('name')][] = $param->getAttribute('value');
                                    }
                                }
                                $ins['antonyms'][] = $ant;
                            }
                            
                            // value context
                            if ($el->getAttribute('name') == 'valcontext') {
                                $valcontextArr = array();
                                // Take all childer elements they belong to same idiom
                                foreach ($el->childNodes as $expl) {
                                    // There are some DOMTExt nodes, so we will ignore them
                                    if (get_class($expl) == 'DOMElement') {
                                        /* @var $expl \DOMElement */
                                        if ($expl->getAttribute('name') == 'example') {
                                            $valcontextArr['examples'][] = array($expl->getAttribute('name') => $this->getChildNodesArray($expl));
                                        } else {
                                            $valcontextArr[$expl->getAttribute('name')][] = $expl->getAttribute('value');
                                        }
                                    }
                                }
                                $ins['valcontexts'][] = $valcontextArr; 
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
                $homonym = (isset($arr['metadata']['homonym'])) ? $arr['metadata']['homonym'] : '';
                $lexIndex[$arr['id']] = $arr['metadata']['lemma'];
                        
                // Lexical entry has multiple senses
                if (isset($lexEntries[$arr['metadata']['lemma']])) {
                     $lexicalEntry  = $lexEntries[$arr['metadata']['lemma']];
                     $lmfLemma = $lexicalEntry->getLemma();
                } else {
                    $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                    $lexicalEntry->setUri($this->getUriFactory()->create('LexicalEntry', 
                                    $arr['metadata']['lemma'],
                                    0));
                    $lmfLemma = new Owl\LmfLemma();
                    $lmfLemma->setWrittenForm($arr['metadata']['lemma']);
                    $lmfLemma->setUri($this->getUriFactory()->create('Lemma', 
                                    $arr['metadata']['lemma'],
                                    $arr['id']));

                    $lexicalEntry->setLemma($lmfLemma);
                }
                
                $lmfSense = new Owl\LmfSense();
                $lmfSense->setLemmaWrittenForm($lmfLemma->getWrittenForm());
                if ($homonym) {
                    $lmfSense->setRank($homonym);
                }
                $lmfSense->setUri($this->getUriFactory()->create('Sense', 
                                $arr['metadata']['lemma'],
                                $arr['id'] . '-' . $homonym));
                
                $lmfDefintion = new Owl\LmfDefinition();
                $lmfDefintion->setUri($this->getUriFactory()->create('Definition', 
                                $arr['metadata']['lemma'],
                                $arr['id'] . '-' . $homonym));
                
                $lmfTextRepresentation = new Owl\LmfTextRepresentation();
                $lmfTextRepresentation ->setUri($this->getUriFactory()->create('TextRepresentation', 
                                $arr['metadata']['lemma'],
                                $arr['id'] . '-' . $homonym));
                $antonyms = $arr['metadata']['antonyms'];
                
                $writtenForm = "<![CDATA[";
                foreach ($arr['metadata']['valcontexts'] as $key => $attr) {
                    if (sizeof($arr['metadata']['valcontexts']) > 1) {
                        $writtenForm .= "\n<span style=\"font-weight: bold;\">" . ($key + 1) . "</span>";
                    }
                    
                    // Explanation
                    if (isset($attr['expl'])) {
                        $writtenForm .= "\n<em>{$attr['expl'][0]}</em> <br />";
                        // Antonyms
                        foreach ($antonyms as $key => $antonym ) {
                            $writtenForm .=  "\n<span style=\"font-weight: bold;\">{$antonym['antonym']}</span>, ";                       
                        }
                        // remove last comma
                        $writtenForm = substr($writtenForm, 0, strlen($writtenForm) - 2) . ' ';
                    }
                    // Antonym explanation
                    if (isset($attr['antexpl'])) {
                        $writtenForm .= "\n<br/> <em>{$attr['antexpl'][0]}</em>";
                    }                    
                    
                    // Examples
                    if (isset($attr['examples'])) {
                        $countExpl = count($attr['examples']);
                        foreach ($attr['examples'] as $key => $expls ) {
                            // Example
                            if (isset($expls['example']['value'])) {                                                 
                                $writtenForm .= "\n<br />{$expls['example']['value']}";

                                // Tags
                                if (isset($expls['example']['children'])) {
                                    foreach ($expls['example']['children'] as $example) {                                     
                                        if (isset($example['exampletag']['value'])) {
                                            $writtenForm .= " {$example['exampletag']['value']}.";
                                        }
                                    }
                                }
                            }
                        }                   
                    }
                }
                
                $writtenForm .= " ]]>";
                     
                $lmfTextRepresentation->setWrittenForm($writtenForm);
                
                $lmfDefintion->addTextRepresentation($lmfTextRepresentation);
            
                $lmfSense->setDefinition($lmfDefintion);

                // Add sense relations
                foreach ($antonyms as $key => $antonym ) {
                    /*
                    $antonymLexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                    $antonymLexicalEntry->setUri($this->getUriFactory()->create('LexicalEntry', 
                                $antonym['antonym'],
                                0));
                    
                    $antonymLmfLemma = new Owl\LmfLemma();
                    $antonymLmfLemma->setWrittenForm($arr['metadata']['lemma']);
                    $antonymLmfLemma->setUri($this->getUriFactory()->create('Lemma', 
                                    $antonym['antonym'],
                                    0));
                    $antonymLmfLemma->setWrittenForm($antonym['antonym']);
                    
                    $antonymLexicalEntry->setLemma($antonymLmfLemma);
                    fwrite($fileIndividuals, $antonymLexicalEntry->toLmfString());
                    */
                    
                    $senseRelation = new Owl\LmfSenseRelation();
                    $senseRelation->setUri($this->getUriFactory()->create('SenseRelation', 
                                $arr['metadata']['lemma'],
                                $arr['id'] . '-'. $key));
                    $senseRelation->setType('Antonimas');
                    $senseRelation->setRank($key + 1);
                    //$senseRelation->addSenseRelatedTo($antonymLexicalEntry);
                    $senseRelation->setWrittenForm( $antonym['antonym']);
                    $lmfSense->addSenseRelation($senseRelation);
                }                
               
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
                // save not homonyms
                if (!$homonym) {
                    fwrite($fileIndividuals, $lexicalEntry->toLmfString());
                }  else {
                    // Update lexical entries
                    $lexEntries[$arr['metadata']['lemma']] = @$lexicalEntry;
                }
            }
        }
       
        // Save homonyms
        foreach($lexEntries as $lexEntry) {
            fwrite($fileIndividuals, $lexEntry->toLmfString());
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
