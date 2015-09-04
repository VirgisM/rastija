<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;
use Rastija\Owl\Uri\AbstractUri;
/**
 * Description of LmfLemma
 *
 * @author Virginijus
 */
class LmfLexicalEntry implements LmfClassInterface
{

    /**
     *
     * @var string 
     */
    private $_partOfSpeech;
    
    /**
     *
     * @var array of LmfSense's 
     */
    private $senses = array();
    
    /**
     * Lemma  of the lexical entry
     * 
     * @var LmfLemma 
     */
    private $lemma;
  
    /* ------------------------ Not LMF ontology parameters ------------------*/
    
    private $_resourceNs = "http://www.rastija.lt/isteklius";
    
    private $resourceName;

    private $_nextSenseRank = 0;

    public function __construct($resourceName) {
        $this->setResource($resourceName);
    }
     
    public function getPartOfSpeech()
    {
        return $this->_partOfSpeech;
    }
    
    /**
     * @todo add leanguage attribute
     * @param type $partOfSpeech
     */
    public function setPartOfSpeech($partOfSpeech)
    {
        $this->_partOfSpeech = $partOfSpeech;
    }

    public function getResource() {
        return $this->resourceName;
    }

    public function setResource($resourceName) {
        $this->resourceName = preg_replace('/\ /', '_', $resourceName);
    }
    
    /**
     * LexicalEntry class Uri 
     * 
     * @var AbstractUri
     */
    private $lexicalEntryUri;
    
    /**
     * {@inheritdoc}
     */
    public function getUri() {
        return $this->lexicalEntryUri->getUri();
    }
    
    /**
     * {@inheritdoc}
     */    
    public function setUri(AbstractUri $uri) {
        $this->lexicalEntryUri = $uri;
    }  
    
    /**
     * Set Lemma
     * Equivalent to hasLemma
     * 
     * @param LmfLemma $lemma
     */
    public function setLemma(LmfLemma $lemma) {
        $this->lemma = $lemma;
    }

    /**
     * Get Lemma
     * 
     * @return LmfLemma 
     */
    public function getLemma()
    {
        return $this->lemma;
    }
    
    /**
     * Function will remove unallowed simbols from uri
     * 
     * @todo Need to remove this function
     * @param string $uri
     */
    private function fixUri($uri)
    {
        return preg_replace('/[\[\]\{\}\<\>\'\"\&\s\t\n]/i', '_', $uri);
    }
    
    public function getResourceUri() {
        return $this->_resourceNs . '#zodynas.' . $this->fixUri($this->getResource()) . '.Resource';
    }
    
    /**
     * Equivalent to hasSense property
     * 
     * @param \Rastija\Owl\LmfSense $sense
     */
    public function addSense(LmfSense $sense)
    {
        if ($this->_nextSenseRank < $sense->getRank()) {
            $this->_nextSenseRank = $sense->getRank () + 1;
        } else {
            $sense->setRank($this->_nextSenseRank);
            $this->_nextSenseRank++;
        }
        array_push($this->senses, $sense);
    }
    
    public function getNextSenseRank() {
        return $this->_nextSenseRank;
    }
                
    public function toLmfString() {
        
        /* Lexical Entry part */
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri() }\">\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;LexicalEntry\"/>\n";
        $str .= "\t<j.1:lexicon rdf:resource=\"{$this->getResourceUri() }\"/>\n";
        
        // <partOfSpeech xml:lang="en">abbr</partOfSpeech>
        if($this->getPartOfSpeech()) {
            $str .= "\t<partOfSpeech>{$this->getPartOfSpeech() }</partOfSpeech>\n";
        }
        
        if ($this->getLemma()) {
            $str .= "\t<hasLemma rdf:resource=\"{$this->getLemma()->getUri() }\"/>\n";
            $str .= "\t<rdfs:label>{$this->getLemma()->getWrittenForm() }</rdfs:label>\n";
        }
        
        foreach ($this->senses as $sense) {
            $str .= "\t<hasSense rdf:resource=\"{$sense->getUri() }\"/>\n";
        }
        
        $str .= "</owl:NamedIndividual>\n";
        
        /* Lemma part */  
        if ($this->getLemma()) {
            $str .= $this->getLemma()->toLmfString();
        }
        
        /* Senses */
        foreach ($this->senses as $sense) {
            /* @var $sense LmfSense */
            $str .= $sense->toLmfString();
        }
        
        return $str;
    }
}
