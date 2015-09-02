<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Owl;

/**
 * Word Form is a Form subclass representing a form that a lexeme can take when 
 * used in a sentence or a phrase. So, Word Form class can manage simple 
 * lexemes, compounds and multi-word expressions.
 *
 * @author Virginijus
 */
class LmfWordForm extends LmfFormAbstract implements LmfClassInterface
{
    /**
     * Base uri. It is required for wordForm uri generation.
     * 
     * @var string
     */
    private $_uriBase;
    
    /**
     * WordForm uri
     * @var string 
     */
    private $_uri;    
    
    public function setUri($uri)
    {
        $this->_uri = $uri;
    }
    
    public function getUri()
    {
        // Generate uri
        if (!$this->_uri && $this->getWrittenForm()) {
            $this->_uri = $this->getUriBase() . '.' . $this->_fixUri($this->getWrittenForm()) . '.WordForm-' . md5('WordForm-' . $this->getWrittenForm(). $this->getRank()); 
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

    /**
     * {@inheritdoc}
     */
    public function toLmfString() {
        /*
        <owl:NamedIndividual rdf:about="http://www.lexinfo.net/lmf#Liepos-tartuvas/adresynas-b47767f992ce8624345aca182-wordform">
          <j.0:sound>https://www.xn--ratija-ckb.lt/dictionaries_media/lietuviu_kalbos_naujazodziu_tartuvas/adresynas.mp3</j.0:sound>
          <j.0:accentuation>1</j.0:accentuation>
          <rdfs:label>adresynas-wordform</rdfs:label>
          <rdf:type rdf:resource="http://www.lexinfo.net/lmf#WordForm"/>
        </owl:NamedIndividual>        
         */
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\">\n";
        if ($this->getSound()) {
            $str = "<sound>{$this->getSound()}</sound>\n";
        }
        if ($this->getAccentuation()) {
            $str = "<accentuation>{$this->getAccentuation()}</accentuation>\n";
        }
        if ($this->getImage()) {
            $str .= "\t<rdfs:label>{$this->getWrittenForm()}-Sense</rdfs:label>\n";
        }
        $str .= "\t<rdf:type rdf:resource=\"&lmf;WordForm\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        // Equivalents
        foreach ($this->_equivalents as $equivalent) {
            /* @var $equivalent LmfEquivalent  */
            $str .= $equivalent->toLmfString();
        }
        return $str;        
    }
}
