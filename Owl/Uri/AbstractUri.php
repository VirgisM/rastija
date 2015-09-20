<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl\Uri;

/**
 * Abstract Uri class
 *
 * @author Virginijus
 */
abstract class AbstractUri
{
    /**
     * Base uri. It is required for wordForm uri generation.
     * 
     * @var string
     */
    private $uriBase;
    
    /**
     * WordForm uri
     * @var string 
     */
    private $uri;    
    
    /**
     * Uri setter
     * 
     * @param string $uri
     */
    public function setUri($uri) {
        $this->uri = $uri;
    }
    
    /**
     * Uri getter
     */
    public function getUri() {
        return $this->uri;
    }
    
    /**
     * Function will remove unallowed simbols from uri
     * 
     * @param string $uri
     */
    protected function fixUri($uri)
    {
        return preg_replace('/[\[\]\{\}\<\>\'\"\&\s\t\n\,\;\%\ \!\?]/i', '_', $uri);
    }
    
    public function setUriBase($uriBase)
    {
        $this->uriBase = $uriBase;
    }
    
    public function getUriBase()
    {
        return $this->uriBase;
    }
    
    /**
     * Build an unique uri based on option
     * 
     * @param array $options Building options
     */
    abstract public function buildUri($options);
}
