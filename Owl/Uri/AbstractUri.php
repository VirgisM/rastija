<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UriAbstract
 *
 * @author Virginijus
 */
abstract class UriAbstract {
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
     * @param type $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }
    
    /**
     * Uri getter
     */
    public function getUri();
    
    /**
     * Function will remove unallowed simbols from uri
     * 
     * @param string $uri
     */
    private function fixUri($uri)
    {
        return preg_replace('/[\[\]\{\}\<\>\'\"\&\s\t\n]/i', '_', $uri);
    }
    
    public function setUriBase($uriBase)
    {
        $this->uriBase = $uriBase;
    }
    
    public function getUriBase()
    {
        return $this->uriBase;
    }
}
