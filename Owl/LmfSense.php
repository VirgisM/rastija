<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Owl;

/**
 * Description of LmfSense
 *
 * @author Virginijus
 */
class LmfSense {
    
    /**
     * LMF Sense rank data property
     * 
     * @var string
     */
    private $_rank = 1;
    
    /* ------------------------ Not LMF ontology parameters ------------------*/

    private $_lemmaWrittenForm;
    /**
     * Base uri. It is required for sense uri generation.
     * 
     * @var string
     */
    private $_uriBase;
    
    /**
     * Sense uri
     * @var string 
     */
    private $_uri;
    
    /**
     * LMF equivalent related to this sense
     * 
     * @var array of \Rastija\Owl\LmfEquivalent's
     */
    private $_equivalents = array();

    public function setRank($rank) 
    {
        $this->_rank = $rank;
    }
    
    public function getRank() 
    {
        return $this->_rank;
    }
    
    /**
     * Lemma written form is required for uri generation. 
     * 
     * @param string $lemmaWrittenForm
     */
    public function setLemmaWrittenForm($lemmaWrittenForm)
    {
        $this->_lemmaWrittenForm = $lemmaWrittenForm; 
    }
    
    public function getLemmaWrittenForm()
    {
        return $this->_lemmaWrittenForm; 
    }
    
    public function setUri($uri)
    {
        $this->_uri = $uri;
    }
    
    public function getUri()
    {
        // Generate uri
        if (!$this->_uri && $this->getLemmaWrittenForm()) {
            $this->_uri = $this->getUriBase() . '.' . $this->_fixUri($this->getLemmaWrittenForm()) 
                    . '.Sense-' . md5('Sense-' . $this->getLemmaWrittenForm() 
                    . $this->getRank()); 
        }
        return $this->_uri;
    }
    
    public function setUriBase($uriBase)
    {
        $this->_uriBase = $uriBase;
    }
    
    public function getUriBase()
    {
        return $this->_uriBase;
    }
    
    public function addEquivalent(LmfEquivalent $equivalent) 
    {
        array_push($this->_equivalents, $equivalent);
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
    
    public function toLmfString()
    {
/*        
<owl:NamedIndividual rdf:about="&lmf;Anglų-lietuvių-kalbų-kompiuterijos-žodynas/sign-60egg58hge90c141a55be26aa-sense"> 
	<rdfs:label>#-sense</rdfs:label> 
	<rdf:type rdf:resource="&lmf;Sense"/> 
	<hasEquivalent rdf:resource="&lmf;Anglų-lietuvių-kalbų-kompiuterijos-žodynas/sign-60egg58hge90c141a55be26aa-equivalent-lie-1"/> 
</owl:NamedIndividual>
 */
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\">\n";
        $str .= "\t<rdfs:label>{$this->getLemmaWrittenForm()}-Sense</rdfs:label>\n";
        foreach ($this->_equivalents as $equivalent) {
            $str .= "\t<hasEquivalent rdf:resource=\"{$equivalent->getUri() }\"/>\n";
        }
        $str .= "\t<rdf:type rdf:resource=\"&lmf;Sense\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        // Equivalents
        foreach ($this->_equivalents as $equivalent) {
            /* @var $equivalent LmfEquivalent  */
            $str .= $equivalent->toLmfString();
        }
        return $str;
    }
}
