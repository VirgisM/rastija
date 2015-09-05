<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Owl;
use Rastija\Owl\Uri\AbstractUri;
/**
 * Description of LmfLemma
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
        $str .= "\t<writtenForm>{$this->getWrittenForm() }</writtenForm>\n";
        
        if ($this->getAccentuation()) {
            $str .= "\t<accentuation>{$this->getAccentuation() }</accentuation>\n";
        }
        
        if ($this->getDeclension()) {
            $str .= "\t<declension>{$this->getDeclension() }</declension>\n";
        }

        if ($this->getFormula()) {
            $str .= "\t<formula>{$this->getFormula() }</formula>\n";
        }
        
        if ($this->getGrammaticalCase()) {
            $str .= "\t<grammaticalCase>{$this->getGrammaticalCase() }</grammaticalCase>\n";
        }        
        
        if ($this->getGrammaticalGender()) {
            $str .= "\t<grammaticalGender>{$this->getGrammaticalGender() }</grammaticalGender>\n";
        }        
     
        if ($this->getGrammaticalGrade()) {
            $str .= "\t<grammaticalGrade>{$this->getGrammaticalGrade() }</grammaticalGrade>\n";
        }

        if ($this->getGrammaticalNumber()) {
            $str .= "\t<grammaticalNumber>{$this->getGrammaticalNumber() }</grammaticalNumber>\n";
        }

        if ($this->getGrammaticalTense()) {
            $str .= "\t<grammaticalTense>{$this->getGrammaticalTense() }</grammaticalTense>\n";
        }

        if ($this->getGraphics()) {
            $str .= "\t<graphics>{$this->getGraphics() }</graphics>\n";
        }

        if ($this->getImage()) {
            $str .= "\t<image>{$this->getImage() }</image>\n";
        }

        if ($this->getOrhographyName()) {
            $str .= "\t<orhographyName>{$this->getOrhographyName() }</orhographyName>\n";
        }        

        if ($this->getPerson()) {
            $str .= "\t<person>{$this->getPerson() }</person>\n";
        }

        if ($this->getPhoneticForm()) {
            $str .= "\t<phoneticForm>{$this->getPhoneticForm() }</phoneticForm>\n";
        }

        if ($this->getScript()) {
            $str .= "\t<script>{$this->getScript() }</script>\n";
        }

        if ($this->getSound()) {
            $str .= "\t<script>{$this->getSound() }</script>\n";
        }
        
        $str .= "\t<rdfs:label>{$this->getWrittenForm() }-Lemma</rdfs:label>\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;Lemma\"/>\n";
        $str .= "</owl:NamedIndividual>\n";

        return $str;
    }
}
