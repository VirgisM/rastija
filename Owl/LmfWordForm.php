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
class LmfWordForm extends AbstractLmfForm
{
    /**
     * @todo Iplemant additional variables
     * 
     * private $homonymNo
     * private $tag
     */
    
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
            $str .= "\t<sound>{$this->getSound() }</sound>\n";
        }
        
        $str .= "\t<rdfs:label>{$this->getUri() }</rdfs:label>\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;WordForm\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        return $str;        
    }
}
