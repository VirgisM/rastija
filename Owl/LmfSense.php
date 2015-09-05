<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;
use Rastija\Owl\Uri\AbstractUri;
use Rastija\Owl\LmfDefinition;

/**
 * Sense is a class representing one meaning of a lexical entry. 
 * The Sense class allows subclasses. The Sense class allows for hierarchical 
 * senses in that a sense may be more specific than another sense of 
 * the same lexical entry.
 *
 * @author Virginijus
 */
class LmfSense extends AbstractLmfClass
{
    /* @todo Nedd to implement 
    private $animacy;
    private $dating;
    private $fequency;
    private $style;
    */
      
    /**
     * LMF equivalent that is related to this sense
     * (object property hasEquivalent)
     * 
     * @var array of \Rastija\Owl\LmfEquivalent's
     */
    private $equivalents = array();
    
    /**
     * LMF definition that is related to this sense
     *
     * @var \Rastija\Owl\LmfDefinition
     */
    private $definition;
    
    
    /* ------------------------ Not LMF ontology parameters ------------------*/
    /**
     * LMF Sense rank data property
     * Added by Martynas to arrange senses
     * 
     * @var string
     */
    private $rank = 1;
    
    private $lemmaWrittenForm;

    public function setRank($rank) {
        $this->rank = $rank;
    }
    
    public function getRank() {
        return $this->rank;
    }
    
    /**
     * Lemma written form is required for uri generation. 
     * 
     * @param string $lemmaWrittenForm
     */
    public function setLemmaWrittenForm($lemmaWrittenForm) {
        $this->lemmaWrittenForm = $lemmaWrittenForm; 
    }
    
    public function getLemmaWrittenForm() {
        return $this->lemmaWrittenForm; 
    }
    
    /**
     * Add Equivalent (hasEquivalent)
     * 
     * @param \Rastija\Owl\LmfEquivalent $equivalent
     */
    public function addEquivalent(LmfEquivalent $equivalent) {
        array_push($this->equivalents, $equivalent);
    }
    
    public function getDefinition() {
        return $this->definition;
    }

    public function setDefinition(LmfDefinition $definition) {
        $this->definition = $definition;
    }

    public function toLmfString() {
        /*        
        <owl:NamedIndividual rdf:about="&lmf;Anglų-lietuvių-kalbų-kompiuterijos-žodynas/sign-60egg58hge90c141a55be26aa-sense"> 
            <rdfs:label>#-sense</rdfs:label> 
            <rdf:type rdf:resource="&lmf;Sense"/> 
            <hasEquivalent rdf:resource="&lmf;Anglų-lietuvių-kalbų-kompiuterijos-žodynas/sign-60egg58hge90c141a55be26aa-equivalent-lie-1"/> 
        </owl:NamedIndividual>
         */
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\">\n";
        $str .= "\t<rank>{$this->getRank()}</rank>\n";
        $str .= "\t<rdfs:label>{$this->getLemmaWrittenForm()}-Sense</rdfs:label>\n";
        
        foreach ($this->equivalents as $equivalent) {
            $str .= "\t<hasEquivalent rdf:resource=\"{$equivalent->getUri() }\"/>\n";
        }
        
        if ($this->getDefinition()) {
            $str .= "\t<hasDefinition rdf:resource=\"{$this->getDefinition()->getUri() }\"/>\n";
        }
        
        $str .= "\t<rdf:type rdf:resource=\"&lmf;Sense\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        // Equivalents
        foreach ($this->equivalents as $equivalent) {
            /* @var $equivalent LmfEquivalent  */
            $str .= $equivalent->toLmfString();
        }
        
        // Definition
        if ($this->getDefinition()) {
            $str .= $this->getDefinition()->toLmfString();
        }
        
        return $str;
    }
}
