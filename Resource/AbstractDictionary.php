<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Resource;
use Rastija\Owl\Uri\UriFactory;

/**
 * Description of DictionaryAbstract
 *
 * @author Virginijus
 */
abstract class AbstractDictionary implements DictionaryInterface {
    /**
     * Resource ID
     * 
     * @var string
     */
    private $resourceId;
    
    /**
     * Resource name
     * 
     * @var string
     */
    private $resourceName;
    
    /**
     * Uri factory for class uri generation
     * 
     * @var Rastija\Owl\Uri\UriFactory 
     */    
    private $uriFactory;
    
    abstract public function generateLmfOwl();
    

    /**
     * {@inheritdoc}
     */
    public function setResourceId($resourceId) {
        $this->resourceId = $resourceId;
    }
    
    public function getResourceId() {
        return $this->resourceId;
    }

    /**
     * {@inheritdoc}
     */    
    public function setResourceName($resourceName) {
        $this->resourceName = $resourceName;
    }
    
    public function getResourceName() {
        return $this->resourceName;
    }
    
    /**
     * Uri factory getter
     * 
     * @return Rastija\Owl\Uri\UriFactory
     */
    public function getUriFactory() {
        return $this->uriFactory;
    }

    public function setUriFactory(UriFactory $uriFactory) {
        $this->uriFactory = $uriFactory;
    }
}
