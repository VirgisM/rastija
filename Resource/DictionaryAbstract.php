<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Resource;
/**
 * Description of DictionaryAbstract
 *
 * @author Virginijus
 */
abstract class DictionaryAbstract implements DictionaryInterface {
    private $_resourceId;
    private $_resourceName;
    
    abstract public function generateLmfOwl();
    

    public function setResourceId($resourceId) {
        $this->_resourceId = $resourceId;
    }
    
    public function getResourceId() {
        return $this->_resourceId;
    }

    public function setResourceName($resourceName) {
        $this->_resourceName = $resourceName;
    }
    
    public function getResourceName() {
        return $this->_resourceName;
    }
}
