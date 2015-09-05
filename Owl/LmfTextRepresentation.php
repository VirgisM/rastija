<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;

/**
 * Text Representation is a class representing one textual content of Definition
 * or Statement. When there is more than one variant orthography, 
 * the Text Representation class contains a Unicode string representing the 
 * textual content as well as the unique attribute-value pairs that describe 
 * the specific language, script, and orthography.
 *
 * @author Virginijus
 */
class LmfTextRepresentation extends AbstractRepresentation
{
    private $orthographyName;
    
    private $script;
    
    private $writtenForm;
    
    public function getOrthographyName() {
        return $this->orthographyName;
    }

    public function getScript() {
        return $this->script;
    }

    public function getWrittenForm() {
        return $this->writtenForm;
    }

    public function setOrthographyName($orthographyName) {
        $this->orthographyName = $orthographyName;
    }

    public function setScript($script) {
        $this->script = $script;
    }

    public function setWrittenForm($writtenForm) {
        $this->writtenForm = $writtenForm;
    }

    public function toLmfString() {
        /*<owl:NamedIndividual rdf:about="http://www.lexinfo.net/lmf#Liepos-tartuvas/parasparnis-688f3fe72241429902623b790-textrepresentation">
          <j.0:writtenForm>į parašiutą panaši skraidyklė, sudaryta iš lengvo audinio ir plonų virvelių. &lt;br /&gt; &lt;img src="https://www.xn--ratija-ckb.lt/dictionaries_media/lietuviu_kalbos_naujazodziu_tartuvas/parasparnis.jpg" alt="Nuotrauka iš Flickr.com" /&gt;&lt;br /&gt;&lt;br /&gt;&lt;b&gt;Vartosenos pavyzdžiai:&lt;/b&gt;&lt;br /&gt;Skristi &lt;b&gt;parasparniù&lt;/b&gt; galima bet kuriuo metų laiku.&lt;br /&gt;Iš tikrųjų &lt;b&gt;paraspar̃niai&lt;/a&gt;&lt;/b&gt; ir parašiutai yra labai panašūs.&lt;br /&gt;Pirmasis &lt;b&gt;paraspar̃nių&lt;/b&gt; čempionatas Lietuvoje įvyko 1995 m.&lt;br /&gt;Kokios rekomenduotumėte literatūros apie &lt;b&gt;parasparniùs&lt;/b&gt;?"</j.0:writtenForm>
          <rdfs:label>į parašiutą panaši skraidyklė, sudaryta iš lengvo audinio ir plonų virvelių. &lt;br /&gt; &lt;img src="https://www.xn--ratija-ckb.lt/dictionaries_media/lietuviu_kalbos_naujazodziu_tartuvas/parasparnis.jpg" alt="Nuotrauka iš Flickr.com" /&gt;&lt;br /&gt;&lt;br /&gt;&lt;b&gt;Vartosenos pavyzdžiai:&lt;/b&gt;&lt;br /&gt;Skristi &lt;b&gt;parasparniù&lt;/b&gt; galima bet kuriuo metų laiku.&lt;br /&gt;Iš tikrųjų &lt;b&gt;paraspar̃niai&lt;/a&gt;&lt;/b&gt; ir parašiutai yra labai panašūs.&lt;br /&gt;Pirmasis &lt;b&gt;paraspar̃nių&lt;/b&gt; čempionatas Lietuvoje įvyko 1995 m.&lt;br /&gt;Kokios rekomenduotumėte literatūros apie &lt;b&gt;parasparniùs&lt;/b&gt;?"</rdfs:label>
          <rdf:type rdf:resource="http://www.lexinfo.net/lmf#TextRepresentation"/>
        </owl:NamedIndividual>   
         */
        $str = "<owl:NamedIndividual rdf:about=\"{$this->getUri()}\">\n";
        
        if ($this->getOrthographyName()) {
            $str .= "\t<orthographyName>{$this->getOrthographyName()}</orthographyName>\n";
        }
 
        if ($this->getScript()) {
            $str .= "\t<script>{$this->getScript()}</script>\n";
        }

        if ($this->getWrittenForm()) {
            $str .= "\t<writtenForm>{$this->getWrittenForm()}</writtenForm>\n";
        }

        $str .= "\t<rdfs:label>{$this->getUri()}</rdfs:label>\n";
        $str .= "\t<rdf:type rdf:resource=\"&lmf;TextRepresentation\"/>\n";
        $str .= "</owl:NamedIndividual>\n";
        
        return $str; 
    }
}
