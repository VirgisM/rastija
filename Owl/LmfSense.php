<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Owl;
use Rastija\Owl\Uri\AbstractUri;
/**
 * Description of LmfSense
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
     * LMF equivalent related to this sense
     * (object property hasEquivalent)
     * 
     * @var array of \Rastija\Owl\LmfEquivalent's
     */
    private $equivalents = array();   
    
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
    
    public function toLmfString() {
/*        
<owl:NamedIndividual rdf:about="&lmf;Anglų-lietuvių-kalbų-kompiuterijos-žodynas/sign-60egg58hge90c141a55be26aa-sense"> 
	<rdfs:label>#-sense</rdfs:label> 
	<rdf:type rdf:resource="&lmf;Sense"/> 
	<hasEquivalent rdf:resource="&lmf;Anglų-lietuvių-kalbų-kompiuterijos-žodynas/sign-60egg58hge90c141a55be26aa-equivalent-lie-1"/> 
</owl:NamedIndividual>
 */
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\">\n";
        $str .= "\t<rank>{$this->getRank()}-Sense</rank>\n";
        $str .= "\t<rdfs:label>{$this->getLemmaWrittenForm()}-Sense</rdfs:label>\n";
        
        foreach ($this->equivalents as $equivalent) {
            $str .= "\t<hasEquivalent rdf:resource=\"{$equivalent->getUri() }\"/>\n";
        }
        
        $str .= "\t<rdf:type rdf:resource=\"&lmf;Sense\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        // Equivalents
        foreach ($this->equivalents as $equivalent) {
            /* @var $equivalent LmfEquivalent  */
            $str .= $equivalent->toLmfString();
        }
        return $str;
    }
}
