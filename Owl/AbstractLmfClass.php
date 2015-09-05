<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Owl;
use Rastija\Owl\Uri\AbstractUri;
/**
 * Description of AbstractLmfClass
 *
 * @author Virginijus
 */
abstract class AbstractLmfClass implements LmfClassInterface
{ 
    /**
     * Class uri 
     * 
     * @var AbstractUri
     */
    private $uri;
    
    /**
     * {@inheritdoc}
     */
    public function getUri() {
        return $this->uri->getUri();
    }
    
    /**
     * {@inheritdoc}
     */    
    public function setUri(AbstractUri $uri) {
        $this->uri = $uri;
    }

    abstract public function toLmfString();
}
