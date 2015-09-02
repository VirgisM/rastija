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
abstract class AbstractLmfForm
{
    private $grammaticalCase;
    
    private $gramamticalGender;
    
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

    public function getGramamticalGender() {
        return $this->gramamticalGender;
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

    public function setGramamticalGender($gramamticalGender) {
        $this->gramamticalGender = $gramamticalGender;
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
        $this->image = $image;
    }

    /**
     * Sound setter
     * 
     * @param string $soundUri link to sound file
     */    
    public function setSound($sound) {
        $this->sound = $sound;
    }
    
    
}
