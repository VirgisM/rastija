<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;
use Rastija\Owl\Uri\AbstractUri;

/**
 * Lexical Entry is a class representing a lexeme in a given language. 
 * The Lexical Entry is a container for managing the Form and Sense classes. 
 * Therefore, the Lexical Entry manages the relationship between the forms and 
 * their related senses. A Lexical Entry instance can contain one to many 
 * different forms, and can have from zero to many different senses. 
 * The Lexical Entry class does not allow subclasses.
 *
 * @author Virginijus
 */
class LmfLexicalEntry extends AbstractLmfClass
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
     *
     * @var array of LmfWordForm's 
     */
    private $wordForms = array();
    
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
    
    /**
     * Equivalent to hasWordForm property
     * 
     * @param \Rastija\Owl\LmgWordForm $wordForm
     */
    public function addWordForm(LmfWordForm $wordForm)
    {
        array_push($this->wordForms, $wordForm);
    }
    
    
    public function getNextSenseRank() {
        return $this->_nextSenseRank;
    }
                
    public function toLmfString() {
        
        /* Lexical Entry part */
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri() }\">\n";
        
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

        foreach ($this->wordForms as $wordForm) {
            $str .= "\t<hasWordForm rdf:resource=\"{$wordForm->getUri() }\"/>\n";
        }
        
        $str .= "\t<j.1:lexicon rdf:resource=\"{$this->getResourceUri() }\"/>\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;LexicalEntry\"/>\n";
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

        /* WordForms */
        foreach ($this->wordForms as $wordForm) {
            /* @var $wordForm LmfWordForm */
            $str .= $wordForm->toLmfString();
        }
        
        return $str;
    }
}
