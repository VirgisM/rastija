<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Owl;
use Rastija\Owl\Uri;
/**
 * Description of LmfLemma
 *
 * @author Virginijus
 */
class LmfLemma extends AbstractLmfForm implements LmfClassInterface 
{
    
    
    public function toLmfString() {
        $str .= "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\">\n";
        $str .= "\t<writtenForm>{$this->getWrittenForm() }</writtenForm>\n";
        $str .= "\t<rdfs:label>{$this->getWrittenForm() }-Lemma</rdfs:label>\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;Lemma\"/>\n";
        $str .= "</owl:NamedIndividual>\n";

        return $str;
    }
}
