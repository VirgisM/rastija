<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;

/**
 * Description of LmfLemma
 *
 * @author Virginijus
 */
class LmfLexicalEntry {
    
    /**
     *
     * @var string 
     */
    private $_partOfSpeech;
    
    private $_resourceName;
    private $_lexicon;
    private $_name;
    private $_lemma;
    private $_seed = "some seed";
    private $_lmfNs = "&lmf;";
    private $_resourceNs = "http://www.rastija.lt/isteklius";
    
    private $_nextSenseRank = 0;
    /**
     *
     * @var array of LmfSense's 
     */
    private $_senses = array();
    
    public function __construct($resourceName) {
        $this->_resourceName = preg_replace('/\ /', '_', $resourceName);
    }
    
    /**
     * @todo add leanguage attribute
     * @param type $partOfSpeech
     */
    public function setPartOfSpeech($partOfSpeech)
    {
        $this->_partOfSpeech = $partOfSpeech;
    }
    
    public function getPartOfSpeech()
    {
        return $this->_partOfSpeech;
    }
    
    public function getResource() {
        return $this->_resourceName;
    }
    
    public function setLexicon($lexicon) {
        $this->_lexicon = $lexicon;
    }
    
    public function setSeed($seed) {
        $this->_seed  = $seed;
    }

    public function getSeed() {
        return $this->_seed;
    }
    
    public function setName($name) {
        $this->_name = preg_replace('/\ /', '_', strtolower($name));
    }
    
    public function setLemma($lemma) {
        if (!$this->_name) {
            $this->setName($lemma);
        }
        $this->_lemma = $lemma;
    }
    
    public function getLemma()
    {
        return $this->_lemma;
    }
    
    public function getLemmaWrittenForm() 
    {
        return $this->_lemma;
    }

    public function getUriBase()
    {
        return $this->_lmfNs . $this->_fixUri($this->getResource());
    }
    
    public function getLexicalEntryUri()
    {
        return $this->getUriBase() . '.' . $this->_fixUri($this->_name) 
                . '.LexicalEntry-' . md5('LexicalEntry-' . $this->getLemmaWrittenForm() . $this->getSeed());
    }
    
    public function getLemmaUri() {
        return $this->getUriBase() . '.' . $this->_fixUri($this->_lemma) 
                . '.Lemma-' . md5('Lemma-' . $this->getLemmaWrittenForm() . $this->getSeed());
    }
    
    public function getResourceUri() {
        return $this->_resourceNs . '#zodynas.' . $this->_fixUri($this->getResource()) . '.Resource';
    }
    
    public function addSense(LmfSense $sense)
    {
        if ($this->_nextSenseRank < $sense->getRank()) {
            $this->_nextSenseRank = $sense->getRank () + 1;
        } else {
            $sense->setRank($this->_nextSenseRank);
            $this->_nextSenseRank++;
        }
        array_push($this->_senses, $sense);
    }
    
    public function getNextSenseRank() {
        return $this->_nextSenseRank;
    }
            

    /**
     * Function will remove unallowed simbols from uri
     * 
     * @param string $uri
     */
    private function _fixUri($uri)
    {
        return preg_replace('/[\[\]\{\}\<\>\'\"\&\s\t\n]/i', '_', $uri);
    }
    
    public function toLmfString() {
        
        /* Lexical Entry part */
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getLexicalEntryUri() }\">\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;LexicalEntry\"/>\n";
        $str .= "\t<j.1:lexicon rdf:resource=\"{$this->getResourceUri() }\"/>\n";
        
        // <partOfSpeech xml:lang="en">abbr</partOfSpeech>
        if($this->getPartOfSpeech()) {
            $str .= "\t<partOfSpeech>{$this->getPartOfSpeech() }</partOfSpeech>\n";
        }
        
        if ($this->getLemma()) {
            $str .= "\t<hasLemma rdf:resource=\"{$this->getLemmaUri() }\"/>\n";
            $str .= "\t<rdfs:label>{$this->getLemmaWrittenForm() }</rdfs:label>\n";
        }
        
        foreach ($this->_senses as $sense) {
            $str .= "\t<hasSense rdf:resource=\"{$sense->getUri() }\"/>\n";
        }
        
        $str .= "</owl:NamedIndividual>\n";
        
        /* Lemma part */
        /*
          <rdf:Description rdf:about="http://www.lexinfo.net/lmf#VU_LatviuLietuviu_zodynas..Lemmada41f9ca-439d-43f4-a20f-321add97889f">
           <j.0:writtenForm>sēkla</j.0:writtenForm>
           <rdfs:label>VU_LatviuLietuviu_zodynas..Lemmada41f9ca-439d-43f4-a20f-321add97889f</rdfs:label>
           <rdf:type rdf:resource="http://www.lexinfo.net/lmf#Lemma"/>
         </rdf:Description>  
        */    
        if ($this->getLemma()) {
           $str .= "<owl:NamedIndividual rdf:about=\"{$this->getLemmaUri()}\">\n";
           $str .= "\t<writtenForm>{$this->getLemmaWrittenForm() }</writtenForm>\n";
           $str .= "\t<rdfs:label>{$this->getLemmaWrittenForm() }-Lemma</rdfs:label>\n";
           $str .= "\t<rdf:type rdf:resource=\"&lmf;Lemma\"/>\n";
           $str .= "</owl:NamedIndividual>\n";
        }
        
        /* Senses */
        foreach ($this->_senses as $sense) {
            /* @var $sense LmfSense */
            $str .= $sense->toLmfString();
        }
        
        return $str;
    }
    
}
