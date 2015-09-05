<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;

/**
 * In a bilingual MRD, the Equivalent class represents the translation 
 * equivalent of the word form managed by the Lemma class. 
 * The Equivalent class is in a zero to many aggregate association with 
 * the Sense class, which allows the lexicon developer to omit the 
 * Equivalent class from a monolingual dictionary.
 *
 * @author Virginijus
 */
class LmfEquivalent {
    /**
     * LMF Equivalent language data property
     * 
     * @var string
     */
    private $_language;
    
    /**
     * LMF Equivalent rank data property
     * 
     * @var string
     */
    private $_rank = 1;
            
            
    /**
     * LMF Equivalent written form data property
     * 
     * @var string
     */
    private $_writtenForm;
    
    /* ------------------------ Not LMF ontology parameters ------------------*/    
    /**
     * LMF individual URI
     * 
     * @var string
     */
    private $_uri;
    
    
    private $_uriBase;
    
    public function setLanguage($lang) 
    {
        $this->_language = ucfirst(strtolower($lang));
    }
    
    public function getLanguage() 
    {
        return $this->_language;
    }
    
    public function setRank($rank) 
    {
        $this->_rank = $rank;
    }
    
    public function getRank() 
    {
        return $this->_rank;
    }
    
    public function setWrittenForm($writtenForm)
    {
        $this->_writtenForm = $writtenForm;
    }
   
    public function getWrittenForm()
    {
        return $this->_writtenForm;
    }
    
    public function setUri($uri)
    {
        $this->_uri = $uri;
    }
    
    public function getUri()
    {
        // Generate uri
        if (!$this->_uri && $this->getWrittenForm()) {
            $this->_uri = $this->getUriBase() . '.' . $this->_fixUri($this->getWrittenForm()) . '.Equivalent-' . md5('Equivalent-' . $this->getWrittenForm(). $this->getRank()); 
        }
        return $this->_uri;
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
    
    public function setUriBase($uriBase)
    {
        $this->_uriBase = $uriBase;
    }
    
    public function getUriBase()
    {
        return $this->_uriBase;
    }
    
    public function toLmfString()
    {
/*
<owl:NamedIndividual rdf:about="http://www.lexinfo.net/lmf#VU_LatviuLietuviu_zodynas.rusvaplaukis.Equivalent1b498250-0409-4ecc-93d5-916a97fdcb8c">
    <writtenForm>rusvaplaukis</writtenForm>
    <language>Lietuvių</language>
    <rdfs:label>rusvaplaukis</rdfs:label>
    <rdf:type rdf:resource="http://www.lexinfo.net/lmf#Equivalent"/>
  </owl:NamedIndividual>
 */          
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\"> \n";
        if ($this->getWrittenForm()) {
            $str .= "<writtenForm>{$this->getWrittenForm()}</writtenForm>";
            $str .= "<rdfs:label>{$this->getWrittenForm()}-Equivalent</rdfs:label>";
        }
        if ($this->getLanguage()) {
            $str .= "<language>{$this->getLanguage()}</language>";
        }
        if ($this->getRank()) {
            $str .= "<rank>{$this->getRank()}</rank>";
        }
        $str .= "\t<rdf:type rdf:resource=\"&lmf;Equivalent\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        return $str;
    }
}
