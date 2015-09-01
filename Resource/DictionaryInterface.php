<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Virginijus
 */
interface DictionaryInterface {
   
    /**
     * Resource Id
     * 
     * @param string $resourceId
     */
    public function setResourceId($resourceId);
    
    /**
     * Resource name
     * @param string $resourceName
     */
    public function setResourceName($resourceName);
    
    /**
     * Generate resource owl to file
     */
    public function generateLmfOwl();

    
}
