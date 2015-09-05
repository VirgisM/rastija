<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;
use Rastija\Owl\LmfTextRepresentation;

/**
 * Definition is a class representing a narrative description of a sense. 
 * It is displayed for human users to facilitate their understanding of 
 * the meaning of a Lexical Entry and is not meant to be processable by computer
 *  programs. A Sense instance can have zero to many definitions. 
 * Each Definition instance may be associated with zero to many 
 * Text Representation instances in order to manage the text definition in more
 * than one language or script. The narrative description can be expressed in a 
 * different language and/or script than the one of the Lexical Entry instance.
 *
 * @author Virginijus
 */
class LmfDefinition extends AbstractLmfClass
{
    /**
     * Text representations of definition
     * 
     * @var array of Rastija\Owl\LmfTextRepresentation
     */
    private $textRepresentations = array();
    
    public function getTextRepresentations() {
        return $this->textRepresentations;
    }

    public function addTextRepresentation(LmfTextRepresentation $textRepresentation) {
        array_push($this->textRepresentations, $textRepresentation);
    }

    /**
     * {@inheritdc}
     */
    public function toLmfString() {
        /*
          <owl:NamedIndividual rdf:about="http://www.lexinfo.net/lmf#Liepos-tartuvas/indaplovė-4d0b954f0bef437c29dfa73fa-definition">  <rdf:Description rdf:about="http://www.lexinfo.net/lmf#Liepos-tartuvas/indaplovė-4d0b954f0bef437c29dfa73fa-definition">df:Description rdf:about="http://www.lexinfo.net/lmf#Liepos-tartuvas/indaplovė-4d0b954f0bef437c29dfa73fa-definition">
            <j.0:hasTextRepresentation rdf:resource="http://www.lexinfo.net/lmf#Liepos-tartuvas/indaplovė-4d0b954f0bef437c29dfa73fa-textrepresentation"/>    <j.0:hasTextRepresentation rdf:resource="http://www.lexinfo.net/lmf#Liepos-tartuvas/indaplovė-4d0b954f0bef437c29dfa73fa-textrepresentation"/>
            <rdfs:label>automatinis indų plovimo prietaisas.&lt;br /&gt;&lt;br /&gt;&lt;b&gt;Vartosenos pavyzdžiai:&lt;/b&gt;&lt;br /&gt;Mestelėjus &lt;b&gt;jausmãženklį&lt;/b&gt;, nebereikia ieškoti žodžių.&lt;br /&gt;Žinutės tekstą mėgstama pabaigti &lt;b&gt;jausmãženkliu&lt;/b&gt;.&lt;br /&gt;Kas Lietuvoje tyrinėja &lt;b&gt;jausmãženklius&lt;/b&gt;?&lt;br /&gt;Grafiniai jausmus reiškiantys ženklai gali būti vadinami &lt;b&gt;jausmãženkliais&lt;/b&gt;.</rdfs:label>    <rdfs:label>automatinis indų plovimo prietaisas.&lt;br /&gt;&lt;br /&gt;&lt;b&gt;Vartosenos pavyzdžiai:&lt;/b&gt;&lt;br /&gt;Mestelėjus &lt;b&gt;jausmãženklį&lt;/b&gt;, nebereikia ieškoti žodžių.&lt;br /&gt;Žinutės tekstą mėgstama pabaigti &lt;b&gt;jausmãženkliu&lt;/b&gt;.&lt;br /&gt;Kas Lietuvoje tyrinėja &lt;b&gt;jausmãženklius&lt;/b&gt;?&lt;br /&gt;Grafiniai jausmus reiškiantys ženklai gali būti vadinami &lt;b&gt;jausmãženkliais&lt;/b&gt;.</rdfs:label>
            <rdf:type rdf:resource="http://www.lexinfo.net/lmf#Definition"/>    <rdf:type rdf:resource="http://www.lexinfo.net/lmf#Definition"/>
          </owl:NamedIndividual>
        */
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\">\n";

        foreach ($this->textRepresentations as $textRepresentation) {
            $str .= "\t<hasTextRepresentation rdf:resource=\"{$textRepresentation->getUri() }\"/>\n";
        }
        
        $str .= "\t<rdfs:label>{$this->getUri()}</rdfs:label>\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;Definition\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        foreach ($this->textRepresentations as $textRepresentation) {
            $str .= $textRepresentation->toLmfString();
        }
        
        return $str;
    }
}
