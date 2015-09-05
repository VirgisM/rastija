<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;
use Rastija\Owl\Uri\AbstractUri;

/**
 * Interface for each LMF class
 *
 * @author Virginijus
 */
interface LmfClassInterface {
    
    /**
     * Class will be converted to LMF OWL string
     */
    public function toLmfString();
    
    
    /**
     * Get clas uri
     *
     * @return AbstractUri object
     */
    public function getUri();
    
    /**
     * Set class uri
     * 
     * @param Rastija\Owl\Uri\AbstractUri $uri
     */
    public function setUri(AbstractUri $uri);
}
