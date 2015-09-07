<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;
use Rastija\Owl\Uri\AbstractUri;

/**
 * Lemma is a Form subclass representing a word form chosen by convention to 
 * designate the Lexical Entry. The Lemma class is in a one to one aggregate 
 * association with the Lexical Entry that overrides the multiplicity inherited 
 * from the Form class. The lemma is usually equivalent to one of the inflected 
 * forms, the root or stem, or MWE, e.g. compound, idiomatic phrase. 
 * The convention for selecting the lemma can vary by language, language family,
 * or editorial choice.
 *
 * @author Virginijus
 */
class LmfLemma extends AbstractLmfForm 
{   
    /**
     * {@inheritdoc}
     */    
    public function toLmfString() {
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\">\n";

        $str .= parent::getAttributesLmfStr();
        
        $str .= "\t<rdfs:label>{$this->getWrittenForm() }-Lemma</rdfs:label>\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;Lemma\"/>\n";
        $str .= "</owl:NamedIndividual>\n";

        return $str;
    }
}
