<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl\Uri;

/**
 * Lemma Uri generator class
 *
 * @author Virginijus
 */
class LemmaUri extends AbstractUri
{
    
    /**
     * {@inheritdoc}
     */
    public function buildUri($options) {
        $writtenForm = $options['writtenForm'];
        $seed = $options['seed'];
        
        $uri = $this->getUriBase() . '.' . $this->fixUri($writtenForm) 
                . '.Lemma-' . md5('Lemma-' . $writtenForm . $seed);
        
        $this->setUri($uri);
        
        return $this->getUri();
    }
}
