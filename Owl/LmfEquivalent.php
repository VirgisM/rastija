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
class LmfEquivalent extends AbstractLmfClass
{
    /**
     * LMF Equivalent language data property
     * 
     * @var string
     */
    private $language;
 
    /**
     * LMF Equivalent orthography name data property
     * 
     * @var string 
     */
    private $orthographyName;
    
    /**
     * LMF Equivalent script data property
     * 
     * @var string
     */
    private $script;
    
    /**
     * LMF Equivalent written form data property
     * 
     * @var string
     */
    private $writtenForm;
    
    /* ------------------------ Not LMF ontology parameters ------------------*/    
    /**
     * LMF Equivalent rank data property
     * Was added by Martynas
     * 
     * @var string
     */
    private $_rank = 1;   
    
    public function setLanguage($lang) 
    {
        $this->language = ucfirst(strtolower($lang));
    }
    
    public function getLanguage() 
    {
        return $this->language;
    }
    
    public function getOrthographyName() {
        return $this->orthographyName;
    }

    public function getScript() {
        return $this->script;
    }

    public function setOrthographyName($orthographyName) {
        $this->orthographyName = $orthographyName;
    }

    public function setScript($script) {
        $this->script = $script;
    }
    
    public function setWrittenForm($writtenForm)
    {
        if (strpos($writtenForm, '&') || strpos($writtenForm, '"') || strpos($writtenForm, '>') || strpos($writtenForm, '<') || strpos($writtenForm, ord("'"))) {
            echo "It is possible problem with Equivalent written form: [" . $writtenForm . "]. Please do replace it with htmlspecialchars </br>\n";        
        }
        $this->writtenForm = $writtenForm;
    }
   
    public function getWrittenForm()
    {
        return $this->writtenForm;
    }
    public function setRank($rank) 
    {
        $this->_rank = $rank;
    }
    
    public function getRank() 
    {
        return $this->_rank;
    }
    
    /**
     * {@inheritdoc}
     */
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
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\">\n";
        if ($this->getWrittenForm()) {
            $str .= "\t<writtenForm>{$this->getWrittenForm()}</writtenForm>\n";
        }
        if ($this->getLanguage()) {
            $str .= "\t<language>{$this->getLanguage()}</language>\n";
        }
        if ($this->getOrthographyName()) {
            $str .= "\t<orthographyName>{$this->getOrthographyName()}</orthographyName>\n";
        }
        if ($this->getScript()) {
            $str .= "\t<script>{$this->getScript()}</script>\n";
        }    
        if ($this->getRank()) {
            $str .= "\t<rank>{$this->getRank()}</rank>\n";
        }
        
        $str .= "\t<rdfs:label>{$this->getUri() }</rdfs:label>\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;Equivalent\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        return $str;
    }
}
