<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;

/**
 * Form class is an abstract class representing a lexeme, a morphological 
 * variant of a lexeme or a morph. The Form class manages one or more 
 * orthographical variants of the abstract Form as well as data categories that 
 * describe the attributes of the word form (e.g. lemma, pronunciation, 
 * syllabification). The Form class allows subclasses.
 *
 * @author Virginijus
 */
abstract class AbstractLmfForm extends AbstractLmfClass
{
    private $grammaticalCase;
    
    private $grammaticalGender;
    
    private $grammaticalGrade;
    
    private $grammaticalNumber;
    
    private $grammaticalTense;
    
    private $orhographyName;
    
    private $person;
    
    private $phoneticForm;
    
    private $script;
 
    private $writtenForm;
    
    /* ------------------------ Not LMF ontology parameters ------------------*/

    /**
     * Added by Mykolas
     * 
     * @var string 
     */
    private $accentuation;

    /**
     * Added by Mykolas
     * 
     * @var string 
     */
    private $declension;
    
    /**
     * Added by Mykolas
     * 
     * @var string 
     */
    private $formula;
    
    private $graphics;
    
    private $image;
    
    private $sound;
    
    
    public function getGrammaticalCase() {
        return $this->grammaticalCase;
    }

    public function getGrammaticalGender() {
        return $this->grammaticalGender;
    }

    public function getGrammaticalGrade() {
        return $this->grammaticalGrade;
    }

    public function getGrammaticalNumber() {
        return $this->grammaticalNumber;
    }

    public function getGrammaticalTense() {
        return $this->grammaticalTense;
    }

    public function getOrhographyName() {
        return $this->orhographyName;
    }

    public function getPerson() {
        return $this->person;
    }

    public function getPhoneticForm() {
        return $this->phoneticForm;
    }

    public function getScript() {
        return $this->script;
    }

    public function getWrittenForm() {
        return $this->writtenForm;
    }

    public function setGrammaticalCase($grammaticalCase) {
        $this->grammaticalCase = $grammaticalCase;
    }

    public function setGrammaticalGender($grammaticalGender) {
        $this->grammaticalGender = $grammaticalGender;
    }

    public function setGrammaticalGrade($grammaticalGrade) {
        $this->grammaticalGrade = $grammaticalGrade;
    }

    public function setGrammaticalNumber($grammaticalNumber) {
        $this->grammaticalNumber = $grammaticalNumber;
    }

    public function setGrammaticalTense($grammaticalTense) {
        $this->grammaticalTense = $grammaticalTense;
    }

    public function setOrhographyName($orhographyName) {
        $this->orhographyName = $orhographyName;
    }

    public function setPerson($person) {
        $this->person = $person;
    }

    public function setPhoneticForm($phoneticForm) {
        $this->phoneticForm = $phoneticForm;
    }

    public function setScript($script) {
        $this->script = $script;
    }

    public function setWrittenForm($writtenForm) {
        $this->writtenForm = $writtenForm;
    }

    /**
     * Accentuation getter
     * 
     * @return string
     */    
    public function getAccentuation() {
        return $this->accentuation;
    }

    public function getDeclension() {
        return $this->declension;
    }

    public function getFormula() {
        return $this->formula;
    }

    public function getGraphics() {
        return $this->graphics;
    }

    /**
     * Image getter
     * 
     * @retun string link to image file
     */    
    public function getImage() {
        return $this->image;
    }
    
    /**
     * 
     * @return string ling to sound file
     */
    public function getSound() {
        return $this->sound;
    }

    /**
     * Accentuation setter
     * 
     * @param string $accentuation
     */
    public function setAccentuation($accentuation) {
        $this->accentuation = $accentuation;
    }

    public function setDeclension($declension) {
        $this->declension = $declension;
    }

    public function setFormula($formula) {
        $this->formula = $formula;
    }

    public function setGraphics($graphics) {
        $this->graphics = $graphics;
    }
    
    /**
     * Image setter
     * 
     * @param string $imageUri link to image file
     */
    public function setImage($image) {
        $this->image = urlencode($image);
    }

    /**
     * Sound setter
     * 
     * @param string $soundUri link to sound file
     */    
    public function setSound($sound) {
        $this->sound = urlencode($sound);
    }
    
    /**
     *  All not empty class attributes will be converted to LMF atributes
     * 
     * @return string
     */
    protected function getAttributesLmfStr() {
        $str = '';
        if ($this->getWrittenForm()) {
            $str .= "\t<writtenForm>{$this->getWrittenForm() }</writtenForm>\n";
        }
            
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
        
        return $str;
    }
}
