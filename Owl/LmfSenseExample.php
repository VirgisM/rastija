<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Owl;

/**
 * Sense Example is a class used to illustrate the particular meaning of a Sense 
 * instance. A Sense can have zero to many examples.
 *
 * @author Virginijus
 */
class LmfSenseExample extends AbstractLmfClass
{
    /**
     * Language
     * 
     * @var string 
     */
    private $language;
    
    /**
     * Source
     *  
     * @var string 
     */
    private $source;
    
    /**
     * Text
     * 
     * @var string 
     */
    private $text;
            
    /* ------------------------ Not LMF ontology parameters ------------------*/
    /** 
     * rank
     * @var string
     */
    private $rank = 1;
    
    public function getLanguage() {
        return $this->language;
    }

    public function getSource() {
        return $this->source;
    }

    public function getText() {
        return $this->text;
    }

    public function setLanguage($language) {
        $this->language = $language;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getRank() {
        return $this->rank;
    }

    public function setRank($rank) {
        $this->rank = $rank;
    }

    public function toLmfString() {
        /*
        <owl:NamedIndividual rdf:about="http://www.lexinfo.net/lmf#Liepos-tartuvas/tartuvas-d1ac549dbfdec4a0d49baec90-senseexample-1">
          <j.0:text>Naujažodžių &lt;b&gt;tartùvui&lt;/b&gt; atrenkami taisyklingi ir vartotini naujažodžiai.</j.0:text>
          <j.0:rank>1</j.0:rank>
          <rdfs:label>Naujažodžių &lt;b&gt;tartùvui&lt;/b&gt; atrenkami taisyklingi ir vartotini naujažodžiai.</rdfs:label>
          <rdf:type rdf:resource="http://www.lexinfo.net/lmf#SenseExample"/>
        </<owl:NamedIndividual>
        */
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\">\n";
        if ($this->getLanguage()) {
            $str .= "\t<language>{$this->getLanguage()}</language>\n";
        }
        
        if ($this->getRank()) {
            $str .= "\t<rank>{$this->getRank()}</rank>\n";
        }
        
        if ($this->getSource()) {
            $str .= "\t<source>{$this->getSource()}</source>\n";
        }

        if ($this->getText()) {
            $str .= "\t<text>{$this->getText()}</text>\n";
        }
        
        $str .= "\t<rdfs:label>{$this->getUri()}</rdfs:label>\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;SenseExample\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        return $str;
    }
}
