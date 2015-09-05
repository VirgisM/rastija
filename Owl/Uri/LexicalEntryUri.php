<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl\Uri;

/**
 * Description of Uri
 *
 * @author Virginijus
 */
class LexicalEntryUri extends AbstractUri
{
    
    /**
     * {@inheritdoc}
     */
    public function buildUri($options) {
        $writtenForm = $options['writtenForm'];
        $seed = $options['seed'];
        
        $uri = $this->getUriBase() . '.' . $this->fixUri($writtenForm) 
                . '.LexicalEntry-' . md5('LexicalEntry-' . $writtenForm . $seed);
        
        $this->setUri($uri);
        
        return $this->getUri();
    }
}
