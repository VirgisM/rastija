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
class LmfWordForm implements LmfClassInterface {
    
    /**
     * Accentuaion of 
     * @var string 
     */
    private $_accentuation;
    
    /**
     * Image 
     * 
     * @var string uri of image location 
     */
    private $_image;
    
    /**
     * Sound
     * 
     * @var type 
     */
    private $_sound;
    
    /**
     * Accentuation setter
     * 
     * @param string $acc
     */
    public function setAccentuation($acc) {
        $this->_accentuation = $acc;
    }
    
    /**
     * Accentuation getter
     * 
     * @return string
     */
    public function getAccentuation(){
        return $this->_accentuation;
    }
    
    /**
     * Image setter
     * 
     * @param string $imageUri link to image file
     */
    public function setImage($imageUri) {
        $this->_image = htmlentities($imageUri);
    }
    
    /**
     * Image getter
     * 
     * @retun string
     */
    public function getImage() {
        return $this->_image;
    }
    
    /**
     * Sound setter
     * 
     * @param string $soundUri link to sound file
     */
    public function setSound($soundUri) {
        $this->_sound = htmlentities($soundUri);
    }

    /**
     * 
     * @return string ling to sound file
     */
    public function getSound() {
        return $this->_sound;
    }
    
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
        if ($this->getSound()) {
            $str = "<sound>{$this->getSound()}</sound>\n";
        }
        if ($this->getAccentuation()) {
            $str = "<accentuation>{$this->getAccentuation()}</accentuation>\n";
        }
        if ($this->getImage()) {
            $str .= "\t<rdfs:label>{$this->getWrittenForm()}-Sense</rdfs:label>\n";
        }
        $str .= "\t<rdf:type rdf:resource=\"&lmf;WordForm\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        // Equivalents
        foreach ($this->_equivalents as $equivalent) {
            /* @var $equivalent LmfEquivalent  */
            $str .= $equivalent->toLmfString();
        }
        return $str;        
    }
}
