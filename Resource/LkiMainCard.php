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
        //$resource = new LkiisResource($this->getResourceId());
        //$resource->getRecords($filename, 7300);
        
        // Sometimes it can find correct end record so number is atjusted
        $partSize = 19 *1024 * 1024;        
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
         *  record
         *      writer (4641)                       * Metrika -> Užrašytojai
         *      imageURLs (48822)                   * Paveikslėlis 126x166
         *      sourcelink (27369)                  * Metrika -> Šaltiniai
         *      gramref_header (26)
         *      attributes (num of instances:
                    - kartoteka (48822)             - visiems rašoma "Kartoteka 1"
                    - word (48822)                  * Antraštė kirčiuotas žodis ar junginys
                    - word_header (48718)           - naudojamas LKIIS paieškoje
                    - cardno (48822)                * Kortelės numeris
                    - box (48822)                   - nenaudojamas
                    - images (48822)                - paveiklėlių numeriai
                    - sourcelocation (16140)        * Metrika->Vietovė (nurodoma prie šaltinio)
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
                
                $lmfLemma->setImage($arr['imageUrl']);
                $lexicalEntry->setLemma($lmfLemma);
                
                $lmfSense = new Owl\LmfSense();
                $lmfSense->setLemmaWrittenForm($lmfLemma->getWrittenForm());

                $lmfSense->setUri($this->getUriFactory()->create('Sense', 
                                $arr['metadata']['word'] . '-' . $arr['metadata']['cardno'], 
                                $arr['id']));
                
                $lmfDefintion = new Owl\LmfDefinition();
                $lmfDefintion->setUri($this->getUriFactory()->create('Definition', 
                                $arr['metadata']['word'] . '-' . $arr['metadata']['cardno'], 
                                $arr['id']));
                
                $lmfTextRepresentation = new Owl\LmfTextRepresentation();
                $lmfTextRepresentation ->setUri($this->getUriFactory()->create('TextRepresentation', 
                                $arr['metadata']['word'] . '-' . $arr['metadata']['cardno'], 
                                $arr['id']));
                $writtenForm = "<![CDATA[";
                
                $writtenForm .= "<div><em>Kortelės numeris:</em> {$arr['metadata']['cardno']}</div>";
                // Spacing between rows
                $writtenForm .= "<div style=\"height: 5px;\"></div>";
                
                $writtenForm .= "<div><img width=\"238\" alt=\"\" src=\"{$arr['imageUrl']}\"></div>";
                // Spacing between rows
                $writtenForm .= "<div style=\"height: 5px;\"></div>";

                if (!empty($arr['metadata']['sourcelocation']) || !empty($arr['sourcelink'])) {
                    $writtenForm .= "<div>Metrika</div>";
                    if(!empty($arr['metadata']['sourcelocation'])) {
                        $writtenForm .= "<div><em>Vietovė:</em> {$arr['metadata']['sourcelocation']}</div>";    
                        // Spacing between rows
                        $writtenForm .= "<div style=\"height: 5px;\"></div>";
                    }
                    if(!empty($arr['sourcelink'])) {
                        $writtenForm .= "<div><em>Šaltiniai:</em> {$arr['sourcelink']}</div>";    
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
