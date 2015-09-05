<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl\Uri;

/**
 * Uri factrory interface
 * 
 * @author Virginijus
 */
interface UriFactoryInterface
{
    
    /**
     * Function to create Uri object
     * 
     * @param string $class          object class type (lemma, lexicalEntry and so  on)
     * @param string $writtenForm   (any text)
     * @param string $seed          (seed with will be included in algorithm for ID generation)
     */
    public function create($class, $writtenForm, $seed);
    
    
    public function setUriBase($baseUri);
    
    
    public function getUriBase();
}
